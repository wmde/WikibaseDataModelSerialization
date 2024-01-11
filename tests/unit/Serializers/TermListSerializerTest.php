<?php

namespace Tests\Wikibase\DataModel\Serializers;

use PHPUnit\Framework\TestCase;
use Serializers\Exceptions\UnsupportedObjectException;
use Wikibase\DataModel\Serializers\TermListSerializer;
use Wikibase\DataModel\Serializers\TermSerializer;
use Wikibase\DataModel\Term\Term;
use Wikibase\DataModel\Term\TermFallback;
use Wikibase\DataModel\Term\TermList;

/**
 * @covers Wikibase\DataModel\Serializers\TermListSerializer
 *
 * @license GPL-2.0-or-later
 * @author Addshore
 */
class TermListSerializerTest extends TestCase {

	/**
	 * @return TermListSerializer
	 */
	private function buildSerializer() {
		return new TermListSerializer( new TermSerializer() );
	}

	/**
	 * @dataProvider serializationProvider
	 */
	public function testSerialization( TermList $input, $expected ) {
		$serializer = $this->buildSerializer();

		$output = $serializer->serialize( $input );

		$this->assertEquals( $expected, $output );
	}

	public static function serializationProvider() {
		return [
			[
				new TermList( [] ),
				[],
			],
			[
				new TermList( [
					new Term( 'en', 'Water' ),
					new Term( 'it', 'Lama' ),
					new TermFallback( 'pt', 'Lama', 'de', 'zh' ),
				] ),
				[
					'en' => [ 'language' => 'en', 'value' => 'Water' ],
					'it' => [ 'language' => 'it', 'value' => 'Lama' ],
					'pt' => [ 'language' => 'de', 'value' => 'Lama', 'source' => 'zh' ],
				],
			],
		];
	}

	public function testWithUnsupportedObject() {
		$serializer = $this->buildSerializer();

		$this->expectException( UnsupportedObjectException::class );
		$serializer->serialize( new \stdClass() );
	}

	public function testTermListSerializerSerializesTermLists() {
		$serializer = $this->buildSerializer();

		$terms = new TermList( [ new Term( 'en', 'foo' ) ] );

		$serial = [];
		$serial['en'] = [
			'language' => 'en',
			'value' => 'foo',
		];

		$this->assertEquals( $serial, $serializer->serialize( $terms ) );
		$this->assertEquals( [], $serializer->serialize( new TermList() ) );
	}
}

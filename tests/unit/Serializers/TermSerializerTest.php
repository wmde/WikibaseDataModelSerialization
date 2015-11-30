<?php

namespace Tests\Wikibase\DataModel\Serializers;

use Wikibase\DataModel\Serializers\FacetContainerSerializer;
use Wikibase\DataModel\Serializers\TermSerializer;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\DataModel\Serializers\TermSerializer
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 */
class TermSerializerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider serializationProvider
	 */
	public function testSerialization( Term $input, array $expected ) {
		$serializer = new TermSerializer( new FacetContainerSerializer( array() ) );

		$output = $serializer->serialize( $input );

		$this->assertEquals( $expected, $output );
	}

	public function serializationProvider() {
		//TODO: test facets
		return array(
			array(
				new Term ( 'en', 'SomeValue' ),
				array(
					'language' => 'en',
					'value' => 'SomeValue',
				)
			),
		);
	}

	public function testWithUnsupportedObject() {
		$serializer = new TermSerializer( new FacetContainerSerializer( array() ) );
		$this->setExpectedException( 'Serializers\Exceptions\UnsupportedObjectException' );
		$serializer->serialize( new \stdClass() );
	}

}

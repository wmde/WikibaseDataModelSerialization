<?php

namespace Tests\Wikibase\DataModel\Serializers;

use PHPUnit_Framework_TestCase;
use Wikibase\DataModel\Serializers\Internal\AliasGroupSerializer;
use Wikibase\DataModel\Term\AliasGroup;
use Wikibase\DataModel\Term\AliasGroupFallback;

/**
 * @covers Wikibase\DataModel\Serializers\Internal\AliasGroupSerializer
 *
 * @licence GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class AliasGroupSerializerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider serializationProvider
	 */
	public function testSerialization( $serialization, $object ) {
		$serializer = new AliasGroupSerializer();
		$this->assertSame( $serialization, $serializer->serialize( $object ) );
	}

	public function serializationProvider() {
		return array(
			array(
				array(),
				new AliasGroup( 'en', array() )
			),
			array(
				array(
					array( 'language' => 'en', 'value' => 'One' )
				),
				new AliasGroup( 'en', array( 'One' ) )
			),
			array(
				array(
					array( 'language' => 'en', 'value' => 'One' ),
					array( 'language' => 'en', 'value' => 'Pony' )
				),
				new AliasGroup( 'en', array( 'One', 'Pony' ) )
			),
			array(
				array(
					array( 'language' => 'de', 'value' => 'One', 'source' => 'fr' ),
					array( 'language' => 'de', 'value' => 'Pony', 'source' => 'fr' ),
				),
				new AliasGroupFallback( 'en', array( 'One', 'Pony' ), 'de', 'fr' )
			)
		);
	}

}

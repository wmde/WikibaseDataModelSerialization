<?php

namespace Tests\Wikibase\DataModel\Serializers;

use PHPUnit_Framework_TestCase;
use stdClass;
use Wikibase\DataModel\Serializers\Internal\AliasGroupListSerializer;
use Wikibase\DataModel\Term\AliasGroup;
use Wikibase\DataModel\Term\AliasGroupList;

/**
 * @covers Wikibase\DataModel\Serializers\Internal\AliasGroupListSerializer
 *
 * @licence GNU GPL v2+
 * @author Addshore
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class AliasGroupListSerializerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @param bool $useObjectsForMaps
	 *
	 * @return AliasGroupListSerializer
	 */
	private function buildSerializer( $useObjectsForMaps = false ) {
		$aliasGroupSerializer = $this->getMock( 'Wikibase\DataModel\Serializers\AliasGroupSerializer' );
		$aliasGroupSerializer->expects( $this->any() )
			->method( 'serialize' )
			->will( $this->returnCallback( function( AliasGroup $aliasGroup ) {
				return $aliasGroup->getAliases();
			} ) );

		return new AliasGroupListSerializer( $aliasGroupSerializer, $useObjectsForMaps );
	}

	/**
	 * @dataProvider serializationProvider
	 */
	public function testSerialization( AliasGroupList $input, $useObjectsForMaps, $expected ) {
		$serializer = $this->buildSerializer( $useObjectsForMaps );

		$output = $serializer->serialize( $input );

		$this->assertEquals( $expected, $output );
	}

	public function serializationProvider() {
		return array(
			array(
				new AliasGroupList( array( new AliasGroup( 'en', array() ) ) ),
				false,
				array(),
			),
			array(
				new AliasGroupList( array( new AliasGroup( 'en', array() ) ) ),
				true,
				new \stdClass()
			),
			array(
				new AliasGroupList( array( new AliasGroup( 'en', array( 'One' ) ) ) ),
				false,
				array( 'en' => array( 'One' ) )
			),
			array(
				new AliasGroupList( array( new AliasGroup( 'en', array( 'One', 'Pony' ) ) ) ),
				false,
				array( 'en' => array( 'One', 'Pony' ) )
			),
			array(
				new AliasGroupList( array(
					new AliasGroup( 'en', array( 'One', 'Pony' ) ),
					new AliasGroup( 'de', array( 'foo', 'bar' ) )
				) ),
				false,
				array(
					'en' => array( 'One', 'Pony' ),
					'de' => array( 'foo', 'bar' ),
				)
			),
		);
	}

	public function testAliasGroupListSerializerWithOptionObjectsForMaps() {
		$serializer = $this->buildSerializer( true );

		$aliases = new AliasGroupList( array( new AliasGroup( 'en', array( 'foo', 'bar' ) ) ) );

		$serial = new \stdClass();
		$serial->en = array( 'foo', 'bar' );

		$this->assertEquals( $serial, $serializer->serialize( $aliases ) );
	}

}

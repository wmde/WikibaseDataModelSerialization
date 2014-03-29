<?php

namespace Tests\Wikibase\DataModel\Serializers;

use DataValues\Serializers\DataValueSerializer;
use DataValues\StringValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Serializers\SnakSerializer;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;

/**
 * @covers Wikibase\DataModel\Serializers\SnakSerializer
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 * @author Adam Shorland
 */
class SnakSerializerTest extends SerializerBaseTest {

	public function buildSerializer() {
		return new SnakSerializer( new DataValueSerializer(), $this->getMockDataTypeLookup() );
	}

	public function buildSerializerWithNoDataTypeLookup() {
		return new SnakSerializer( new DataValueSerializer() );
	}

	/**
	 * @return \Wikibase\DataModel\Lookups\DataTypeLookup
	 */
	private function getMockDataTypeLookup() {
		$mock = $this->getMock( '\Wikibase\DataModel\Lookups\DataTypeLookup' );
		$mock->expects( $this->any() )
			->method( 'getDataTypeIdForProperty' )
			->with( $this->isInstanceOf( '\Wikibase\DataModel\Entity\PropertyId' ) )
			->will( $this->returnValue( 'imaDataTypeId' ) );
		return $mock;
	}

	public function serializableProvider() {
		return array(
			array(
				new PropertyNoValueSnak( 42 )
			),
			array(
				new PropertySomeValueSnak( 42 )
			),
			array(
				new PropertyValueSnak( 42, new StringValue( 'hax' ) )
			),
		);
	}

	public function nonSerializableProvider() {
		return array(
			array(
				5
			),
			array(
				array()
			),
			array(
				new ItemId( 'Q42' )
			),
		);
	}

	public function serializationProvider() {
		return array(
			array(
				array(
					'snaktype' => 'novalue',
					'property' => 'P42',
					'datatype' => 'imaDataTypeId'
				),
				new PropertyNoValueSnak( 42 )
			),
			array(
				array(
					'snaktype' => 'somevalue',
					'property' => 'P42',
					'datatype' => 'imaDataTypeId'
				),
				new PropertySomeValueSnak( 42 )
			),
			array(
				array(
					'snaktype' => 'value',
					'property' => 'P42',
					'datavalue' => array(
						'type' => 'string',
						'value' => 'hax'
					),
					'datatype' => 'imaDataTypeId'
				),
				new PropertyValueSnak( 42, new StringValue( 'hax' ) )
			),
		);
	}

	public function testSerializeWithNoDataTypeLookup() {
		$serializer = $this->buildSerializerWithNoDataTypeLookup();
		$serialization = $serializer->serialize( new PropertyValueSnak( 42, new StringValue( 'hax' ) ) );

		$this->assertEquals(
			array(
				'snaktype' => 'value',
				'property' => 'P42',
				'datavalue' => array(
					'type' => 'string',
					'value' => 'hax'
				)
			),
			$serialization
		);
	}
}

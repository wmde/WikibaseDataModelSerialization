<?php

namespace Tests\Wikibase\DataModel\Serializers;

use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Serializers\ClaimSerializer;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\SnakList;

/**
 * @covers Wikibase\DataModel\Serializers\ClaimSerializer
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class ClaimSerializerTest extends SerializerBaseTest {

	protected function buildSerializer() {
		$snakSerializerMock = $this->getMock( '\Serializers\Serializer' );
		$snakSerializerMock->expects( $this->any() )
			->method( 'serialize' )
			->will( $this->returnValue( array(
				'snaktype' => 'novalue',
				'property' => 'P42'
			) ) );

		$snaksSerializerMock = $this->getMock( '\Serializers\Serializer' );
		$snaksSerializerMock->expects( $this->any() )
			->method( 'serialize' )
			->will( $this->returnValue( array(
				'P42' => array(
					array(
						'snaktype' => 'novalue',
						'property' => 'P42'
					)
				)
			) ) );

		return new ClaimSerializer( $snakSerializerMock, $snaksSerializerMock );
	}

	public function serializableProvider() {
		return array(
			array(
				new Claim( new PropertyNoValueSnak( 42 ) )
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
		$serializations = array();

		$serializations[] = array(
			array(
				'mainsnak' => array(
					'snaktype' => 'novalue',
					'property' => 'P42'
				)
			),
			new Claim( new PropertyNoValueSnak( 42 ) )
		);

		$claim = new Claim( new PropertyNoValueSnak( 42 ) );
		$claim->setGuid( 'q42' );
		$serializations[] = array(
			array(
				'id' => 'q42',
				'mainsnak' => array(
					'snaktype' => 'novalue',
					'property' => "P42"
				)
			),
			$claim
		);

		return $serializations;
	}

	public function testQualifiersOrderSerialization() {
		$snakSerializerMock = $this->getMock( '\Serializers\Serializer' );
		$snakSerializerMock->expects( $this->any() )
			->method( 'serialize' )
			->will( $this->returnValue( array(
				'snaktype' => 'novalue',
				'property' => 'P42'
			) ) );

		$snaksSerializerMock = $this->getMock( '\Serializers\Serializer' );
		$snaksSerializerMock->expects( $this->any() )
			->method( 'serialize' )
			->will( $this->returnValue( array() ) );

		$referencesSerializerMock = $this->getMock( '\Serializers\Serializer' );
		$claimSerializer = new ClaimSerializer( $snakSerializerMock, $snaksSerializerMock, $referencesSerializerMock );

		$claim = new Claim( new PropertyNoValueSnak( 42 ) );
		$claim->setQualifiers( new SnakList( array(
			new PropertyNoValueSnak( 42 ),
			new PropertySomeValueSnak( 24 ),
			new PropertyNoValueSnak( 24 )
		) ) );
		$this->assertEquals(
			array(
				'mainsnak' => array(
					'snaktype' => 'novalue',
					'property' => 'P42'
				),
				'qualifiers' => array(),
				'qualifiers-order' => array(
					'P42',
					'P24'
				)
			),
			$claimSerializer->serialize( $claim )
		);
	}

}

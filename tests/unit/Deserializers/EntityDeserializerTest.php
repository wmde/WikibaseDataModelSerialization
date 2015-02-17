<?php

namespace Tests\Wikibase\DataModel\Deserializers;

use Deserializers\Deserializer;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Claim\Claims;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Statement\Statement;

/**
 * @covers Wikibase\DataModel\Deserializers\EntityDeserializer
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class EntityDeserializerTest extends DeserializerBaseTest {

	/**
	 * @return Deserializer
	 */
	public function buildDeserializer() {
		$entityIdDeserializerMock = $this->getMock( '\Deserializers\Deserializer' );
		$entityIdDeserializerMock->expects( $this->any() )
			->method( 'deserialize' )
			->with( $this->equalTo( 'Q42' ) )
			->will( $this->returnValue( new ItemId( 'Q42' ) ) );

		$claim = new Statement( new Claim( new PropertyNoValueSnak( 42 ) ) );
		$claim->setGuid( 'test' );

		$claimsDeserializerMock = $this->getMock( '\Deserializers\Deserializer' );
		$claimsDeserializerMock->expects( $this->any() )
			->method( 'deserialize' )
			->with( $this->equalTo( array(
				'P42' => array(
					array(
						'mainsnak' => array(
							'snaktype' => 'novalue',
							'property' => 'P42'
						),
						'type' => 'statement',
						'rank' => 'normal'
					)
				)
			) ) )
			->will( $this->returnValue( new Claims( array( $claim ) ) ) );

		$entityDeserializerMock = $this->getMockForAbstractClass(
			'\Wikibase\DataModel\Deserializers\EntityDeserializer',
			array( 'item', $entityIdDeserializerMock, $claimsDeserializerMock )
		);
		$entityDeserializerMock->expects( $this->any() )
			->method( 'getPartiallyDeserialized' )
			->will( $this->returnValue( new Item() ) );

		return $entityDeserializerMock;
	}

	public function deserializableProvider() {
		return array(
			array(
				array(
					'type' => 'item'
				)
			),
		);
	}

	public function nonDeserializableProvider() {
		return array(
			array(
				5
			),
			array(
				array()
			),
			array(
				array(
					'type' => 'property'
				)
			),
		);
	}

	public function deserializationProvider() {
		$provider = array(
			array(
				new Item(),
				array(
					'type' => 'item'
				)
			),
		);

		$entity = new Item( new ItemId( 'Q42' ) );
		$provider[] = array(
			$entity,
			array(
				'type' => 'item',
				'id' => 'Q42'
			)
		);


		$entity = new Item();
		$entity->setLabels( array(
			'en' => 'Nyan Cat',
			'fr' => 'Nyan Cat'
		) );
		$provider[] = array(
			$entity,
			array(
				'type' => 'item',
				'labels' => array(
					'en' => array(
						'language' => 'en',
						'value' => 'Nyan Cat'
					),
					'fr' => array(
						'language' => 'fr',
						'value' => 'Nyan Cat'
					)
				)
			)
		);

		$entity = new Item();
		$entity->setDescriptions( array(
			'en' => 'A Nyan Cat',
			'fr' => 'A Nyan Cat'
		) );
		$provider[] = array(
			$entity,
			array(
				'type' => 'item',
				'descriptions' => array(
					'en' => array(
						'language' => 'en',
						'value' => 'A Nyan Cat'
					),
					'fr' => array(
						'language' => 'fr',
						'value' => 'A Nyan Cat'
					)
				)
			)
		);

		$entity = new Item();
		$entity->setAliases( 'en', array( 'Cat', 'My cat' ) );
		$entity->setAliases( 'fr', array( 'Cat' ) );
		$provider[] = array(
			$entity,
			array(
				'type' => 'item',
				'aliases' => array(
					'en' => array(
						array(
							'language' => 'en',
							'value' => 'Cat'
						),
						array(
							'language' => 'en',
							'value' => 'My cat'
						)
					),
					'fr' => array(
						array(
							'language' => 'fr',
							'value' => 'Cat'
						)
					)
				)
			)
		);

		$entity = new Item();
		$entity->getStatements()->addNewStatement( new PropertyNoValueSnak( 42 ), null, null, 'test' );
		$provider[] = array(
			$entity,
			array(
				'type' => 'item',
				'claims' => array(
					'P42' => array(
						array(
							'mainsnak' => array(
								'snaktype' => 'novalue',
								'property' => 'P42'
							),
							'type' => 'statement',
							'rank' => 'normal'
						)
					)
				)
			)
		);

		return $provider;
	}

	/**
	 * @dataProvider invalidDeserializationProvider
	 */
	public function testInvalidSerialization( $serialization ) {
		$this->setExpectedException( '\Deserializers\Exceptions\DeserializationException' );
		$this->buildDeserializer()->deserialize( $serialization );
	}

	public function invalidDeserializationProvider() {
		return array(
			'label with integer language code' => array(
				array(
					'type' => 'item',
					'labels' => array(
						8 => array(
							'language' => 8,
							'value' => 'Cat',
						),
					),
				),
			),
			'label without array key for language code' => array(
				array(
					'type' => 'item',
					'labels' => array(
						array(
							'language' => 'en',
							'value' => 'Cat',
						),
					),
				),
			),
			'label with integer value' => array(
				array(
					'type' => 'item',
					'labels' => array(
						'en' => array(
							'language' => 'en',
							'value' => 8,
						),
					),
				),
			),
			'alias with interger language code' => array(
				array(
					'type' => 'item',
					'aliases' => array(
						8 =>
							array(
								array(
									'language' =>  8,
									'value' => 'Cat',
								),
							),
					),
				)
			),
			'alias without array key for language code' => array(
				array(
					'type' => 'item',
					'aliases' => array(
						array(
							array(
								'language' =>  'en',
								'value' => 'Cat',
							),
						),
					),
				)
			),
			'alias as a string only' => array(
				array(
					'type' => 'item',
					'aliases' => array(
						'en' => 'Cat'
					)
				)
			),
			'label fallback language term' => array(
				array(
					'type' => 'item',
					'labels' => array(
						'en' => array(
							'language' => 'en-cat',
							'value' => 'mew',
						),
					),
				),
			),
			'label with integer fallback language code' => array(
				array(
					'type' => 'item',
					'labels' => array(
						'en' => array(
							'language' => 8,
							'value' => 'Cat',
						),
					),
				),
			),
			'label language term with source' => array(
				array(
					'type' => 'item',
					'labels' => array(
						'en-cat' => array(
							'language' => 'en-cat',
							'value' => 'mew',
							'source' => 'en',
						),
					),
				),
			),
			'description fallback language term' => array(
				array(
					'type' => 'item',
					'descriptions' => array(
						'en' => array(
							'language' => 'en-cat',
							'value' => 'mew',
						),
					),
				),
			),
			'description language term with source' => array(
				array(
					'type' => 'item',
					'descriptions' => array(
						'en-cat' => array(
							'language' => 'en-cat',
							'value' => 'mew',
							'source' => 'en',
						),
					),
				),
			),
			'alias fallback language term' => array(
				array(
					'type' => 'item',
					'aliases' => array(
						'en' =>
							array(
								array(
									'language' =>  'en-cat',
									'value' => 'mew',
								),
							),
					),
				)
			),
			'alias language term with source' => array(
				array(
					'type' => 'item',
					'aliases' => array(
						'en-cat' =>
							array(
								array(
									'language' =>  'en-cat',
									'value' => 'mew',
									'source' => 'en',
								),
							),
					),
				)
			),
		);
	}

}

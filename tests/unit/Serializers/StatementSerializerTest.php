<?php

namespace Tests\Wikibase\DataModel\Serializers;

use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Claim\Statement;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Reference;
use Wikibase\DataModel\ReferenceList;
use Wikibase\DataModel\Serializers\StatementSerializer;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\SnakList;

/**
 * @covers Wikibase\DataModel\Serializers\StatementSerializer
 *
 * @licence GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class StatementSerializerTest extends SerializerBaseTest {

	protected function buildSerializer() {
		$statementSerializerMock = $this->getMock( '\Serializers\Serializer' );
		$statementSerializerMock->expects( $this->any() )
			->method( 'serialize' )
			->will( $this->returnValue( array( 
				'mainsnak' => array(
					'snaktype' => 'novalue',
					'property' => 'P42'
				)
			) ) );

		$referencesSerializerMock = $this->getMock( '\Serializers\Serializer' );
		$referencesSerializerMock->expects( $this->any() )
			->method( 'serialize' )
			->will( $this->returnValue( array(
				array(
					'hash' => 'da39a3ee5e6b4b0d3255bfef95601890afd80709',
					'snaks' => array()
				)
			) ) );

		return new StatementSerializer( $statementSerializerMock, $referencesSerializerMock );
	}

	public function serializableProvider() {
		return array(
			array(
				new Statement( new PropertyNoValueSnak( 42 ) )
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
				'claim' => array(
					'mainsnak' => array(
						'snaktype' => 'novalue',
						'property' => 'P42'
					),
				),
				'rank' => 'normal'
			),
			new Statement( new PropertyNoValueSnak( 42 ) )
		);

		$statement = new Statement( new PropertyNoValueSnak( 42 ) );
		$statement->setRank( Claim::RANK_PREFERRED );
		$serializations[] = array(
			array(
				'claim' => array(
					'mainsnak' => array(
						'snaktype' => 'novalue',
						'property' => 'P42'
					),
				),
				'rank' => 'preferred'
			),
			$statement
		);

		$statement = new Statement( new PropertyNoValueSnak( 42 ) );
		$statement->setRank( Claim::RANK_DEPRECATED );
		$serializations[] = array(
			array(
				'claim' => array(
					'mainsnak' => array(
						'snaktype' => 'novalue',
						'property' => 'P42'
					),
				),
				'rank' => 'deprecated'
			),
			$statement
		);

		$statement = new Statement( new PropertyNoValueSnak( 42 ) );
		$statement->setQualifiers( new SnakList( array() ) );
		$serializations[] = array(
			array(
				'claim' => array(
					'mainsnak' => array(
						'snaktype' => 'novalue',
						'property' => 'P42'
					),
				),
				'rank' => 'normal'
			),
			$statement
		);

		$statement = new Statement( new PropertyNoValueSnak( 42 ) );
		$statement->setReferences( new ReferenceList( array(
			new Reference()
		) ) );
		$serializations[] = array(
			array(
				'claim' => array(
					'mainsnak' => array(
						'snaktype' => 'novalue',
						'property' => 'P42'
					),
				),
				'references' => array(
					array(
						'hash' => 'da39a3ee5e6b4b0d3255bfef95601890afd80709',
						'snaks' => array()
					)
				),
				'rank' => 'normal'
			),
			$statement
		);

		return $serializations;
	}

}

<?php

namespace Tests\Wikibase\DataModel\Deserializers;

use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Deserializers\StatementDeserializer;
use Wikibase\DataModel\Reference;
use Wikibase\DataModel\ReferenceList;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\SnakList;
use Wikibase\DataModel\Statement\Statement;

/**
 * @covers Wikibase\DataModel\Deserializers\StatementDeserializer
 *
 * @licence GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class StatementDeserializerTest extends DeserializerBaseTest {

	/**
	 * @var References $references
	 */
	static $references = null;

	public function buildDeserializer() {
		$statementDeserializerMock = $this->getMock( '\Deserializers\Deserializer' );
		$statementDeserializerMock->expects( $this->any() )
			->method( 'deserialize' )
			->with( $this->equalTo( array( 'this is' => 'a claim' ) ) )
			->will( $this->returnValue( new Claim( new PropertyNoValueSnak( 42 ) ) ) );

		$referencesDeserializerMock = $this->getMock( '\Deserializers\Deserializer' );
		$referencesDeserializerMock->expects( $this->any() )
			->method( 'deserialize' )
			->with( $this->equalTo( array( 'ref' ) ) )
			->will( $this->returnValue( self::$references ) );

		return new StatementDeserializer( $statementDeserializerMock, $referencesDeserializerMock );
	}

	public function deserializableProvider() {
		return array(
			array(
				array(
					'claim' => array( 'this is' => 'a claim' )
				)
			),
			array(
				array(
					'claim' => array( 'this is' => 'a claim' ),
					'rank' => 'normal'
				)
			),
			array(
				array(
					'claim' => array( 'this is' => 'a claim' ),
					'references' => array( 'ref' ),
					'rank' => 'normal'
				)
			)
		);
	}

	public function nonDeserializableProvider() {
		return array(
			array(
				42
			),
			array(
				array(
					'rank' => 'normal'
				)
			)
		);
	}

	public function deserializationProvider() {
		$serializations = array();

		$serializations[] = array(
			new Statement( new PropertyNoValueSnak( 42 ) ),
			array(
				'claim' => array( 'this is' => 'a claim' )
			)
		);

		$statement = new Statement( new PropertyNoValueSnak( 42 ) );
		$statement->setRank( Statement::RANK_PREFERRED );
		$serializations[] = array(
			$statement,
			array(
				'claim' => array( 'this is' => 'a claim' ),
				'rank' => 'preferred'
			)
		);

		$statement = new Statement( new PropertyNoValueSnak( 42 ) );
		$statement->setRank( Statement::RANK_NORMAL );
		$serializations[] = array(
			$statement,
			array(
				'claim' => array( 'this is' => 'a claim' ),
				'rank' => 'normal'
			)
		);

		$statement = new Statement( new PropertyNoValueSnak( 42 ) );
		$statement->setRank( Statement::RANK_DEPRECATED );
		$serializations[] = array(
			$statement,
			array(
				'claim' => array( 'this is' => 'a claim' ),
				'rank' => 'deprecated'
			)
		);

		if ( self::$references === null ) {
			self::$references = new ReferenceList( array( new Reference( new SnakList( new PropertyNoValueSnak( 42 ) ) ) ) );
		}

		$statement = new Statement( new PropertyNoValueSnak( 42 ) );
		$statement->setReferences( self::$references );
		$serializations[] = array(
			$statement,
			array(
				'claim' => array( 'this is' => 'a claim' ),
				'references' => array( 'ref' )
			)
		);

		return $serializations;
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
			array(
				array(
					'foo' => 'bar'
				)
			),
			array(
				array(
					'claim' => array( 'this is' => 'a claim' ),
					'rank' => 'nyan-cat'
				)
			),
		);
	}
}

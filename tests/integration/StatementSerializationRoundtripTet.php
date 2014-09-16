<?php

namespace Tests\Wikibase\DataModel;

use DataValues\Deserializers\DataValueDeserializer;
use DataValues\Serializers\DataValueSerializer;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Claim\Statement;
use Wikibase\DataModel\DeserializerFactory;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\SerializerFactory;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\SnakList;

/**
 * @licence GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class StatementSerializationRoundtripTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider statementsProvider
	 */
	public function testStatementSerializationRoundtrips( Statement $statement ) {
		$serializerFactory = new SerializerFactory( new DataValueSerializer() );
		$deserializerFactory = new DeserializerFactory(
			new DataValueDeserializer(),
			new BasicEntityIdParser()
		);

		$serialization = $serializerFactory->newStatementSerializer()->serialize( $statement );
		$newStatement = $deserializerFactory->newStatementDeserializer()->deserialize( $serialization );
		$this->assertEquals( $statement->getHash(), $newStatement->getHash() );
	}

	public function statementsProvider() {
		$statements = array();

		$statements[] = array(
			new Statement( new Claim( new PropertyNoValueSnak( 42 ) ) )
		);

		$statement = new Claim( new PropertyNoValueSnak( 42 ) );
		$statement->setGuid( 'q42' );
		$statements[] = array( $statement );

		$statement = new Statement( new Claim( new PropertyNoValueSnak( 42 ) ) );
		$statement->setRank( Claim::RANK_PREFERRED );
		$statements[] = array( $statement );

		$statement = new Statement( new Claim( new PropertyNoValueSnak( 42 ) ) );
		$statement->setRank( Claim::RANK_DEPRECATED );
		$statements[] = array( $statement );

		$statement = new Statement( new Claim( new PropertyNoValueSnak( 42 ) ) );
		$statement->setQualifiers( new SnakList( array() ) );
		$statements[] = array( $statement );

		$statement = new Claim( new PropertyNoValueSnak( 42 ) );
		$statement->setQualifiers( new SnakList( array(
			new PropertySomeValueSnak( 42 ),
			new PropertyNoValueSnak( 42 ),
			new PropertySomeValueSnak( 24 )
		) ) );
		$statements[] = array( $statement );

		$statement = new Statement( new Claim( new PropertyNoValueSnak( 42 ) ) );
		$statement->setQualifiers( new SnakList( array(
			new PropertyNoValueSnak( 42 )
		) ) );
		$statements[] = array( $statement );

		return $statements;
	}
}

<?php

namespace Tests\Wikibase\DataModel;

use DataValues\Deserializers\DataValueDeserializer;
use DataValues\Serializers\DataValueSerializer;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\DeserializerFactory;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\SerializerFactory;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;

/**
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class StatementListSerializationRoundtripTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider statementListProvider
	 */
	public function testStatementListSerializationRoundtrips( StatementList $statementList ) {
		$serializerFactory = new SerializerFactory( new DataValueSerializer() );
		$deserializerFactory = new DeserializerFactory(
			new DataValueDeserializer(),
			new BasicEntityIdParser()
		);

		$serialization = $serializerFactory->newStatementListSerializer()->serialize( $statementList );
		$newStatementList = $deserializerFactory->newStatementListDeserializer()->deserialize( $serialization );
		$this->assertEquals( $statementList, $newStatementList );
	}

	public function statementListProvider() {
		$statement1 = new Statement( new Claim( new PropertyNoValueSnak( 42 ) ) );
		$statement1->setGuid( 'test' );

		$statement2 = new Statement( new Claim( new PropertyNoValueSnak( 42 ) ) );
		$statement2->setGuid( 'test2' );

		$statement3 = new Statement( new Claim( new PropertyNoValueSnak( 42 ) ) );
		$statement3->setGuid( 'teststatement' );

		return array(
			array(
				new StatementList()
			),
			array(
				new StatementList( array(
					$statement1
				) )
			),
			array(
				new StatementList( array(
					$statement1,
					$statement2
				) )
			),
			array(
				new StatementList( array(
					$statement1,
					$statement3
				) )
			),
		);
	}
}

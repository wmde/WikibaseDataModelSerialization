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
	public function testStatementSerializationRoundtrips( Claim $claim ) {
		$serializerFactory = new SerializerFactory( new DataValueSerializer() );
		$deserializerFactory = new DeserializerFactory(
			new DataValueDeserializer(),
			new BasicEntityIdParser()
		);

		$serialization = $serializerFactory->newClaimSerializer()->serialize( $claim );
		$newClaim = $deserializerFactory->newClaimDeserializer()->deserialize( $serialization );
		$this->assertEquals( $claim->getHash(), $newClaim->getHash() );
	}

	public function statementsProvider() {
		$claims = array();

		$claims[] = array(
			new Claim( new PropertyNoValueSnak( 42 ) )
		);

		$claims[] = array(
			new Statement( new PropertyNoValueSnak( 42 ) )
		);

		$claim = new Claim( new PropertyNoValueSnak( 42 ) );
		$claim->setGuid( 'q42' );
		$claims[] = array( $claim );

		$claim = new Statement( new PropertyNoValueSnak( 42 ) );
		$claim->setRank( Claim::RANK_PREFERRED );
		$claims[] = array( $claim );

		$claim = new Statement( new PropertyNoValueSnak( 42 ) );
		$claim->setRank( Claim::RANK_DEPRECATED );
		$claims[] = array( $claim );

		$claim = new Statement( new PropertyNoValueSnak( 42 ) );
		$claim->setQualifiers( new SnakList( array() ) );
		$claims[] = array( $claim );

		$claim = new Claim( new PropertyNoValueSnak( 42 ) );
		$claim->setQualifiers( new SnakList( array(
			new PropertySomeValueSnak( 42 ),
			new PropertyNoValueSnak( 42 ),
			new PropertySomeValueSnak( 24 )
		) ) );
		$claims[] = array( $claim );

		$claim = new Statement( new PropertyNoValueSnak( 42 ) );
		$claim->setQualifiers( new SnakList( array(
			new PropertyNoValueSnak( 42 )
		) ) );
		$claims[] = array( $claim );

		return $claims;
	}
}

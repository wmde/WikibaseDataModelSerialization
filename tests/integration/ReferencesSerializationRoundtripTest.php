<?php

namespace Tests\Wikibase\DataModel;

use DataValues\Deserializers\DataValueDeserializer;
use DataValues\Serializers\DataValueSerializer;
use Wikibase\DataModel\DeserializerFactory;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\Reference;
use Wikibase\DataModel\ReferenceList;
use Wikibase\DataModel\References;
use Wikibase\DataModel\SerializerFactory;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\SnakList;

/**
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class ReferencesSerializationRoundtripTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider referencesProvider
	 */
	public function testReferencesSerializationRoundtrips( References $references ) {
		$serializerFactory = new SerializerFactory( new DataValueSerializer() );
		$deserializerFactory = new DeserializerFactory(
			new DataValueDeserializer(),
			new BasicEntityIdParser()
		);

		$serialization = $serializerFactory->newReferencesSerializer()->serialize( $references );
		$newReferences = $deserializerFactory->newReferencesDeserializer()->deserialize( $serialization );
		$this->assertReferencesEquals( $references, $newReferences );
	}

	public function referencesProvider() {
		return array(
			array(
				new ReferenceList( array() )
			),
			array(
				new ReferenceList( array(
					new Reference()
				) )
			),
			array(
				new ReferenceList( array(
					new Reference( new SnakList( array(
						new PropertyNoValueSnak( 42 )
					) ) ),
					new Reference( new SnakList( array(
						new PropertyNoValueSnak( 43 )
					) ) )
				) )
			),
		);
	}

	/**
	 * @param References $expected
	 * @param References $actual
	 */
	public function assertReferencesEquals( References $expected, References $actual ) {
		$this->assertTrue( $actual->equals( $expected ), 'The two References are different' );
	}
}

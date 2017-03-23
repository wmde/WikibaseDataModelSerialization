<?php

namespace Tests\Wikibase\DataModel;

use DataValues\Deserializers\DataValueDeserializer;
use DataValues\Serializers\DataValueSerializer;
use Wikibase\DataModel\DeserializerFactory;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\SerializerFactory;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;

/**
 * @license GPL-2.0+
 * @author Thomas Pellissier Tanon
 * @author Thiemo Mättig
 */
class EntitySerializationRoundtripTest extends \PHPUnit_Framework_TestCase {

	public function itemProvider() {
		$empty = new Item( new ItemId( 'Q42' ) );

		$withLabels = new Item();
		$withLabels->setLabel( 'en', 'Nyan Cat' );
		$withLabels->setLabel( 'fr', 'Nyan Cat' );

		$withDescriptions = new Item();
		$withDescriptions->setDescription( 'en', 'Nyan Cat' );
		$withDescriptions->setDescription( 'fr', 'Nyan Cat' );

		$withAliases = new Item();
		$withAliases->setAliases( 'en', array( 'Cat', 'My cat' ) );
		$withAliases->setAliases( 'fr', array( 'Cat' ) );

		$withStatements = new Item();
		$withStatements->getStatements()->addNewStatement( new PropertyNoValueSnak( 42 ), null, null, 'guid' );

		$withSiteLinks = new Item();
		$withSiteLinks->getSiteLinkList()->addNewSiteLink( 'enwiki', 'Nyan Cat' );

		return [
			[ $empty ],
			[ $withLabels ],
			[ $withDescriptions ],
			[ $withAliases ],
			[ $withStatements ],
			[ $withSiteLinks ],
		];
	}

	/**
	 * @dataProvider itemProvider
	 */
	public function testItemSerializationRoundtrips( Item $item ) {
		$serializer = $this->newSerializerFactory()->newItemSerializer();
		$deserializer = $this->newDeserializerFactory()->newItemDeserializer();

		$serialization = $serializer->serialize( $item );
		$newEntity = $deserializer->deserialize( $serialization );

		$this->assertTrue( $item->equals( $newEntity ) );
	}

	public function propertyProvider() {
		return [
			[ Property::newFromType( 'string' ) ],
		];
	}

	/**
	 * @dataProvider propertyProvider
	 */
	public function testPropertySerializationRoundtrips( Property $property ) {
		$serializer = $this->newSerializerFactory()->newPropertySerializer();
		$deserializer = $this->newDeserializerFactory()->newPropertyDeserializer();

		$serialization = $serializer->serialize( $property );
		$newEntity = $deserializer->deserialize( $serialization );

		$this->assertTrue( $property->equals( $newEntity ) );
	}

	private function newSerializerFactory() {
		return new SerializerFactory( new DataValueSerializer() );
	}

	private function newDeserializerFactory() {
		return new DeserializerFactory( new DataValueDeserializer(), new BasicEntityIdParser() );
	}

}

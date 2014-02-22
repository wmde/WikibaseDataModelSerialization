<?php

namespace Wikibase\DataModel\Deserializers;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Wikibase\DataModel\Entity\Item;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class ItemDeserializer extends EntityDeserializer {

	/**
	 * @var Deserializer
	 */
	private $siteLinkDeserializer;

	/**
	 * @param Deserializer $entityIdDeserializer
	 * @param Deserializer $claimsDeserializer
	 * @param Deserializer $siteLinkDeserializer
	 */
	public function __construct( Deserializer $entityIdDeserializer, Deserializer $claimsDeserializer, Deserializer $siteLinkDeserializer ) {
		parent::__construct( 'item', $entityIdDeserializer, $claimsDeserializer );

		$this->siteLinkDeserializer = $siteLinkDeserializer;
	}

	protected function getPartiallyDeserialized( array $serialization ) {
		$item = Item::newEmpty();

		$this->setSiteLinksFromSerialization( $serialization, $item );

		return $item;
	}

	private function setSiteLinksFromSerialization( array $serialization, Item $item ) {
		if ( !array_key_exists( 'sitelinks', $serialization ) ) {
			return;
		}
		$this->assertAttributeIsArray( $serialization, 'sitelinks' );

		foreach( $serialization['sitelinks'] as $sitelinkSerialization ) {
			$item->addSiteLink( $this->siteLinkDeserializer->deserialize( $sitelinkSerialization ) );
		}
	}
}
<?php

namespace Wikibase\DataModel\Serializers;

use Serializers\DispatchableSerializer;
use Serializers\Exceptions\SerializationException;
use Serializers\Exceptions\UnsupportedObjectException;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\SiteLink;
use Wikibase\DataModel\Slotty;

/**
 * Package private
 *
 * @license GPL-2.0+
 * @author Thomas Pellissier Tanon
 */
class SiteLinkSerializer implements DispatchableSerializer {

	/**
	 * @see Serializer::isSerializerFor
	 *
	 * @param mixed $object
	 *
	 * @return bool
	 */
	public function isSerializerFor( $object ) {
		return $object instanceof SiteLink;
	}

	/**
	 * @see Serializer::serialize
	 *
	 * @param SiteLink $object
	 *
	 * @throws SerializationException
	 * @return array
	 */
	public function serialize( $object ) {
		if ( !$this->isSerializerFor( $object ) ) {
			throw new UnsupportedObjectException(
				$object,
				'SiteLinkSerializer can only serialize SiteLink objects'
			);
		}

		return $this->getSerialized( $object );
	}

	private function getSerialized( SiteLink $siteLink ) {
		$serialization = array(
			'site' => $siteLink->getSiteId(),
			'title' => $siteLink->getPageName(),
			'badges' => $this->serializeBadges( $siteLink->getBadges() )
		);

		if ( $siteLink instanceof Slotty ) {
			$slottySerializer = new SlottySerializer();
			$slots = $slottySerializer->serialize( $siteLink );
			$serialization = array_merge( $slots, $serialization );
		}

		return $serialization;
	}

	/**
	 * @param ItemId[] $badges
	 *
	 * @return string[]
	 */
	private function serializeBadges( array $badges ) {
		$serialization = array();

		foreach ( $badges as $badge ) {
			$serialization[] = $badge->getSerialization();
		}

		return $serialization;
	}

}

<?php

namespace Wikibase\DataModel\Serializers;

use Serializers\DispatchableSerializer;
use Serializers\Exceptions\SerializationException;
use Serializers\Exceptions\UnsupportedObjectException;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\ExtraValuesAssigner;
use Wikibase\DataModel\SiteLink;

/**
 * Package private
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class SiteLinkSerializer implements DispatchableSerializer {

	/**
	 * @var ExtraValuesAssigner
	 */
	private $extraValuesAssigner;

	public function __construct( ExtraValuesAssigner $extraValuesAssigner ) {
		$this->extraValuesAssigner = $extraValuesAssigner;
	}

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

		return $this->extraValuesAssigner->addExtraValues( $serialization, $siteLink );
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

<?php

namespace Wikibase\DataModel\Serializers\Internal;

use Wikibase\DataModel\Serializers\AliasGroupListSerializer as AliasGroupListSerializerInterface;
use Wikibase\DataModel\Serializers\AliasGroupSerializer as AliasGroupSerializerInterface;
use Wikibase\DataModel\Term\AliasGroupList;

/**
 * Package private
 *
 * @licence GNU GPL v2+
 * @author Addshore
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class AliasGroupListSerializer implements AliasGroupListSerializerInterface {

	/**
	 * @var AliasGroupSerializerInterface
	 */
	private $aliasGroupSerializer;

	/**
	 * @var bool
	 */
	private $useObjectsForMaps;

	/**
	 * @param AliasGroupSerializerInterface $aliasGroupSerializer
	 * @param bool $useObjectsForMaps
	 */
	public function __construct( AliasGroupSerializerInterface $aliasGroupSerializer, $useObjectsForMaps ) {
		$this->aliasGroupSerializer = $aliasGroupSerializer;
		$this->useObjectsForMaps = $useObjectsForMaps;
	}

	/**
	 * @see \Wikibase\DataModel\Serializers\AliasGroupListSerializer::serialize
	 *
	 * @param AliasGroupList $aliasGroupList
	 *
	 * @return array[]
	 */
	public function serialize( AliasGroupList $aliasGroupList ) {
		$serialization = array();

		foreach ( $aliasGroupList->getIterator() as $aliasGroup ) {
			$serialization[$aliasGroup->getLanguageCode()] = $this->aliasGroupSerializer->serialize( $aliasGroup );
		}

		if ( $this->useObjectsForMaps ) {
			$serialization = (object)$serialization;
		}

		return $serialization;
	}

}

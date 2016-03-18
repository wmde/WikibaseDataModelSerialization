<?php

namespace Wikibase\DataModel\Serializers\Internal;

use Serializers\Exceptions\UnsupportedObjectException;
use Serializers\Serializer;
use Wikibase\DataModel\Term\AliasGroup;
use Wikibase\DataModel\Term\AliasGroupFallback;

/**
 * Package private
 *
 * @licence GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class AliasGroupSerializer implements Serializer {

	/**
	 * @param AliasGroup $object
	 *
	 * @return array[]
	 */
	public function serialize( $object ) {
		$this->assertIsSerializerFor( $object );
		return $this->getSerialized( $object );
	}

	private function assertIsSerializerFor( $object ) {
		if ( !( $object instanceof AliasGroup ) ) {
			throw new UnsupportedObjectException(
				$object,
				'AliasGroupSerializer can only serialize AliasGroup objects'
			);
		}
	}

	/**
	 * @param AliasGroup $aliasGroup
	 *
	 * @return array[]
	 */
	private function getSerialized( AliasGroup $aliasGroup ) {
		$serialization = array();
		$language = $aliasGroup->getLanguageCode();

		foreach ( $aliasGroup->getAliases() as $value ) {
			$result = array(
				'language' => $language,
				'value' => $value
			);

			if ( $aliasGroup instanceof AliasGroupFallback ) {
				$result['language'] = $aliasGroup->getActualLanguageCode();
				$result['source'] = $aliasGroup->getSourceLanguageCode();
			}

			$serialization[] = $result;
		}

		return $serialization;
	}

}
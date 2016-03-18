<?php

namespace Wikibase\DataModel\Serializers\Internal;

use Wikibase\DataModel\Serializers\AliasGroupSerializer as AliasGroupSerializerInterface;
use Wikibase\DataModel\Term\AliasGroup;
use Wikibase\DataModel\Term\AliasGroupFallback;

/**
 * Package private
 *
 * @licence GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class AliasGroupSerializer implements AliasGroupSerializerInterface {

	/**
	 * @see \Wikibase\DataModel\Serializers\AliasGroupSerializer::serialize
	 *
	 * @param AliasGroup $aliasGroup
	 *
	 * @return array[]
	 */
	public function serialize( AliasGroup $aliasGroup ) {
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

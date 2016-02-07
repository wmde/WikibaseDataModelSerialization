<?php

namespace Wikibase\DataModel\Serializers;

use Serializers\Exceptions\UnsupportedObjectException;
use Serializers\Serializer;
use Wikibase\DataModel\Term\Term;
use Wikibase\DataModel\Term\LanguageFallbackInfo;

/**
 * Package private
 *
 * @since 2.1
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 * @author Daniel Kinzler
 */
class LanguageFallbackInfoSerializer implements Serializer {

	/**
	 * @param Term $object
	 *
	 * @return array
	 */
	public function serialize( $object ) {
		$this->assertIsSerializerFor( $object );
		return $this->getSerialized( $object );
	}

	private function assertIsSerializerFor( $object ) {
		if ( !( $object instanceof LanguageFallbackInfo ) ) {
			throw new UnsupportedObjectException(
				$object,
				'LanguageFallbackInfoSerializer can only serialize LanguageFallbackInfo objects'
			);
		}
	}

	/**
	 * @param Term $term
	 *
	 * @return array
	 */
	private function getSerialized( LanguageFallbackInfo $term ) {
		$result = array();

		//FIXME: LanguageFallbackInfo probably doesn't need getActualLanguageCode() at all.
		//$result['language'] = $term->getActualLanguageCode();
		$result['source'] = $term->getSourceLanguageCode();

		return $result;
	}

}

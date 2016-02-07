<?php

namespace Wikibase\DataModel\Deserializers;

use Deserializers\DispatchableDeserializer;
use Deserializers\Exceptions\DeserializationException;
use Wikibase\DataModel\Term\LanguageFallbackInfo;
use Wikibase\DataModel\Term\Term;

/**
 * Package private
 *
 * @since 2.1
 *
 * @licence GNU GPL v2+
 * @author Daniel Kinzler
 */
class LanguageFallbackInfoDeserializer implements DispatchableDeserializer {

	/**
	 * @param mixed $serialization
	 *
	 * @return Term
	 * @throws DeserializationException
	 */
	public function deserialize( $serialization ) {
		return $this->getDeserialized( $serialization );
	}

	/**
	 * @param array $serialization
	 *
	 * @return Term
	 */
	private function getDeserialized( $serialization ) {
		return new LanguageFallbackInfo( $serialization['language'], $serialization['source'] );
	}

	/**
	 * @param mixed $serialization
	 *
	 * @return boolean
	 */
	public function isDeserializerFor( $serialization ) {
		return isset( $serialization['language'] ) && isset( $serialization['source'] );
	}
}

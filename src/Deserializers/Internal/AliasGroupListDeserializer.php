<?php

namespace Wikibase\DataModel\Deserializers\Internal;

use Deserializers\Exceptions\DeserializationException;
use Deserializers\Exceptions\InvalidAttributeException;
use Deserializers\Exceptions\MissingAttributeException;
use Wikibase\DataModel\Deserializers\AliasGroupListDeserializer as AliasGroupListDeserializerInterface;
use Wikibase\DataModel\Term\AliasGroup;
use Wikibase\DataModel\Term\AliasGroupList;

/**
 * Package private
 *
 * @licence GNU GPL v2+
 * @author Addshore
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class AliasGroupListDeserializer implements AliasGroupListDeserializerInterface {

	/**
	 * @see \Wikibase\DataModel\Deserializers\AliasGroupListDeserializer::deserialize
	 *
	 * @param array[] $serialization
	 *
	 * @throws DeserializationException
	 * @return AliasGroupList
	 */
	public function deserialize( array $serialization ) {
		$this->assertCanDeserialize( $serialization );
		return $this->getDeserialized( $serialization );
	}

	/**
	 * @param array[] $serialization
	 *
	 * @return AliasGroupList
	 */
	private function getDeserialized( array $serialization ) {
		$aliasGroupList = new AliasGroupList();

		foreach ( $serialization as $languageCode => $aliasGroupSerialization ) {
			$aliasGroupList->setGroup( $this->deserializeAliasGroup( $aliasGroupSerialization, $languageCode ) );
		}

		return $aliasGroupList;
	}

	/**
	 * @param array $serialization
	 * @param string $languageCode
	 *
	 * @return AliasGroup
	 */
	private function deserializeAliasGroup( array $serialization, $languageCode ) {
		$aliases = array();

		foreach ( $serialization as $aliasSerialization ) {
			$aliases[] = $aliasSerialization['value'];
		}

		return new AliasGroup(
			$languageCode,
			$aliases
		);
	}

	/**
	 * @param array[] $serialization
	 *
	 * @throws DeserializationException
	 */
	private function assertCanDeserialize( array $serialization ) {
		foreach ( $serialization as $requestedLanguage => $valueSerialization ) {
			$this->assertAttributeIsArray( $serialization, $requestedLanguage );

			foreach ( $valueSerialization as $aliasSerialization ) {
				$this->assertIsValidAliasSerialization( $aliasSerialization, $requestedLanguage );
			}
		}
	}

	private function assertIsValidAliasSerialization( $serialization, $requestedLanguage ) {
		if ( !is_array( $serialization ) ) {
			throw new DeserializationException( 'Term serializations must be arrays' );
		}

		$this->requireAttribute( $serialization, 'language' );
		$this->requireAttribute( $serialization, 'value' );
		// Do not deserialize alias group fallbacks
		$this->assertNotAttribute( $serialization, 'source' );

		$this->assertAttributeInternalType( $serialization, 'language', 'string' );
		$this->assertAttributeInternalType( $serialization, 'value', 'string' );
		$this->assertRequestedAndActualLanguageMatch( $serialization, $requestedLanguage );
	}

	private function requireAttribute( array $array, $attributeName ) {
		if ( !array_key_exists( $attributeName, $array ) ) {
			throw new MissingAttributeException(
				$attributeName
			);
		}
	}

	private function assertNotAttribute( array $array, $key ) {
		if ( array_key_exists( $key, $array ) ) {
			throw new InvalidAttributeException(
				$key,
				$array[$key],
				'Deserialization of attribute ' . $key . ' not supported.'
			);
		}
	}

	private function assertRequestedAndActualLanguageMatch(
		array $serialization,
		$requestedLanguage
	) {
		if ( $serialization['language'] !== $requestedLanguage ) {
			throw new DeserializationException(
				'Deserialization of a value of the attribute language (actual)'
					. ' that is not matching the language key (requested) is not supported: '
					. $serialization['language'] . ' !== ' . $requestedLanguage
			);
		}
	}

	private function assertAttributeIsArray( array $array, $attributeName ) {
		$this->assertAttributeInternalType( $array, $attributeName, 'array' );
	}

	private function assertAttributeInternalType( array $array, $attributeName, $internalType ) {
		if ( gettype( $array[$attributeName] ) !== $internalType ) {
			throw new InvalidAttributeException(
				$attributeName,
				$array[$attributeName],
				"The internal type of attribute '$attributeName' needs to be '$internalType'"
			);
		}
	}

}

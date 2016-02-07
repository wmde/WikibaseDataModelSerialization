<?php

namespace Wikibase\DataModel\Deserializers;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Deserializers\Exceptions\InvalidAttributeException;
use Deserializers\Exceptions\MissingAttributeException;
use Wikibase\DataModel\Serializers\FacetContainerDeserializer;
use Wikibase\DataModel\Term\Term;

/**
 * Package private
 *
 * @licence GNU GPL v2+
 * @author Addshore
 */
class TermDeserializer implements Deserializer {

	/**
	 * @var FacetContainerDeserializer
	 */
	private $facetDeserializer;

	/**
	 * @param FacetContainerDeserializer $facetDeserializer
	 */
	public function __construct( FacetContainerDeserializer $facetDeserializer ) {
		$this->facetDeserializer = $facetDeserializer;
	}

	/**
	 * @param mixed $serialization
	 *
	 * @return Term
	 * @throws DeserializationException
	 */
	public function deserialize( $serialization ) {
		$this->assertCanDeserialize( $serialization );
		return $this->getDeserialized( $serialization );
	}

	/**
	 * @param array $serialization
	 *
	 * @return Term
	 */
	private function getDeserialized( $serialization ) {
		$term = new Term( $serialization['language'], $serialization['value'] );
		$this->facetDeserializer->deserializeFacets( $term, $serialization );
		return $term;
	}

	/**
	 * @param array $serialization
	 */
	private function assertCanDeserialize( $serialization ) {
		if ( !is_array( $serialization ) ) {
			throw new DeserializationException( 'The term serialization should be an array' );
		}

		$this->requireAttribute( $serialization, 'language' );
		$this->requireAttribute( $serialization, 'value' );

		$this->assertAttributeInternalType( $serialization, 'language', 'string' );
		$this->assertAttributeInternalType( $serialization, 'value', 'string' );
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

	/**
	 * @param array $serialization
	 * @param string $attribute
	 */
	private function requireAttribute( $serialization, $attribute ) {
		if ( !is_array( $serialization ) || !array_key_exists( $attribute, $serialization ) ) {
			throw new MissingAttributeException( $attribute );
		}
	}

}

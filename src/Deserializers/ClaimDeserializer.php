<?php

namespace Wikibase\DataModel\Deserializers;

use Deserializers\Deserializer;
use Deserializers\DispatchableDeserializer;
use Deserializers\Exceptions\DeserializationException;
use Deserializers\Exceptions\InvalidAttributeException;
use Deserializers\Exceptions\MissingAttributeException;
use Wikibase\DataModel\Claim\Claim;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class ClaimDeserializer implements DispatchableDeserializer {

	/**
	 * @var Deserializer
	 */
	private $snakDeserializer;

	/**
	 * @var Deserializer
	 */
	private $snaksDeserializer;

	public function __construct( Deserializer $snakDeserializer, Deserializer $snaksDeserializer ) {
		$this->snakDeserializer = $snakDeserializer;
		$this->snaksDeserializer = $snaksDeserializer;
	}

	/**
	 * @see DispatchableDeserializer::isDeserializerFor
	 *
	 * @param mixed $serialization
	 *
	 * @return bool
	 */
	public function isDeserializerFor( $serialization ) {
		return is_array( $serialization ) && array_key_exists( 'mainsnak', $serialization );
	}

	/**
	 * @see Deserializer::deserialize
	 *
	 * @param mixed $serialization
	 *
	 * @return Claim
	 * @throws DeserializationException
	 */
	public function deserialize( $serialization ) {
		if ( !$this->isDeserializerFor( $serialization ) ) {
			throw new MissingAttributeException( 'mainsnak' );
		}

		return $this->getDeserialized( $serialization );
	}

	private function getDeserialized( array $serialization ) {
		$mainSnak = $this->snakDeserializer->deserialize( $serialization['mainsnak'] );

		$claim = new Claim( $mainSnak );

		$this->setGuidFromSerialization( $serialization, $claim );
		$this->setQualifiersFromSerialization( $serialization, $claim );

		return $claim;
	}

	private function setGuidFromSerialization( array &$serialization, Claim $claim ) {
		if ( !array_key_exists( 'id', $serialization ) ) {
			return;
		}

		if ( !is_string( $serialization['id'] ) ) {
			throw new DeserializationException( 'The id ' . $serialization['id'] . ' is not a valid GUID.' );
		}

		$claim->setGuid( $serialization['id'] );
	}

	private function setQualifiersFromSerialization( array &$serialization, Claim $claim ) {
		if ( !array_key_exists( 'qualifiers', $serialization ) ) {
			return;
		}

		$qualifiers = $this->snaksDeserializer->deserialize( $serialization['qualifiers'] );

		if( array_key_exists( 'qualifiers-order', $serialization ) ) {
			$this->assertQualifiersOrderIsArray( $serialization );

			$qualifiers->orderByProperty( $serialization['qualifiers-order'] );
		}

		$claim->setQualifiers( $qualifiers );
	}

	private function assertQualifiersOrderIsArray( array $serialization ) {
		if ( !is_array( $serialization['qualifiers-order'] ) ) {
			throw new InvalidAttributeException(
				'qualifiers-order',
				$serialization['qualifiers-order'],
				'qualifiers-order attribute is not a valid array'
			);
		}
	}

}

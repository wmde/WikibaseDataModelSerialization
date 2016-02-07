<?php

namespace Wikibase\DataModel\Serializers;

use Serializers\Exceptions\SerializationException;
use Serializers\Serializer;
use Wikibase\DataModel\Facet\FacetContainer;
use Wikimedia\Assert\Assert;

/**
 * Package private
 *
 * @since 2.1
 *
 * @licence GNU GPL v2+
 * @author Daniel Kinzler
 */
class FacetContainerSerializer  {

	/**
	 * @var Serializer[] by facet name
	 */
	private $serializers;

	/**
	 * @param Serializer[] $serializers
	 */
	public function __construct( array $serializers ) {
		Assert::parameterElementType( 'Serializers\Serializer', $serializers, '$serializers' );

		$this->serializers = $serializers;
	}

	private function getSupportedFacettes() {
		return array_keys( $this->serializers );
	}

	/**
	 * @param object $object The object to serialize.
	 * @param array &$target The array to merge the facet serializations into
	 */
	public function addFacetSerialization( $object, array &$target ) {
		if ( !( $object instanceof FacetContainer ) ) {
			return;
		}

		$names = array_intersect_key( $object->listFacets(), $this->getSupportedFacettes() );
		foreach ( $names as $name ) {
			$facet = $object->getFacet( $name );
			$serializer = $this->serializers[$name];

			$data = $serializer->serialize( $facet );
			$this->mergeData( $data, $target );
		}
	}

	/**
	 * @param array $data
	 * @param array &$target
	 */
	private function mergeData( array $data, array &$target ) {
		foreach ( $data as $key => $value ) {
			if ( array_key_exists( $key, $target ) ) {
				throw new SerializationException( 'Conflicting facet serializations: key ' . $key . ' is already set.' );
			}

			$target[$key] = $value;
		}
	}
}

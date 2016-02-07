<?php

namespace Wikibase\DataModel\Serializers;

use Deserializers\DispatchableDeserializer;
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
class FacetContainerDeserializer  {

	/**
	 * @var DispatchableDeserializer[] by facet name
	 */
	private $deserializers;

	/**
	 * @param DispatchableDeserializer[] $deserializers
	 */
	public function __construct( array $deserializers ) {
		Assert::parameterElementType( 'Deserializers\DispatchableDeserializer', $deserializers, '$deserializers' );

		$this->deserializers = $deserializers;
	}

	/**
	 * @param FacetContainer $object The object to add facets to.
	 * @param array $data Serialized data to deserialize
	 */
	public function deserializeFacets( FacetContainer $object, array $data ) {
		foreach ( $this->deserializers as $name => $deserializer ) {
			if ( $deserializer->isDeserializerFor( $data ) ) {
				$facet = $deserializer->deserialize( $data );
				$object->addFacet( $name, $facet );
			}
		}
	}

}

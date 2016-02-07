<?php

namespace Wikibase\DataModel\Serializers;

use Serializers\Exceptions\UnsupportedObjectException;
use Serializers\Serializer;
use Wikibase\DataModel\Term\Term;

/**
 * Package private
 *
 * @licence GNU GPL v2+
 * @author Addshore
 */
class TermSerializer implements Serializer {

	/**
	 * @var FacetContainerSerializer
	 */
	private $facetSerializer;

	/**
	 * @param FacetContainerSerializer $facetDeserializer
	 */
	public function __construct( FacetContainerSerializer $facetSerializer ) {
		$this->facetSerializer = $facetSerializer;
	}

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
		if ( !( $object instanceof Term ) ) {
			throw new UnsupportedObjectException(
				$object,
				'TermSerializer can only serialize Term objects'
			);
		}
	}

	/**
	 * @param Term $term
	 *
	 * @return array
	 */
	private function getSerialized( Term $term ) {
		$result = array(
			'language' => $term->getLanguageCode(),
			'value' => $term->getText(),
		);

		$this->facetSerializer->addFacetSerialization( $term, $result );
		return $result;
	}

}

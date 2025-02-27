<?php

declare( strict_types = 1 );

namespace Wikibase\DataModel\Serializers;

use Serializers\Exceptions\SerializationException;
use Serializers\Exceptions\UnsupportedObjectException;
use Serializers\Serializer;
use Wikibase\DataModel\Term\TermList;

/**
 * Package private
 *
 * @license GPL-2.0-or-later
 * @author Addshore
 */
class TermListSerializer extends MapSerializer implements Serializer {

	private TermSerializer $termSerializer;

	public function __construct( TermSerializer $termSerializer, bool $useObjectsForEmptyMaps ) {
		parent::__construct( $useObjectsForEmptyMaps );
		$this->termSerializer = $termSerializer;
	}

	/**
	 * @see Serializer::serialize
	 *
	 * @param TermList $object
	 *
	 * @return array[]
	 * @throws SerializationException
	 */
	public function serialize( $object ) {
		$this->assertIsSerializerFor( $object );
		return $this->serializeMap( $this->generateSerializedArrayRepresentation( $object ) );
	}

	/**
	 * @param TermList $object
	 */
	private function assertIsSerializerFor( $object ) {
		if ( !( $object instanceof TermList ) ) {
			throw new UnsupportedObjectException(
				$object,
				'TermListSerializer can only serialize TermList objects'
			);
		}
	}

	protected function generateSerializedArrayRepresentation( TermList $termList ): array {
		$serialization = [];

		foreach ( $termList->getIterator() as $term ) {
			$serialization[$term->getLanguageCode()] = $this->termSerializer->serialize( $term );
		}

		return $serialization;
	}
}

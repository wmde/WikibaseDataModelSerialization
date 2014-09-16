<?php

namespace Wikibase\DataModel\Serializers;

use Serializers\DispatchableSerializer;
use Serializers\Exceptions\SerializationException;
use Serializers\Exceptions\UnsupportedObjectException;
use Serializers\Serializer;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Snak\Snak;
use Wikibase\DataModel\Snak\Snaks;

/**
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class ClaimSerializer implements DispatchableSerializer {

	/**
	 * @var Serializer
	 */
	private $snakSerializer;

	/**
	 * @var Serializer
	 */
	private $snaksSerializer;

	public function __construct( Serializer $snakSerializer, Serializer $snaksSerializer ) {
		$this->snakSerializer = $snakSerializer;
		$this->snaksSerializer = $snaksSerializer;
	}

	/**
	 * @see Serializer::isSerializerFor
	 *
	 * @param mixed $object
	 *
	 * @return bool
	 */
	public function isSerializerFor( $object ) {
		return $object instanceof Claim;
	}

	/**
	 * @see Serializer::serialize
	 *
	 * @param mixed $object
	 *
	 * @return array
	 * @throws SerializationException
	 */
	public function serialize( $object ) {
		if ( !$this->isSerializerFor( $object ) ) {
			throw new UnsupportedObjectException(
				$object,
				'ClaimSerializer can only serialize Claim objects'
			);
		}

		return $this->getSerialized( $object );
	}

	private function getSerialized( Claim $claim ) {
		$serialization = array(
			'mainsnak' => $this->snakSerializer->serialize( $claim->getMainSnak() )
		);

		$this->addQualifiersToSerialization( $claim, $serialization );
		$this->addGuidToSerialization( $claim, $serialization );

		return $serialization;
	}

	private function addGuidToSerialization( Claim $claim, array &$serialization ) {
		$guid = $claim->getGuid();
		if ( $guid !== null ) {
			$serialization['id'] = $guid;
		}
	}

	private function addQualifiersToSerialization( Claim $claim, &$serialization ) {
		$qualifiers = $claim->getQualifiers();

		if ( $qualifiers->count() !== 0 ) {
			$serialization['qualifiers'] = $this->snaksSerializer->serialize( $qualifiers );
			$serialization['qualifiers-order'] = $this->buildQualifiersOrderList( $qualifiers );
		}
	}

	private function buildQualifiersOrderList( Snaks $snaks ) {
		$list = array();

		/**
		 * @var Snak $snak
		 */
		foreach ( $snaks as $snak ) {
			$id = $snak->getPropertyId()->getSerialization();
			if ( !in_array( $id, $list ) ) {
				$list[] = $id;
			}
		}

		return $list;
	}

}

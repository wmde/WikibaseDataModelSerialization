<?php

namespace Wikibase\DataModel\Serializers;

use Serializers\DispatchableSerializer;
use Serializers\Exceptions\SerializationException;
use Serializers\Exceptions\UnsupportedObjectException;
use Serializers\Serializer;
use Wikibase\DataModel\Entity\Property;

/**
 * Package private
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 * @author Jan Zerebecki < jan.wikimedia@zerebecki.de >
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class PropertySerializer implements DispatchableSerializer {

	/**
	 * @var Serializer
	 */
	private $termListSerializer;

	/**
	 * @var AliasGroupListSerializer
	 */
	private $aliasGroupListSerializer;

	/**
	 * @var Serializer
	 */
	private $statementListSerializer;

	/**
	 * @param Serializer $termListSerializer
	 * @param AliasGroupListSerializer $aliasGroupListSerializer
	 * @param Serializer $statementListSerializer
	 */
	public function __construct(
		Serializer $termListSerializer,
		AliasGroupListSerializer $aliasGroupListSerializer,
		Serializer $statementListSerializer
	) {
		$this->termListSerializer = $termListSerializer;
		$this->aliasGroupListSerializer = $aliasGroupListSerializer;
		$this->statementListSerializer = $statementListSerializer;
	}

	/**
	 * @see Serializer::isSerializerFor
	 *
	 * @param mixed $object
	 *
	 * @return bool
	 */
	public function isSerializerFor( $object ) {
		return $object instanceof Property;
	}

	/**
	 * @see Serializer::serialize
	 *
	 * @param Property $object
	 *
	 * @throws SerializationException
	 * @return array
	 */
	public function serialize( $object ) {
		if ( !$this->isSerializerFor( $object ) ) {
			throw new UnsupportedObjectException(
				$object,
				'PropertySerializer can only serialize Property objects.'
			);
		}

		return $this->getSerialized( $object );
	}

	private function getSerialized( Property $property ) {
		$serialization = array(
			'type' => $property->getType(),
			'datatype' => $property->getDataTypeId(),
		);

		$this->addIdToSerialization( $property, $serialization );
		$this->addTermsToSerialization( $property, $serialization );
		$this->addStatementListToSerialization( $property, $serialization );

		return $serialization;
	}

	private function addIdToSerialization( Property $property, array &$serialization ) {
		$id = $property->getId();

		if ( $id !== null ) {
			$serialization['id'] = $id->getSerialization();
		}
	}

	private function addTermsToSerialization( Property $property, array &$serialization ) {
		$fingerprint = $property->getFingerprint();

		$serialization['labels'] = $this->termListSerializer->serialize( $fingerprint->getLabels() );
		$serialization['descriptions'] = $this->termListSerializer->serialize( $fingerprint->getDescriptions() );
		$serialization['aliases'] = $this->aliasGroupListSerializer->serialize( $fingerprint->getAliasGroups() );
	}

	private function addStatementListToSerialization( Property $property, array &$serialization ) {
		$serialization['claims'] = $this->statementListSerializer->serialize( $property->getStatements() );
	}

}

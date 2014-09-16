<?php

namespace Wikibase\DataModel\Serializers;

use Wikibase\DataModel\Statement\Statement;
use Serializers\DispatchableSerializer;
use Serializers\Exceptions\SerializationException;
use Serializers\Exceptions\UnsupportedObjectException;
use Serializers\Serializer;

/**
 * @since 1.2
 *
 * @license GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class StatementSerializer implements DispatchableSerializer {

	private $rankLabels = array(
		Statement::RANK_DEPRECATED => 'deprecated',
		Statement::RANK_NORMAL => 'normal',
		Statement::RANK_PREFERRED => 'preferred'
	);

	/**
	 * @var Serializer
	 */
	private $claimSerializer;

	/**
	 * @var Serializer
	 */
	private $referencesSerializer;

	public function __construct( Serializer $claimSerializer, Serializer $referencesSerializer ) {
		$this->claimSerializer = $claimSerializer;
		$this->referencesSerializer = $referencesSerializer;
	}

	/**
	 * @see Serializer::isSerializerFor
	 *
	 * @param mixed $object
	 *
	 * @return bool
	 */
	public function isSerializerFor( $object ) {
		return $object instanceof Statement;
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
				'StatementSerializer can only serialize Statement objects'
			);
		}

		return $this->getSerialized( $object );
	}

	private function getSerialized( Statement $statement ) {
		$serialization = array(
			'claim' => $this->claimSerializer->serialize( $statement->getClaim() )
		);

		$this->addRankToSerialization( $statement, $serialization );
		$this->addReferencesToSerialization( $statement, $serialization );

		return $serialization;
	}

	private function addRankToSerialization( Statement $statement, array &$serialization ) {
		$serialization['rank'] = $this->rankLabels[$statement->getRank()];
	}

	private function addReferencesToSerialization( Statement $statement, array &$serialization ) {
		$references = $statement->getReferences();

		if ( $references->count() != 0 ) {
			$serialization['references'] = $this->referencesSerializer->serialize( $statement->getReferences() );
		}
	}

}

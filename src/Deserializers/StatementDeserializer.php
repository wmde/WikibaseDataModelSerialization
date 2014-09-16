<?php

namespace Wikibase\DataModel\Deserializers;

use Deserializers\DispatchableDeserializer;
use Deserializers\Exceptions\DeserializationException;
use Deserializers\Exceptions\MissingAttributeException;
use Wikibase\DataModel\Statement\Statement;

/**
 * @since 1.2
 *
 * @license GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class StatementDeserializer implements DispatchableDeserializer {

	private $rankIds = array(
		'deprecated' => Statement::RANK_DEPRECATED,
		'normal' => Statement::RANK_NORMAL,
		'preferred' => Statement::RANK_PREFERRED
	);

	/**
	 * @var Deserializer
	 */
	private $claimDeserializer;

	/**
	 * @var Deserializer
	 */
	private $referencesDeserializer;

	public function __construct( Deserializer $claimDeserializer, Deserializer $referencesDeserializer ) {
		$this->claimDeserializer = $claimDeserializer;
		$this->referencesDeserializer = $referencesDeserializer;
	}

	/**
	 * @see DispatchableDeserializer::isDeserializerFor
	 *
	 * @param mixed $serialization
	 *
	 * @return bool
	 */
	public function isDeserializerFor( $serialization ) {
		return array_key_exists( 'claim', $serialization );
	}

	/**
	 * @see Deserializer::deserialize
	 *
	 * @param mixed $serialization
	 *
	 * @return Statement
	 * @throws DeserializationException
	 */
	public function deserialize( $serialization ) {
		if ( !$this->isDeserializerFor( $serialization ) ) {
			throw new MissingAttributeException( 'claim' );
		}

		return $this->getDeserialized( $serialization );
	}

	private function getDeserialized( array $serialization ) {
		$claim = $this->claimDeserializer->deserialize( $serialization['claim'] );

		$statement = new Statement( $claim );

		$this->setRankFromSerialization( $serialization, $statement );
		$this->setReferencesFromSerialization( $serialization, $statement );

		return $statement;
	}

	private function setRankFromSerialization( array &$serialization, Statement $statement ) {
		if ( !array_key_exists( 'rank', $serialization ) ) {
			return;
		}

		if ( !array_key_exists( $serialization['rank'], $this->rankIds ) ) {
			throw new DeserializationException( 'The rank ' . $serialization['rank'] . ' is not a valid rank.' );
		}

		$statement->setRank( $this->rankIds[$serialization['rank']] );
	}

	private function setReferencesFromSerialization( array &$serialization, Statement $statement ) {
		if ( !array_key_exists( 'references', $serialization ) ) {
			return;
		}

		$statement->setReferences( $this->referencesDeserializer->deserialize( $serialization['references'] ) );
	}

}

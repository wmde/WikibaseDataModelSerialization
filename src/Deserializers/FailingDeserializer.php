<?php

namespace Wikibase\DataModel\Deserializers;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Wikibase\DataModel\Term\Term;
use Wikimedia\Assert\Assert;

/**
 * A Deserializer that fails when certain fields are present in the serialization.
 * This can be used to prevent ingestion of certain data structures.
 *
 * @since 2.1
 *
 * @licence GNU GPL v2+
 * @author Daniel Kinzler
 */
class FailingDeserializer implements Deserializer {

	/**
	 * @var string[]
	 */
	private $fields;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @param string[] $fields
	 * @param $message
	 */
	public function __construct( array $fields, $message ) {
		Assert::parameterElementType( 'string', $fields, '$fields' );
		Assert::parameterType( 'string', $message, '$message' );

		$this->fields = $fields;
		$this->message = $message;
	}

	/**
	 * @param mixed $serialization
	 *
	 * @return Term
	 * @throws DeserializationException
	 */
	public function deserialize( $serialization ) {
		foreach ( $this->fields as $field ) {
			if ( !array_key_exists( $field, $serialization ) ) {
				return;
			}
		}

		throw new DeserializationException( $this->message );
	}

}

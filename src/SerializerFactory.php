<?php

namespace Wikibase\DataModel;

use InvalidArgumentException;
use Serializers\DispatchingSerializer;
use Serializers\Serializer;
use Wikibase\DataModel\Serializers\ClaimSerializer;
use Wikibase\DataModel\Serializers\ClaimsSerializer;
use Wikibase\DataModel\Serializers\FingerprintSerializer;
use Wikibase\DataModel\Serializers\ItemSerializer;
use Wikibase\DataModel\Serializers\PropertySerializer;
use Wikibase\DataModel\Serializers\ReferenceListSerializer;
use Wikibase\DataModel\Serializers\ReferenceSerializer;
use Wikibase\DataModel\Serializers\SiteLinkSerializer;
use Wikibase\DataModel\Serializers\SnakSerializer;
use Wikibase\DataModel\Serializers\SnaksSerializer;
use Wikibase\DataModel\Serializers\StatementListSerializer;
use Wikibase\DataModel\Serializers\TypedSnakSerializer;

/**
 * Factory for constructing Serializer objects that can serialize WikibaseDataModel objects.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class SerializerFactory {

	const OPTION_DEFAULT = 0;
	const OPTION_OBJECTS_FOR_MAPS = 1;

	/**
	 * @var int
	 */
	private $options;

	/**
	 * @var Serializer
	 */
	private $dataValueSerializer;

	/**
	 * @param Serializer $dataValueSerializer serializer for DataValue objects
	 * @param int $options set multiple with bitwise or
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( Serializer $dataValueSerializer, $options = 0 ) {
		if ( !is_int( $options ) ) {
			throw new InvalidArgumentException( '$options must be an integer' );
		}

		$this->dataValueSerializer = $dataValueSerializer;
		$this->options = $options;
	}

	/**
	 * @return bool
	 */
	private function shouldUseObjectsForMaps() {
		return (bool)( $this->options & self::OPTION_OBJECTS_FOR_MAPS );
	}

	/**
	 * Returns a Serializer that can serialize Entity objects.
	 *
	 * @return Serializer
	 */
	public function newEntitySerializer() {
		$fingerprintSerializer = new FingerprintSerializer( $this->shouldUseObjectsForMaps() );
		return new DispatchingSerializer( array(
			new ItemSerializer( $fingerprintSerializer, $this->newStatementListSerializer(), $this->newSiteLinkSerializer(), $this->shouldUseObjectsForMaps() ),
			new PropertySerializer( $fingerprintSerializer, $this->newStatementListSerializer() ),
		) );
	}

	/**
	 * Returns a Serializer that can serialize SiteLink objects.
	 *
	 * @return Serializer
	 */
	public function newSiteLinkSerializer() {
		return new SiteLinkSerializer();
	}

	/**
	 * Returns a Serializer that can serialize StatementList objects.
	 *
	 * @return Serializer
	 */
	public function newStatementListSerializer() {
		return new StatementListSerializer( $this->newClaimSerializer(), $this->shouldUseObjectsForMaps() );
	}

	/**
	 * Returns a Serializer that can serialize Claim objects.
	 *
	 * @return Serializer
	 */
	public function newClaimSerializer() {
		return new ClaimSerializer( $this->newSnakSerializer(), $this->newSnaksSerializer(), $this->newReferencesSerializer() );
	}

	/**
	 * Returns a Serializer that can serialize ReferenceList objects.
	 *
	 * @return Serializer
	 */
	public function newReferencesSerializer() {
		return new ReferenceListSerializer( $this->newReferenceSerializer() );
	}

	/**
	 * Returns a Serializer that can serialize Reference objects.
	 *
	 * @return Serializer
	 */
	public function newReferenceSerializer() {
		return new ReferenceSerializer( $this->newSnaksSerializer() );
	}

	/**
	 * Returns a Serializer that can serialize Snaks objects.
	 *
	 * @return Serializer
	 */
	public function newSnaksSerializer() {
		return new SnaksSerializer( $this->newSnakSerializer(), $this->shouldUseObjectsForMaps() );
	}

	/**
	 * Returns a Serializer that can serialize Snak objects.
	 *
	 * @return Serializer
	 */
	public function newSnakSerializer() {
		return new SnakSerializer( $this->dataValueSerializer );
	}

	/**
	 * Returns a Serializer that can serialize TypedSnak objects.
	 *
	 * @since 1.3
	 *
	 * @return Serializer
	 */
	public function newTypedSnakSerializer() {
		return new TypedSnakSerializer( $this->newSnakSerializer() );
	}

}

<?php

namespace Wikibase\DataModel;

use InvalidArgumentException;
use Serializers\DispatchingSerializer;
use Serializers\Serializer;
use Wikibase\DataModel\Serializers\ClaimsSerializer;
use Wikibase\DataModel\Serializers\FingerprintSerializer;
use Wikibase\DataModel\Serializers\ItemSerializer;
use Wikibase\DataModel\Serializers\PropertySerializer;
use Wikibase\DataModel\Serializers\ReferenceListSerializer;
use Wikibase\DataModel\Serializers\ReferenceSerializer;
use Wikibase\DataModel\Serializers\SiteLinkSerializer;
use Wikibase\DataModel\Serializers\SnakListSerializer;
use Wikibase\DataModel\Serializers\SnakSerializer;
use Wikibase\DataModel\Serializers\StatementListSerializer;
use Wikibase\DataModel\Serializers\StatementSerializer;
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
	 * @return DispatchingSerializer
	 */
	public function newEntitySerializer() {
		$mapOption = $this->shouldUseObjectsForMaps()
			? FingerprintSerializer::USE_OBJECTS_FOR_MAPS
			: FingerprintSerializer::USE_ARRAYS_FOR_MAPS;
		$fingerprintSerializer = new FingerprintSerializer( $mapOption );
		return new DispatchingSerializer( array(
			new ItemSerializer( $fingerprintSerializer, $this->newStatementListSerializer(), $this->newSiteLinkSerializer(), $this->shouldUseObjectsForMaps() ),
			new PropertySerializer( $fingerprintSerializer, $this->newStatementListSerializer() ),
		) );
	}

	/**
	 * Returns a Serializer that can serialize SiteLink objects.
	 *
	 * @return SiteLinkSerializer
	 */
	public function newSiteLinkSerializer() {
		return new SiteLinkSerializer();
	}

	/**
	 * Returns a Serializer that can serialize Claims objects.
	 *
	 * @return ClaimsSerializer
	 */
	public function newClaimsSerializer() {
		return new ClaimsSerializer( $this->newStatementSerializer(), $this->shouldUseObjectsForMaps() );
	}

	/**
	 * Returns a Serializer that can serialize StatementList objects.
	 *
	 * @since 1.4
	 *
	 * @return Serializer
	 */
	public function newStatementListSerializer() {
		return new StatementListSerializer( $this->newStatementSerializer(), $this->shouldUseObjectsForMaps() );
	}

	/**
	 * Returns a Serializer that can serialize Statement objects.
	 *
	 * @since 1.4
	 *
	 * @return Serializer
	 */
	public function newStatementSerializer() {
		return new StatementSerializer(
			$this->newSnakSerializer(),
			$this->newSnakListSerializer(),
			$this->newReferencesSerializer()
		);
	}

	/**
	 * Returns a Serializer that can serialize claims.
	 *
	 * @deprecated since 1.4, use newStatementSerializer instead
	 *
	 * @return ClaimSerializer
	 */
	public function newClaimSerializer() {
		return $this->newStatementSerializer();
	}

	/**
	 * Returns a Serializer that can serialize ReferenceList objects.
	 *
	 * @return ReferenceListSerializer
	 */
	public function newReferencesSerializer() {
		return new ReferenceListSerializer( $this->newReferenceSerializer() );
	}

	/**
	 * Returns a Serializer that can serialize Reference objects.
	 *
	 * @return ReferenceSerializer
	 */
	public function newReferenceSerializer() {
		return new ReferenceSerializer( $this->newSnakListSerializer() );
	}

	/**
	 * Returns a Serializer that can serialize SnakList objects.
	 *
	 * @since 1.4
	 *
	 * @return Serializer
	 */
	public function newSnakListSerializer() {
		return new SnakListSerializer( $this->newSnakSerializer(), $this->shouldUseObjectsForMaps() );
	}

	/**
	 * b/c alias for newSnakListSerializer
	 *
	 * @deprecated since 1.4 - use newSnakListSerializer instead
	 *
	 * @return SnaksSerializer
	 */
	public function newSnaksSerializer() {
		return $this->newSnakListSerializer();
	}

	/**
	 * Returns a Serializer that can serialize Snak objects.
	 *
	 * @return SnakSerializer
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

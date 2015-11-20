<?php

namespace Wikibase\DataModel;

use InvalidArgumentException;
use Serializers\DispatchingSerializer;
use Serializers\Serializer;
use Wikibase\DataModel\Serializers\AliasGroupListSerializer;
use Wikibase\DataModel\Serializers\AliasGroupSerializer;
use Wikibase\DataModel\Serializers\ItemSerializer;
use Wikibase\DataModel\Serializers\PropertySerializer;
use Wikibase\DataModel\Serializers\ReferenceListSerializer;
use Wikibase\DataModel\Serializers\ReferenceSerializer;
use Wikibase\DataModel\Serializers\SiteLinkSerializer;
use Wikibase\DataModel\Serializers\SnakListSerializer;
use Wikibase\DataModel\Serializers\SnakSerializer;
use Wikibase\DataModel\Serializers\StatementListSerializer;
use Wikibase\DataModel\Serializers\StatementSerializer;
use Wikibase\DataModel\Serializers\TermListSerializer;
use Wikibase\DataModel\Serializers\TermSerializer;
use Wikibase\DataModel\Serializers\TypedSnakSerializer;

/**
 * Factory for constructing Serializer objects that can serialize WikibaseDataModel objects.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 * @author Bene* < benestar.wikimedia@gmail.com >
 * @author Adam Shorland
 */
class SerializerFactory {

	const OPTION_DEFAULT = 0;
	/** @since 1.2.0 */
	const OPTION_OBJECTS_FOR_MAPS = 1;
	/** @since 1.7.0 */
	const OPTION_SERIALIZE_MAIN_SNAKS_WITHOUT_HASH = 2;
	const OPTION_SERIALIZE_QUALIFIER_SNAKS_WITHOUT_HASH = 4;
	const OPTION_SERIALIZE_REFERENCE_SNAKS_WITHOUT_HASH = 8;

	/**
	 * @var int
	 */
	private $options;

	/**
	 * @var Serializer
	 */
	private $dataValueSerializer;

	/**
	 * @var ExtraValuesAssigner
	 */
	private $extraValuesAssigner;

	/**
	 * @param Serializer $dataValueSerializer serializer for DataValue objects
	 * @param int $options set multiple with bitwise or
	 * @param ExtraValuesAssigner|null $extraValuesAssigner
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct(
		Serializer $dataValueSerializer,
		$options = 0,
		ExtraValuesAssigner $extraValuesAssigner = null
	) {
		if ( !is_int( $options ) ) {
			throw new InvalidArgumentException( '$options must be an integer' );
		}

		$this->dataValueSerializer = $dataValueSerializer;
		$this->options = $options;
		$this->extraValuesAssigner = $extraValuesAssigner ?: new ExtraValuesAssigner();
	}

	/**
	 * @return bool
	 */
	private function shouldUseObjectsForMaps() {
		return (bool)( $this->options & self::OPTION_OBJECTS_FOR_MAPS );
	}

	/**
	 * @return bool
	 */
	private function shouldSerializeMainSnaksWithHash() {
		return !(bool)( $this->options & self::OPTION_SERIALIZE_MAIN_SNAKS_WITHOUT_HASH );
	}

	/**
	 * @return bool
	 */
	private function shouldSerializeQualifierSnaksWithHash() {
		return !(bool)( $this->options & self::OPTION_SERIALIZE_QUALIFIER_SNAKS_WITHOUT_HASH );
	}

	/**
	 * @return bool
	 */
	private function shouldSerializeReferenceSnaksWithHash() {
		return !(bool)( $this->options & self::OPTION_SERIALIZE_REFERENCE_SNAKS_WITHOUT_HASH );
	}

	/**
	 * Returns a Serializer that can serialize Entity objects.
	 *
	 * @return Serializer
	 */
	public function newEntitySerializer() {
		return new DispatchingSerializer( array(
			new ItemSerializer(
				$this->newTermListSerializer(),
				$this->newAliasGroupListSerializer(),
				$this->newStatementListSerializer(),
				$this->newSiteLinkSerializer(),
				$this->shouldUseObjectsForMaps()
			),
			new PropertySerializer(
				$this->newTermListSerializer(),
				$this->newAliasGroupListSerializer(),
				$this->newStatementListSerializer()
			)
		) );
	}

	/**
	 * Returns a Serializer that can serialize SiteLink objects.
	 *
	 * @return Serializer
	 */
	public function newSiteLinkSerializer() {
		return new SiteLinkSerializer( $this->extraValuesAssigner );
	}

	/**
	 * Returns a Serializer that can serialize StatementList objects.
	 *
	 * @since 1.4
	 *
	 * @return Serializer
	 */
	public function newStatementListSerializer() {
		return new StatementListSerializer(
			$this->newStatementSerializer(),
			$this->shouldUseObjectsForMaps()
		);
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
			$this->newSnakSerializer( $this->shouldSerializeMainSnaksWithHash() ),
			$this->newSnakListSerializer( $this->shouldSerializeQualifierSnaksWithHash() ),
			$this->newReferencesSerializer()
		);
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
		return new ReferenceSerializer(
			$this->newSnakListSerializer(
				$this->shouldSerializeReferenceSnaksWithHash()
			)
		);
	}

	/**
	 * Returns a Serializer that can serialize SnakList objects.
	 *
	 * @param bool $serializeSnaksWithHash
	 *
	 * @since 1.4
	 *
	 * @return Serializer
	 */
	public function newSnakListSerializer( $serializeSnaksWithHash = true ) {
		return new SnakListSerializer(
			$this->newSnakSerializer( $serializeSnaksWithHash ),
			$this->shouldUseObjectsForMaps()
		);
	}

	/**
	 * Returns a Serializer that can serialize Snak objects.
	 *
	 * @param bool $serializeWithHash
	 *
	 * @return Serializer
	 */
	public function newSnakSerializer( $serializeWithHash = true ) {
		return new SnakSerializer(
			$this->dataValueSerializer,
			$serializeWithHash
		);
	}

	/**
	 * Returns a Serializer that can serialize TypedSnak objects.
	 *
	 * @param bool $serializeWithHash
	 *
	 * @since 1.3
	 *
	 * @return Serializer
	 */
	public function newTypedSnakSerializer( $serializeWithHash = true ) {
		return new TypedSnakSerializer( $this->newSnakSerializer( $serializeWithHash ) );
	}

	/**
	 * Returns a Serializer that can serialize Term objects.
	 *
	 * @since 1.5
	 *
	 * @return Serializer
	 */
	public function newTermSerializer() {
		return new TermSerializer();
	}

	/**
	 * Returns a Serializer that can serialize TermList objects.
	 *
	 * @since 1.5
	 *
	 * @return Serializer
	 */
	public function newTermListSerializer() {
		return new TermListSerializer( $this->newTermSerializer(), $this->shouldUseObjectsForMaps() );
	}

	/**
	 * Returns a Serializer that can serialize AliasGroup objects.
	 *
	 * @since 1.6
	 *
	 * @return Serializer
	 */
	public function newAliasGroupSerializer() {
		return new AliasGroupSerializer();
	}

	/**
	 * Returns a Serializer that can serialize AliasGroupList objects.
	 *
	 * @since 1.5
	 *
	 * @return Serializer
	 */
	public function newAliasGroupListSerializer() {
		return new AliasGroupListSerializer( $this->newAliasGroupSerializer(), $this->shouldUseObjectsForMaps() );
	}

}

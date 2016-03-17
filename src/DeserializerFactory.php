<?php

namespace Wikibase\DataModel;

use Deserializers\Deserializer;
use Deserializers\DispatchableDeserializer;
use Deserializers\DispatchingDeserializer;
use Wikibase\DataModel\Deserializers\AliasGroupListDeserializer;
use Wikibase\DataModel\Deserializers\EntityIdDeserializer;
use Wikibase\DataModel\Deserializers\ItemDeserializer;
use Wikibase\DataModel\Deserializers\PropertyDeserializer;
use Wikibase\DataModel\Deserializers\ReferenceDeserializer;
use Wikibase\DataModel\Deserializers\ReferenceListDeserializer;
use Wikibase\DataModel\Deserializers\SiteLinkDeserializer;
use Wikibase\DataModel\Deserializers\SnakDeserializer;
use Wikibase\DataModel\Deserializers\SnakListDeserializer;
use Wikibase\DataModel\Deserializers\StatementDeserializer;
use Wikibase\DataModel\Deserializers\StatementListDeserializer;
use Wikibase\DataModel\Deserializers\TermDeserializer;
use Wikibase\DataModel\Deserializers\TermListDeserializer;
use Wikibase\DataModel\Entity\EntityIdParser;

/**
 * Factory for constructing Deserializer objects that can deserialize WikibaseDataModel objects.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class DeserializerFactory {

	/**
	 * @var Deserializer
	 */
	private $dataValueDeserializer;

	/**
	 * @var EntityIdParser
	 */
	private $entityIdParser;

	/**
	 * @param Deserializer $dataValueDeserializer deserializer for DataValue objects
	 * @param EntityIdParser $entityIdParser
	 */
	public function __construct(
		Deserializer $dataValueDeserializer,
		EntityIdParser $entityIdParser
	) {
		$this->dataValueDeserializer = $dataValueDeserializer;
		$this->entityIdParser = $entityIdParser;
	}

	/**
	 * Returns a Deserializer that can deserialize Item and Property objects.
	 *
	 * @return DispatchableDeserializer
	 */
	public function newEntityDeserializer() {
		return new DispatchingDeserializer( array(
			$this->newItemDeserializer(),
			$this->newPropertyDeserializer()
		) );
	}

	/**
	 * Returns a Deserializer that can deserialize Item objects.
	 *
	 * @since 2.1
	 *
	 * @return ItemDeserializer
	 */
	public function newItemDeserializer() {
		return new ItemDeserializer(
			$this->newEntityIdDeserializer(),
			$this->newTermListDeserializer(),
			$this->newAliasGroupListDeserializer(),
			$this->newStatementListDeserializer(),
			$this->newSiteLinkDeserializer()
		);
	}

	/**
	 * Returns a Deserializer that can deserialize Property objects.
	 *
	 * @since 2.1
	 *
	 * @return PropertyDeserializer
	 */
	public function newPropertyDeserializer() {
		return new PropertyDeserializer(
			$this->newEntityIdDeserializer(),
			$this->newTermListDeserializer(),
			$this->newAliasGroupListDeserializer(),
			$this->newStatementListDeserializer()
		);
	}

	/**
	 * Returns a Deserializer that can deserialize SiteLink objects.
	 *
	 * @return SiteLinkDeserializer
	 */
	public function newSiteLinkDeserializer() {
		return new SiteLinkDeserializer( $this->newEntityIdDeserializer() );
	}

	/**
	 * Returns a Deserializer that can deserialize StatementList objects.
	 *
	 * @since 1.4
	 *
	 * @return StatementListDeserializer
	 */
	public function newStatementListDeserializer() {
		return new StatementListDeserializer( $this->newStatementDeserializer() );
	}

	/**
	 * Returns a Deserializer that can deserialize Statement objects.
	 *
	 * @since 1.4
	 *
	 * @return DispatchableDeserializer
	 */
	public function newStatementDeserializer() {
		return new StatementDeserializer(
			$this->newSnakDeserializer(),
			$this->newSnakListDeserializer(),
			$this->newReferencesDeserializer()
		);
	}

	/**
	 * Returns a Deserializer that can deserialize ReferenceList objects.
	 *
	 * @return ReferenceListDeserializer
	 */
	public function newReferencesDeserializer() {
		return new ReferenceListDeserializer( $this->newReferenceDeserializer() );
	}

	/**
	 * Returns a Deserializer that can deserialize Reference objects.
	 *
	 * @return ReferenceDeserializer
	 */
	public function newReferenceDeserializer() {
		return new ReferenceDeserializer( $this->newSnakListDeserializer() );
	}

	/**
	 * Returns a Deserializer that can deserialize SnakList objects.
	 *
	 * @since 1.4
	 *
	 * @return SnakListDeserializer
	 */
	public function newSnakListDeserializer() {
		return new SnakListDeserializer( $this->newSnakDeserializer() );
	}

	/**
	 * Returns a Deserializer that can deserialize Snak objects.
	 *
	 * @return SnakDeserializer
	 */
	public function newSnakDeserializer() {
		return new SnakDeserializer( $this->dataValueDeserializer, $this->newEntityIdDeserializer() );
	}

	/**
	 * Returns a Deserializer that can deserialize EntityId objects.
	 *
	 * @return EntityIdDeserializer
	 */
	public function newEntityIdDeserializer() {
		return new EntityIdDeserializer( $this->entityIdParser );
	}

	/**
	 * Returns a Deserializer that can deserialize Term objects.
	 *
	 * @since 1.5
	 *
	 * @return TermDeserializer
	 */
	public function newTermDeserializer() {
		return new TermDeserializer();
	}

	/**
	 * Returns a Deserializer that can deserialize TermList objects.
	 *
	 * @since 1.5
	 *
	 * @return TermListDeserializer
	 */
	public function newTermListDeserializer() {
		return new TermListDeserializer( $this->newTermDeserializer() );
	}

	/**
	 * Returns a Deserializer that can deserialize AliasGroupList objects.
	 *
	 * @since 1.5
	 *
	 * @return AliasGroupListDeserializer
	 */
	public function newAliasGroupListDeserializer() {
		return new AliasGroupListDeserializer();
	}

}

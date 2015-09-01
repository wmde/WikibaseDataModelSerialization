<?php

namespace Wikibase\DataModel;

/**
 * Assigns extra values to the serialization of an object
 * based on the given extra values providers.
 *
 * @since 2.1
 *
 * @licence GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class ExtraValuesAssigner {

	/**
	 * @var ExtraValuesProvider[]
	 */
	private $extraValuesProviders;

	/**
	 * @param ExtraValuesProvider[] $extraValuesProviders
	 */
	public function __construct( array $extraValuesProviders = array() ) {
		$this->extraValuesProviders = $extraValuesProviders;
	}

	/**
	 * @param array $serialization
	 * @param mixed $object
	 * @return array
	 */
	public function addExtraValues( $serialization, $object ) {
		foreach ( $this->extraValuesProviders as $extraValuesProvider ) {
			if ( $extraValuesProvider->isProviderFor( $object ) ) {
				$serialization = array_merge(
					$extraValuesProvider->getExtraValues( $object ),
					$serialization
				);
			}
		}

		return $serialization;
	}

}

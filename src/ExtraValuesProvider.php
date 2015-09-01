<?php

namespace Wikibase\DataModel;

/**
 * Provides extra values for the serialization of an object.
 *
 * @since 2.1
 *
 * @licence GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
interface ExtraValuesProvider {

	/**
	 * @param mixed $object
	 * @return bool
	 */
	public function isProviderFor( $object );

	/**
	 * @param mixed $object
	 * @return array
	 */
	public function getExtraValues( $object );

}

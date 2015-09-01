<?php


namespace Tests\Wikibase\DataModel;

use Wikibase\DataModel\ExtraValuesProvider;

/**
 * @licence GNU GPL v2+
 * @author Bene* < benestar.wikimedia@gmail.com >
 */
class FakeExtraValuesProvider implements ExtraValuesProvider {

	/**
	 * @param mixed $object
	 * @return bool
	 */
	public function isProviderFor( $object ) {
		return true;
	}

	/**
	 * @param mixed $object
	 * @return array
	 */
	public function getExtraValues( $object ) {
		return array(
			'foo' => 'bar'
		);
	}
}

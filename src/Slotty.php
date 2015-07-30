<?php

namespace Wikibase\DataModel;

/**
 * @licence GNU GPL v2+
 * @author Adam Shorland
 * @since 1.9.0
 */
interface Slotty {

	/**
	 * @return array
	 *     Keys: The name of the slot.
	 *     Values: The value of the slot.
	 *         array[]|int[]|string[]|bool[]|float[]
	 *         A possibly nested structure consisting of only arrays and scalar values
	 */
	public function getSlots();

}

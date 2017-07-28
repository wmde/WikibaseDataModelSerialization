<?php

namespace Wikibase\DataModel\Serializers;

use Serializers\DispatchableSerializer;
use Serializers\Exceptions\SerializationException;
use Wikibase\DataModel\Slotty;

/**
 * Package private
 *
 * @licence GNU GPL v2+
 * @author Adam Shorland
 * @since 1.9.0
 */
class SlottySerializer implements DispatchableSerializer {

	/**
	 * @since 3.0
	 *
	 * @param mixed $object
	 *
	 * @return boolean
	 */
	public function isSerializerFor( $object ) {
		return $object instanceof Slotty;
	}

	/**
	 * @since 1.0
	 *
	 * @param Slotty $object
	 *
	 * @return array|int|string|bool|float A possibly nested structure consisting of only arrays and scalar values
	 * @throws SerializationException
	 */
	public function serialize( $object ) {
		return $object->getSlots();
	}
}

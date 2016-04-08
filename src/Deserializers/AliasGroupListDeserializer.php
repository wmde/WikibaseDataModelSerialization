<?php

namespace Wikibase\DataModel\Deserializers;

use Deserializers\Exceptions\DeserializationException;
use Wikibase\DataModel\Term\AliasGroupList;

/**
 * @since 2.3
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
interface AliasGroupListDeserializer {

	/**
	 * @param array[] $serialization
	 *
	 * @throws DeserializationException
	 * @return AliasGroupList
	 */
	public function deserialize( array $serialization );

}

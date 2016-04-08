<?php

namespace Wikibase\DataModel\Serializers;

use Wikibase\DataModel\Term\AliasGroup;

/**
 * @since 2.3
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
interface AliasGroupSerializer {

	/**
	 * @param AliasGroup $aliasGroup
	 *
	 * @return array[]
	 */
	public function serialize( AliasGroup $aliasGroup );

}

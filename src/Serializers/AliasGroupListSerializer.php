<?php

namespace Wikibase\DataModel\Serializers;

use Wikibase\DataModel\Term\AliasGroupList;

/**
 * @since 2.3
 *
 * @licence GNU GPL v2+
 * @author Thiemo Mättig
 */
interface AliasGroupListSerializer {

	/**
	 * @param AliasGroupList $aliasGroupList
	 *
	 * @return array[]
	 */
	public function serialize( AliasGroupList $aliasGroupList );

}

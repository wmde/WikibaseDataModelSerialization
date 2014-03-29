<?php

namespace Wikibase\DataModel\Lookups;

use Wikibase\DataModel\Entity\PropertyId;

/**
 * Interface for objects that can find the if of the DataType
 * for the Property of which the id is given.
 *
 * @since 1.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Adam Shorland
 */
interface DataTypeLookup {

	/**
	 * Returns the DataType for the Property of which the id is given.
	 *
	 * @since 1.0
	 *
	 * @param PropertyId $propertyId
	 *
	 * @return string
	 * @throws LookupException
	 */
	public function getDataTypeIdForProperty( PropertyId $propertyId );

}

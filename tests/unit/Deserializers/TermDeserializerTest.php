<?php

namespace Tests\Wikibase\DataModel\Deserializers;

use Deserializers\Deserializer;
use Wikibase\DataModel\Deserializers\TermDeserializer;
use Wikibase\DataModel\Serializers\FacetContainerSerializer;
use Wikibase\DataModel\Term\Term;

/**
 * @covers Wikibase\DataModel\Deserializers\TermDeserializer
 *
 * @licence GNU GPL v2+
 * @author Addshore
 */
class TermDeserializerTest extends DeserializerBaseTest {

	/**
	 * @return Deserializer
	 */
	public function buildDeserializer() {
		return new TermDeserializer( new FacetContainerSerializer( array() ) );
	}

	/**
	 * @return array[] things that are deserialized by the deserializer
	 */
	public function deserializableProvider() {
		return array(
			array(
				'language' => 'en',
				'value' => 'FooBar',
			),
			array(
				'language' => 'en-gb',
				'value' => 'Kittens, Kittens and Unicorns',
			),
			//TODO: test facets
		);
	}

	/**
	 * @return array[] things that aren't deserialized by the deserializer
	 */
	public function nonDeserializableProvider() {
		return array(
			array(
				'language' => 123,
				'value' => 'FooBar',
			),
			array(
				'language' => 'de',
				'value' => 999,
			),
		);
	}

	/**
	 * @return array[] an array of array( object deserialized, serialization )
	 */
	public function deserializationProvider() {
		return array(
			array(
				new Term( 'en', 'Value' ),
				array(
					'language' => 'en',
					'value' => 'Value',
				),
			),
		);
	}

}

<?php

namespace Tests\Wikibase\DataModel\Serializers;

use Tests\Wikibase\DataModel\FakeExtraValuesProvider;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\ExtraValuesAssigner;
use Wikibase\DataModel\Serializers\SiteLinkSerializer;
use Wikibase\DataModel\SiteLink;

/**
 * @covers Wikibase\DataModel\Serializers\SiteLinkSerializer
 *
 * @licence GNU GPL v2+
 * @author Thomas Pellissier Tanon
 */
class SiteLinkSerializerTest extends SerializerBaseTest {

	protected function buildSerializer() {
		return new SiteLinkSerializer( new ExtraValuesAssigner() );
	}

	public function serializableProvider() {
		return array(
			array(
				new SiteLink( 'enwiki', 'Nyan Cat' )
			),
			array(
				new SiteLink( 'enwiki', 'Nyan Cat', array(
					new ItemId( 'Q42' )
				) )
			),
		);
	}

	public function nonSerializableProvider() {
		return array(
			array(
				5
			),
			array(
				array()
			),
			array(
				new ItemId( 'Q42' )
			),
		);
	}

	public function serializationProvider() {
		return array(
			array(
				array(
					'site' => 'enwiki',
					'title' => 'Nyan Cat',
					'badges' => array()
				),
				new SiteLink( 'enwiki', 'Nyan Cat' )
			),
			array(
				array(
					'site' => 'enwiki',
					'title' => 'Nyan Cat',
					'badges' => array( 'Q42' )
				),
				new SiteLink( 'enwiki', 'Nyan Cat', array(
					new ItemId( 'Q42' )
				) )
			),
			array(
				array(
					'site' => 'frwikisource',
					'title' => 'Nyan Cat',
					'badges' => array( 'Q42', 'Q43' )
				),
				new SiteLink( 'frwikisource', 'Nyan Cat', array(
					new ItemId( 'Q42' ),
					new ItemId( 'q43' )
				) )
			),
		);
	}

	public function testSerializeWithExtraValues() {
		$serializer = new SiteLinkSerializer( new ExtraValuesAssigner( array( new FakeExtraValuesProvider() ) ) );
		$siteLink = new SiteLink( 'enwiki', 'Nyan Cat' );

		$this->assertEquals(
			array(
				'site' => 'enwiki',
				'title' => 'Nyan Cat',
				'badges' => array(),
				'foo' => 'bar'
			),
			$serializer->serialize( $siteLink )
		);
	}

}

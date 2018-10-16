<?php
/**
 * Created by JetBrains PhpStorm.
 * User: krzychu
 * Date: 30.10.13
 * Time: 10:47
 * To change this template use File | Settings | File Templates.
 */

namespace Wikia\api;


class ApiAccessServiceTest extends \WikiaBaseTest {
	private $org_wgApiAccess;

	public function setUp() {
		global $wgApiAccess, $wgApiEnvironment;
		$this->org_wgApiAccess = $wgApiAccess;
		$this->org_wgApiEnvironment = $wgApiEnvironment;
		$wgApiAccess = [
			'AAA' => 111,
			'BBB' => [ 'CCC' => 333 ]
		];

		$wgApiEnvironment = 'XXX';
		parent::setUp();
	}

	public function tearDown() {
		global $wgApiAccess, $wgApiEnvironment;
		$wgApiAccess = $this->org_wgApiAccess;
		$wgApiEnvironment = $this->org_wgApiEnvironment;
		parent::tearDown();
	}

	/**
	 * @covers  ApiAccessService::getApiAccess
	 */
	public function testGetApiAccess() {
		$mock = $this->getMockBuilder( "\ApiAccessService" )
			->disableOriginalConstructor()
			->setMethods( [ '__construct' ] )
			->getMock();

		$refl = new \ReflectionMethod( $mock, 'getApiAccess' );

		$refl->setAccessible( true );
		$this->assertEquals( 111, $refl->invoke( $mock, 'AAA', null ) );
		$this->assertEquals( 333, $refl->invoke( $mock, 'BBB', 'CCC' ) );
		$this->assertEquals( 0, $refl->invoke( $mock, 'ZZZ', null ) );
	}

	/**
	 * @covers  ApiAccessService::getEnvValue
	 */
	public function testGetProductionValue() {
		$mock = $this->getMockBuilder( "\ApiAccessService" )
			->disableOriginalConstructor()
			->setMethods( [ '__construct' ] )
			->getMock();

		$prop = new \ReflectionProperty( $mock, 'envValues' );
		$prop->setAccessible( true );
		$prop->setValue( $mock, [ 'XXX' => 765 ] );

		$refl = new \ReflectionMethod( $mock, 'getEnvValue' );

		$refl->setAccessible( true );
		$this->assertEquals( 765, $refl->invoke( $mock, 'XXX' ) );

	}

	/**
	 * @covers ApiAccessService::canUse
	 * @dataProvider dp_canUse
	 */
	public function testCanUse( $access, $test, $env, $result, $isCorporate, $isCommunity ) {
		$mock = $this->getMockBuilder( "\ApiAccessService" )
			->disableOriginalConstructor()
			->setMethods( [ '__construct', 'getApiAccess', 'isTestLocation', 'getEnvValue', 'isCorporateWiki', 'isCommunityCentralWiki' ] )
			->getMock();

		$mock->expects( $this->any() )
			->method( 'getApiAccess' )
			->will( $this->returnValue( $access ) );
		$mock->expects( $this->any() )
			->method( 'isTestLocation' )
			->will( $this->returnValue( $test ) );
		$mock->expects( $this->any() )
			->method( 'getEnvValue' )
			->will( $this->returnValue( $env ) );
		$mock->expects( $this->any() )
			->method( 'isCorporateWiki' )
			->will( $this->returnValue( $isCorporate ) );
		$mock->expects( $this->any() )
			->method( 'isCommunityCentralWiki' )
			->will( $this->returnValue($isCommunity) );

			
		$this->assertEquals( $result, $mock->canUse( '', '' ) );
	}

	/**
	 * @covers ApiAccessService::checkUse
	 * @expectedException NotFoundApiException
	 */
	public function testCheckUse( ) {
		$mock = $this->getMockBuilder( "\ApiAccessService" )
			->disableOriginalConstructor()
			->setMethods( [ '__construct', 'canUse'] )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'canUse' )
			->will( $this->returnValue( false ) );

		$mock->checkUse("something", "something");
	}

	public function dp_canUse() {
		return [
			[ \ApiAccessService::URL_TEST | \ApiAccessService::ENV_DEVELOPMENT, true, \ApiAccessService::ENV_DEVELOPMENT, true, false, false ],
			[ \ApiAccessService::URL_TEST | \ApiAccessService::ENV_DEVELOPMENT, false, \ApiAccessService::ENV_DEVELOPMENT, false, false, false ],
			[ \ApiAccessService::ENV_SANDBOX, false, \ApiAccessService::ENV_SANDBOX, true, false, false ],
			[ \ApiAccessService::ENV_SANDBOX, false, \ApiAccessService::ENV_DEVELOPMENT, false, false, false ],
			[ \ApiAccessService::WIKIA_CORPORATE, false, 0, true, true, false ],
			[ \ApiAccessService::WIKIA_CORPORATE, false, 0, false, false, false ],
			[ \ApiAccessService::WIKIA_NON_CORPORATE, false, 0, false, true, false ],
			[ \ApiAccessService::WIKIA_NON_CORPORATE, false, 0, true, false, false ],
			[ \ApiAccessService::WIKIA_COMMUNITY, false, 0, true, false, true ],
			[ \ApiAccessService::WIKIA_COMMUNITY, false, 0, false, false, false ]
		];
	}


}

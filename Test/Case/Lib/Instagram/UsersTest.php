<?php

/**
 * UsersTestCase
 */
class UsersTestCase extends CakeTestCase {

	private $_instagramConfig;

	/**
	 * setUp
	 *
	 * Setup the classes the crud component needs to be testable
	 */
	public function setUp() {
		// Use my Instagram client
		$this->_instagramConfig = Configure::read('Instagram');
		Configure::write('Instagram', array(
			'clientId'     => 'cdd97394669e453dabf1671cfadd7152',
			'clientSecret' => '62f381929d584c15b66bd18395f5a786',
			'accessToken'  => '180947758.cdd9739.cc9de8bab1ef4a93a84edd9c93fa4b3d',
		));

		// @todo: do not connect to Instagram API

		parent::setUp();
	}

	/**
	 * tearDown method
	 */
	public function tearDown() {
		Configure::write('Instagram', $this->_instagramConfig);
		parent::tearDown();
	}

	// Tests

	/**
	 * testFeed
	 */
	public function testFeed() {
		$result = \Instagram\Users::feed();
		$this->assertIsA($result, 'stdClass', 'Result is not an object');
		$this->assertTrue(isset($result->data), 'Result does not contain data');
	}

	/**
	 * testGet
	 */
	public function testGet() {
		$result = \Instagram\Users::get(1574083);
		$this->assertIsA($result, 'stdClass', 'Result is not an object');
		$this->assertTrue(isset($result->data), 'Result does not contain data');
	}

	/**
	 * testLiked
	 */
	public function testLiked() {
		$result = \Instagram\Users::liked();
		$this->assertIsA($result, 'stdClass', 'Result is not an object');
		$this->assertTrue(isset($result->data), 'Result does not contain data');
	}

	/**
	 * testRecent
	 */
	public function testRecent() {
		$result = \Instagram\Users::recent(1574083);
		$this->assertIsA($result, 'stdClass', 'Result is not an object');
		$this->assertTrue(isset($result->data), 'Result does not contain data');
	}

	/**
	 * testSearch
	 */
	public function testSearch() {
		$result = \Instagram\Users::search('test');
		$this->assertIsA($result, 'stdClass', 'Result is not an object');
		$this->assertTrue(isset($result->data), 'Result does not contain data');
	}

}

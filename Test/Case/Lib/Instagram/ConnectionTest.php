<?php

/**
 * UsersTestCase
 */
class ConnectionCase extends CakeTestCase {

	private $_connection;
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
		$this->_connection = new \Instagram\Connection();

		// @todo: do not connect to Instagram API

		parent::setUp();
	}

	/**
	 * tearDown method
	 */
	public function tearDown() {
		Configure::write('Instagram', $this->_instagramConfig);
		unset($this->_connection);

		parent::tearDown();
	}

	// Tests

	/**
	 * testAuthorizeURL
	 */
	public function testAuthorizeURL() {
		$redirectURL = 'http://example.com';
		$url = $this->_connection->authorizeURL($redirectURL);
		$this->assertEquals('https://instagram.com/oauth/authorize/?client_id=cdd97394669e453dabf1671cfadd7152&redirect_uri=' . $redirectURL . '&response_type=code', $url, 'Authorize URL is incorrectly formatted');
	}

}

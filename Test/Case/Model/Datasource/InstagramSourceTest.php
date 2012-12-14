<?php
App::uses('InstagramSource', 'InstagramDatasource.Model/Datasource');
App::uses('Media', 'InstagramDatasource.Model');

/**
 * InstagramSourceTestCase
 */
class InstagramSourceTestCase extends CakeTestCase {

/**
 * setUpBeforeClass
 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		// My test client; please don't abuse
		ConnectionManager::create('instagram_test', array(
			'datasource' => 'InstagramDatasource.InstagramSource',
			'client_id' => 'd58ea0b1eb4d4c008c9fb63fc82a7e95',
			'client_secret' => '634d7f65d0334b8fb277c14d720910a5',
			'redirect_url' => 'http://localhost'
		));
	}

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->InstagramSource = ConnectionManager::getDataSource('instagram_test');
		$this->Media = ClassRegistry::init('InstagramDatasource.Media');
	}

	public function testAuthenticate() {
		// Get the authentication URL
		$result = $this->InstagramSource->authenticate();
		$this->assertStringStartsWith('https://instagram.com', $result, 'Authenticate URL does not point to instagram.com');
		$this->assertContains($this->InstagramSource->config['client_id'], $result, 'Authenticate URL does have the client ID');

		// Send bogus authentication code
		$result = $this->InstagramSource->authenticate('hello world');
		$this->assertFalse($result, 'Bogus authentication code != false');
	}

	public function testCreate() {
		$this->expectException('InstagramSourceException');
		$this->InstagramSource->create($this->Media);
	}

	public function testDelete() {
		$this->expectException('InstagramSourceException');
		$this->InstagramSource->delete($this->Media);
	}

	public function testRead() {
		$result = $this->InstagramSource->read($this->Media);
		$this->assertNotEmpty($result);
	}

	public function testUpdate() {
		$this->expectException('InstagramSourceException');
		$this->InstagramSource->update($this->Media);
	}

}

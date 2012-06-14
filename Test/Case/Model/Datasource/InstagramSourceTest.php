<?php

/**
 * InstagramSourceTestCase
 */
class InstagramSourceTestCase extends CakeTestCase {

	/**
	 * setUp
	 */
	public function setUp() {
		// @todo: create data source
		parent::setUp();
	}

	/**
	 * tearDown
	 */
	public function tearDown() {
		parent::tearDown();
	}

	// Tests

	public function testAuthenticate() {
		$instagram = ConnectionManager::getDataSource('instagram');

		// Get the authentication URL
		$result = $instagram->authenticate();
		$this->assertStringStartsWith('https://instagram.com', $result, 'Authenticate URL does not point to instagram.com');
		$this->assertContains($instagram->config['client_id'], $result, 'Authenticate URL does have the client ID');

		// Send bogus authentication code
		$result = $instagram->authenticate('hello world');
		$this->assertFalse($result, 'Bogus authentication code != false');
	}

	public function testCreate() {
		$instagram = ConnectionManager::getDataSource('instagram');

		// Like a random image
		$result = $instagram->create('media/1234/likes');
		$this->assertIsA($result, 'stdClass', 'Random media like result is not an object');
		$this->assertTrue(isset($result->meta), 'Random media like result does not contain meta data');
		$this->assertEqual(400, $result->meta->code, 'Random media like result was successful');
		$this->assertTrue(empty($result->data), 'Random media like result has data');
	}

	public function testDelete() {
		$instagram = ConnectionManager::getDataSource('instagram');

		// Delete a random comment
		$result = $instagram->delete('media/1234/comment', 4321);
		$this->assertNull($result, 'Random comment delete result != null');
	}

	public function testRead() {
		$instagram = ConnectionManager::getDataSource('instagram');

		// Getting a tag
		$result = $instagram->read('tags/test');
		$this->assertIsA($result, 'stdClass', 'Tag read result is not an object');
		$this->assertTrue(isset($result->meta), 'Tag read result does not contain meta data');
		$this->assertEqual(200, $result->meta->code, 'Tag read result was not successful');
		$this->assertTrue(!empty($result->data), 'Tag read result has no data');

		// Searching for a user
		$result = $instagram->read('users/search', array(
			'q' => 'test'
		));
		$this->assertIsA($result, 'stdClass', 'User search result is not an object');
		$this->assertTrue(isset($result->meta), 'User search result does not contain meta data');
		$this->assertEqual(200, $result->meta->code, 'User search result was not successful');
		$this->assertTrue(!empty($result->data), 'User search result has no data');

		// Bogus action
		$result = $instagram->read('foo/bar');
		$this->assertNull($result, 'Bogus URL result != null');
	}

	public function testUpdate() {
		$instagram = ConnectionManager::getDataSource('instagram');

		// Update shouldn't work
		$result = $instagram->update('media');
		$this->assertFalse($result);
	}

}

<?php
namespace Instagram;

/**
 * Wrapper for the likes end-point of the Instagram API.
 *
 * @package InstagramApi
 * @author Michael Enger <mien@nodesagency.no>
 * @see http://instagram.com/developer/endpoints/likes/
 */
class Likes {

	/**
	 * Remove the current user's like from an image
	 *
	 * @param int $id ID of the media
	 * @return object
	 */
	public static function delete($id) {
		$connection = new Connection();
		return $connection->delete('media/' . $id . '/likes', array(), true);
	}

	/**
	 * Get the likes on an image
	 *
	 * @param int $id ID of the media
	 * @return object
	 */
	public static function get($id) {
		$connection = new Connection();
		return $connection->get('media/' . $id . '/likes', array(), true);
	}

	/**
	 * Like an image as the current user
	 *
	 * @param int    $id   ID of the media
	 * @param string $text Text of the comment
	 * @return object
	 */
	public static function post($id) {
		$connection = new Connection();
		return $connection->post('media/' . $id . '/likes', array(), true);
	}

}

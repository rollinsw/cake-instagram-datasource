<?php
namespace Instagram;

/**
 * Wrapper for the comments end-point of the Instagram API.
 *
 * @package InstagramApi
 * @author Michael Enger <mien@nodesagency.no>
 * @see http://instagram.com/developer/endpoints/comments/
 */
class Comments {

	/**
	 * Delete a comment from an image
	 *
	 * @param int $id      ID of the media
	 * @param int $comment ID of the comment to delete
	 * @return object
	 */
	public static function delete($id, $comment) {
		$connection = new Connection();
		return $connection->delete('media/' . $id . '/comments/' . $comment, array(), true);
	}

	/**
	 * Get all comments from an image
	 *
	 * @param int $id      ID of the media
	 * @return object
	 */
	public static function get($id) {
		$connection = new Connection();
		return $connection->get('media/' . $id . '/comments', array(), true);
	}

	/**
	 * Add a comment to an image
	 *
	 * @param int    $id   ID of the media
	 * @param string $text Text of the comment
	 * @return object
	 */
	public static function post($id, $text) {
		$connection = new Connection();
		return $connection->post('media/' . $id . '/comments', array('text' => $text), true);
	}

}

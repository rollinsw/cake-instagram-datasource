<?php
namespace Instagram;

/**
 * Wrapper for the users end-point of the Instagram API.
 *
 * @package InstagramApi
 * @author Michael Enger <mien@nodesagency.no>
 * @see http://instagram.com/developer/endpoints/users/
 */
class Users {

	/**
	 * Get feed for currently logged in user.
	 * Note: Requires authentication
	 *
	 * Parameters
	 *   count  : Count of media to return
	 *   min_id : Return media later than this min_id
	 *   max_id : Return media earlier than this max_id
	 *
	 * @param array $params (optional) Parameters to send
	 * @return object
	 */
	public static function feed($params = array()) {
		$connection = new Connection();
		return $connection->get('users/self/feed', $params, true);
	}

	/**
	 * Get information about a user.
	 *
	 * @param int $id ID of the user
	 * @return object
	 */
	public static function get($id) {
		$connection = new Connection();
		return $connection->get('users/' . $id);
	}

	/**
	 * Get liked media for currently logged in user.
	 * Note: Requires authentication
	 *
	 * Parameters
	 *   count       : Count of media to return
	 *   min_like_id : Return media liked before this id
	 *
	 * @param array $params (optional) Parameters to send
	 * @return object
	 */
	public static function liked($params = array()) {
		$connection = new Connection();
		return $connection->get('users/self/media/liked', $params, true);
	}

	/**
	 * Recent media published by a user.
	 * Note: Requires authentication
	 *
	 * Parameters
	 *   count         : Count of media to return
	 *   min_id        : Return media later than this min_id
	 *   max_id        : Return media earlier than this max_id
	 *   min_timestamp : Return media after this UNIX timestamp
	 *   max_timestamp : Return media before this UNIX timestamp
	 *
	 * @param int   $id     ID of the user
	 * @param array $params (optional) Parameters to send
	 * @return object
	 */
	public static function recent($id, $params = array()) {
		$connection = new Connection();
		return $connection->get('users/' . $id . '/media/recent', $params, true);
	}

	/**
	 * Search for a user by name
	 *
	 * Parameters
	 *   count : Count of users to return
	 *
	 * @param string $name   Name to search for
	 * @param array  $params (optional) Parameters to send
	 * @return object
	 */
	public static function search($name, $params = array()) {
		$connection = new Connection();
		$params['q'] = $name;
		return $connection->get('users/search', $params);
	}

}

<?php
namespace Instagram;

/**
 * Wrapper for the relationships end-point of the Instagram API.
 *
 * @package InstagramApi
 * @author Michael Enger <mien@nodesagency.no>
 * @see http://instagram.com/developer/endpoints/relationships/
 */
class Relationships {

	/**
	 * Add a relationship between the current user and another.
	 *
	 * @param int    $id     ID of the other user
	 * @param string $action The action to update the relationship to (follow/unfollow/block/unblock/approve/deny)
	 * @return object
	 */
	public static function add($id, $action) {
		$connection = new Connection();
		return $connection->get('users/' . $id . '/relationship', array('action' => $action), true);
	}

	/**
	 * Get a list of the users the specified user is followed by.
	 *
	 * @param int $id ID of the user
	 * @return object
	 */
	public static function followedBy($id) {
		$connection = new Connection();
		return $connection->get('users/' . $id . '/followed-by', array(), true);
	}

	/**
	 * Get a list of user this user follows.
	 *
	 * @param int $id ID of the user
	 * @return object
	 */
	public static function follows($id) {
		$connection = new Connection();
		return $connection->get('users/' . $id . '/follows', array(), true);
	}

	/**
	 * Get the relationship between the current user and another.
	 *
	 * @param int $id ID of the other user
	 * @return object
	 */
	public static function get($id) {
		$connection = new Connection();
		return $connection->get('users/' . $id . '/relationship', array(), true);
	}

	/**
	 * Get a list of the users who have requested to follow the current user.
	 *
	 * @return object
	 */
	public static function requestedBy() {
		$connection = new Connection();
		return $connection->get('users/self/requested-by', array(), true);
	}

}

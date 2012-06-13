<?php
namespace Instagram;

/**
 * Wrapper for the tags end-point of the Instagram API.
 *
 * @package InstagramApi
 * @author Michael Enger <mien@nodesagency.no>
 * @see http://instagram.com/developer/endpoints/tags/
 */
class Tags {

	/**
	 * Get information about a tag.
	 *
	 * @param string $name Name of the tag
	 * @return object
	 */
	public static function get($name) {
		$connection = new Connection();
		return $connection->get('tags/' . $name);
	}

	/**
	 * Get a list of recent images tagged with the specified tag
	 *
	 * Parameters
	 *   min_id        : Return media before this min_id
	 *   max_id        : Return media after this max_id
	 *
	 * @param string $name  Name of the tag
	 * @param array  $param (optional) List of parameters to send
	 * @return object
	 */
	public static function recent($name, $params = array()) {
		$connection = new Connection();
		return $connection->get('tags/' . $name . '/media/recent', $params);
	}

	/**
	 * Search for a tag
	 *
	 * @param string $text Tag to search for
	 * @return object
	 */
	public static function search($text) {
		$connection = new Connection();
		return $connection->get('tags/search', array('q' => $text));
	}

}

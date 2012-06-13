<?php
namespace Instagram;

/**
 * Wrapper for the geographies end-point of the Instagram API.
 *
 * @package InstagramApi
 * @author Michael Enger <mien@nodesagency.no>
 * @see http://instagram.com/developer/endpoints/geographies/
 */
class Geographies {

	/**
	 * Gets the recent media posted near a Geography subscription.
	 *
	 * Parameters
	 *   count  : Max number of media to return
	 *   min_id : Return media before this min_id
	 *
	 * @param int   $id     ID of the geography subscription
	 * @param array $params (optional) List of parameters to send
	 * @return object
	 */
	public static function recent($id, $params = array()) {
		$connection = new Connection();
		return $connection->get('geographies/' . $id . '/media/recent', $params);
	}

}

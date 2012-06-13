<?php
namespace Instagram;

/**
 * Wrapper for the locations end-point of the Instagram API.
 *
 * @package InstagramApi
 * @author Michael Enger <mien@nodesagency.no>
 * @see http://instagram.com/developer/endpoints/locations/
 */
class Locations {

	/**
	 * Get information about a location.
	 *
	 * @param int $id ID of the location
	 * @return object
	 */
	public static function get($id) {
		$connection = new Connection();
		return $connection->get('locations/' . $id);
	}

	/**
	 * Get a list of recent media by a user.
	 *
	 * Parameters
	 *   min_timestamp : Return media after this UNIX timestamp
	 *   max_timestamp : Return media before this UNIX timestamp
	 *   min_id        : Return media before this min_id
	 *   max_id        : Return media after this max_id
	 *
	 * @param int   $id    ID of the location
	 * @param array $param (optional) List of parameters to send
	 * @return object
	 */
	public static function recent($id, $params = array()) {
		$connection = new Connection();
		return $connection->get('locations/' . $id . '/media/recent', $params);
	}

	/**
	 * Search for a location by geographic coordinates
	 *
	 * Parameters
	 *   lat              : Latitude of the center search coordinate. If used, lng is required
	 *   lng              : Longitude of the center search coordinate. If used, lat is required
	 *   distance         : Default is 1000m (distance=1000), max distance is 5000
	 *   foursquare_id    : Returns a location mapped off of a foursquare v1 api location id. If used,
	 *                      you are not required to use lat and lng. Note that this method is
	 *                      deprecated; you should use the new foursquare IDs with V2 of their API.
	 *   foursquare_v2_id : Returns a location mapped off of a foursquare v2 api location id. If used,
	 *                      you are not required to use lat and lng.
	 *
	 * @param array $param (optional) List of parameters to send
	 * @return object
	 */
	public static function search($params = array()) {
		$connection = new Connection();
		return $connection->get('locations/search', $params);
	}

}

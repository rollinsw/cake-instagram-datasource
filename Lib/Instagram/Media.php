<?php
namespace Instagram;

/**
 * Wrapper for the media end-point of the Instagram API.
 *
 * @package InstagramApi
 * @author Michael Enger <mien@nodesagency.no>
 * @see http://instagram.com/developer/endpoints/media/
 */
class Media {

	/**
	 * Get information about an image.
	 *
	 * @param int $id ID of the image
	 * @return object
	 */
	public static function get($id) {
		$connection = new Connection();
		return $connection->get('media/' . $id);
	}

	/**
	 * Get a list of imagest that are popular at the moment.
	 *
	 * @return object
	 */
	public static function popular() {
		$connection = new Connection();
		return $connection->get('media/popular');
	}

	/**
	 * Search for an image by geographic coordinates
	 *
	 * Parameters
	 *   lat           : Latitude of the center search coordinate. If used, lng is required
	 *   lng           : Longitude of the center search coordinate. If used, lat is required
	 *   distance      : Default is 1000m (distance=1000), max distance is 5000
	 *   min_timestamp : A unix timestamp. All media returned will be taken later than this timestamp
	 *   max_timestamp : A unix timestamp. All media returned will be taken earlier than this timestamp
	 *
	 * @param array $param (optional) List of parameters to send
	 * @return object
	 */
	public static function search($params = array()) {
		$connection = new Connection();
		return $connection->get('media/search', $params);
	}

}

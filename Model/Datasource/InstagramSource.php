<?php

/**
 * Provides a generic connection to the Instagram API.
 *
 * @package InstagramDatasource
 * @author Michael Enger <mien@nodesagency.no>
 */
class InstagramSource extends DataSource {

/**
 * Properties
 */
	public $description = 'Instagram API Source';

/**
 * Authenticate the Instagram connection with a user.
 *
 * @param string  $code     (optional) Code to use when authenticating
 * @oaram boolean $complete (optional) Whether the authentication is complete
 * @return mixed
 */
	public function authenticate($code = null, $complete = false) {
		if ($complete) {
			// Set access token
			$this->config['access_token'] = $code;
			return true;
		} elseif (!empty($code)) {
			// Attempt to get access token from Instagram
			$url = 'https://api.instagram.com/oauth/access_token';
			$params = array(
				'client_id'     => $this->config['client_id'],
				'client_secret' => $this->config['client_secret'],
				'grant_type'    => 'authorization_code',
				'redirect_uri'  => $this->config['redirect_url'],
				'code'          => $code
			);

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

			$response = json_decode(curl_exec($curl));
			curl_close($curl);

			if (!empty($response->code) && 400 === $response->code) {
				return false;
			}

			if (!empty($response->access_token)) {
				$this->config['access_token'] = $response->access_token;
			}

			return $response;
		} else {
			// Get authentication URL
			return 'https://instagram.com/oauth/authorize/?client_id=' . $this->config['client_id'] . '&redirect_uri=' . urlencode($this->config['redirect_url']) . '&response_type=code';
		}
	}

/**
 * Used to create new records. The "C" CRUD.
 *
 * @param Model $model  The Model to be created.
 * @param array $fields (optional) List of fields to be saved
 * @param array $values (optional) List of values to save
 * @return mixed
 */
	public function create(Model $model, $fields = null, $values = null) {
		die('@todo: create');
	}

/**
 * Delete a record(s) in the datasource.
 *
 * @param Model $model The model class having record(s) deleted
 * @param mixed $id    (optional) ID of the model to delete
 */
	public function delete(Model $model, $id = null) {
		die('@todo: delete');
	}

/**
 * Returns a Model description (metadata) or null if none found.
 *
 * @param Model|string $model
 * @return array Metadata for the $model
 */
	public function describe($model) {
		die('@todo: describe');
	}

/**
 * Caches/returns cached results for child instances
 *
 * @param mixed $data
 * @return array Sources available in this datasource
 */
	public function listSources($data = null) {
		return null; // caching is disabled (for now)
    }

/**
 * Used to read records from the Datasource. The "R" in CRUD
 *
 * @param Model   $model     The model being read
 * @param array   $queryData (optional) List of query data used to find the data you want
 * @param boolean $recursive (optional) Whether to make the read recursive
 * @return mixed
 */
	public function read(Model $model, $queryData = array(), $recursive = null) {
		die('@todo: read');
	}

/**
 * Update a record(s) in the datasource.
 *
 * @param Model $model      Instance of the model class being updated
 * @param array $fields     (optional) List of fields to be updated
 * @param array $values     (optional) List of values to update the $fields to
 * @param array $contitions (optional) Conditions for the update
 * @return boolean
 */
	public function update(Model $model, $fields = null, $values = null, $conditions = null) {
		die('@todo: update');
	}

/**
 * Get a URL to the Instagram API
 *
 * @param string $action Action (API endpoint) to contact
 * @param array  $params (optional) List of paramters to send to the URL
 * @return string
 */
	protected function _url($action, $params = array()) {
		$url = 'https://api.instagram.com/v1/' . $action;

		// Authenticate URL
		if (!empty($this->config['access_token'])) {
			$params['access_token'] = $this->config['access_token'];
		} else {
			$params['client_id'] = $this->config['client_id'];
		}

		// Add params
		$i = 0;
		foreach ($params as $key => $value) {
			$url .= (0 !== $i ? '&' : '?') . urlencode($key) . '=' . urlencode($value);
			$i++;
		}

		return $url;
	}
}
<?php

/**
 * Provides a generic connection to the Instagram API.
 *
 * @package InstagramDatasource
 * @author Michael Enger <mien@nodesagency.no>
 */
class InstagramSource extends DataSource {

/**
 * Description of the source.
 *
 * @var string
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

			if (empty($response) || (!empty($response->code) && 400 === $response->code)) {
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
		switch (get_class($model)) {
		case 'Media':
			throw new InstagramSourceException('Media entries do not support create');
			break;
		default:
			throw new InstagramSourceException('Unhandled model type: ' . get_class($model));
		}
	}

/**
 * Delete a record(s) in the datasource.
 *
 * @param Model $model The model class having record(s) deleted
 * @param mixed $id    (optional) ID of the model to delete
 */
	public function delete(Model $model, $id = null) {
		switch (get_class($model)) {
		case 'Media':
			throw new InstagramSourceException('Media entries do not support delete');
			break;
		default:
			throw new InstagramSourceException('Unhandled model type: ' . get_class($model));
		}
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
		$data = array();
		$limit = !empty($queryData['limit']) ? $queryData['limit'] : 0;
		$offset = !empty($queryData['offset']) ? $queryData['offset'] : 0;

		switch (get_class($model)) {
		case 'Media':
			if (empty($queryData['conditions'])) {
				$data = $this->_request('GET', 'media/popular');
			} else {
				$conditions = $this->_extractFields($queryData['conditions'], 'Media');
				if (!empty($conditions['id'])) {
					$id = $conditions['id'];
					unset($conditions['id']);
					$data = array(
						'data' => $this->_request('GET', 'media/' . $id, $conditions)
					);
				} elseif (!empty($conditions['location_id'])) {
					$location_id = $conditions['location_id'];
					unset($conditions['location_id']);
					$data = $this->_request('GET', 'locations/' . $location_id . '/media/recent',  $conditions);
				} elseif (!empty($conditions['tag'])) {
					$tag = $conditions['tag'];
					unset($conditions['tag']);
					$data = $this->_request('GET', 'tags/' . $tag . '/media/recent',  $conditions);
				} else {
					$data = $this->_request('GET', 'media/search', $conditions);
				}
			}

			if (!empty($data['data'])) {
				$pagination = !empty($data['pagination']) ? $data['pagination'] : null;
				$data = $this->_wrapResults($data['data'], $model->alias);

				// Apply offset
				if (!empty($offset)) {
					if ($offset < count($data)) {
						$data = array_slice($data, $offset);
					} else {
						$queryData['offset'] = $offset - count($data);
						$data = $this->read($model, $queryData);
					}
				}

				// Apply limit
				if (!empty($limit)) {
					if ($limit < count($data)) {
						$data = array_slice($data, 0, $limit);
					} elseif ($limit > count($data)) {
						$queryData['limit'] = $limit - count($data);
						if (!empty($pagination['next_max_tag_id'])) {
							$queryData['conditions']['max_tag_id'] = $pagination['next_max_tag_id'];
						} elseif (!empty($pagination['next_max_timestamp'])) {
							$queryData['conditions']['max_timestamp'] = $pagination['next_max_timestamp'];
						} elseif (!empty($pagination['next_max_id'])) {
							$queryData['conditions']['max_id'] = $pagination['next_max_id'];
						}
						
						if ((!empty($queryData['conditions']['max_tag_id']) || 
							!empty($queryData['conditions']['max_timestamp']) || 
							!empty($queryData['conditions']['max_id'])) AND
							is_array($this->read($model, $queryData))) {
							$data = array_merge($data, $this->read($model, $queryData));
						}
					}
				}
			} else {
				$data = false;
			}
			break;
		default:
			throw new InstagramSourceException('Unhandled model type: ' . get_class($model));
		}

		return !empty($data)
			? $data
			: false;
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
		switch (get_class($model)) {
		case 'Media':
			throw new InstagramSourceException('Media entries do not support update');
			break;
		default:
			throw new InstagramSourceException('Unhandled model type: ' . get_class($model));
		}
	}

/**
 * Get the list of fields (for example from the conditions array) based on the model name.
 *
 * @param array  $fields Array of keys/values
 * @param string $model  Name of the model to extract fields for
 * @return array
 */
	protected function _extractFields($fields, $model) {
		$temp = array();
		foreach ($fields as $key => $value) {
			if (preg_match('/^' . $model . '\.\w+/', $key)) { // ModelName.fieldName
				$key = substr($key, strlen($model) + 1);
				$temp[$key] = $value;
			} elseif (strpos($key, '.') === false) { // fieldName
				$temp[$key] = $value;
			}
		}

		return $temp;
	}

/**
 * Request an action from the Instagram API.
 *
 * @param string $type   HTTP request type
 * @param string $action Instagram API action
 * @param array  $params (optional) Parameters to send with the request
 * @return array
 */
	protected function _request($type, $action, $params = array()) {
		switch ($type) {
		case 'GET':
			$curl = curl_init($this->_url($action, $params));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$response = json_decode(curl_exec($curl), true);
			curl_close($curl);
			break;
		case 'DELETE':
		case 'POST':
		case 'PUT':
			// @todo
		default:
			throw new InstagramSourceException('Unandled request type: ' . $type);
		}

		if (!empty($response['meta']['error_type'])) {
			throw new InstagramSourceException(sprintf('Instagram API failed with %s (error code %d): %s', $response['meta']['error_type'], $response['meta']['code'], $response['meta']['error_message']));
		}

		return $response;
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
			if (empty($value)) {
				continue;
			}

			$url .= (0 !== $i ? '&' : '?') . urlencode($key) . '=' . urlencode($value);
			$i++;
		}

		return $url;
	}

/**
 * Wrap a list of results in a model.
 *
 * @param array  $results List of results
 * @param string $model   Name of the model
 * @return array
 */
	protected function _wrapResults($results, $model) {
		$temp = array();
		foreach ($results as $entry) {
			$temp[] = array(
				$model => $entry
			);
		}
		return $temp;
	}
}

/**
 * Exception wrapper to differentiate it from other exceptions.
 */
class InstagramSourceException extends Exception {}

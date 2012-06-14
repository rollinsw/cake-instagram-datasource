<?php

/**
 * Provides a generic connection to the Instagram API.
 *
 * @package InstagramApi
 * @author Michael Enger <mien@nodesagency.no>
 */
class InstagramSource extends DataSource {
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

			return $this->config['access_token'];
		} else {
			// Get authentication URL
			return 'https://instagram.com/oauth/authorize/?client_id=' . $this->config['client_id'] . '&redirect_uri=' . $this->config['redirect_url'] . '&response_type=code';
		}
	}

	/**
	 * Used to create new records. The "C" CRUD.
	 *
	 * @param string $action  The name of the action
	 * @param array  $fields  (optional) List of fields to be saved
	 * @param array  $values  (optional) List of values to save
	 * @return mixed
	 */
	public function create($action, $fields = null, $values = null) {
		// Combine the fields and values
		$params = array();
		if (!empty($fields) && !empty($values)) {
			foreach ($fields as $k => $v) {
				$params[$v] = !empty($values[$k]) ? $values[$k] : null;
			}
		}

		$url = $this->url($action);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

		$response = json_decode(curl_exec($curl));
		curl_close($curl);

		return $response;
	}

	/**
	 * Delete a record(s) in the datasource.
	 *
	 * @param string $action The name of the action
	 * @param mixed  $id     (optional) ID of the model to delete
	 */
	public function delete($action, $id = null) {
		$url = $this->url($action . '/' . $id);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');

		$response = json_decode(curl_exec($curl));
		curl_close($curl);

		return $response;
	}

	/**
	 * Used to read records from the Datasource. The "R" in CRUD
	 *
	 * @param string $action    The name of the action
	 * @param array  $queryData (optional) List of query data used to find the data you want
	 * @return mixed
	 */
	public function read($action, $queryData = array()) {
		$url = $this->url($action, $queryData);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($curl));
		curl_close($curl);

		return $response;
	}

	/**
	 * Update a record(s) in the datasource.
	 *
	 * @param string $action The name of the action
	 * @param array  $fields (optional) List of fields to be updated
	 * @param array  $values (optional) List of values to update the $fields to
	 * @return boolean
	 */
	public function update($action, $fields = null, $values = null) {
		return false; // Instagram does not support updating entries
	}

	/**
	 * Get a URL to the Instagram API
	 *
	 * @param string $action Action (API endpoint) to contact
	 * @param array  $params (optional) List of paramters to send to the URL
	 * @return string
	 */
	public function url($action, $params = array()) {
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
<?php
namespace Instagram;

/**
 * Provides a generic connection to the Instagram API.
 *
 * @package InstagramApi
 * @author Michael Enger <mien@nodesagency.no>
 */
class Connection {

	// Variables

	private $_clientId;
	private $_clientSecret;
	private $_accessToken;

	/**
	 * Creates a connection to the API.
	 *
	 * @param string $clientId     (optional) Client ID from Instagram
	 * @param string $clientSecret (optional) Client secret from Instagram
	 * @see http://instagram.com/developer/clients/manage/
	 */
	public function __construct($clientId = null, $clientSecret = null) {
		$this->_clientId = (!empty($clientId) ? $clientId : \Configure::read('Instagram.clientId'));
		$this->_clientSecret = (!empty($clientSecret) ? $clientSecret : \Configure::read('Instagram.clientSecret'));
	}

	/**
	 * Get the access token based on the return code.
	 *
	 * @param string $redirectURL Redirect URL used to generate the authorization URL
	 * @param string $code        Code returned by the Instagram authorization
	 */
	public function authorize($redirectURL, $code) {
		$url = 'https://api.instagram.com/oauth/access_token';
		$params = array(
			'client_id'     => $this->_clientId,
			'client_secret' => $this->_clientSecret,
			'grant_type'    => 'authorization_code',
			'redirect_uri'  => $redirectURL,
			'code'          => $code
		);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

		$response = json_decode(curl_exec($curl));
		curl_close($curl);

		if (!empty($response->code) && 400 === $response->code) {
			throw new \Exception('Unable to authorize Instagram API: '. $response->error_message);
		}

		if (!empty($response->access_token)) {
			$this->_access_token = $response->access_token;
			\CakeSession::write('Instagram.' . $this->_clientId . '.accessToken', $this->_access_token);
		}

		return $response;
	}

	/**
	 * Check whether the application is authorized.
	 *
	 * @return boolean
	 */
	public function authorized() {
		if (!empty($this->_accessToken)) return true;

		// Attempt to get the access token
		$this->_accessToken = \Configure::read('Instagram.accessToken');
		if (empty($this->_accessToken)) {
			$this->_accessToken = \CakeSession::read('Instagram.' . $this->_clientId . '.accessToken');
		}

		return !empty($this->_accessToken);
	}

	/**
	 * Get the authorization URL.
	 *
	 * @param string $redirectURL URL to redirect to once completed (note: must be registered with the Instagram client being used)
	 */
	public function authorizeURL($redirectURL) {
		return 'https://instagram.com/oauth/authorize/?client_id=' . $this->_clientId . '&redirect_uri=' . $redirectURL . '&response_type=code';
	}

	/**
	 * Delete content.
	 *
	 * @param string  $url        API endpoint to delete content from
	 * @param array   $params     (optional) List of paramteters to send
	 * @param boolean $authorized Whether the request needs to be authorized
	 */
	public function delete($url, $params = array(), $authorized = false) {
		if ($authorized) {
			$this->authorized(); // ensure that the access token is present
			$params['access_token'] = $this->_accessToken;
		} else {
			$params['client_id'] = $this->_clientId;
		}
		$url = $this->url($url, $params);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');

		$response = json_decode(curl_exec($curl));
		curl_close($curl);

		return $response;
	}

	/**
	 * Get some content.
	 *
	 * @param string  $url        API endpoint to get content from
	 * @param array   $params     (optional) List of paramteters to send
	 * @param boolean $authorized Whether the request needs to be authorized
	 */
	public function get($url, $params = array(), $authorized = false) {
		if ($authorized) {
			$this->authorized(); // ensure that the access token is present
			$params['access_token'] = $this->_accessToken;
		} else {
			$params['client_id'] = $this->_clientId;
		}
		$url = $this->url($url, $params);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($curl));
		curl_close($curl);

		return $response;
	}

	/**
	 * Post some content.
	 *
	 * @param string  $url        API endpoint to post content to
	 * @param array   $params     (optional) List of paramteters to send
	 * @param boolean $authorized Whether the request needs to be authorized
	 */
	public function post($url, $params = array(), $authorized = false) {
		if ($authorized) {
			$this->authorized(); // ensure that the access token is present
			$params['access_token'] = $this->_accessToken;
		} else {
			$params['client_id'] = $this->_clientId;
		}
		$url = $this->url($url);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

		$response = json_decode(curl_exec($curl));
		curl_close($curl);

		return $response;
	}

	/**
	 * Creates a full URL from an API endpoint and parameters.
	 *
	 * @param string $url    URL to extend
	 * @param array  $params (optional) List of parameters
	 * @return string
	 */
	private function url($url, $params = array()) {
		$url = 'https://api.instagram.com/v1/' . $url;

		$i = 0;
		foreach ($params as $key => $value) {
			$url .= (0 !== $i ? '&' : '?') . urlencode($key) . '=' . urlencode($value);
			$i++;
		}

		return $url;
	}

}

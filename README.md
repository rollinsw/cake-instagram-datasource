# Instagram API

Provides a DataSource for connecting to the Instagram API. The data source allows you to get and modify entries as well as provides means to

## Installation

To use the Instagram API you need to have a client setup. See [the client manager](http://instagram.com/developer/clients/manage/) to setup a new client or use an existing one. The API uses the client ID, client secret and redirect URL to authenticate with the Instagram API. Note that most of the general read actions do not require that an Instagram user is logged in, so there may never be a need to authenticate the client.

### app/Config/bootstrap.php

```php
<?php
CakePlugin::load('InstagramApi');
?>
```

### app/Config/database.php

```php
<?php
class DATABASE_CONFIG {

	public $sample = array(
		'datasource'    => 'InstagramApi.InstagramSource',
		'client_id'     => '' // from your Instagram client
		'client_secret' => '' // from your Instagram client
		'redirect_url'  => '' // from your Instagram client
	);

}
?>
```

## Usage

The data source works as a wrapper around the API end points specified in the [Instagram API documentation](http://instagram.com/developer/endpoints/users/) and is used by directly calling the CRUD methods in the data source with the specified endpoint and an array of parameters. However, the client ID (or access token if the connection has been autherized) is automatically added to the parameters sent to the API.

The response data is parsed from JSON, so a generic object is returned from the methods (or false in the case of an error).

### Examples

```php
<?php

// Search for a tag called "test"
$instagram = ConnectionManager::getDataSource('instagram');
$tags = $instagram->read('tags/search', array(
	'q' => 'test'
));

// Delete a comment
$instagram = ConnectionManager::getDataSource('instagram');
$result = $instagram->delete('media/1234/comment', 4321);

// Add a like (requires authentication)
$instagram = ConnectionManager::getDataSource('instagram');
$result = $instagram->create('media/1234/like');

?>
```

### Authorization

Authorization with the Instagram API requires two steps, much like the Facebook API. You need to have set up the client_id, client_secret and redirect_url in the database configuration (see above) and they must match the ones in your Instagram client manager.

First, you call the _InstagramSource::authenticate()_ method with no parameters to get the URL to send the user to.
```php
<?php
$instagram = ConnectionManager::getDataSource('instagram');
$url = $instagram->authenticate();
$this->redirect($url);
?>
```

Once the user has authenticated your client, he will be sent back to the URL specified in _redirect_url_, but with the query parameter _code_ attached. To authenticate the Instagram API, you need to send this code to the data source. This will contact Instagram and retrieve an access token.

```php
<?php
$code = $this->request->query['code'];
$token = $instagram->authenticate($code);
?>
```

The data source doesn't store the token, so you'll need to save it (in the database or session or wherever) and when you want to authorize your client with Instagram you call the method with the token and _true_ as the other parameter, which will authorize the API.

```php
<?php
$instagram->authorize($token, true);
?>
```

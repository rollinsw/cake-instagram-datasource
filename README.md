# Instagram Datasource

Provides a Datasource for connecting to the Instagram API. As of now the datasource only supports getting media entries (photos), but hopefully we can expand on that soon.

## Installation

To use the Instagram API you need to have a client setup. See [the client manager](http://instagram.com/developer/clients/manage/) to setup a new client or use an existing one. The API uses the client ID, client secret and redirect URL to authenticate with the Instagram API. Note that most of the general read actions do not require that an Instagram user is logged in, so there may never be a need to authenticate the client.

### 1. Get the source

Clone the project to an appropriately named plugin folder:
```
git clone git://github.com/nodesagency/cake-instagram-datasource.git app/Plugin/InstagramDatasource
```

Or add it as a submodule to your pre-existing git repository.
```
git submodule add git://github.com/nodesagency/cake-instagram-datasource.git app/Plugin/InstagramDatasource
```

### 2. Add the plugin to your project

**app/Config/bootstrap.php**
```php
CakePlugin::load('InstagramDatasource');
```

### 3. Configure the Datasource

The datasource can be configured in the database file.

**app/Config/database.php**
```php
class DATABASE_CONFIG {

	public $instagram = array(
		'datasource'    => 'InstagramDatasource.InstagramSource',
		'client_id'     => '', // from your Instagram client
		'client_secret' => '', // from your Instagram client
		'redirect_url'  => '' // from your Instagram client
	);

}
```

Alternatively, it can be added to the ```ConnectionManager``` manually.

```php
ConnectionManager::add('instagram', array(
	'datasource'    => 'InstagramDatasource.InstagramSource',
	'client_id'     => '', // from your Instagram client
	'client_secret' => '', // from your Instagram client
	'redirect_url'  => '' // from your Instagram client
));
```

## Usage

The data source attempts to wrap the [Instagram API endpoints ](http://instagram.com/developer/endpoints/) to CakePHP-style models so you can use the ```find()```, ```save()``` and ```delete()``` methods as you normally would. However, the endpoints don't handle the usual parameters (limit, order, etc) so each model acts independendtly.

### Media

The Media model is a wrapper for the media endpoints, providing access to the media entries (photographs) in the Instagram API. Calling ```find()``` on this model uses the [/media/search](http://instagram.com/developer/endpoints/media/) action, unless an id or tag is specified in the conditions. If no conditions are specified it will retrieve the popular media entries.

**Note that the Instragram API doesn't support creating, editing or deleting media entries**

#### Examples

```php
// Get the latest popular entries
$entries = $this->Media->find('all');

// Get a specific media entry by ID
$entries = $this->Media->find('first', array(
	'conditions' => array('id' => $id)
));

// Search for media items by a specific tag and retrieve 100 entries
$entries = $this->Media->find('all', array(
	'conditions' => array('tag' => $tag),
	'limit' => 100
));

// Search for media items within a geographical area
$entries = $this->Media->find('all', array(
	'conditions' => array(
		'lat' => $lat,
		'lng' => $lng
	)
));

// Search for media items by Instagram location id
$entries = $this->Media->find('all', array(
	'conditions' => array('location_id' => $location_id)
));
```

### Authorization

Authorization with the Instagram API requires two steps, much like the Facebook API. You need to have set up the client_id, client_secret and redirect_url in the database configuration (see above) and they must match the ones in your Instagram client manager.

First, you call the _InstagramSource::authenticate()_ method with no parameters to get the URL to send the user to.
```php
$instagram = ConnectionManager::getDataSource('instagram');
$url = $instagram->authenticate();
$this->redirect($url);
```

Once the user has authenticated your client, he will be sent back to the URL specified in _redirect_url_, but with the query parameter _code_ attached. To authenticate the Instagram API, you need to send this code to the data source. This will contact Instagram and retrieve a response object including an access token and a user object.

```php
$code = $this->request->query['code'];
$response = $instagram->authenticate($code);
$token = $response->access_token;
$user = $response->user;
```

The data source doesn't store the token, so you'll need to save it (in the database or session or wherever) and when you want to authorize your client with Instagram you call the method with the token and _true_ as the other parameter, which will authorize the API.

```php
$instagram->authorize($token, true);
```

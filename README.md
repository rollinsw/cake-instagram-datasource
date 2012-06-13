# Instagram API

Provides a connection and function calls to the Instagram API.

## Installation

_@todo_

## Usage

To use the Instagram API you need to have a client setup. See [the client manager](http://instagram.com/developer/clients/manage/) to setup a new client or use an existing one. The API uses the client ID and client secret to ensure that all connections are to the correct client.

```php
$instagram = new \Instagram\Connection($clientId, $clientSecret);
```

### Authorization

Some of the API end points requires it to be authorized with a specific user, which is done by redirecting the user to an authorization URL where the user will either allow or refuse access to the client. The authorization URL is generated based on a redirect URL, which is specified in the Instagram client manager, but which allows you to add query parameters to the URL. For example, if the redirect URL you've specified in the client manager is **http://example.com/app/** you can redirect the authorized user to **http://example.com/app/?foo=bar&herp=derp**, allowing for customized redirection.

If the user allows the client access, it will redirect with the parameter **code** appended to the URL. In the example above, it would redirect to **http://example.com/app/?foo=bar&herp=derp&code=XXXX**.

```php
if (!empty($this->request->query['code'])) {
	$instagram->authorize($redirectURL, $this->request->query['code']);
} elseif (!$instagram->authorized()) {
	$url = $instagram->authorizeURL($redirectURL);
	$this->redirect($url);
}
```

### Config

Using the static class methods to fetch content from Instagram requires that the client information is defined in the config variables.

```php
Configure::write('Instagram.clientId', 'cdd97394669e453dabf1671cfadd7152');
Configure::write('Instagram.clientSecret', '62f381929d584c15b66bd18395f5a786');
```

_@todo: It may be useful to be able to set the instance of the Instagram\Connection class so that you can choose to set the client id and secret in the config, or to create an instance of your own. It'd also make it easier to test the API end points if you could subclass the connection class to avoid calling the Instagram API._

## Class Reference

### Connection

Used to provide a connection to the Instagram API. It can be used directly to request get/post/delete methods, but should be avoided in preference of the other classes. To provide a connection you need a client ID and secret, which are retrieved from the [client manager](http://instagram.com/developer/clients/manage/).

#### Methods

##### __construct ( _string_, _string_ )

 * _**$clientId** (string) ID of the client_
 * _**$clientSecret** (string) Secret of the client_

Provides a connection to the Instragram API. If the **$clientId** and **$clientSecret** aren't provided, it uses ones stored in the Config using the keys _"Instagram.clientId"_ and _"Instagram.clientSecret"_ respectively.

##### authorize ( string, string )

##### authorized

Checks whether the client has been authorized with an Instagram user.

##### authorizeURL ( string )

 * **$redirectURL** (string) The URL to redirect to after authorization

Creates a URL to send the user to so that he/she can authorize your client. Once authorized, the user will be sent back to the URL specified in **$redirectURL** with the query parameter **code**.

##### delete ( string, _array_, _boolean_ )

##### get ( string, _array_, _boolean_ )

##### post ( string, _array_, _boolean_ )

#### Example

```php
$instagram = new \Instagram\Connection($clientId, $clientSecret);
$result = $instagram->get('media/popular');
```

### Comments

Getting and posting comments by the currently logged in user.

#### Methods

##### delete ( int, int )

##### get ( int )

##### post ( int, string )

### Geographies

#### Methods

##### recent ( int, array )

### Likes

#### Methods

##### delete ( int )

##### get ( int )

##### post ( int )

### Locations

#### Methods

##### get ( int )

##### recent ( int, array )

##### search ( array )

### Media

#### Methods

##### get ( int )

##### popular

##### search ( array )

### Relationships

#### Methods

##### add ( int, string )

##### followedBy ( int )

##### follows ( int )

##### get ( int )

##### requestedBy

### Tags

#### Methods

##### get ( string )

##### recent ( string, array )

##### search ( string )

### Users

#### Methods

##### feed ( array )

##### get ( int )

##### liked ( array )

##### recent ( int, array )

##### search ( string, array )

## Notes

 * @todo: This API is by no means complete; there are a lot of holes in the implementation and only a few of the API calls have actually been tested beyond the superficial test cases.

[![Build Status](https://travis-ci.org/pelevesque/freesound-api.svg?branch=master)](https://travis-ci.org/pelevesque/freesound-api)


# freesound-api

## About

A PSR-4 PHP class to consume freesound's API.

[http://www.freesound.org/](http://www.freesound.org/)

## Usage

Here's an example of how to use the freesound-api class.

```php
include('FreesoundAPI.php');

$api_key = 'g1d11a5117a4143be0f5f';
$curl_options = array();

$api = new Pel\Helper\FreesoundAPI($api_key, $curl_options);

$result = $api->sound(123);

if ($result === FALSE) {
    echo 'Error code = ' . $api->error['code'] . '<br/>';
    echo 'Error message = ' . $api->error['message'] . '<br/>';
} else {
    var_dump($result);
}
```

Take a look at the class's source code to see all the possible API calls.

# freesound API

## About

freesound API is a PHP class used to consume freesounds's API.

[http://www.freesound.org/](http://www.freesound.org/)

## Usage

Here's an example of how to use the freesound API class.

    include('freesound_API.php');

    $api_key = 'your_api_key_here';

    $api = new freesound_API($api_key);

    $result = $api->sound(123);

    if ($result === FALSE)
    {
        echo 'Error code = ' . $api->error['code'] . '<br/>';
        echo 'Error message = ' . $api->error['message'] . '<br/>';
    }
    else
    {
        var_dump($result);
    }

Take a look at the class to see all the possible API calls.

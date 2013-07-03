<?php
/**
 * A PHP class to consume freesound's API.
 *
 * http://www.freesound.org/
 * http://www.freesound.org/docs/api/
 *
 * @version     1.0
 * @author      Pierre-Emmanuel Lévesque
 * @email       pierre.e.levesque@gmail.com
 * @copyright   Copyright 2013, Pierre-Emmanuel Lévesque
 * @license     MIT License - @see README.md
 */
class Freesound_API {

	/**
	 * @var  array   error from the last API call
	 *
	 * array(
	 *   'code' => error code
	 *   'message' => error message
	 * )
	 */
	public $error = array();

	/**
	 * @var  string  API key
	 */
	public $api_key;

	/**
	 * @var  array   Curl options
	 */
	public $curl_options;

	/**
	 * @var  string  API url
	 */
	protected $_api_url = 'http://www.freesound.org/api';

	/**
	 * Constructor
	 *
	 * @param   string  API key
	 * @param   array   Curl options
	 * @return  void
	 */
	public function __construct($api_key, $curl_options = array())
	{
		$this->api_key = $api_key;
		$this->curl_options = $curl_options;
	}

	/**
	 * Sound search resource
	 *
	 * @param   array   params
	 * @return  json    response, or FALSE on failure
	 */
	public function sound_search($params = array())
	{
		return $this->_api_call($params, '/sounds/search');
	}

	/**
	 * Sound content search resource
	 *
	 * @param   array   params
	 * @return  json    response, or FALSE on failure
	 */
	public function sound_content_search($params = array())
	{
		return $this->_api_call($params, '/sounds/content_search');
	}

	/**
	 * Sound resource
	 *
	 * @param   int     sound id
	 * @return  json    response, or FALSE on failure
	 */
	public function sound($sound_id)
	{
		return $this->_api_call(array(), '/sounds/' . $sound_id);
	}

	/**
	 * Sound geotag resource
	 *
	 * @param   array   params
	 * @return  json    response, or FALSE on failure
	 */
	public function sound_geotag($params = array())
	{
		return $this->_api_call($params, '/sounds/geotag/');
	}

	/**
	 * Sound analysis resource
	 *
	 * @param   int     sound id
	 * @param   array   filters
	 * @param   array   params
	 * @return  json    response, or FALSE on failure
	 */
	public function sound_analysis($sound_id, $filters = array(), $params = array())
	{
		$uri = '/sounds/' . $sound_id . '/analysis';

		foreach ($filters as $filter)
		{
			$uri .= '/' . $filter;
		}

		return $this->_api_call($params, $uri);
	}

	/**
	 * Sound similar resource
	 *
	 * @param   int     sound id
	 * @param   array   params
	 * @return  json    response, or FALSE on failure
	 */
	public function sound_similar($sound_id, $params = array())
	{
		return $this->_api_call($params, '/sounds/' . $sound_id . '/similar');
	}

	/**
	 * User resource
	 *
	 * @param   string  username
	 * @return  json    response, or FALSE on failure
	 */
	public function user($username)
	{
		return $this->_api_call(array(), '/people/' . $username);
	}

	/**
	 * User sounds collection resource
	 *
	 * @param   string  username
	 * @param   array   params
	 * @return  json    response, or FALSE on failure
	 */
	public function user_sounds_collection($username, $params = array())
	{
		return $this->_api_call($params, '/people/' . $username . '/sounds');
	}

	/**
	 * User packs collection resource
	 *
	 * @param   string  username
	 * @return  json    response, or FALSE on failure
	 */
	public function user_packs_collection($username)
	{
		return $this->_api_call(array(), '/people/' . $username . '/packs');
	}

	/**
	 * User bookmark categories resource
	 *
	 * @param   string  username
	 * @return  json    response, or FALSE on failure
	 */
	public function user_bookmark_categories($username)
	{
		return $this->_api_call(array(), '/people/' . $username . '/bookmark_categories');
	}

	/**
	 * User bookmark category sound collection resource
	 *
	 * @param   string  username
	 * @param   int     category id
	 * @param   array   params
	 * @return  json    response, or FALSE on failure
	 */
	public function user_bookmark_category_sound_collection($username, $category_id, $params = array())
	{
		return $this->_api_call($params, '/people/' . $username . '/bookmark_categories/' . $category_id . '/sounds');
	}

	/**
	 * Pack resource
	 *
	 * @param   int     pack id
	 * @return  json    response, or FALSE on failure
	 */
	public function pack($pack_id)
	{
		return $this->_api_call(array(), '/packs/' . $pack_id);
	}

	/**
	 * Pack sounds collection resource
	 *
	 * @param   int     pack id
	 * @param   array   params
	 * @return  json    response, or FALSE on failure
	 */
	public function pack_sounds_collection($pack_id, $params)
	{
		return $this->_api_call($params, '/packs/' . $pack_id . '/sounds');
	}

	/**
	 * Runs an API call and sets $this->errors
	 *
	 * @param   array   params
	 * @param   string  uri
	 * @return  object  response, or FALSE on failure
	 */
	protected function _api_call($params, $uri = NULL)
	{
		$params['api_key'] = $this->api_key;

		$params = $this->_parse_params($params);

		if ($params != NULL)
		{
			$params = '?' . $params;
		}

		$url = $this->_api_url . $uri . $params;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		if ( ! ini_get('safe_mode'))
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		}

		if ( ! empty($this->curl_options))
		{
			curl_setopt_array($ch, $this->curl_options);
		}

		$response = curl_exec($ch);

		if ($response === FALSE)
		{
			$this->error = array(
				'code' => curl_errno($ch),
				'message' => curl_error($ch)
			);
		}
		else
		{
			$response = trim($response);

			$HTTP_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if ($HTTP_code != 200)
			{
				$response = json_decode($response);

				$this->error = array(
					'code' => $response->status_code,
					'message' => $response->explanation
				);

				$response = FALSE;
			}
			else
			{
				$this->error = array();
			}
		}

		curl_close($ch);

		return $response;
	}

	/**
	 * Parses the API call params
	 *
	 * NOTE: Do not modify the recursive call parameter.
	 * It is reserved for this function's internal use.
	 *
	 * @param   array   params
	 * @param   bool    recursive call (do not modify)
	 * @return  string  parsed params
	 */
	protected function _parse_params($params, $recursive_call = FALSE)
	{
		$parsed_params = '';

		foreach ($params as $param => $value)
		{
			if (is_array($value))
			{
				foreach ($value as $par => $val)
				{
					$parsed_params .= $this->_parse_params(array($par => $val), TRUE);
				}
			}
			elseif ($value !== NULL)
			{
				$parsed_params .= $param . '=' . urlencode($value) . '&';
			}
		}

		if ($recursive_call === FALSE)
		{
			$parsed_params = substr($parsed_params, 0, -1);
		}

		return $parsed_params;
	}

} // End Freesound_API

<?php

/*
 * Copyright (C) 2016 thmarx
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace TMA;

/**
 * Description of TMA_Request
 *
 * @author thmarx
 */
class TMA_Request {

	private $options;

	public function __construct() {
		$this->options = get_option('tma_webtools_option');
	}

	public static function getUserID() {
		return TMA_COOKIE_HELPER::getCookie(TMA_COOKIE_HELPER::$COOKIE_USER, UUID::v4(), TMA_COOKIE_HELPER::$COOKIE_USER_EXPIRE);
	}

	public function track($event, $page, $customAttributes = null) {
		if (!isset($this->options["webtools_apikey"]) || !isset($this->options['webtools_url'])) {
			return;
		}

		$uid = TMA_COOKIE_HELPER::getCookie(TMA_COOKIE_HELPER::$COOKIE_USER, UUID::v4(), TMA_COOKIE_HELPER::$COOKIE_USER_EXPIRE);
		$rid = TMA_COOKIE_HELPER::getCookie(TMA_COOKIE_HELPER::$COOKIE_REQUEST, UUID::v4(), TMA_COOKIE_HELPER::$COOKIE_REQUEST_EXPIRE);
		$vid = TMA_COOKIE_HELPER::getCookie(TMA_COOKIE_HELPER::$COOKIE_VISIT, UUID::v4(), TMA_COOKIE_HELPER::$COOKIE_VISIT_EXPIRE);
		$fp = $_COOKIE['_tma_fp'];
		$apikey = $this->options["webtools_apikey"];
		$url = $this->options['webtools_url'];
		$siteid = get_option('blogname');
		if (isset(get_option('tma_webtools_option')['webtools_siteid'])) {
			$siteid = get_option('tma_webtools_option')['webtools_siteid'];
		}

		// http://localhost:8082/rest/track?
		//	event=pageview&site=demosite&page=testpage&fp=6e289159b1106008e0379c9565a44f03&uid=3694ff4e-668b-4484-8ddf-52662bbcc44c&
		//	reqid=a42be86b-2930-4f72-b499-f3dcab983633&vid=8251e717-afea-43bc-b270-1033f482359f&_t=1454323264835&apikey=apikey

		$url .= 'rest/track?event=' . $event;
		$url .= '&site=' . $siteid . '&page=' . urlencode($page);
		$url .= "&fp=" . $fp . "&uid=" . $uid . '&reqid=' . $rid . '&vid=' . $vid;
		$url .= "&apikey=" . $apikey;

		// add the custom parameters
		if (isset($customAttributes)) {
			foreach ($customAttributes as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $vk) {
						$url .= '&c_' . urldecode($key) . '=' . urlencode($vk);
					}
				} else {
					$url .= '&c_' . urldecode($key) . '=' . urlencode($value);
				}
			}
		}

		$this->loadContent($url, "{}");
	}

	/**
	 * REST call for the user segments.
	 * 
	 * @param type $userid
	 * @return type
	 */
	public function getSegments($userid) {
		if (tma_is_preview()) {
			if (isset($_GET['segment'])) {
				$segments = filter_input(INPUT_GET, 'segment', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
				$result = '{"user" :{"segments" : ["' . implode("\",\"", $segments) . '"]}}';
				return json_decode($result);
			} else {
				$result = '{"user" : {"segments" : []}}';
				return json_decode($result);
			}
		}


		if (!isset($this->options["webtools_apikey"]) || !isset($this->options['webtools_url'])) {
			$result = '{"user" : {"segments" : []}}';
			return json_decode($result);
		}
		$result = wp_cache_get($userid);
		$apikey = $this->options["webtools_apikey"];
		if (false === $result) {
			$url = $this->options['webtools_url'] . 'rest/userinformation/user?apikey=' . $apikey . '&user=' . $userid;
			$result = $this->loadContent($url, '{"user" : {"segments" : []}}');

			wp_cache_set($userid, $result, "", 60);
		}

		return $result;
	}

	/**
	 * REST call to get defined segments
	 * 
	 * @return object the segments
	 */
	public function getAllSegments() {
		if (!isset($this->options["webtools_apikey"]) || !isset($this->options['webtools_url'])) {
			$result = '{"status" : "default", segments" : []}';
			return json_decode($result);
		}
		$result = wp_cache_get("tma-all-segments");
		$apikey = $this->options["webtools_apikey"];
		if (false === $result) {
			$url = $this->options['webtools_url'] . 'rest/segments/all?apikey=' . $apikey;
			$result = $this->loadContent($url, '{"status" : "default", segments" : []}');
			wp_cache_set("tma-all-segments", $result, "", 60);
		}

		return $result;
	}
	
	/**
	 * calls a rest extension
	 * 
	 * e.g. the recommendation module:
	 * <url>/rest/extension?extension=recommendation-module&recommendation=<recommendation_id>&id=<user id or item id> 
	 * 
	 * @param type $cachekey
	 * @param type $extension
	 * @param type $attributes
	 * @return type
	 */
	public function extension_get($cachekey, $extension, $attributes) {
		$result = NULL;
		if (!isset($this->options["webtools_apikey"]) || !isset($this->options['webtools_url'])) {
			$result = '{"error" : true}';
			return json_decode($result);
		}

		if (!is_null($cachekey)) {
			$result = wp_cache_get($cachekey);
		}
		$apikey = $this->options["webtools_apikey"];

		$url = $this->options['webtools_url'] . "rest/extension?extension={$extension}&apikey=" . $apikey;

		// add the custom parameters
		if (isset($attributes)) {
			foreach ($attributes as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $vk) {
						$url .= '&' . urldecode($key) . '=' . urlencode($vk);
					}
				} else {
					$url .= '&' . urldecode($key) . '=' . urlencode($value);
				}
			}
		}
		if ($result === false) {
			$result = $this->loadContent($url, '{"error" : true}');

			if ($cachekey !== NULL) {
				wp_cache_set($cachekey, $result, "", 60);
			}
		}

		return $result;
	}

	/**
	 * calls a rest extension
	 * 
	 * e.g. the recommendation module:
	 * <url>/rest/extension?extension=recommendation-module&recommendation=<recommendation_id>&id=<user id or item id> 
	 * 
	 * @param type $cachekey
	 * @param type $extension
	 * @param type $attributes
	 * @param type $body
	 * @return type
	 */
	public function extension_post($cachekey, $extension, $attributes, $body) {
		$result = NULL;
		if (!isset($this->options["webtools_apikey"]) || !isset($this->options['webtools_url'])) {
			$result = '{"error" : true}';
			return json_decode($result);
		}

		if (!is_null($cachekey)) {
			$result = wp_cache_get($cachekey);
		}
		$apikey = $this->options["webtools_apikey"];

		$url = $this->options['webtools_url'] . "rest/extension?extension={$extension}&apikey=" . $apikey;

		// add the custom parameters
		if (isset($attributes)) {
			foreach ($attributes as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $vk) {
						$url .= '&' . urldecode($key) . '=' . urlencode($vk);
					}
				} else {
					$url .= '&' . urldecode($key) . '=' . urlencode($value);
				}
			}
		}

		if (is_null($result)) {
			$result = $this->postContent($url, $body, '{"error" : true}');
			if ($cachekey !== NULL) {
				wp_cache_set($cachekey, $result, "", 60);
			}
		}

		return $result;
	}

	private function loadContent($url, $defaultContent) {
		$result = $defaultContent;

		$parameters = array();
		$parameters['method'] = "GET";
		$parameters['timeout'] = "45";
		$parameters['headers'] = array();
		$parameters['headers']['Content-Type'] = "text/plain";

		$response = wp_remote_get($url, $parameters);
		
		if ((is_object($response) || is_array($response)) && !is_wp_error($response)) {
			$result = $response['body']; // use the content
		}

		return json_decode($result);
	}

	private function postContent($url, $body, $defaultContent) {
		$result = $defaultContent;

		$parameters = array();
		$parameters['body'] = $body;
		$parameters['method'] = "POST";
		$parameters['timeout'] = "45";
		$parameters['headers'] = array();
		$parameters['headers']['Content-Type'] = "text/plain";


		$response = wp_remote_post($url, $parameters);
		if (is_array($response) && !is_wp_error($response)) {
			$result = $response['body']; // use the content
		}

		return json_decode($result);
	}

}

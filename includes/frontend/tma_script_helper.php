<?php

/*
 * Copyright (C) 2016 marx
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
 * Description of TMAScriptHelper
 *
 * @author marx
 */
class TMAScriptHelper {

	public function isTrackingEnabled() {
		
		$trackUser = false;
		if  (isset(get_option('tma_webtools_option')['webtools_track_logged_in_users']) && get_option('tma_webtools_option')['webtools_track_logged_in_users'] == true) {
			$trackUser = true;
		} else {
			$trackUser = !is_user_logged_in();
		}
		
		if (function_exists('vc_is_frontend_editor') && vc_is_frontend_editor()) {
			return false;
		} else if (function_exists('siteorigin_panels_is_preview') && siteorigin_panels_is_preview()) {
			return false;
		} else if (is_preview()){
			return false;
		}
		
		
		
		return isset(get_option('tma_webtools_option')['webtools_track']) && get_option('tma_webtools_option')['webtools_track'] == true 
		&& $trackUser
		&& isset(get_option('tma_webtools_option')['webtools_url']) && get_option('tma_webtools_option')['webtools_url'];
	}

	/**
	 * check if scoreing is enabled and a single post or page is shown.
	 * 
	 * @return type
	 */
	public function shouldScore() {
		return isset(get_option('tma_webtools_option')['webtools_score']) 
				&& get_option('tma_webtools_option')['webtools_score']
				&& (is_single() || is_page());
	}

	public function getLibrary() {
		return '<script src="' . $this->getWebTools_Url() . '/js/tma.js"></script>';
	}

	private function getWebTools_Url() {
		$url = get_option('tma_webtools_option')['webtools_url'];
		return rtrim($url, "/");
	}

	public function getCode() {
		$output = '';
		if ($this->isTrackingEnabled() && get_post() != NULL) {

			$siteid = get_option('blogname');
			if (isset(get_option('tma_webtools_option')['webtools_siteid'])) {
				$siteid = get_option('tma_webtools_option')['webtools_siteid'];
			}
			$cookieDomain = null;
			if (isset(get_option('tma_webtools_option')['webtools_cookiedomain'])) {
				$cookieDomain = get_option('tma_webtools_option')['webtools_cookiedomain'];
				
				$query = ".";
				if (substr($cookieDomain, 0, strlen($query)) !== $query) {
					$cookieDomain = "." . $cookieDomain;
				}
			}

			$output .= '<script>';
			
			$post_categories = wp_get_post_categories( get_post()->post_id );
			$cats = array();
     
			$output .= "function tma_custom_parameter () {";
			$output .= "return {";
			$output .= "categories : [";
			foreach($post_categories as $c){
				$output .= '"'.$cat->slug.'"';
			}
			$output .= "]";
			$output .= "};";
			$output .= "}";
			
			$output .= 'function tma_webtools_init () {';
			$output .= 'TMA_WEBTOOLS = new TMA.WebTools("' . $this->getWebTools_Url() . '", "' . $siteid . '", "' . (get_post()->post_type . '%23' . get_post()->post_name) . '");';
			$output .= 'TMA_WEBTOOLS.setCookieDomain("' . $cookieDomain . '");';
			$output .= 'TMA.onload(TMA.delegate(TMA_WEBTOOLS.register, TMA_WEBTOOLS));';

			
			if ($this->shouldScore()) {
				$score = $this->getScoring();

				$output .= $score;
			}


			$output .= '}';
			$output .= '</script>';
			$output .= $this->getLibrary();
		}

		return $output;
	}

	function getScoring() {
		$score = '{';
		$hasScore = false;
		//$custom_field_keys = get_post_custom_keys();
		
		$metaData = get_post_meta(get_the_ID(), Constants::$META_KEY_SEGMENT_SCORE);
		$segments = array();
		if (isset($metaData[0])) {
			$segments = $metaData[0];
		}
		if ($segments != null) {
			foreach ($segments as $key => $value) {
				if ($hasScore) {
					$score .= ', ';
				}
				$scoreValue = $value;//array_values(get_post_custom_values($value))[0];
				$value = str_replace('tma_score_', '', $value);
				$score .= $key . ' : ' . $scoreValue;
				$hasScore = true;
			}
		}

		$score .= '}';
		if ($hasScore) {
			return 'TMA_WEBTOOLS.score(' . $score . ');';
		} else {
			return '';
		}
	}

	public static function startsWith($haystack, $needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}

	function endsWith($haystack, $needle) {
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
	}

}

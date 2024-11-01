<?php

namespace TMA;

/**
 * Description of class
 *
 * @author marx
 */
abstract class Integration {

	private $onlySingleItemPerGroup = true;

	public function __construct() {
		$options = get_option('tma_webtools_option');
		if ($options !== false && is_array($options) && array_key_exists("webtools_shortcode_single_item_per_group", $options)) {
			$this->onlySingleItemPerGroup = $options['webtools_shortcode_single_item_per_group'];
		}
	}

	protected function is_visible($settings) {
		$visible = TRUE;
//		var_dump($settings);
		// frontend
		if (!$this->isActivated($settings)) {
			return true;
		}
//		var_dump($settings);
		$group = $this->getGroup($settings);
		if (!$this->matching($settings)) {
			// segmentation activated AND not matching AND not group default OR group content already added
			if ($this->isGroupDefault($settings) && $this->contentAdded($group)) {
				$visible = FALSE;
			} else if (!$this->isGroupDefault($settings)) {
				$visible = FALSE;
			}
		} else if ($this->singleItemPerGroup() && $this->contentAdded($group)) {
			// segmentation activated & only single item per group & group content already added
			$visible = FALSE;
		}
		if ($visible) {
			$this->addGroupContent($group);
			//$_REQUEST["tmagroup_" . $group] = true;
		}

		return $visible;
	}

	protected function singleItemPerGroup() {
		return $this->onlySingleItemPerGroup === true;
	}

	protected function addGroupContent($group) {
		$_REQUEST["tmagroup_" . $group] = true;
	}

	protected function contentAdded($group) {
		if (array_key_exists("tmagroup_".$group, $_REQUEST)) {
			return $_REQUEST["tmagroup_" . $group];
		}
		return false;
	}
	
	protected function isActivated($args) {
		return (is_array($args) 
		&& !empty($args['tma_personalization']) 
		&& $args['tma_personalization'] !== "np" 
		&& $args['tma_personalization'] !== "disabled"); 
	}

	protected function getGroup($args) {
		$group = 'default';

		if (is_array($args) && !empty($args['tma_group'])) {
			$group = $args['tma_group'];
		}

		return $group;
	}

	protected function isGroupDefault($args) {
		return (is_array($args) 
		&& !empty($args['tma_default']) 
		&& $args['tma_default'] !== "np" 
		&& $args['tma_default'] !== "disabled"); 
	}
	
	/**
	 * returns an array of the configured segments for the integration
	 */
	protected function getSegments ($args) {
		$attr_segments = [];
		if (array_key_exists("segments", $args)) {
			$attr_segments = explode(",", $args['segments']);
		} else if (array_key_exists("tma_segments", $args)) {
			$attr_segments = $args['tma_segments'];
		} else {
			foreach ($args as $key => $value) {
				if (!empty($args[$key]) && TMAScriptHelper::startsWith($key, "tma_segment_")) {
					$attr_segments[] = substr($key, 12);
				}
			}
		}
		
		return $attr_segments;
	}

	protected function matching($args) {

		$matching_mode = $args['tma_matching'];
		
		$attr_segments = $this->getSegments($args);

		$uid = TMA_COOKIE_HELPER::getCookie(TMA_COOKIE_HELPER::$COOKIE_USER, UUID::v4(), TMA_COOKIE_HELPER::$COOKIE_USER_EXPIRE);
		$request = new TMA_Request();
		$response = $request->getSegments($uid);
		
		$segments = ["default"];
		if ($response !== NULL) {
			if (sizeof($response->user->segments) > 0) {
				$segments = $response->user->segments;
			}
		}

		$matching = false;
		$segments = array_map('trim', $segments);
		$attr_segments = array_map('trim', $attr_segments);
		if ($matching_mode === ShortCode_TMA_CONTENT::$match_mode_all) {
			$matching = ShortCode_TMA_CONTENT::matching_mode_all($segments, $attr_segments);
		} else if ($matching_mode === ShortCode_TMA_CONTENT::$match_mode_single) {
			$matching = ShortCode_TMA_CONTENT::matching_mode_single($segments, $attr_segments);
		}

		return $matching;
	}

}

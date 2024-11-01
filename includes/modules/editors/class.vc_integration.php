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

namespace TMA {

	class VisualComposer_Integration extends Integration {

		/**
		 * Start up
		 */
		public function __construct() {
			parent::__construct();

//			add_action('vc_before_init', array($this, 'tma_content_shortcode'));
			// dialog for row settings
			add_action('vc_after_mapping', array($this, 'configure'));
			// dialog for all shortcodes
			add_filter('vc_shortcodes_css_class', array($this, 'change_element_class_name'), 10, 3);
			add_filter('vc_shortcode_output', array($this, 'vc_shortcode_output'), 10, 3);
		}

		protected function getSegments($args) {
			$segments = parent::getSegments($args);
			if (!is_array($segments)) {
				$segments_array = [];
				$segments_array[] = $segments;

				return $segments_array;
			}
			return $segments;
		}

		function value_if_present($array, $key, $default) {
			return array_key_exists($key, $array) ? $array[$key] : $default;
		}

		function vc_shortcode_output($output, $shortcode, $attributes) {


//			if (vc_is_frontend_editor()) {
			if ((vc_is_page_editable() || tma_is_preview()) && array_key_exists("tma_personalization", $attributes)) {
				$attrs = [];
				$attrs["data-tma-personalization"] = $attributes['tma_personalization'];
				$attrs["data-tma-matching"] = $this->value_if_present($attributes, "tma_matching", "single");
				$attrs["data-tma-group"] = $this->value_if_present($attributes, "tma_group", "default");
				$attrs["data-tma-default"] = $this->value_if_present($attributes, 'tma_default', "false");
				$segments = $this->value_if_present($attributes, 'tma_segments', []);
				if (is_array($segments)) {
					$attrs["data-tma-segments"] = implode(",", $segments);
				} else {
					$attrs["data-tma-segments"] = $segments;
				}

				$html_attrs = implode(
						' ', array_map(
								function ($k, $v) {
							return $k . '="' . htmlspecialchars($v) . '"';
						}, array_keys($attrs), $attrs
						)
				);

				return '<div class="reset-this" ' . $html_attrs . ' >' . $output . '</div>';
			}

			return $output;
		}

		protected function isGroupDefault($args) {
			return (is_array($args) && !empty($args['tma_default']) && $args['tma_default'] == "true");
		}

		function change_element_class_name($class_string, $tag, $args) {
			if (/* $tag === "vc_row" && */!(vc_is_page_editable())) {
					
				if (!$this->is_visible($args)) {
					$class_string .= " tma-hide";
				}
			}


			return $class_string;
		}

		private function is_custom_shortcodes_selected($options) {
			if ($options !== false && is_array($options) && isset($options['webtools_wpbakery_custom'])) {
				return $options['webtools_wpbakery_custom'] === "on";
			}
			return false;
		}

		private function getShortCodes() {
			$options = get_option("tma-webtools-options-wpbakery");
			if ($this->is_custom_shortcodes_selected($options)) {
				return $options['webtools_wpbakery_shortcodes'];
			} else {
				return \WPBMap::getAllShortCodes();
			}
		}

		public function configure() {
			foreach ($this->getShortCodes() AS $key => $shortcode) {
				$this->configure_shortcode($key);
			}

//			$shortcodes = array("vc_row");
//
//			foreach ($shortcodes as $code => $function) {
//				$this->configure_shortcode($function);
//			}
		}

		public function configure_shortcode($shortcode) {
			$group = 'Target Audience';

			/* Intro */
			vc_add_param($shortcode, array(
				'type' => 'checkbox',
				'heading' => 'Activate',
				'param_name' => 'tma_personalization',
				'value' => array(
					__("Targeting", "tma-webtools") => 'true'
				),
				'description' => __("If activated, only users matching the segments will see the content.", "tma-webtools"),
				'group' => $group
			));
			vc_add_param($shortcode, array(
				'type' => 'textfield',
				'heading' => 'Group',
				'param_name' => 'tma_group',
				'value' => array(
					__("Activate targeting", "tma-webtools") => 'default'
				),
				'description' => __("The name of the group.", "tma-webtools"),
				'group' => $group,
				'dependency' => array(
					'element' => 'tma_personalization',
					'value' => array('true'),
				)
			));
			vc_add_param($shortcode, array(
				'type' => 'checkbox',
				'heading' => 'Group default',
				'param_name' => 'tma_default',
				'value' => array(
					__("Is group default", "tma-webtools") => 'true'
				),
				'description' => __("If activated, it is show if no other element of the groups matchs.", "tma-webtools"),
				'group' => $group,
				'dependency' => array(
					'element' => 'tma_personalization',
					'value' => array('true'),
				)
			));
			vc_add_param($shortcode, array(
				'type' => 'dropdown',
				'heading' => 'Matching mode',
				'param_name' => 'tma_matching',
				'value' => array(
					__('Single', 'tma-webtools') => 'single',
					__('All', 'tma-webtools') => 'all',
				),
				'description' => __('User must match all or just a single segment.', 'tma-webtools'),
				'group' => $group,
				'dependency' => array(
					'element' => 'tma_personalization',
					'value' => array('true'),
				)
			));
			$request = new TMA_Request();
			$response = $request->getAllSegments();
			$check_segments = [];
			if ($response !== NULL && $response->status === "ok") {
				foreach ($response->segments as $segment) {
					$check_segments[$segment->name] = $segment->id;
				}
			}
			vc_add_param($shortcode, array(
				"type" => "checkbox",
				'group' => $group,
				"heading" => __("Segments", "tma-webtools"),
				"description" => __("For which segments the content should be visible.", "tma-webtools"),
				"value" => $check_segments,
				"param_name" => "tma_segments",
				'dependency' => array(
					'element' => 'tma_personalization',
					'value' => array('true'),
				)
			));
		}

		public function tma_content_shortcode() {

			$request = new TMA_Request();
			$response = $request->getAllSegments();
			$params = [];
			$params[] = array(
				'type' => 'dropdown',
				'heading' => __('Matching mode', "tma-webtools"),
				'param_name' => 'mode',
				'description' => __('User must match all or just a single segment.', 'tma-webtools'),
				'value' => array(
					__('Single', "tma-webtools") => "single",
					__('All', "tma-webtools") => "all",
				),
				'default' >= 'all',
				'std' >= 'all',
			);
			$params[] = array(
				'type' => 'textfield',
				'heading' => __('Group', "tma-webtools"),
				'param_name' => 'group',
				'value' => '',
				'description' => __('The group', "tma-webtools")
			);
			$params[] = array(
				'type' => 'checkbox',
				'heading' => __('Group default', "tma-webtools"),
				'param_name' => 'default',
				'value' => '',
				'description' => __('The group default', "tma-webtools")
			);

			$check_segments = [];
			if ($response !== NULL && $response->status === "ok") {
				foreach ($response->segments as $segment) {
					$check_segments[$segment->name] = $segment->id;
				}
			}
			$params[] = array(
				"type" => "checkbox",
				"weight" => 10,
				"heading" => __("Segments", "tma-webtools"),
				"description" => __("For which segments the content should be visible.", "tma-webtools"),
				"value" => $check_segments,
				"param_name" => "segments"
			);

			vc_map(array(
				'name' => __('TMA-Content', "tma-webtools"),
				'base' => 'tma_content',
				"description" => __("TMA Content Plugin", "tma-webtools"),
				'category' => __('TMA Widgets', "tma-webtools"),
				"icon" => plugins_url('tma-webtools') . "/images/tma_content_vc_icon_trans.png",
				"is_container" => true,
				"as_parent" => array('except' => ''),
				"content_element" => true,
				"show_settings_on_create" => false,
				'params' => $params,
				"js_view" => 'VcColumnView',
			));
		}

	}

	$tma_vc_integration = new VisualComposer_Integration();

	// TODO: Visual Composer Integration überarb
	require_once 'class.vc_integration.preview.php';
	require_once 'vc_settings.php';
}

namespace {
	if (class_exists('WPBakeryShortCodesContainer')) {

		class WPBakeryShortCode_tma_content extends WPBakeryShortCodesContainer {
			
		}

	}
}


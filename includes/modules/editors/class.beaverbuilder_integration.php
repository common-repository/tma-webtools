<?php

/*
 * Copyright (C) 2017 marx
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

class BeaverBuilder_Integration extends Integration {

	/**
	 * Start up
	 */
	public function __construct() {

		parent::__construct();

		add_filter('fl_builder_register_settings_form', array($this, 'addControls'), 10, 2);
		add_filter('fl_builder_row_custom_class', array($this, 'render_content'), 10, 2);
		add_filter('fl_builder_module_custom_class', array($this, 'render_content'), 10, 2);
		add_filter('fl_builder_column_custom_class', array($this, 'render_content'), 10, 2);

		add_filter("fl_builder_row_attributes", array($this, "custom_attributes"), 10, 2);
		add_filter("fl_builder_column_attributes", array($this, "custom_attributes"), 10, 2);
		add_filter("fl_builder_module_attributes", array($this, "custom_attributes"), 10, 2);
	}

	function custom_attributes($attrs, $module) {
		if (\FLBuilderModel::is_builder_active() && property_exists($module->settings, "tma_personalization")) {
			$attrs["data-tma-personalization"] = $module->settings->tma_personalization;
			$attrs["data-tma-matching"] = $module->settings->tma_matching;
			$attrs["data-tma-group"] = $module->settings->tma_group;
			$attrs["data-tma-default"] = $module->settings->tma_default;
			if (is_array($module->settings->tma_segments)) {
				$attrs["data-tma-segments"] = implode(",", $module->settings->tma_segments);
			} else {
				$attrs["data-tma-segments"] = $module->settings->tma_segments;
			}
		}

		return $attrs;
	}

	protected function isActivated($args) {
		return (is_array($args) 
		&& !empty($args['tma_personalization']) 
		&& $args['tma_personalization'] === "enabled"); 
	}
	protected function isGroupDefault($args) {
		return (is_array($args) 
		&& !empty($args['tma_default']) 
		&& $args['tma_default'] === "yes"); 
	}
	
	function render_content($class, $module) {
		if (!\FLBuilderModel::is_builder_active() && !$this->is_visible((array) $module->settings)) {
			$class .= " tma-hide";
		} else {
			$class .= " tma-show";
		}
		return $class;
	}

	private function get_settiings() {

		$segment_options = [];
		$request = new TMA_Request();
		$response = $request->getAllSegments();
		if ($response !== NULL && $response->status === "ok") {
			foreach ($response->segments as $segment) {
				$segment_options[$segment->id] = $segment->name;
			}
		}

		return array(
			'title' => __('Targeting', 'tma-webtools'),
			'sections' => array(
				'general' => array(
					"title" => __('General', 'tma-webtools'),
					'fields' => array(
						'tma_personalization' => array(
							'type' => 'select',
							'label' => __('Activate Targeting', 'tma-webtools'),
							'default' => 'disabled',
							'options' => array(
								'enabled' => __('Yes', 'tma-webtools'),
								'disabled' => __('No', 'tma-webtools')
							),
							'toggle' => array(
								'enabled' => array(
									'fields' => array('expand_bg'),
									'sections' => array('targeting'),
								),
								'disabled' => array()
							)
						)
					)
				),
				'targeting' => array(
					'title' => __('Configure', 'tma-webtools'),
					'fields' => array(
						'tma_matching' => array(
							'type' => 'select',
							'label' => __('Matching mode', 'tma-webtools'),
							'description' => __('User must match all or just a single segment.', 'tma-webtools'),
							'default' => 'all',
							'options' => array(
								'all' => __('All', 'tma-webtools'),
								'single' => __('Single', 'tma-webtools'),
							),
							'preview' => 'none'
						),
						'tma_group' => array(
							'type' => 'text',
							'label' => __('Group', 'tma-webtools'),
							'default' => 'default',
							'placeholder' => __('Group', 'tma-webtools'),
							'description' => __('The name of the group. Groups can be used to group elements together', 'tma-webtools'),
						),
						'tma_default' => array(
							'type' => 'select',
							'label' => __('Is Group default', 'tma-webtools'),
							'help' => __('Is group default element. The default is used if not other element of the groups matchs the user. The default element must be the last element on the page.', 'tma-webtools'),
							'default' => 'yes',
							'options' => array(
								'yes' => __('Yes', 'tma-webtools'),
								'no' => __('No', 'tma-webtools'),
							),
							'preview' => 'none'
						),
						'tma_segments' => array(
							'type' => 'select',
							'label' => __('Segments', 'tma-webtools'),
							'help' => __("For which segments the content should be visible.", "tma-webtools"),
							'default' => 'yes',
							'options' => $segment_options,
							'preview' => 'none',
							'multi-select' => true
						)
					)
				)
			)
		);
	}

	public function addControls($form, $id) {
		//echo $id . " - ";
		if ('row' == $id || $id == 'col') {
			if (!array_key_exists("tma-webtools", $form["tabs"])) {
				$column_form = array(
					'tabs' => array(
						'tma-webtools' => $this->get_settiings()
					)
				);
				$form = array_merge_recursive($form, $column_form);
			}
		} else {
			if (!array_key_exists("tma-webtools", $form)) {
				$form['tma-webtools'] = $this->get_settiings();
			}
		}

		return $form;
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

}

$tma_beaverbuilder_integration = new BeaverBuilder_Integration ();

require_once 'class.beaverbuilder.preview.php';

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

class Elementor_Integration extends Integration {

	/**
	 * Start up
	 */
	public function __construct() {

		parent::__construct();

		add_action('elementor/element/after_section_end', array($this, 'addControls'), 10, 3);
//		add_action('elementor/widget/render_content', array($this, 'render_content'), 10, 2);
		add_action('elementor/frontend/widget/before_render', array($this, 'widget_before_render'), 10, 2);
		add_action('elementor/frontend/widget/after_render', array($this, 'widget_after_render'), 10, 2);

//		add_action('elementor/widget/render_content', function ( $content, $widget ) {
//			return "<div class='render_the_damn_widget' type='" . get_class($widget) . "'>" . $content . "</div>";
//		}, 10, 2);
//		
//		add_action('elementor/frontend/widget/before_render', function ( \Elementor\Element_Base $element ) {
//			echo "<div class='hide_that_damn_widget' type='" . get_class($element) . "'>";
//		});
//		add_action('elementor/frontend/widget/after_render', function ( \Elementor\Element_Base $element ) {
//			echo "</div>";
//		});
//		add_action('elementor/frontend/element/before_render', function ( \Elementor\Element_Base $element ) {
//			echo "<div class='hide_that_damn_element' type='" . get_class($element) . "'>";
//		});
//		add_action('elementor/frontend/element/after_render', function ( \Elementor\Element_Base $element ) {
//			echo "</div>";
//		});
//		add_action('elementor/frontend/section/before_render', function ( \Elementor\Element_Base $element ) {
//			echo "<div class='hide_that_damn_section' type='" . get_class($element) . "'>";
//		});
//		add_action('elementor/frontend/section/after_render', function ( \Elementor\Element_Base $element ) {
//			echo "</div>";
//		});
	}
	
	function widget_before_render (\Elementor\Element_Base $element) {
		if (!$this->is_widget_visible($element)){
			echo "<div class='tma-hide'>";
		}
	}
	function widget_after_render (\Elementor\Element_Base $element) {
		if (!$this->is_widget_visible($element)){
			echo "</div>";
		}
	}

	function is_editor_active() {
		$method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

		$action = NULL;
		if ($method === "POST") {
			$action = filter_input(INPUT_POST, 'action', FILTER_DEFAULT);
		} else {
			$action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
		}
		if ($action !== NULL) {
			return $action === "elementor" || TMAScriptHelper::startsWith($action, "elementor_");
		}
		return FALSE;
	}

	function isActivated($args) {
		return (is_array($args) 
		&& !empty($args['tma_personalization']) 
		&& $args['tma_personalization'] === "yes");
	}
	protected function isGroupDefault($args) {
		return (is_array($args) 
		&& !empty($args['tma_default']) 
		&& $args['tma_default'] === "yes"); 
	}
	
	function is_widget_visible ($widget) {
		$visible = TRUE;
		$args = $widget->get_settings();
//		var_dump($args);
		if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
			$visible = TRUE;
		} else {
			$visible = $this->is_visible($args);
		}
		return $visible;
	}
	
	function render_content($content, $widget) {
		$args = $widget->get_settings();
		if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
			// editor
//			if ($this->isActivated($args)) {
//				$style = "position: absolute; color: purple; text-shadow: 2px 2px 1px #000000; border-radius: 5px; border: solid 1px; padding: 2px; bottom: -24px; right: 0px;";
//				$marker_div = "<div style=\"$style\" class=\"fa fa-users fa-2x\" />";
//				$content .= $marker_div;
//			}
		} else {
			// frontend

			if (!$this->is_visible($args)) {
				// segmentation activated & not matching
				$content = '';
			}
		}

		return $content;
	}

	function addControls($section, $section_id, $args) {
		if (\Elementor\Controls_Manager::TAB_ADVANCED !== $args['tab'] || ( '_section_responsive' !== $section_id /* Section/Widget */ && 'section_responsive' !== $section_id /* Column */ )) {
			return;
		}
		$section->start_controls_section(
				'tma-webtools', [
			'label' => __('Targeting', 'tma-webtools'),
			'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
				]
		);

		$section->add_control(
				'tma_personalization', [
			'label' => __('Activate', 'tma-webtools'),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'description' => __('If enabled, the content will only be visible to users matching the selected segments.', 'tma-webtools'),
			'default' => 'np',
			'return_value' => 'yes',
			'label_off' => __('Deactive', 'tma-webtools'),
			'label_on' => __('Active', 'tma-webtools'),
				]
		);

		$section->add_control(
				'tma_matching', [
			'label' => __('Matching mode', 'tma-webtools'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'description' => __('User must match all or just a single segment.', 'tma-webtools'),
			'default' => 'all',
			'options' => [
				'all' => __('All', 'tma-webtools'),
				'single' => __('Single', 'tma-webtools'),
			]
				]
		);

		$section->add_control(
				'tma_group', [
			'label' => __('Group', 'tma-webtools'),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => "",
			'description' => __('The name of the group. Groups can be used to group elements together', 'tma-webtools'),
				]
		);

		$section->add_control(
				'tma_default', [
			'label' => __('Is Group default', 'tma-webtools'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'description' => __('Is group default element. The default is used if not other element of the groups matchs the user. The default element must be the last element on the page.', 'tma-webtools'),
			'default' => 'yes',
			'options' => [
				'yes' => __('Yes', 'tma-webtools'),
				'no' => __('No', 'tma-webtools'),
			]
				]
		);

		$segment_options = [];
		$request = new TMA_Request();
		$response = $request->getAllSegments();
		if ($response !== NULL && $response->status === "ok") {
			foreach ($response->segments as $segment) {
				$segment_options[$segment->id] = $segment->name;
			}
		}

		$section->add_control(
				'tma_segments', [
			'label' => __('Segments', 'tma-webtools'),
			'type' => \Elementor\Controls_Manager::SELECT2,
			"description" => __("For which segments the content should be visible.", "tma-webtools"),
			'default' => [],
			'options' => $segment_options,
			'multiple' => true,
				]
		);

		$section->end_controls_section();
	}

}

$tma_elementor_integration = new Elementor_Integration();

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

class SiteOrigin_Integration extends Integration {

	/**
	 * Start up
	 */
	public function __construct() {

		parent::__construct();

		add_filter('siteorigin_panels_row_style_groups', array($this, 'addGroup'));
		add_filter('siteorigin_panels_widget_style_groups', array($this, 'addGroup'));

		add_filter('siteorigin_panels_row_style_fields', array($this, 'custom_row_style_fields'));
		add_filter('siteorigin_panels_widget_style_fields', array($this, 'custom_row_style_fields'));

		add_filter('siteorigin_panels_row_style_attributes', array($this, 'custom_row_style_attributes'), 10, 2);
		add_filter('siteorigin_panels_widget_style_attributes', array($this, 'custom_row_style_attributes'), 10, 2);

//		add_filter('siteorigin_panels_row_attributes', array($this, 'backend_custom_attributes'), 100, 2);
//		add_filter('siteorigin_panels_row_attributes', array($this, 'backend_custom_attributes'), 98, 2);
	}

//	function backend_custom_attributes ($attributes, $row) {
//		$attributes[ 'data-color-label' ] = intval(4);
//		if(empty($attributes['style'])) $attributes['style'] = array();
//		$attributes['style']['background'] = '#000000';
//		return $attributes;
//	}

	function addGroup($groups) {
		$groups['tma_personalization'] = array(
			'name' => __('Target Audience', 'tma-webtools'),
			'priority' => 50
		);
		return $groups;
	}

	function custom_row_style_fields($fields) {
		$fields['tma_personalization'] = array(
			'name' => __('Targeting', 'tma-webtools'),
			'type' => 'checkbox',
			'group' => 'tma_personalization',
			'description' => __('If enabled, the content will only be visible to users matching the selected segments.', 'tma-webtools'),
			'priority' => 1,
		);
		$fields['tma_matching'] = array(
			'name' => __('Matching mode', 'tma-webtools'),
			'type' => 'select',
			'group' => 'tma_personalization',
			'default' => 'all',
			'options' => array(
				'all' => __('All', 'tma-webtools'),
				'single' => __('Single', 'tma-webtools'),
			),
			'description' => __('User must match all or just a single segment.', 'tma-webtools'),
			'priority' => 2,
		);
		$fields['tma_group'] = array(
			'name' => __('Group', 'tma-webtools'),
			'type' => 'text',
			'group' => 'tma_personalization',
			'description' => __('The name of the group. Groups can be used to group elements together', 'tma-webtools'),
			'priority' => 3,
		);

		$fields['tma_default'] = array(
			'name' => __('Is Group default', 'tma-webtools'),
			'type' => 'checkbox',
			'group' => 'tma_personalization',
			'description' => __('Is group default element. The default is used if not other element of the groups matchs the user. The default element must be the last element on the page.', 'tma-webtools'),
			'priority' => 4,
		);


		$request = new TMA_Request();
		$response = $request->getAllSegments();
		if ($response !== NULL && $response->status === "ok") {
			foreach ($response->segments as $segment) {
				$fields['tma_segment_' . $segment->id] = array(
					'name' => $segment->name,
					'type' => 'checkbox',
					'group' => 'tma_personalization',
					'priority' => 10,
				);
			}
		}
		return $fields;
	}

	function custom_row_style_attributes($attributes, $args) {
		$group = $this->getGroup($args);

		$content_added = true;
		if (!$this->is_visible($args)) {
			$attributes['style'] .= 'display:none;';
		}

		return $attributes;
	}

}

$tma_siteorigin_integration = new SiteOrigin_Integration();

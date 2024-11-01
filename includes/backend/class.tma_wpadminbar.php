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
 * Description of class
 *
 * @author marx
 */
class TMA_WPAdminBar {

	public function __construct() {
		add_action('wp_enqueue_scripts', array($this, 'init_javascript'));

		add_action('admin_bar_menu', array($this, 'tma_segment_links'), 900);
	}

	function tma_segment_links($wp_admin_bar) {
		if (!tma_is_preview()) {
			return;
		}
		$args = array(
			'id' => 'segment_selector',
			'title' => __("Target Audience", "tma-webtools"),
			'meta' => array('class' => 'first-toolbar-group'),
		);
		$wp_admin_bar->add_node($args);

		$segments = [];
		if(isset($_GET['segment'])) {
			$segments = filter_input(INPUT_GET, 'segment', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
			$segments = array_map('trim', $segments);
		}

		$args = array();

		$request = new TMA_Request();
		$response = $request->getAllSegments();
		if ($response !== NULL && $response->status === "ok") {
			foreach ($response->segments as $segment) {
				$class = "";
				if (in_array($segment->id, $segments)) {
					$class = "tma-selected-segment";
				}
				array_push($args, array(
					'id' => $segment->id,
					'title' => $segment->name,
					'href' => ' ',
					'parent' => 'segment_selector',
					'meta' => array(
						'onclick' => 'tma_segment_selector("'. $segment->id . '"); return false;',
						'class' => $class
					)
				));
			}
		} else {
			array_push($args, array(
				'id' => 'emtpy',
				'title' => __("No segments found", "tma-webtools"),
				'parent' => 'segment_selector',
			));
		}

		sort($args);

		array_push($args, array(
			'id' => 'clear',
			'title' => 'Clear Segments',
			'href' => '#',
			'parent' => 'segment_selector',
			'meta' => array(
				'onclick' => "tma_segment_clear(); return false;"
			)
		));


		for ($a = 0; $a < sizeOf($args); $a++) {
			$wp_admin_bar->add_node($args[$a]);
		}
	}

	public function init_javascript() {
		wp_register_script('tma-webtools', plugins_url('../../js/tma-webtools.js', __FILE__));
		
		wp_enqueue_script('tma-webtools');
	}

}

if (is_admin_bar_showing()) {
	$tma_adminbar = new TMA_WPAdminBar();	
}


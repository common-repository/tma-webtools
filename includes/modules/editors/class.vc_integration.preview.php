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

class VisualComposer_Integration_Preview {

	/**
	 * Start up
	 */
	public function __construct() {

		add_filter('vc_nav_front_controls', array(
			$this,
			'vcNavControlsFrontend',
		));		
	}

	public function vcNavControlsFrontend($list) {
		if (is_array($list)) {
			$list[] = array(
				'tma-webtools',
				$this->getControlSelectDropdownFrontend(),
			);
		}
		return $list;
	}

	public function getControlSelectDropdownFrontend() {
		return '<li class="vc_pull-right" > '
				. $this->highlightButton()
				. $this->targetButton()
				. '</li > ';
	}

	public function highlightButton() {
		$output = '';
		$output .= '<button class="vc_btn vc_btn-default vc_btn-sm vc_navbar-btn fl-builder-tma-highlight-button" id="vc_button-hightlight" title="' . __("Hightlight targeted content", "tma-webtools") . '">' . __("Hightlight", "tma-webtools") . '</button>';
		return $output;
	}

	public function targetButton() {
		$output = '';
		$output .= '<button class="vc_btn vc_btn-default vc_btn-sm vc_navbar-btn fl-builder-tma-targeting-button" id="vc_button-targeting" title="' . __("Select segments fot preview", "tma-webtools") . '">' . __("Audience", "tma-webtools") . '</button>';
		return $output;
	}

}

$tma_vc_preview = new VisualComposer_Integration_Preview();


add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style('fl-builder-audiences', TMA_WEBTOOLS_URL . 'css/fl-builder-audiences.css', array(), "1");
	wp_enqueue_style('tma-webtools', TMA_WEBTOOLS_URL . 'css/tma-webtools.css', array(), "1");
});
add_action('init', function () {
	if (tma_is_debug()) {
		wp_enqueue_script('tma-webtools-backend', TMA_WEBTOOLS_URL . 'js/webtools-backend.js', array(), "1");
		wp_enqueue_script('vc-audiences', TMA_WEBTOOLS_URL . 'js/vc-audiences.js', array("wpb_composer_front_js", "tma-webtools-backend"), "1");
	} else {
		wp_enqueue_script('tma-webtools-backend', TMA_WEBTOOLS_URL . 'js/webtools-backend-min.js', array(), "1");
		wp_enqueue_script('vc-audiences', TMA_WEBTOOLS_URL . 'js/vc-audiences.js', array("wpb_composer_front_js", "tma-webtools-backend"), "1");
	}
	
	wp_enqueue_style('fl-builder-audiences', TMA_WEBTOOLS_URL . 'css/fl-builder-audiences.css', array(), "1");
	wp_enqueue_style('tma-webtools', TMA_WEBTOOLS_URL . 'css/tma-webtools.css', array(), "1");
});
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
class TMA_Backend_Hooks {

	public function __construct() {
		add_action('save_post', array($this, 'tma_save_post'), 10, 3);
	}

	function tma_save_post($post_id, $post, $update) {

		$content = $post->post_content;

		error_log($content);

		if (strpos($content, Constants::$NEEDLE_VC) !== false || strpos($content, Constants::$NEEDLE_SO) !== false) {
			error_log("real time targeting active");
			update_post_meta($post_id, Constants::$META_KEY_HAS_CONTENT, true);
		} else {
			error_log("real time targeting not active");
			delete_post_meta($post_id, Constants::$META_KEY_HAS_CONTENT);
		}
	}
}

if (is_admin()) {
	$tma_backend_hooks = new TMA_Backend_Hooks();
}
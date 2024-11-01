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
class TMA_ShortCodes_Plugin {
	
	public function __construct() {
		add_action('admin_init', array($this, 'my_tinymce_button'));
	}
	
	// TINYMCE START
	function my_tinymce_button() {
		if (current_user_can('edit_posts') && current_user_can('edit_pages')) {
			add_filter('mce_buttons', array($this, 'my_register_tinymce_button'));
			add_filter('mce_external_plugins', array($this, 'my_add_tinymce_button'));
		}
	}

	function my_register_tinymce_button($buttons) {
		array_push($buttons, "button_tma_content"/*, "button_green"*/);
		return $buttons;
	}

	function my_add_tinymce_button($plugin_array) {
		$plugin_array['tma_shortcodes_plugin'] = plugins_url('../../js/tinymce/plugin.tma.shortcodes.js', __FILE__);
		return $plugin_array;
	}

// TINYMCE END
}
$tma_shortcode_plugin = new TMA_ShortCodes_Plugin();
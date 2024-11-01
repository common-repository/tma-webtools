<?php
/*
 * Copyright (C) 2016 thmarx
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
 * Add Segment selection to the product settings.
 *
 * @author thmarx
 */
class TMA_Product_Settings {

	public function __construct() {

		add_filter('woocommerce_product_data_tabs', array($this, 'add_my_custom_product_data_tab'), 99, 1);

		add_action('woocommerce_product_data_panels', array($this, 'add_my_custom_product_data_fields'));

		add_action('woocommerce_process_product_meta', array($this, 'woocommerce_process_product_meta_fields_save'));
	}

	function add_my_custom_product_data_fields() {
		global $woocommerce, $post;

		$metaData = get_post_meta(get_the_ID(), "tma_segments");
		$segments = array();
		if (isset($metaData[0])) {
			$segments = $metaData[0];
		}

		$request = new TMA_Request();
		$response = $request->getAllSegments();
		?>
		<!-- id below must match target registered in above add_my_custom_product_data_tab function -->
		<div id="my_custom_product_data" class="panel woocommerce_options_panel">
<!--			<div id="tma_default_segment">
				<?php
//				woocommerce_wp_checkbox(array(
//					'id' => 'tma_segment_field',
//					'name' => 'tma_segments[]',
//					'wrapper_class' => 'show_if_simple',
//					'label' => __("Default", "tma-webtools"),
//					'default' => '0',
//					'desc_tip' => false,
//					'cbvalue' => "default",
//					'value' => in_array("default", $segments) ? 'default' : '',
//				));
				?>
			</div>-->
			<?php
			if ($response !== NULL && $response->status === "ok") {

				foreach ($response->segments as $segment) {
					woocommerce_wp_checkbox(array(
						'id' => 'tma_segment_field',
						'name' => 'tma_segments[]',
						'wrapper_class' => 'show_if_simple',
						'label' => $segment->name,
						'default' => '0',
						'desc_tip' => false,
						'cbvalue' => $segment->id,
						'value' => in_array($segment->id, $segments) ? $segment->id : '',
					));
				}
			}
			?>
		</div>
		<?php
	}

	function woocommerce_process_product_meta_fields_save($post_id) {
		// This is the case to save custom field data of checkbox. You have to do it as per your custom fields

		if (isset($_POST['tma_segments'])) {
			$segments = filter_input(INPUT_POST, 'tma_segments', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
			update_post_meta($post_id, 'tma_segments', $segments);
		} else {
			delete_post_meta($post_id, 'tma_segments');
		}
	}

	function add_my_custom_product_data_tab($product_data_tabs) {
		$product_data_tabs['my-custom-tab'] = array(
			'label' => __('Segments', 'tma-wc-targeting'),
			'target' => 'my_custom_product_data',
		);
		return $product_data_tabs;
	}

}

new TMA_Product_Settings();

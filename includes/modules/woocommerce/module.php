<?php

/**
 * Description of TMA_WC_Module
 *
 * @author marx
 */

function tma_webtools_modules_woocommerce_settings($fields) {
	
	$recommendation_options = ["segment" => __("Segment based", "tma-webtools")];
	$recommendation_options = apply_filters("tma-webtools/modules/woocommerce/recommendation/options", $recommendation_options);
	
	$settings_fields = array(
		'tma-webtools-options-wc' => array(
			array(
				'name' => 'webtools_wc_tracking',
				'label' => __("Track WooCommerce events?", "tma-webtools"),
				'desc' => __("Tracked events are: order, add item to basket, remove item from basket.", "tma-webtools"),
				'type' => 'checkbox',
				'default' => ''
			),
			array(
				'name' => 'webtools_wc_recommendation',
				'type' => 'checkbox',
				'label' => __("Replace Recommendations?", "tma-webtools"),
				'desc' => __("Let webTools generate recommendations", "tma-webtools")
			),
			array(
				'name' => 'webtools_wc_recommendation',
				'label' => __("Replace Recommendations?", "tma-webtools"),
				'desc' => __("Replace the default related products.", "tma-webtools"),
				'type' => 'select',
				'options' => $recommendation_options
			),
			array(
				'name' => 'webtools_wc_recommendation_segment_match',
				'label' => __("Matching mode?", "tma-webtools"),
				'desc' => __("If &quot;single&quot;, the product must match at least one user segments. If &quot;all&quot;, the product must match all user segments.", "tma-webtools"),
				'options' => [
					"OR" => __("Single", "tma-webtools"),
					"AND" => __("All", "tma-webtools")
				],
				'type' => 'select',
				"default" => "OR"
			)
		)
	);
	$fields = array_merge_recursive($fields, $settings_fields);
	return $fields;
}

function tma_webtools_modules_woocommerce_sections($sections) {
	$custom_sections = array(
		array(
			'id' => 'tma-webtools-options-wc',
			'title' => __('WooCommerce', 'tma-webtools')
		)
	);
	$sections = array_merge_recursive($sections, $custom_sections);
	return $sections;
}

if (\TMA\Plugins::getInstance()->woocommerce()) {

	if (is_user_logged_in() && (is_admin() || is_preview() )) {
		require_once 'class.backend.product_settings.php';
	}
	require_once 'class.product_loader.php';
	require_once 'class.frontend.wc_tracker.php';
	require_once 'class.frontend.tma_recommendation_product.php';

	//add_action("tma-webtools-settings", "tma_webtools_modules_woocommerce_settings");

	add_filter('tma-webtools/settings/fields', 'tma_webtools_modules_woocommerce_settings');
	add_filter('tma-webtools/settings/sections', 'tma_webtools_modules_woocommerce_sections');
}
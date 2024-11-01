<?php

/**
 * Description of TMA_VC_Module
 *
 * @author marx
 */
function tma_webtools_modules_wpbakery_pagebuilder_settings($fields) {

	$shortcode_options = [];
	foreach (\WPBMap::getAllShortCodes() AS $key => $shortcode) {
		$shortcode_options[$key] = $shortcode['name'];
	}
	$shortcode_options = apply_filters("tma-webtools/modules/visualcomposer/shortcodes/options", $shortcode_options);

	$settings_fields = array(
		'tma-webtools-options-wpbakery' => array(
			array(
				'name' => 'webtools_wpbakery_custom',
				'label' => __("Select shortcodes?", "tma-webtools"),
				'desc' => __("If enabled, only for the selected shortcode a targeting can configured!", "tma-webtools"),
				'type' => 'checkbox',
				'default' => ''
			),
			array(
				'name' => 'webtools_wpbakery_shortcodes',
				'type' => 'multicheck',
				'label' => __("Shortcodes?", "tma-webtools"),
				'desc' => __("Targeting is only available for the selected shortcodes.", "tma-webtools"),
				'options' => $shortcode_options
			)
		)
	);
	$fields = array_merge_recursive($fields, $settings_fields);
	return $fields;
}

function tma_webtools_modules_wpbakery_pagebuilder_sections($sections) {
	$custom_sections = array(
		array(
			'id' => 'tma-webtools-options-wpbakery',
			'title' => __('WPBakery Page Builder', 'tma-webtools')
		)
	);
	$sections = array_merge_recursive($sections, $custom_sections);
	return $sections;
}

if (\TMA\Plugins::getInstance()->visualComposer()) {
	add_filter('tma-webtools/settings/sections', 'tma_webtools_modules_wpbakery_pagebuilder_sections');

	add_action('vc_after_mapping', function () {
		add_filter('tma-webtools/settings/fields', 'tma_webtools_modules_wpbakery_pagebuilder_settings');
	});
}
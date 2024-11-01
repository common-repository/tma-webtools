<?php

/*
  Widget Name: TMA content widget
  Description: Deliver content targeting user behaviour.
  Author: Thorsten Marx
  Author URI: http://thorstenmarx.com
 */


//namespace TMA;

/**
 * Description of class
 *
 * @author thmarx
 */
class TMA_Content_Widget extends SiteOrigin_Widget {

	function __construct() {
		//Here you can do any preparation required before calling the parent constructor, such as including additional files or initializing variables.
		//Call the parent constructor with the required arguments.
		parent::__construct(
				// The unique id for your widget.
				'tma-content-widget',
				// The name of the widget for display purposes.
				__('TMA Content Widget', 'tma-webtools'),
				// The $widget_options array, which is passed through to WP_Widget.
				// It has a couple of extras like the optional help URL, which should link to your sites help or support page.
				array(
			'description' => __('Deliver content targeting user behaviour.', 'tma-webtools'),
			'help' => 'http://thorstenmarx.com/webtools',
				),
				//The $control_options array, which is passed through to WP_Widget
				array(
				),
				//The $form_options array, which describes the form fields used to configure SiteOrigin widgets. We'll explain these in more detail later.
				array(
			'text' => array(
				'type' => 'text',
				'label' => __('Hello world! goes here.', 'tma-webtools'),
				'default' => 'Hello world!'
			),
				),
				//The $base_folder path string.
				plugin_dir_path(__FILE__)
		);
	}

	function get_template_name($instance) {
		return 'tma-content';
	}

	function get_template_dir($instance) {
		return 'tpl';
	}

}

siteorigin_widget_register('tma-content-widget', __FILE__, 'TMA_Content_Widget');

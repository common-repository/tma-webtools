<?php

namespace TMA;

require_once(TMA_WEBTOOLS_DIR . "/modules/class.settings-api.php");
/**
 * Description of class
 *
 * @author marx
 */
class TMA_Settings {
	
	private $settings_api;
	
	function __construct () {
		$this->settings_api = new \WeDevs_Settings_API();
        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
	}
	
	  function admin_init() {
        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );
        //initialize settings
        $this->settings_api->admin_init();
    }
    function admin_menu() {
		add_menu_page(
				__("TMA WebTools", "tma-webtools"), __("TMA WebTools", "tma-webtools"), 'manage_options', 'tma-webtools/pages/tma-webtools-admin.php');
		add_submenu_page('tma-webtools/pages/tma-webtools-admin.php', __("Settings", "tma-webtools"), __("Settings", "tma-webtools"), 'manage_options', 'tma-webtools-setting-admin', array($this, 'plugin_page'));
        //add_options_page( 'Settings API', 'Settings API', 'delete_posts', 'settings_api_test', array($this, 'plugin_page') );
    }
    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'tma_webtools_option',
                'title' => __( 'Basic Settings', 'wedevs' )
            )
        );
		
		$sections = apply_filters("tma-webtools/settings/sections", $sections);
        return $sections;
    }
    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'tma_webtools_option' => array(
                array(
                    'name'              => 'webtools_siteid',
                    'label'             => __("Site id", "tma-webtools"),
                    'desc'              => __("The id should be unique and is used to filter in the webTools-Platform.", "tma-webtools"),
                    'placeholder'       => __( 'Your site id', 'wedevs' ),
                    'type'              => 'text',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'webtools_url',
                    'label'             => __("Url", "tma-webtools"),
                    'desc'              => __("The url where the webTools-Platform is installed.", "tma-webtools"),
                    'placeholder'       => __( 'The webTools url', 'wedevs' ),
                    'type'              => 'text',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'webtools_apikey',
                    'label'             => __("ApiKey", "tma-webtools"),
                    'desc'              => __("The apikey to use the webTools-Platform.", "tma-webtools"),
                    'placeholder'       => __( 'The apikey', 'wedevs' ),
                    'type'              => 'text',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name'              => 'webtools_cookiedomain',
                    'label'             => __("Cookie domain", "tma-webtools"),
                    'desc'              => __("Share the webTools cookie with subdomains. e.q. .your_domain.com", "tma-webtools"),
                    'placeholder'       => __( 'The cookiedomain', 'wedevs' ),
                    'type'              => 'text',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
				array(
                    'name'  => 'webtools_track',
                    'label' => __("Enable Tracking?", "tma-webtools"),
                    'desc'  => __("Tracked events are: pageview", "tma-webtools"),
                    'type'  => 'checkbox'
                ),
				array(
                    'name'  => 'webtools_track_logged_in_users',
                    'label' => __("Track logged in users?", "tma-webtools"),
                    'desc'  => __("Activate tracking of logged in users.", "tma-webtools"),
                    'type'  => 'checkbox'
                ),
				array(
                    'name'  => 'webtools_score',
                    'label' => __("Enable Scoring?", "tma-webtools"),
                    'desc'  => __("If enabled, you can user the scoring metabox to set scorings for all your post types.", "tma-webtools"),
                    'type'  => 'checkbox'
                ),
				array(
                    'name'  => 'webtools_shortcode_single_item_per_group',
                    'label' => __("Single item per group?", "tma-webtools"),
                    'desc'  => __("If enabled, only the first matching group is delivered.", "tma-webtools"),
                    'type'  => 'checkbox'
                ),
				
				/*
                array(
                    'name'              => 'number_input',
                    'label'             => __( 'Number Input', 'wedevs' ),
                    'desc'              => __( 'Number field with validation callback `floatval`', 'wedevs' ),
                    'placeholder'       => __( '1.99', 'wedevs' ),
                    'min'               => 0,
                    'max'               => 100,
                    'step'              => '0.01',
                    'type'              => 'number',
                    'default'           => 'Title',
                    'sanitize_callback' => 'floatval'
                ),
                array(
                    'name'        => 'textarea',
                    'label'       => __( 'Textarea Input', 'wedevs' ),
                    'desc'        => __( 'Textarea description', 'wedevs' ),
                    'placeholder' => __( 'Textarea placeholder', 'wedevs' ),
                    'type'        => 'textarea'
                ),
                array(
                    'name'        => 'html',
                    'desc'        => __( 'HTML area description. You can use any <strong>bold</strong> or other HTML elements.', 'wedevs' ),
                    'type'        => 'html'
                ),
                array(
                    'name'  => 'checkbox',
                    'label' => __( 'Checkbox', 'wedevs' ),
                    'desc'  => __( 'Checkbox Label', 'wedevs' ),
                    'type'  => 'checkbox'
                ),
                array(
                    'name'    => 'radio',
                    'label'   => __( 'Radio Button', 'wedevs' ),
                    'desc'    => __( 'A radio button', 'wedevs' ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    )
                ),
                array(
                    'name'    => 'selectbox',
                    'label'   => __( 'A Dropdown', 'wedevs' ),
                    'desc'    => __( 'Dropdown description', 'wedevs' ),
                    'type'    => 'select',
                    'default' => 'no',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    )
                ),
                array(
                    'name'    => 'password',
                    'label'   => __( 'Password', 'wedevs' ),
                    'desc'    => __( 'Password description', 'wedevs' ),
                    'type'    => 'password',
                    'default' => ''
                ),
                array(
                    'name'    => 'file',
                    'label'   => __( 'File', 'wedevs' ),
                    'desc'    => __( 'File description', 'wedevs' ),
                    'type'    => 'file',
                    'default' => '',
                    'options' => array(
                        'button_label' => 'Choose Image'
                    )
                )*/
            ),
			/*
            'wedevs_advanced' => array(
                array(
                    'name'    => 'color',
                    'label'   => __( 'Color', 'wedevs' ),
                    'desc'    => __( 'Color description', 'wedevs' ),
                    'type'    => 'color',
                    'default' => ''
                ),
                array(
                    'name'    => 'password',
                    'label'   => __( 'Password', 'wedevs' ),
                    'desc'    => __( 'Password description', 'wedevs' ),
                    'type'    => 'password',
                    'default' => ''
                ),
                array(
                    'name'    => 'wysiwyg',
                    'label'   => __( 'Advanced Editor', 'wedevs' ),
                    'desc'    => __( 'WP_Editor description', 'wedevs' ),
                    'type'    => 'wysiwyg',
                    'default' => ''
                ),
                array(
                    'name'    => 'multicheck',
                    'label'   => __( 'Multile checkbox', 'wedevs' ),
                    'desc'    => __( 'Multi checkbox description', 'wedevs' ),
                    'type'    => 'multicheck',
                    'default' => array('one' => 'one', 'four' => 'four'),
                    'options' => array(
                        'one'   => 'One',
                        'two'   => 'Two',
                        'three' => 'Three',
                        'four'  => 'Four'
                    )
                ),
            )*/
        );
		
		$settings_fields = apply_filters("tma-webtools/settings/fields", $settings_fields);
		
        return $settings_fields;
    }
    function plugin_page() {
        echo '<div class="wrap">';
        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();
        echo '</div>';
    }
    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }
        return $pages_options;
    }
}

if (is_admin()) {
	new TMA_Settings();
}

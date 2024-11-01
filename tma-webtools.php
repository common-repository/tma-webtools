<?php
/*
  Plugin Name: TMA WebTools
  Plugin URI: https://thorstenmarx.com/projekte/webtools/
  Description: The integration for webtools user segmentation.
  Author: Thorsten Marx
  Version: 1.5.1
  Author URI: https://thorstenmarx.com/
  Text Domain: tma-webtools
  Domain Path: /languages
 */
if (!defined('ABSPATH')) {
	exit;
}

add_action('plugins_loaded', 'tma_load_textdomain');
function tma_load_textdomain() {
	load_plugin_textdomain( 'tma-webtools', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

add_action("init", "tma_webtools_init");
add_action("plugins_loaded", "tma_webtools_plugins_loaded");

require_once 'includes/tma_request.php';
require_once 'includes/modules/editors/class.integration.php';
require_once 'includes/class.plugins.php';

define("TMA_VERSION", "1.5.1");
define("TMA_SEGMENT_MATCHING_ALL", "all");
define("TMA_SEGMENT_MATCHING_SINGLE", "single");

define("TMA_WEBTOOLS_DIR", plugin_dir_path(__FILE__));
define("TMA_WEBTOOLS_URL", plugins_url('/', __FILE__));

/*
 * debug method, used only for development
 */

function tma_webtools_log($message) {
	// open file
//	$fd = fopen("tma-webtools.log", "a");
//	// append date/time to message
//	$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $message;
//	// write string
//	fwrite($fd, $str . "\n");
//	// close file
//	fclose($fd);
}

function tma_is_debug() {

	$debug = false;

	if (defined('WP_DEBUG') && WP_DEBUG) {
		$debug = true;
	}

	return $debug;
}

function tma_is_elementor_preview() {
	if (class_exists('\Elementor\Plugin')) {
		//return \Elementor\Plugin::$instance->preview->is_preview_mode();
		return isset($_GET['preview_nonce']);
	}

	return false;
}

/**
 * the WP is_preivew did not always return the correct value
 * 
 * @return true if in preview otherwise false
 */
function tma_is_preview() {
	return is_preview() || (isset($_GET['preview']) && $_GET['preview'] == true) || tma_is_elementor_preview();
}

function tma_webtools_plugins_loaded() {
//	add_filter('fl_builder_register_settings_form', 'tma_settings_form', 10, 2);
	if (\TMA\Plugins::getInstance()->siteoriginPanels()) {
		require_once 'includes/modules/editors/class.siteorigin_integration.php';
	}

	if (\TMA\Plugins::getInstance()->visualComposer()) {
		require_once 'includes/modules/editors/class.vc_integration.php';
	}
	if (\TMA\Plugins::getInstance()->elementor()) {
		require_once 'includes/modules/editors/class.elementor_integration.php';
	}
	if (\TMA\Plugins::getInstance()->beaverBuilder()) {
		tma_webtools_log("beaver builder");
		require_once 'includes/modules/editors/class.beaverbuilder_integration.php';
	}
}

function tma_webtools_init() {

	tma_webtools_log("init");
	wp_register_style('tma-webtools', plugins_url('css/tma-webtools.css', __FILE__));
	wp_enqueue_style('tma-webtools');
	// has to be global
	// Settings
	require_once 'includes/class.constants.php';
	require_once 'includes/tma_cookie_helper.php';
	require_once 'includes/frontend/tma_script_helper.php';
	require_once 'includes/frontend/class.shortcode_tma_content.php';
	require_once 'includes/frontend/template_tags.php';
	require_once 'includes/widgets/class.widget_targeting.php';

	/*
	 * load modules
	 * 
	 * modules must be laoded first so hooks are called correctly
	 */
	require_once 'includes/modules/woocommerce/module.php';

	tma_webtools_log(is_preview() ? "preview" : "no preview");
	tma_webtools_log(tma_is_preview() ? "tma_preview" : "no tma_preview");

	if (is_user_logged_in() && (is_admin() || tma_is_preview() )) {
		require_once 'includes/backend/class.tma_metabox.php';
		require_once 'includes/backend/class.tma_shortcodes_plugin.php';
		require_once 'includes/backend/class.tma_wpadminbar.php';

		require_once 'includes/backend/class.tma_ajax.php';

		require_once 'includes/backend/class.tma_settings.php';

		require_once 'includes/backend/class.tma_hooks.php';

		add_filter("tma_config", function ($tma_config) {
			$options = get_option('tma_webtools_option');
			$siteid = isset($options['webtools_siteid']) ? $options["webtools_siteid"] : get_option('blogname');
			$apikey = isset($options['webtools_apikey']) ? $options["webtools_apikey"] : "";
			$url = isset($options['webtools_url']) ? $options["webtools_url"] : "";
			$tma_config['apikey'] = $apikey;
			$tma_config['site'] = $siteid;
			$tma_config['url'] = $url;
			
			return $tma_config;
		});
	}

	add_action('wp_head', 'tma_webtools_hook_js');
	add_action('wp_head', 'tma_js_variables', -100);
	add_action('admin_head', 'tma_js_variables', -100);

	tma_init_cookie();
}

/*
  add_filter("tma_config", 'tma_recommendation_tma_config');
  function tma_recommendation_tma_config($tmaConfig) {
  $recConfig = array();
  $recConfig['plugin_url'] = plugins_url('', __FILE__);

  $tmaConfig['recommendation'] = $recConfig;

  return $tmaConfig;
  }
 */

function tma_js_variables() {



	$tma_config = [];
	$tma_config['plugin_url'] = plugins_url('', __FILE__);

	$request = new TMA\TMA_Request();
	$response = $request->getAllSegments();
	if ($response !== NULL && $response->status === "ok") {
		$tma_config['segments'] = $response->segments;
	}

	$tma_config = apply_filters("tma_config", $tma_config);
	?>
	<script type="text/javascript">
		var TMA_CONFIG = <?php echo json_encode($tma_config); ?>;
	</script><?php
}

function tma_webtools_hook_js() {
	$scriptHelper = new \TMA\TMAScriptHelper();
	echo $scriptHelper->getCode();
}

function tma_init_cookie() {
	TMA\TMA_COOKIE_HELPER::getCookie(TMA\TMA_COOKIE_HELPER::$COOKIE_USER, TMA\UUID::v4(), TMA\TMA_COOKIE_HELPER::$COOKIE_USER_EXPIRE, true);
	TMA\TMA_COOKIE_HELPER::getCookie(TMA\TMA_COOKIE_HELPER::$COOKIE_REQUEST, TMA\UUID::v4(), TMA\TMA_COOKIE_HELPER::$COOKIE_REQUEST_EXPIRE, true);
	TMA\TMA_COOKIE_HELPER::getCookie(TMA\TMA_COOKIE_HELPER::$COOKIE_VISIT, TMA\UUID::v4(), TMA\TMA_COOKIE_HELPER::$COOKIE_VISIT_EXPIRE, true);
}

function tma_save_post($post_id, $post, $update) {

	$content = $post->post_content;



	if (strpos($content, TMA\Constants::$NEEDLE_VC) != false || strpos($content, TMA\Constants::$NEEDLE_SO) != false) {
		error_log("real time targeting active");
		update_post_meta($post_id, TMA\Constants::$META_KEY_HAS_CONTENT, true);
	} else {
		error_log("real time targeting not active");
		delete_post_meta($post_id, TMA\Constants::$META_KEY_HAS_CONTENT);
	}
}

//add_action( 'save_post', 'tma_save_pos	t', 10, 3 );

function tma_settings_form($form, $id) {
	if ('row' == $id || $id == 'col') {
		//var_dump($form);
		$column_form = array(
			'tabs' => array(
				'tma-webtools2' => array(
					'title' => "WebTools 2",
					'sections' => array(
						'targeting' => array(
							'title' => "Targeting",
							'fields' => array(
								'expand_bg' => array(
									'type' => 'select',
									'label' => __('Sichtbar', 'zestsms'),
									'help' => 'Expand the background past the container',
									'default' => '1',
									'options' => array(
										'0' => __('No', 'zestsms'),
										'1' => __('Yes', 'zestsms')
									),
									'preview' => 'none'
								)
							)
						)
					)
				)
			)
		);
		$form = array_merge_recursive($form, $column_form);
	}

	return $form;
}
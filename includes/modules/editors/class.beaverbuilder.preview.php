<?php

namespace TMA;

/**
 * Handles the revisions UI for the builder.
 *
 * @since 2.0
 */
final class TMA_BeaverBuilderPreview {

	protected static $_instance = null;

	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 2.0
	 * @return void
	 */
	public function init() {
//		add_filter('fl_builder_ui_js_config', array($this, 'ui_js_config'));
//		add_filter('fl_builder_main_menu', array($this, 'main_menu_config'));
		add_filter('fl_builder_ui_bar_buttons', array($this, "ui_bar_config"));
	}

	function ui_bar_config($buttons) {
		$buttons["tma-targeting"] = array(
			'label' => __('Audience', 'tma-webtools'),
			'show' => true,
		);
		$buttons["tma-highlight"] = array(
			'label' => __('Highlight', 'tma-webtools'),
			'show' => true,
		);

		return $buttons;
	}
}

TMA_BeaverBuilderPreview::getInstance()->init();

add_action('wp_enqueue_scripts', function () {
	if (tma_is_debug()) {
		wp_enqueue_script('tma-webtools-backend', TMA_WEBTOOLS_URL . 'js/webtools-backend.js', array(), "1");
		wp_enqueue_script('fl-builder-audiences', TMA_WEBTOOLS_URL . 'js/fl-builder-audiences.js', array("fl-builder", "tma-webtools-backend"), "1");
	} else {
		wp_enqueue_script('tma-webtools-backend', TMA_WEBTOOLS_URL . 'js/webtools-backend-min.js', array(), "1");
		wp_enqueue_script('fl-builder-audiences', TMA_WEBTOOLS_URL . 'js/fl-builder-audiences.js', array("fl-builder-min", "tma-webtools-backend"), "1");
	}
	
	wp_enqueue_style('fl-builder-audiences', TMA_WEBTOOLS_URL . 'css/fl-builder-audiences.css', array(), "1");
});

add_action("wp_head", function () {
	?>
	<style>
		/* Audience actions */
		.fl-builder--audience-actions {
			display:none;
			position: fixed;
			top: 4px;
			left: 4px;
			z-index: 100008;
			padding: 4px 4px 6px;
			justify-content: center;
			background:white;
			border-radius: 4px;
		}
		.fl-builder--audience-actions * {
			margin-right: 5px;
		}
		.fl-builder--audience-actions *:last-child {
			margin: 0;
		}
	</style>
	<?php

});

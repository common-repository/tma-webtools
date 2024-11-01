<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TMA;

/**
 * Description of Plugins
 *
 * @author marx
 */
class Plugins {

	protected static $_instance = null;
	
	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	protected function __clone() {	}
	
	
	private $plugins;
	protected function __construct() {
		$this->plugins = apply_filters('active_plugins', get_option('active_plugins'));
	}
	
	public function woocommerce () {
		return in_array('woocommerce/woocommerce.php', $this->plugins);
	}
	public function siteoriginPanels () {
		return in_array('siteorigin-panels/siteorigin-panels.php', $this->plugins);
	}
	public function visualComposer () {
		return in_array('js_composer/js_composer.php', $this->plugins);
	}

	public function elementor () {
		return in_array('elementor/elementor.php', $this->plugins);
	}
	
	public function beaverBuilder () {
		return class_exists( 'FLBuilder' );
	}
}

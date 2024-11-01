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
 * Tracking of WooCommerce events.
 */
class TMA_WC_TRACKER {

	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		$this->options = get_option('tma-webtools-options-wc');
	}

	public function shouldInit() {
		return isset($this->options['webtools_wc_tracking']) && $this->options['webtools_wc_tracking'] === true;
	}

	public function init() {
		add_action('woocommerce_order_status_completed', array($this, 'woocommerce_order_status_completed'));

		add_action('woocommerce_add_to_cart', array($this, 'woocommerce_add_to_cart'));
		add_action('woocommerce_remove_cart_item', array($this, 'woocommerce_remove_cart_item'));
	}

	public function woocommerce_add_to_cart($cart_item_key) {
		$cart = WC()->cart;
		$item = $cart->get_cart_item($cart_item_key);
		
		// loop through the cart looking 
		$product_ids = array();
		foreach ($cart->get_cart() as $item_key => $values) {
			$product_ids[] = $values['product_id'];
		}

		$customAttributes = array();
		$customAttributes['item_id'] = $item['product_id'];
		$customAttributes['cart_items'] = $product_ids; //implode(":", $product_ids);
		$request = new \TMA\TMA_Request();
		$request->track("cart_item_add", "#cart", $customAttributes);
	}

	public function woocommerce_remove_cart_item($cart_item_key) {
		$cart = WC()->cart;
		$item = $cart->get_cart_item($cart_item_key);

		// loop through the cart looking 
		$product_ids = array();
		foreach ($cart->get_cart() as $item_key => $values) {
			if ($cart_item_key === $item_key) {
				continue;
			}
			$product_ids[] = $values['product_id'];
		}

		$customAttributes = array();
		$customAttributes['item_id'] = $item['product_id'];
		$customAttributes['cart_items'] = $product_ids; //implode(":", $product_ids);
		$request = new \TMA\TMA_Request();
		$request->track("cart_item_remove", "#cart", $customAttributes);
	}

	public function woocommerce_order_status_completed($order_id) {
		$order = new \WC_Order($order_id);
		$items = $order->get_items();
		$product_ids = array();
		foreach ($items as $item => $product) {
			$product_ids[] = $product['product_id'];
		}
		$request = new \TMA\TMA_Request();
		$customAttributes = array();
		$customAttributes['order_id'] = $order_id;
		$customAttributes['order_items'] = $product_ids; //implode(":", $product_ids);
		$request->track("order", "#order", $customAttributes);
	}

}

$tma_wc_tracker = new TMA_WC_TRACKER();
if ($tma_wc_tracker->shouldInit()) {
	$tma_wc_tracker->init();
}


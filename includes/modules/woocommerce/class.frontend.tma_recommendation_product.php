<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TMA;

/**
 * If configured, the related products are replaced by products of the configured recommendation
 *
 * @author marx
 */
class TMA_Recommendation_Product {

	private $options;

	function __construct() {
		$this->options = get_option('tma-webtools-options-wc');

		add_action("tma_wc_recommendations_action_segment", array($this, "action_related_products_by_segments"));

		if (isset($this->options["webtools_wc_recommendation"]) && $this->options["webtools_wc_recommendation"] !== '') {
			do_action("tma_wc_recommendations_action_" . $this->options["webtools_wc_recommendation"]);
		}
	}

	function action_related_products_by_segments() {
		add_filter('woocommerce_related_products_args', array($this, 'filter_related_products_by_segment'));
	}

	function filter_related_products_by_segment($args) {

		global $product;

		$posts_per_page = 4;

		$segments = $this->getUserSegments();

		if ($segments === NULL || sizeof($segments) === 0) {
//			$segments[] = "default";
			if (empty($product) || !$product->exists()) {
				return $args;
			}

			if (!$related = $product->get_related($posts_per_page)) {
				return $args;
			}
			return array(
				'post_type' => 'product',
				'ignore_sticky_posts' => 1,
				'no_found_rows' => 1,
				'posts_per_page' => $posts_per_page,
				'orderby' => 'rand',
				'post__in' => $related,
				'post__not_in' => array($product->id)
			);
		} else {

			$related = array();
			$products = TMA_ProductLoader::getProducts($segments, $posts_per_page);
			foreach ($products as $i => $product) {
				$related[] = $product->get_id();
			}

			if ($related && sizeof($related) > 0) { // remove category based filtering
				$args['post__in'] = $related;
			} else {
				$args['post__in'] = array(0);
			}
		}


		return $args;
	}

	public function getUserSegments() {
		$request = new TMA_Request();
		
		$uid = TMA_COOKIE_HELPER::getCookie(TMA_COOKIE_HELPER::$COOKIE_USER, UUID::v4(), TMA_COOKIE_HELPER::$COOKIE_USER_EXPIRE);

		if (isset($uid)) {
			$response = $request->getSegments($uid);
			if (isset($response->user) && isset($response->user->segments)) {
				$segments = $response->user->segments;
			} else {
				$segments = array();
			}
		}

		if (!isset($segments) || sizeof($segments) == 0) {
			$segments[] = "default";
		}

		return $segments;
	}

}

new \TMA\TMA_Recommendation_Product();

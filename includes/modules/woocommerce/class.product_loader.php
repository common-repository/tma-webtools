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

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Description of TMA_ProductLoader
 *
 * @author thmarx
 */
class TMA_ProductLoader {
	public static function getProducts($segments, $productCount, $matchSegments="OR") {

		//error_log(serialize($segments));
		
		
		$metaQuery = array();
		
		$options = get_option('tma-webtools-options-wc');
		if (isset($options["webtools_wc_recommendation_segment_match"]) && $options["webtools_wc_recommendation_segment_match"] !== "") {
			$metaQuery ["relation"] = $options["webtools_wc_recommendation_segment_match"];
		} else {
			$metaQuery ["relation"] = "OR";
		}
		
		foreach ($segments as $segment) {
			$subQuery = array(
				'key' => 'tma_segments',
				'value' => '"' . $segment . '"',
				'compare' => 'LIKE'
			);
			$metaQuery [] = $subQuery;
		}
		
		$params = array(
			'post_type' => array('product', 'product_variation'),
			'orderby' => 'rand',
			'posts_per_page' => $productCount,
			'post__not_in' => array(get_the_ID()),
			'meta_query' => $metaQuery
		);

		$wc_query = new \WP_Query($params);

		$products = array();

		if ($wc_query->have_posts()) :
			while ($wc_query->have_posts()) :
				$wc_query->the_post();
				$product = new \WC_Product(get_the_ID());
				array_push($products, $product);
			endwhile;
			wp_reset_postdata();
		endif;
		
		return $products;
	}
}

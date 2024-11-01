<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TMA;

/**
 * Description of class
 *
 * @author marx
 */
class TMA_Backend_Ajax {

	public function __construct() {
		add_action('wp_ajax_tma_post_types', array($this, 'tma_post_types'));
		add_action('wp_ajax_tma_post_search', array($this, 'tma_post_search'));
	}

	function title_like_posts_where($where, &$wp_query) {
		global $wpdb;
		
		if ($search_term = $wp_query->get('post_title_like')) {
			/* using the esc_like() in here instead of other esc_sql() */
			$search_term = $wpdb->esc_like($search_term);
			$search_term = ' \'%' . $search_term . '%\'';
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE ' . $search_term;
		}

		return $where;
	}

	function tma_post_types() {
		error_log("AJAX");
		$args = array(
			'public' => true,
			'_builtin' => true,
			'show_ui' => true
		);

		$output = 'objects'; // 'names' or 'objects' (default: 'names')
		$operator = 'and'; // 'and' or 'or' (default: 'and')

		$post_types = get_post_types($args, $output, $operator);
//		$post_types = $types = get_post_types( '', 'objects' );

		echo json_encode($post_types);

		wp_die(); // this is required to terminate immediately and return a proper response
	}

	function tma_post_search() {
		$query = filter_input(INPUT_GET, 'query', FILTER_DEFAULT);
		$type = filter_input(INPUT_GET, 'type', FILTER_DEFAULT);

		$args = array(
			'post_title_like' => $query,
			'post_type' => $type,
		);
		add_filter( 'posts_where', array($this, 'title_like_posts_where'), 10, 2 );
		$wp_query = new \WP_Query($args);
		remove_filter('posts_where', array($this, 'title_like_posts_where'), 10, 2 );
		
		$result = [];

		while ($wp_query->have_posts()) {
			$wp_query->the_post();
			$post = [];
			$post['id'] = get_the_ID();
			$post['title'] = get_the_title();

			$result[] = $post;
		}
		wp_reset_query();

		echo json_encode($result);

		wp_die();
	}

}

if (is_admin()) {
	$tma_backend_ajax = new TMA_Backend_Ajax();
}
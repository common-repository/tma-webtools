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
 * Description of class
 *
 * @author marx
 */
class TMA_MetaBox {
	
	

	public function __construct() {
		add_action('load-post.php', array($this, 'smashing_post_meta_boxes_setup'));
		add_action('load-post-new.php', array($this, 'smashing_post_meta_boxes_setup'));
	}

	/* Meta box setup function. */

	function smashing_post_meta_boxes_setup() {

		/* Add meta boxes on the 'add_meta_boxes' hook. */
		add_action('add_meta_boxes', array($this, 'smashing_add_post_meta_boxes'));
		/* Save post meta on the 'save_post' hook. */
		add_action('save_post', array($this, 'smashing_save_post_class_meta'), 10, 2);
	}

	/* Create one or more meta boxes to be displayed on the post editor screen. */

	function smashing_add_post_meta_boxes() {

		add_meta_box(
				'smashing-post-class', // Unique ID
				esc_html__('Segment Scoring', 'tma-webtools'), // Title
				array($this, 'smashing_post_class_meta_box'), // Callback function
				array('post', 'page', 'product'), // Admin page (or post type)
				'side', // Context
				'default'   // Priority
		);
	}

	/* Display the post meta box. */

	function smashing_post_class_meta_box($object, $box) {
		?>

		<?php wp_nonce_field(basename(__FILE__), 'smashing_post_class_nonce'); ?>

		<p>
			<?php _e("Configure scoring for segments.", 'tma-webtools'); ?>
			<br />
			<?php
			
			$metaData = get_post_meta(get_the_ID(), Constants::$META_KEY_SEGMENT_SCORE);
			$postScores = array();
			if (isset($metaData[0])) {
				$postScores = $metaData[0];
			}
			
			$request = new TMA_Request();
			$response = $request->getAllSegments();
			if ($response !== NULL && $response->status === "ok") {
				?>
				<!-- id below must match target registered in above add_my_custom_product_data_tab function -->
			<table>
				<?php
				foreach ($response->segments as $segment) {
					echo $this->handleSegment($segment, $postScores);
				}
				?>
			</table>
			<?php
		}
		?>
		</p>
		<?php
	}
	
	private function handleSegment ($segment, $postScores) {
		$segmentHTML = "";
		if ($segment->rules == NULL) {
			return $segmentHTML;
		}
		foreach ($segment->rules as $rule) {
			if ($rule->type === "score") {
				echo "<tr>";
				echo "<td>";
				echo $rule->name;
				echo "</td>";
				echo "<td>";
				echo "<input type='number' name='" . Constants::$META_KEY_SEGMENT_SCORE . "[" . $rule->name . "]' ";
				echo " min='0' step='1' ";
				if (isset($postScores[$rule->name])) {
					echo " value='" . $postScores[$rule->name] . "' ";
				}
				echo "/>";
				echo "</td>";
				echo "</tr>";
			}
		}
		
		return $segmentHTML;
	}

	/* Save the meta box's post metadata. */

	function smashing_save_post_class_meta($post_id, $post) {
		/* Verify the nonce before proceeding. */
		if (!isset($_POST['smashing_post_class_nonce']) || !wp_verify_nonce($_POST['smashing_post_class_nonce'], basename(__FILE__))) {
			return $post_id;
		}

		/* Get the post type object. */
		$post_type = get_post_type_object($post->post_type);

		/* Check if the current user has permission to edit the post. */
		if (!current_user_can($post_type->cap->edit_post, $post_id)) {
			return $post_id;
		}

		/* Get the posted data and sanitize it for use as an HTML class. */
		$new_meta_value_temp = ( isset($_POST[Constants::$META_KEY_SEGMENT_SCORE]) ? filter_input(INPUT_POST, Constants::$META_KEY_SEGMENT_SCORE, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) : array() );
		$new_meta_value = \array_filter($new_meta_value_temp);

		/* Get the meta key. */
		$meta_key = Constants::$META_KEY_SEGMENT_SCORE;

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta($post_id, $meta_key, true);
		//error_log(json_encode($meta_value));

		/* If a new meta value was added and there was no previous value, add it. */
		if (sizeof($new_meta_value) > 0) {
			update_post_meta($post_id, $meta_key, $new_meta_value);
		} else {
			delete_post_meta($post_id, $meta_key, $meta_value);
		}
	}

}
if (is_admin()) {
	$tma_metabox = new TMA_MetaBox();
}
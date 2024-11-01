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
if (!class_exists('TMA_Widget_Targeting')) {

	class TMA_Widget_Targeting extends Integration {

		public function __construct() {
			parent::__construct();
//			$screen =  get_current_screen();
			// && !$screen->parent_base == 'edit'
			
			global $pagenow;
			if (is_admin() && $pagenow == 'widgets.php') {
				add_filter('in_widget_form', array($this, 'add_widget_form'), 10, 3);
				add_filter('widget_update_callback', array($this, 'save_widget_form'), 10, 2);
			} else {
				add_filter('widget_display_callback', array($this, 'filter_widgets'), 11, 3);
			}
		}

		function filter_widgets($instance, $widget, $args) {
//			var_dump($instance);
			if (!empty($instance['tma_personalization']) && $instance['tma_personalization'] === 1) {
				if (!parent::matching($instance)) {
					return false;
				}
			}
			return $instance;
		}

		function add_widget_form($widget, $return, $instance) {
			// Display the description option.
			$enable = isset($instance['tma_personalization']) ? $instance['tma_personalization'] : '';
			$matching = isset($instance['tma_matching']) ? $instance['tma_matching'] : '';
			$group = isset($instance['tma_group']) ? $instance['tma_group'] : '';
			$group_default = isset($instance['tma_default']) ? $instance['tma_default'] : '';

			$request = new TMA_Request();
			$response = $request->getAllSegments();
			?>
			<p>
			<fieldset>
				<legend style="font-weight: bold;">Targeting</legend>
				<p>
				<label for="<?php echo $widget->get_field_id('tma_personalization'); ?>">
					<?php _e('Enable', 'tma-webtools'); ?>
					<input class="checkbox" type="checkbox" id="<?php echo $widget->get_field_id('tma_personalization'); ?>" name="<?php echo $widget->get_field_name('tma_personalization'); ?>" <?php checked(true, $enable); ?> />
				</label>
				</p>
				<p>
				<label for="<?php echo $widget->get_field_id('tma_group'); ?>">
					<?php _e('Group', 'tma-webtools'); ?>
					<input class="widefat" type="text" id="<?php echo $widget->get_field_id('tma_group'); ?>" name="<?php echo $widget->get_field_name('tma_group'); ?>" value="<?php echo $group ?>" />
				</label>
				</p>
				<p>
				<label for="<?php echo $widget->get_field_id('tma_default'); ?>">
					<?php _e('Group default', 'tma-webtools'); ?>
					<input class="checkbox" type="checkbox" id="<?php echo $widget->get_field_id('tma_default'); ?>" name="<?php echo $widget->get_field_name('tma_default'); ?>" <?php checked(true, $group_default); ?> />
				</label>
				</p>
				<p>
				<label for="<?php echo $widget->get_field_id('tma_matching'); ?>">
					<?php _e('Matching mode', 'tma-webtools'); ?>
					<select id="<?php echo $widget->get_field_id('tma_matching'); ?>" name="<?php echo $widget->get_field_name('tma_matching'); ?>">
						<option <?php selected( $matching, ShortCode_TMA_CONTENT::$match_mode_all ); ?> value="<?php echo ShortCode_TMA_CONTENT::$match_mode_all; ?>"><?php _e('All', 'tma-webtools'); ?></option>
						<option <?php selected( $matching, ShortCode_TMA_CONTENT::$match_mode_single ); ?> value="<?php echo ShortCode_TMA_CONTENT::$match_mode_single; ?>"><?php _e('Single', 'tma-webtools'); ?></option>
					</select>
				</label>
				</p>
				<p>
				<h4><?php _e('Visible for segments', 'tma-webtools'); ?></h4>
				<?php
				if ($response !== NULL && $response->status === "ok") {
					foreach ($response->segments as $segment) {
						$segment_id = 'tma_segment_' . $segment->id;
						$segment_checked = isset($instance[$segment_id]) ? 1 : '';
				?>
					<label for="<?php echo $widget->get_field_id($segment_id); ?>">
						<?php echo $segment->name; ?>
						<input class="checkbox" type="checkbox" id="<?php echo $widget->get_field_id($segment_id); ?>" value="<?php echo $segment->id;?>" name="<?php echo $widget->get_field_name($segment_id); ?>" <?php checked(true, $segment_checked); ?> />
					</label>
				<?php
					}
				}
				?>
			</p>
			</fieldset>
			</p>
			<?php
		}

		function save_widget_form($instance, $new_instance) {

			// Is the instance a nav menu and are descriptions enabled?
			if (!empty($new_instance['tma_personalization'])) {
				$new_instance['tma_personalization'] = 1;
			}

			return $new_instance;
		}

	}

	$tma_widget_targeting = new TMA_Widget_Targeting();
}
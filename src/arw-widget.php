<?php
/*
Archives Calendar Widget
Author URI: http://alek.be
License: GPLv3
*/

require_once 'ARCW.class.php';

/***** WIDGET CLASS *****/
class Archives_Calendar extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'archives_calendar',
			'Archives Calendar',
			array( 'description' => __( 'Show archives as calendar', 'arwloc' ), )
		);
	}


	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		archive_calendar( $instance );

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 * @return void
	 */
	public function form( $instance ) {
		$defaults = array(
			'title'              => __( 'Archives' ),
			'next_text'          => '>',
			'prev_text'          => '<',
			'post_count'         => 1,
			'month_view'         => 0,
			'month_select'       => 'default',
			'disable_title_link' => 0,
			'different_theme'    => 0,
			'theme'              => null,
			'categories'         => null,
			'post_type'          => array( 'post' ),
			'show_today'         => 0
		);
		$instance = wp_parse_args( $instance, $defaults );

		$title              = $instance['title'];
		$prev               = $instance['prev_text'];
		$next               = $instance['next_text'];
		$count              = $instance['post_count'];
		$month_view         = $instance['month_view'];
		$month_select       = $instance['month_select'];
		$disable_title_link = $instance['disable_title_link'];
		$different_theme    = $instance['different_theme'];
		$arw_theme          = $instance['theme'];
		$cats               = $instance['categories'];
		$post_type          = $instance['post_type'];


		if ( is_array( $post_type ) && empty( $post_type ) || ( ! $post_type || $post_type == '' ) ) {
			$post_type = array( 'post' );
		}

		// Widget Settings form is in external file
		include 'arw-widget-settings.php';
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['title']     = strip_tags( $new_instance['title'] );
		$instance['next_text'] = htmlspecialchars( $new_instance['next_text'] );
		$instance['prev_text'] = htmlspecialchars( $new_instance['prev_text'] );

		if ( $instance['next_text'] == htmlspecialchars( '>' ) ) {
			$instance['prev_text'] = htmlspecialchars( '<' );
		}

		$instance['post_count']         = ( $new_instance['post_count'] ) ? $new_instance['post_count'] : 0;
		$instance['month_view']         = $new_instance['month_view'];
		$instance['month_select']       = $new_instance['month_select'];
		$instance['disable_title_link'] = ( $new_instance['disable_title_link'] ) ? $new_instance['disable_title_link'] : 0;
		$instance['different_theme']    = ( $new_instance['different_theme'] ) ? $new_instance['different_theme'] : 0;
		$instance['theme']              = $new_instance['theme'];
		$instance['categories']         = $new_instance['categories'];
		$instance['post_type']          = ( $new_instance['post_type'] ) ? $new_instance['post_type'] : array( 'post' );

		return $instance;
	}
}

// Register and load the widget
function archives_calendar_load_widget() {
	register_widget( 'Archives_Calendar' );
}

add_action( 'widgets_init', 'archives_calendar_load_widget' );

global $wp_locale, $arcw;

/***** WIDGET CONSTRUCTION FUNCTION *****/
/* can be called directly archive_calendar($args) */
function archive_calendar( $instance ) {
	global $wp_locale, $arcw;

	$arcw = new ARCW( $instance );

	include 'templates/calendar.php';
}

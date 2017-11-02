<?php
/*
Plugin Name: Archives Calendar Widget 2
Plugin URI: https://wordpress.org/plugins/archives-calendar-widget/
Description: Archives widget that makes your monthly/daily archives look like a calendar.
Version: @@version
Author: Aleksei Polechin (alek´)
Author URI: http://alek.be
License: GPLv3
*/

/***** GPLv3 LICENSE *****
 *
 * Archives Calendar Widget for Wordpress
 * Copyright (C) 2013-2017 Aleksei Polechin (http://alek.be)
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 ****/

// current version of the plugin
define( 'ARCWV', '@@version' );
// enable or disable debug (for dev instead of echo or print_r use debug() function)
define( 'ARCW_DEBUG', true );

$themes = array(
	'calendrier'          => 'Calendrier',
	'pastel'              => 'Pastel',
	'classiclight'        => 'Classic',
	'classicdark'         => 'Classic Dark',
	'twentytwelve'        => 'Twenty Twelve',
	'twentythirteen'      => 'Twenty Thirteen',
	'twentyfourteen'      => 'Twenty Fourteen',
	'twentyfourteenlight' => 'Twenty Fourteen Light',
);

// ACTIVATION
require( 'arw-install.php' );
require( 'arw-settings.php' );
require( 'arw-widget.php' );
register_activation_hook( __FILE__, 'archivesCalendar_activation' );
register_uninstall_hook( __FILE__, 'archivesCalendar_uninstall' );
add_action( 'wpmu_new_blog', 'archivesCalendar_new_blog', 10, 6 ); // in case of creation of a new site in WPMU

// LOCALISATION
add_action( 'init', 'archivesCalendar_init' );
// ADD setting action link
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'arcw_plugin_action_links' );
// Register and enqueue Archives Calendar Widget JS
add_action( 'wp_enqueue_scripts', 'archivesCalendar_script' );

// Scripts to be included on Widget configuration page
add_action( 'admin_print_scripts-widgets.php', 'arcw_admin_widgets_scripts' );
add_action( 'admin_print_scripts-customize.php', 'arcw_admin_widgets_scripts' );

// Archives Calendar Widget Themes CSS
if ( $archivesCalendar_options['css'] == 1 ) {
	add_action( 'wp_enqueue_scripts', 'archives_calendar_styles' );
}

// WIDGET INITIALISATION
add_action( 'widgets_init', create_function( '', 'register_widget( "Archives_Calendar" );' ) );

/**** INIT/ENQUEUE FUNCTIONS ****/
function archivesCalendar_init() {
	load_plugin_textdomain( 'arwloc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/** ADD actions links to the plugin entry in the administration "Plugins" page */
function arcw_plugin_action_links( $links ) {
	$links[] = '<a href="' . get_admin_url( null, 'options-general.php?page=Archives_Calendar_Widget' ) . '">' . __( 'Settings' ) . '</a>';

	return $links;
}

/**
 * Register the required front-end JS library in the footer
 * Without this script you will not be able to change calendar pages
 */
function archivesCalendar_script() {
	wp_register_script( 'arcw-script', plugins_url( '/js/arcw.js', __FILE__ ), array(), ARCWV, true );
	wp_enqueue_script( 'arcw-script' );
}

/**
 * Register and enqueue default theme CSS
 */
function archives_calendar_styles() {
	$archivesCalendar_options = get_option( 'archivesCalendar' );
	wp_register_style( 'archives-cal-' . $archivesCalendar_options['theme'], plugins_url( 'themes/' . $archivesCalendar_options['theme'] . '.css', __FILE__ ), array(), ARCWV );
	wp_enqueue_style( 'archives-cal-' . $archivesCalendar_options['theme'] );
}

/**
 * ADMINISTRATION ONLY
 * Register and enqueue Scripts required in the Administration (plugin/widget settings)
 */
function arcw_admin_widgets_scripts() {
	wp_register_style( 'arcw-widget-settings', plugins_url( '/admin/css/widget-settings.css', __FILE__ ), array(), ARCWV );
	wp_register_script( 'arcwpWidgetsPage', plugins_url( '/admin/js/widgets-page.js', __FILE__ ), array(), ARCWV );
	wp_enqueue_script( 'arcwpWidgetsPage' );
	wp_enqueue_style( 'arcw-widget-settings' );
}

// Activate filter in archives page
if ( isset( $archivesCalendar_options['filter'] ) && $archivesCalendar_options['filter'] == 1 ) {
	add_action( 'pre_get_posts', 'arcw_filter' );
}

function arcw_filter( $query ) {
	if ( $query->is_main_query() && $query->is_archive() && ! is_admin() ) {
		if ( isset( $_GET ) && isset( $_GET['arcf'] ) ) {
			_arcw_add_filter_query( $query );
		}
	}

	return $query;
}

function _arcw_get_filter_params( $str = '' ) {
	$re = "/(\\w+):([^:]+)/";
	preg_match_all( $re, $str, $matches );

	$post_types = null;
	$cats       = null;

	foreach ( $matches[1] as $key => $value ) {
		if ( $value == 'post' ) {
			$post_types = explode( ' ', $matches[2][ $key ] );
		}
		if ( $value == 'cat' ) {
			$cats = explode( ' ', $matches[2][ $key ] );
		}
	}

	return array( "posts" => $post_types, "cats" => $cats );
}

function _arcw_add_filter_query( $query ) {
	$str        = ( $_GET['arcf'] );
	$params     = _arcw_get_filter_params( $str );
	$post_types = $params['posts'];
	$cats       = $params['cats'];

	if ( isset( $post_types ) ) {
		$query->set( 'post_type', $post_types );

		if ( isset( $cats ) ) {
			$query->set( 'tax_query', array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'category',
						'field'    => 'term_id',
						'terms'    => $cats,
						'operator' => 'IN',
					),
				)
			);
		}
	} elseif ( isset ( $cats ) ) {
		$query->set( 'cat', implode( ',', $cats ) );
	}

	return $query;
}

/***** CHECK MULTISITE NETWORK *****/
if ( ! function_exists( 'isMU' ) ) {
	function isMU() {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			return true;
		}

		return false;
	}
}

/** DEBUG FUNCTION **/
if ( ! function_exists( 'debug' ) ) {
	function debug( $context = "" ) {
		if ( ARCW_DEBUG === true ) {
			print_r( $context );
		}
	}
}

<?php
/*
Archives Calendar Widget
Author URI: http://alek.be
License: GPLv3
*/

require_once 'ARCW.class.php';
require_once 'ARCW-Widget.class.php';

// Register and load the widget
function archives_calendar_load_widget() {
	register_widget( 'Archives_Calendar' );
}

add_action( 'widgets_init', 'archives_calendar_load_widget' );


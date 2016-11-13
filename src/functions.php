<?php
/**
 * Created by PhpStorm.
 * User: alek
 * Date: 5/11/16
 * Time: 00:16
 */

/**
 * @param $year {int}
 * @param $month {int}
 * @param $post_type {array}
 * @param $categories {array|string}
 *
 * @return null|int
 */
function get_post_count( $year, $month, $post_type, $categories ) {
	global $wpdb;

	$cats = implode( ', ', $categories );
	$types = "'" . implode( "','", explode( ',', $post_type ) ) . "'"; // 'post','custom'

	$sql = "SELECT COUNT(DISTINCT(ID)) FROM $wpdb->posts wpposts ";

	if ( count( $categories ) ) {
		$sql .= "JOIN $wpdb->term_relationships tr ON ( wpposts.ID = tr.object_id )
					JOIN $wpdb->term_taxonomy tt ON ( tr.term_taxonomy_id = tt.term_taxonomy_id
					AND tt.term_taxonomy_id IN(" . $cats . ") ) ";
	}
	$sql .= "WHERE post_type IN (" . $types . ")
					AND post_status IN ('publish')
					AND post_password=''
					AND YEAR(post_date) = $year
					AND MONTH(post_date) = $month";

	return $wpdb->get_var( $sql );
}


function get_first_month() {

}

<?php
/**
 * @global Arcw $arcw
 * @global WP_Locale $wp_locale
 */

// get the weekdays names array
$weekdays = getWeekDays();
?>
	<!-- This is the weekdays name over the page of months-->
	<div class="weekdays">
		<?php foreach ( $weekdays as $day ): ?>
			<span class="weekday">
				<?php echo $day; ?>
			</span>
		<?php endforeach; ?>
	</div>

	<div class="arcw-pages">
		<?php
		// get the WP option "Week starts on"
		$week_begins = intval( get_option( 'start_of_week' ) );

		$index = 0;
		// for each year
		foreach ( $arcw->dates as $year => $months ):

			// and each month of the year
			foreach ( $months as $month => $days ):

				// determine if the month is the active one
				$active = ( $year == $arcw->activeDate->year && $month == $arcw->activeDate->month );
				// build the month grid that we will use to display the month
				$grid = get_month_grid( $year, $month, $days, $week_begins );

				?>
				<div class="arcw-page month-page <?php echo $year ?>-<?php echo $month ?> <?php echo $active ? 'active' : '' ?>"
					 style="z-index:<?php echo $index; ?>">

					<?php
					echo "<div class=\"week-row\">";
					foreach ( $grid as $i => $day ) {
						if ( $i !== 0 && $i % 7 === 0 ) {
							echo '</div><!-- week end -->' . "\n" . '<div class="week-row">' . "\n";
						}
						// for year grid item display the day box
						make_day( $year, $month, $day );
					}
					echo "</div><!-- week end -->";
					?>

				</div>

				<?php
				$index ++;
			endforeach;

		endforeach;
		?>
	</div>

<?php

/**
 * Will return the text with number of posts
 *
 * @param $year
 * @param $month
 * @param $day
 *
 * @return null|string
 */
function posts_count( $year, $month, $day ) {
	global $arcw;

	if ( $arcw->config['post_count'] ) {
		$count = $arcw->get_post_count( $year, $month, $day );

		return "{$count} " . _n( 'Post', 'Posts', $count );
	}

	return null;
}

/**
 * The day template
 *
 * @param $year
 * @param $month
 * @param $day
 */
function make_day( $year, $month, $day ) {
	global $arcw;
	if ( $day ) {
		$url   = $day['has-posts'] ? $arcw->filter_link( get_day_link( $year, $month, $day['date'] ) ) : null;
		$title = date_i18n( get_option( 'date_format' ), strtotime( "{$year}-{$month}-{$day['date']}" ) );
		?>
		<span class="day <?php echo $url ? 'has-posts' : ''; ?>">
			<?php if ( $url ) {
				echo "<a href=\"$url\" title=\"$title\">";
			}

			echo $day['date'];

			if ( $url ) {
				echo "</a>";
			} ?>
		</span>
		<?php
	} else {
		echo "<span class=\"day empty\">&nbsp;</span>";
	}
}

/**
 * Return ordered list of weekday names (long => short) starting with the day
 * configured in the WP options
 * @return array
 */
function getWeekDays() {
	global $wp_locale;

	// get the WP option "Week starts on"
	// 0 => Sunday
	$week_begins = intval( get_option( 'start_of_week' ) );

	$weekdays = array();

	for ( $i = 0; $i <= 6; $i ++ ) {
		$long_name              = $wp_locale->get_weekday( ( $i + $week_begins ) % 7 );
		$weekdays[ $long_name ] = $wp_locale->get_weekday_abbrev( $long_name );
	}

	return $weekdays;

}

/**
 * Build an array for the complete month grid
 * with empty entries for the previous/next month days
 * days of the month will be array("date" => integer, "has-posts" => boolean)
 *
 * @param $year
 * @param $month
 * @param $days
 * @param $week_begins
 *
 * @return array
 */
function get_month_grid( $year, $month, $days, $week_begins ) {
	global $arcw;

	// first weekday of the month
	$firstweekday = intval( date( 'w', strtotime( $year . '-' . $month . '-01' ) ) );

	// number of days in the month
	$daysInMonth = $arcw->get_month_days( $year, $month );

	// the grid array
	$monthGrid = array();

	$k = 0; // total grid counter

	// put empty days in the grid till the month starts
	for ( $i = $week_begins; $i < $firstweekday; $i ++ ) {
		$monthGrid[] = '';
		$k ++; // increment the grid counter
	}

	// for the number of days in the month add a day array
	for ( $j = 1; $j <= $daysInMonth; $j ++ ) {
		$monthGrid[] = array(
			"date"      => $j,
			"has-posts" => in_array( $j, $days )
		);
		$k ++; // increment the grid counter
	}

	// fill the rest with empty days
	for ( $k; $k < 42; $k ++ ) {
		$monthGrid[] = '';
	}

	return $monthGrid;
}
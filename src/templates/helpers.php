<?php
/*
* HELPERS
* =======
*/

/**
 * prints the navigation title
 * with or without link
 */
function headerTite() {
	global $arcw, $wp_locale;

	$view = $arcw->config['month_view'] ? 'months' : 'years';

	if ( $view == "months" ) {
		$title = $wp_locale->get_month( $arcw->activeDate->month ) . " " . $arcw->activeDate->year;

		if ( $arcw->config['disable_title_link'] == false ) {
			$href = get_month_link( $arcw->activeDate->year, $arcw->activeDate->month );
		}
	} else {
		$title = $arcw->activeDate->year;

		if ( $arcw->config['disable_title_link'] == false ) {
			$href = get_year_link( $arcw->activeDate->year );
		}
	}

	if ( $arcw->config['disable_title_link'] == false ) {
		$href = " href=\"{$arcw->filter_link( $href )}\"";
		$tag  = 'a';
	} else {
		$href = "";
		$tag  = "span";
	}

	$format = '<%s%s class="arcw-title">%s</%s>';
	echo sprintf( $format, $tag, $href, $title, $tag );

}

/**
 * Complete the navigation list with additional data for templating
 * like title or url
 * @return mixed
 */
function prepare_nav() {
	global $arcw, $wp_locale;

// this is simple list of arrays
	$navigation = $arcw->get_navigation_list();

// we update it with some useful data for the template
	foreach ( $navigation as &$nav ) {

		if ( $arcw->config['month_view'] ) {
			$nav['active'] = $nav['year'] == $arcw->activeDate->year && $nav['month'] == $arcw->activeDate->month;
			$nav['url']    = $arcw->filter_link( get_month_link( intval( $nav['year'] ), intval( $nav['month'] ) ) );
			$nav['title']  = $wp_locale->get_month( intval( $nav['month'] ) ) . ' ' . $nav['year'];
		} else {
			$nav['active'] = $nav['year'] == $arcw->activeDate->year;
			$nav['url']    = $arcw->filter_link( get_year_link( $nav['year'] ) );
			$nav['title']  = $nav['year'];
		}
	}

	return $navigation;
}

/**
 * prints the post count of the month
 * @param $year
 * @param $month
 */
function month_posts_count( $count ) {
	if ( $count !== null ) {
		?>
		<span class="arcw-postcount">
			<span class="arcw-count-number">
				<?php echo $count ?>
			</span>
			<span class="arcw-count-text">
				<?php echo _n( 'Post', 'Posts', $count ); ?>
			</span>
		</span>
		<?php
	}
}

/**
 * Will return the text with number of posts
 *
 * @param $year
 * @param $month
 * @param $day
 *
 * @return null|string
 */
function day_posts_count( $year, $month, $day ) {
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

		$is_today = $arcw->is_today($year, $month, $day['date']);
		?>

		<span class="arcw-day-box <?php echo $url ? 'has-posts' : ''; ?> <?php echo $is_today ? "arcw-day-box--today" : ""; ?>">
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
		echo "<span class=\"arcw-day-box empty\">&nbsp;</span>";
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
function getMonthGrid( $year, $month, $days, $week_begins ) {
	global $arcw;

	// first weekday of the month
	$firstWeekday = intval( date( 'w', strtotime( $year . '-' . $month . '-01' ) ) );

	// number of days in the month
	$daysInMonth = $arcw->get_month_days_number( $month, $year );

	// the grid array
	$monthGrid = array();

	// total grid counter
	$gridCounter = 0;
	// put empty days in the grid till the month starts
	$j = $week_begins;
	while ( $j !== $firstWeekday ) {
		$monthGrid[] = '';
		$j = $j === 6 ? 0 : $j + 1;
		$gridCounter ++;
	}
	// for the number of days in the month add a day array
	for ( $j = 1; $j <= $daysInMonth; $j ++ ) {
		$monthGrid[] = array(
			"date"      => $j,
			"has-posts" => in_array( $j, $days )
		);
		$gridCounter ++; // increment the grid counter
	}
	// fill the rest with empty days
	for ( $k = $gridCounter; $k < 42; $k ++ ) {
		$monthGrid[] = '';
	}

	return $monthGrid;
}
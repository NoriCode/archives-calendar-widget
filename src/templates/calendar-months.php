<?php
/**
 * @global ARCWidget $arcw
 */

// get the weekdays names array
$weekdays = $arcw->getWeekDays();
?>
	<!-- This is the weekdays name over the page of months-->
	<div class="arcw-weekdays">
		<?php foreach ( $weekdays as $day ): ?>
			<span class="arcw-weekday">
				<?php echo $day; ?>
			</span>
		<?php endforeach; ?>
	</div>

	<div class="arcw-pages arcw-months">
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
				$grid = $arcw->getMonthGrid( $year, $month, $days, $week_begins );

				?>
				<div class="arcw-page <?php echo $active ? 'active' : '' ?>"
					 style="z-index:<?php echo $index; ?>" data-date="<?php echo $year . '-' . $month ?>">

					<?php
					echo "<div class=\"arcw-week-row\">";
					foreach ( $grid as $i => $day ) {
						if ( $i !== 0 && $i % 7 === 0 ) {
							echo '</div>' . "\n" . '<div class="arcw-week-row">' . "\n";
						}
						// for year grid item display the day box
						make_day( $year, $month, $day );
					}
					// arcw-week-row end
					echo "</div>";
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
 * The day template
 *
 * @param $year
 * @param $month
 * @param $day
 */
function make_day( $year, $month, $day ) {
	if ( $day ) {
		$url   = $day['has-posts'] ? $this->filter_link( get_day_link( $year, $month, $day['date'] ) ) : null;
		$title = date_i18n( get_option( 'date_format' ), strtotime( "{$year}-{$month}-{$day['date']}" ) );

		$is_today = $this->is_today( $year, $month, $day['date'] );
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

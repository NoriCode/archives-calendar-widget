<?php
/**
 * @global Arcw $arcw
 * @global WP_Locale $wp_locale
 */
foreach ( $arcw->dates as $year => $months ):

	$active = ( $year == $arcw->activeDate->year );

	?>
	<div class="page year <?php echo $year ?> <?php echo $active ? 'active' : '' ?>">
		<?php
		for ( $monthNum = 1; $monthNum <= 12; $monthNum ++ ):


			if ( array_key_exists( $monthNum, $months ) ) {
				$month = array(
					"url" => $arcw->filter_link( get_month_link( $year, $monthNum ) )
				);
			} else {
				$month['url'] = null;
			}

			$month['name'] = $wp_locale->get_month_abbrev( $wp_locale->get_month( $monthNum ) );

			?>
			<div class="month <?php echo $month['url'] ? "has-posts" : "" ?>">
				<?php if ( $month['url'] ) : ?>
				<a href="<?php echo $month['url']?>"
				   title="<?php echo $month['full_date']?>"
				   data-date="<?php echo $year['year'] . '-' . $monthNum ?>">
				<?php endif; ?>
					<span class="month-name">
						<?php echo $month['name'] ?>
					</span>

					<?php posts_count( $year, $monthNum ); ?>

				<?php if ( $month['url'] ) : ?>
				</a>
				<?php endif; ?>
			</div>
		<?php endfor ?>
	</div>
	<?php
endforeach;

function posts_count( $year, $month ) {
	global $arcw;

	if ( $arcw->config['post_count'] ) {
		$count = $arcw->get_post_count( $year, $month );
		?>
		<span class="postcount">
			<span class="count-number">
				<?php echo $count ?>
			</span>
			<span class="count-text">
				<?php echo _n( 'Post', 'Posts', $count ); ?>
			</span>
		</span>
		<?php
	}
}

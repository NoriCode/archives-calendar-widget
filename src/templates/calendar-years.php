<?php
/**
 * @global ARCWidget $arcw
 */
?>
	<div class="arcw-pages arcw-years">
		<?php
		$index = count( $arcw->dates );
		foreach ( $arcw->dates as $year => $months ):

			$active = ( $year == $arcw->activeDate->year );

			?>
			<div class="arcw-page <?php echo $active ? 'active' : '' ?>"
				 style="z-index:<?php echo $index; ?>">
				<?php
				echo "<div class=\"arcw-month-row\">";
				for ( $monthNum = 1; $monthNum <= 12; $monthNum ++ ):

					if ( ( $monthNum - 1 ) !== 0 && ( $monthNum - 1 ) % 4 === 0 ) {
						echo '</div>' . "\n" . '<div class="arcw-month-row">' . "\n";
					}

					if ( array_key_exists( $monthNum, $months ) ) {
						$month = array(
							"url" => $arcw->filter_link( get_month_link( $year, $monthNum ) )
						);
					} else {
						$month['url'] = null;
					}

					// get the post count if enabled then construct the title for the month box
					$post_count         = $arcw->config['post_count'] ? $arcw->get_post_count( $year, $monthNum ) : null;
					$month['full_date'] = date_i18n( get_option( 'date_format' ), strtotime( "{$year}-{$monthNum}" ) );
					if ( $post_count !== null ) {
						$month['full_date'] .= " - $post_count " . _n( 'Post', 'Posts', $post_count );
					}

					$month['name'] = $arcw->wp_locale->get_month_abbrev( $arcw->wp_locale->get_month( $monthNum ) );

					?>
					<div class="arcw-month-box <?php echo $month['url'] ? "has-posts" : "" ?>">
						<?php if ( $month['url'] ) : ?>
						<a href="<?php echo $month['url'] ?>"
						   title="<?php echo $month['full_date'] ?>"
						   data-date="<?php echo $year['year'] . '-' . $monthNum ?>">
							<?php endif; ?>
							<span class="arcw-month-name">
								<?php echo $month['name'] ?>
							</span>

							<?php
							if ( $post_count !== null ) {
								?>
								<span class="arcw-postcount">
									<span class="arcw-count-number">
										<?php echo $post_count ?>
									</span>
									<span class="arcw-count-text">
										<?php echo _n( 'Post', 'Posts', $post_count ); ?>
									</span>
								</span>
								<?php
							}
							?>

							<?php if ( $month['url'] ) : ?>
						</a>
					<?php endif; ?>
					</div>
				<?php endfor;
				// arcw-month-row end
				echo "</div>";
				?>
			</div>
			<?php
			$index --;
		endforeach;

		?>
	</div>

<?php

<?php
/**
 * @global Arcw $arcw
 * @global WP_Locale $wp_locale
 */
$view       = $arcw->config['month_view'] ? 'months' : 'years';
$navigation = prepare_nav();
$nbPages    = count( $navigation );
?>

<?php if ( $nbPages > 1 ) : ?>
	<div class="prev-page">
		<div class="btn-nav" data-nav="prev">
			<?php echo html_entity_decode( $arcw->config['prev_text'] ) ?>
		</div>
	</div>
<?php endif ?>


	<div class="menu-container <?php echo $view ?>">

		<?php headerTite() ?>

		<ul class="menu">
			<?php
			foreach ( $navigation as $nav ) {
				?>
				<li class="nav-item <?php echo $nav['active'] ? 'active' : '' ?>" data-href="<?php echo $nav['url'] ?>"><?php echo $nav['title']; ?></li>
				<?php
			}
			?>
		</ul>

		<?php if ( $nbPages > 1 ): ?>
			<div class="nav-toggle"></div>
		<?php endif ?>
	</div>

<?php if ( $nbPages > 1 ): ?>
	<div class="next-page">
		<div class="btn-nav" data-nav="next">
			<?php echo html_entity_decode( $arcw->config['next_text'] ) ?>
		</div>
	</div>
<?php endif;

/*
 * HELPERS
 * =======
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

	$format = '<%s%s class="title">%s</%s>';
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



<?php
/**
 * In case calendar.php is included in a function.
 *
 * @global Arcw $arcw
 * @global WP_Locale $wp_locale
 */

require_once 'helpers.php';
?>
<!-- Archives Calendar Widget by Aleksei Polechin - alek´ - http://alek.be -->
<div class="arcw calendar-archives  <?php echo $arcw->get_theme() ?>">
	<?php // Calendar header/navigation ?>
	<div class="arcw-nav">
		<?php include 'navigation.php'; ?>
	</div>
	<?php
	if ( $arcw->config['month_view'] ) {
		include 'calendar-months.php';
	} else {
		include 'calendar-years.php';
	}
	?>
</div>
<!-- [END] Archives Calendar Widget by Aleksei Polechin - alek´ - http://alek.be -->
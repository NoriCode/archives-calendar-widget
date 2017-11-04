<?php
/**
 * @global ARCWidget $arcw
 */

?>
<!-- Archives Calendar Widget by Aleksei Polechin - alek´ - http://alek.be -->
<div class="arcw calendar-archives  <?php echo $arcw->get_theme() ?>">
	<?php
	// Calendar header/navigation
	$arcw->render( 'templates/navigation.php' );
	// Calendar pages
	if ( $arcw->config['month_view'] ) {
		$arcw->render( 'templates/calendar-months.php' );
	} else {
		$arcw->render( 'templates/calendar-years.php' );
	}
	?>
</div>
<!-- [END] Archives Calendar Widget by Aleksei Polechin - alek´ - http://alek.be -->
<?php
/**
 * In case calendar.php is included in a function.
 *
 * @global Arcw 		$arcw
 * @global WP_Locale 	$wp_locale
 */
?>
<!-- Archives Calendar Widget by Aleksei Polechin - alek´ - http://alek.be -->
<div class="arcw calendar-archives <?php echo $arcw->get_theme() ?>">
	<?php // Calendar header/navigation ?>
	<div class="arcw-nav">
		<?php include 'header.php'; ?>
	</div>
	<div class="arcw-pages">
		<?php
		if($arcw->config['month_view']){
			include 'calendar-months.php';
		}
		else {
			include 'calendar-years.php';
		}
		?>
	</div>
</div>
<!-- [END] Archives Calendar Widget by Aleksei Polechin - alek´ - http://alek.be -->
<?php
/**
 * In case calendar.php is included in a function.
 *
 * @global Arcw 		$arcw
 * @global WP_Locale 	$wp_locale
 */
?>
<!-- Archives Calendar Widget by Aleksei Polechin - alek´ - http://alek.be -->
<div class="calendar-archives <?php echo $arcw->get_theme() ?>">
	<?php // Calendar header/navigation ?>
	<div class="calendar-navigation">
		<?php include 'header.php'; ?>
	</div>
	<div class="archives-years">
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
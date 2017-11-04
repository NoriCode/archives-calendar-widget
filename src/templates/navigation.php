<?php
/**
 * @global ARCWidget $arcw
 */

$navigation = $arcw->get_navigation();
$nbPages    = count( $navigation );
?>
<div class="arcw-nav">
<?php if ( $nbPages > 1 ) : ?>
	<div class="arcw-btn-nav" data-nav="prev">
		<?php echo html_entity_decode( $arcw->config['prev_text'] ) ?>
	</div>
<?php endif ?>


	<div class="arcw-menu-container <?php echo $arcw->get_view_mode() ?>">

		<?php $arcw->headerTite() ?>

		<ul class="arcw-menu">
			<?php
			foreach ( $navigation as $nav ) {
				?>
				<li class="arcw-nav-item <?php echo $nav['active'] ? 'active' : '' ?>"
					data-href="<?php echo $nav['url'] ?>"><?php echo $nav['title']; ?></li>
				<?php
			}
			?>
		</ul>

		<?php if ( $nbPages > 1 ): ?>
			<div class="arcw-nav-toggle"></div>
		<?php endif ?>
	</div>

<?php if ( $nbPages > 1 ): ?>
	<div class="arcw-btn-nav" data-nav="next">
		<?php echo html_entity_decode( $arcw->config['next_text'] ) ?>
	</div>
<?php endif; ?>
</div>
<?php

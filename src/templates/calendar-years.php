<?php
/**
 * @global Arcw $arcw
 * @global WP_Locale $wp_locale
 */
foreach ( $arcw->dates as $months ):
	print_r($date);
//	if($this->archiveYear && $this->archiveYear == $year['year']){
//		$current = "current";
//	}
//	$current = $index === 0 ? "current" : ""
//	?>
<!--	<div class="year --><?php //echo $year['year']; echo " $current" ?><!--" rel="--><?php //echo $index ?><!--">-->
<!--		--><?php
//		for ( $i = 0; $i < count( $year["months"] ); $i ++ ):
//			$month = $year["months"][ $i ];
//
//			?>
<!--			<div class="month --><?php //echo $month['url'] ? "has-posts" : "" ?><!--">-->
<!--				--><?php //if ( $month['url'] ) : ?>
<!--				<a href="--><?php //echo $month['url'] ?><!--" title="--><?php //echo $month['full_date'] ?><!--"-->
<!--				   data-date="--><?php //echo $year['year'] . '-' . $month['month'] ?><!--">-->
<!--					--><?php //endif; ?>
<!--					<span class="month-name">-->
<!--						--><?php //echo $month['name'] ?>
<!--					</span>-->
<!---->
<!--					--><?php //if ( $this->config['post_count'] ) {
//						postCountTmpl( $month );
//					} ?>
<!---->
<!--					--><?php //if ( $month['url'] ) : ?>
<!--				</a>-->
<!--			--><?php //endif; ?>
<!--			</div>-->
<!--		--><?php //endfor ?>
<!--	</div>-->
	<?php
endforeach;

function postCountTmpl( $item ) {
	?>
	<span class="postcount">
			<span class="count-number">
				<?php echo $item['post_count'] ?>
			</span>
			<span class="count-text">
				<?php echo _n( 'Post', 'Posts', $item['post_count'] ); ?>
			</span>
		</span>
	<?php
}

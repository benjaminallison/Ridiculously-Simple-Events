<div class="rse-events">
<?php while ($eventsQuery->have_posts()) : $eventsQuery->the_post(); ?>
	<div class="rse-event">
		<div class="rse-event-thumbnail">
			<?php $eventThumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), "thumbnail"); ?>
			<?php if ( $eventThumb ) : ?>
				<img src="<?php echo $eventThumb[0];?>" />
			<?php endif; ?>
		</div>
		<div class="rse-event-content">
			<header class="rse-event-header">
				<h5 class="rse-event-title">
					<?php echo rse_event_linked_title($post->ID, $post->post_title);?>
				</h5>
				<h6 class="rse-event-times">
					<?php rse_event_date($post->ID);?>
				</h6>
			</header>
			<div class="rse-event-body rse-event-excerpt">
				<?php if($post->post_excerpt) {?>
					<p><?php echo $post->post_excerpt;?></p>
				<?php } ?>
				<?php echo rse_event_link($post->ID, "Read More", "button");?>
			</div>
		</div>
	</div>
<?php endwhile; wp_reset_postdata(); ?>
</div>
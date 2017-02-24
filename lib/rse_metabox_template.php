<div class="rse_event_block">
	<label for="rse_event_external_link">
	<?php _e( 'External link (overrides permalink)', 'rse' ); ?>
	</label>
	<input type="text" id="rse_event_external_link" name="rse_event_external_link" class="rse_text_field" value="<?php echo esc_attr( $rse_event_external_link);?>"/>
</div>
<div class="rse_event_block">
	<div class="rse_event_block_date">
		<label for="rse_event_start_date">
		<?php _e( 'Event Start Date:', 'rse' ); ?>
		</label>
		<input type="text" id="rse_event_start_date" name="rse_event_start_date" value="<?php echo esc_attr( $rse_event_start_date );?>" class="datepicker"/>
	</div>
	<div class="rse_event_block_date">
		<label for="rse_event_end_date">
		<?php _e( 'Event End Date:', 'rse' ); ?>
		</label>
		<input type="text" id="rse_event_end_date" name="rse_event_end_date" value="<?php echo esc_attr( $rse_event_end_date );?>" class="datepicker"/>
	</div>
</div>
<div class="rse_event_block">
	<label for="rse_event_all_day">
	<?php _e( 'All Day Event (will ignore times and end date)', 'rse' ); ?>
	</label>
	<?php $rse_event_all_day_checked = ""; ?>
	<?php if ($rse_event_all_day === "yes") { $rse_event_all_day_checked = 'checked="checked"'; } ?>
	<input type="checkbox" id="rse_event_all_day" name="rse_event_all_day" class="" value="yes" <?php echo $rse_event_all_day_checked;?>/>
</div>
<div class="rse_event_block">
	<label for="rse_expiry">
	<?php _e( 'When even expires:', 'rse' ); ?>
	</label>
	<select id="rse_expiry" name="rse_expiry">
		<option value="draft"<?php selected( $rse_expiry, 'draft');?>>Set to Draft</option>
		<option value="delete"<?php selected( $rse_expiry, 'delete');?>>Set to Delete</option>
		<option value="archive"<?php selected( $rse_archive, 'archive');?>>Set to Archive</option>
	</select>
</div>
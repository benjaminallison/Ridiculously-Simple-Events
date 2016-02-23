<div class="rse_event_block">
	<?php $rse_archive_checked = ""; ?>
	<?php if ($rse_archive === "yes") { $rse_archive_checked = 'checked="checked"'; } ?>
	<input type="checkbox" id="rse_archive" name="rse_archive" class="" value="yes" <?php echo $rse_archive_checked;?>/>
	<label for="rse_archive">
	<?php _e( 'Archived event?', 'rse' ); ?>
	</label>
</div>
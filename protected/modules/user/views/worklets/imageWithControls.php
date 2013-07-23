<div class="clearfix">
	<div class="editableImage">
		<img src="<?php echo $src; ?>">
		<div class="controls">
		<?php foreach($controls as $label=>$url) { ?>
			<a href="<?php echo $url; ?>"><?php echo $label; ?></a>
		<?php } ?>
		</div>
	</div>
</div>
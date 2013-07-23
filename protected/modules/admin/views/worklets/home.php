<div class='info'>
	<?php foreach($info as $k=>$v){ ?>
	<div class='row'>
		<label><?php echo txt()->format($k,':'); ?></label>
		<?php echo $v; ?>
	</div>
	<?php } ?>	
	<hr />
	<?php foreach($stats as $k=>$v){ ?>
	<div class='row'>
		<label><?php echo txt()->format($k,':'); ?></label>
		<?php echo $v; ?>
	</div>
	<?php } ?>
</div>
<div class='grid-view'>
	<table class='items'>
		<tr><td colspan='4'><h3><?php echo $this->t('Application'); ?></h3></td></tr>
		<?php
		$this->render('_thead');
		$this->render('_row', array('i' => 0, 'app' => $app));
		if(!$app['install']):
		
		?>
		<tr><td colspan='4'><h3><?php echo $this->t('Application Modules'); ?></h3></td></tr>
		<?php
		$this->render('_thead');
		foreach($appModules as $i=>$m)
			$this->render('_row', array('i' => $i, 'app' => $m));	
		?>
		<tr><td colspan='4'><h3><?php echo $this->t('Add-On/Custom Modules'); ?></h3></td></tr>
		<?php
		$this->render('_thead');
		foreach($customModules as $i=>$m)
			$this->render('_row', array('i' => $i, 'app' => $m));
			
		endif;
		?>
	</table>
</div>
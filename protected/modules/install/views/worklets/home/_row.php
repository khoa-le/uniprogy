<tr class='<?php echo $i%2==0?'odd':'even'; ?>'>
	<td><?php echo $app['title']; ?></td>
	<td><?php echo $app['version']; ?></td>
	<td><?php echo CHtml::submitButton($app['install']?$this->t('Install'):$this->t('Re-Install'),
		array('name' => 'install.'.$app['id'])); ?></td>
	<td><?php echo $app['upgrade']?CHtml::submitButton($this->t('Upgrade'), array('name' => 'upgrade.'.$app['id'])):'&nbsp;'; ?></td>
</tr>
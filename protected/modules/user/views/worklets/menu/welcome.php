<div class="column welcome"><div>
	<?php echo $this->t('Hi {user}!',array('{user}'=>$model->name)); ?>
	<?php
		if($model->avatar)
			echo CHtml::image(app()->storage->bin($model->avatarBin)->getFileUrl('original'), $model->name, array('align' => 'right'));
	?>
</div></div>
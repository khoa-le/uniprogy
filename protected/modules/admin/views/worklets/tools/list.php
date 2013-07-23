<?php
foreach($this->tools as $id)
{
	$w = wm()->get($id);
	?><hr /><h3><?php echo $w->title(); ?></h3>
	<p><?php echo $w->description(); ?></p>
	<p><?php echo CHtml::link($this->t('Use this tool'),url($w->getPath())); ?></p><?php
}
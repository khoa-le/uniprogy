<?php
class WBaseMenu extends UMenuWorklet
{
	public $space = 'content';
	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function properties()
	{
		return array(
			'items'=>array(
				array('label'=>$this->t('Home'), 'url'=>aUrl('/')),
				array('label'=>$this->t('How {site} Works', array('{site}'=>app()->name)), 'url'=>url('base/page', array('view' => 'how-it-works'))),
			),
			'htmlOptions'=>array(
				'class' => 'horizontal clearfix'
			)
		);
	}
}
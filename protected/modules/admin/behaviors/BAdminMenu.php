<?php
class BAdminMenu extends UWorkletBehavior
{
	public function afterConfig()
	{
		$this->getOwner()->insert('bottom',array(
			array('label'=>$this->getModule()->t('Admin Console'), 'url'=>array('/admin'),
				'visible' => app()->user->checkAccess('administrator')),
		));
	}
}
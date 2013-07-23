<?php
class BUserAdminMenu extends UWorkletBehavior
{
	public function afterConfig()
	{
		$this->getOwner()->insertBefore('Logout',array(
			array('label'=>$this->getModule()->t('Users'), 'url'=>array('/user/admin/list'),
				'visible' => app()->user->checkAccess('administrator')),
		));
	}
}
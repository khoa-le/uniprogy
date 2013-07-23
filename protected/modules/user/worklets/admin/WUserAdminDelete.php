<?php
class WUserAdminDelete extends UDeleteWorklet
{
	public $modelClassName = 'MUser';
	
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('administrator')),
			array('deny', 'users'=>array('*'))
		);
	}
	
	public function taskDelete($id)
	{
		$model = CActiveRecord::model($this->modelClassName)->findByPk($id);
		if($model->role == 'administrator' && $this->adminCount() == 1)
		{
			if($this->isSingle)
				$this->accessDenied(null,$this->t('You can\'t delete the last administrator account.'));
			return false;
		}
		
		if($model->avatar)
		{
			$bin = app()->storage->bin($model->avatar);
			if($bin)
				$bin->purge();
		}
		parent::taskDelete($id);
	}
	
	public function adminCount()
	{
		return MUser::model()->count('role=?',array('administrator'));
	}
}
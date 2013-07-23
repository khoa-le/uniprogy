<?php
Yii::import('application.modules.base.worklets.auth.WBaseAuthWebUser');
class WUserAuthWebUser extends WBaseAuthWebUser
{	
	public function taskModel($refresh=false)
	{
		static $model;
		if(!$this->owner->isGuest && (!isset($model) || $refresh))
			$model = MUser::model()->findByPk($this->owner->id);
		return $model;
	}
	
	public function taskGetRole()
	{
		$r = $this->modelData('role');
		return $r?$r:'guest';
	}
	
	public function taskInit()
	{
		$this->owner->loginUrl = array('/user/login');
	}
	
	public function taskModelData($name)
	{
		$m = $this->model();
		return $m?$m->$name:null;
	}
	
	public function taskLogin($identity,$duration=0)
	{
		static $call;
		if(!isset($call))
		{
			$call = true;
			$this->owner->login($identity,$duration);
			$this->model(true);
			app()->authManager->assign(app()->user->role, app()->user->id);
		}
		return null;
	}
}
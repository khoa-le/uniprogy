<?php
class WAdminCron extends UWidgetWorklet
{	
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('administrator')),
			array('deny', 'users'=>array('*'))
		);
	}
	
	public function title()
	{
		return $this->t('Crons Execution Results');
	}
	
	public function beforeAccess()
	{
		if(app()->user->checkAccess('administrator'))
			return;
			
		if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
		{
			if($this->login($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']))
				return;
		}
		Header('WWW-Authenticate: Basic realm="' .$this->t('Admin Panel Login'). '"');  
       	throw new CHttpException(401,Yii::t('yii','You are not authorized to perform this action.'));
	}
	
	public function taskLogin($login,$password)
	{
		$identity=new UUserIdentity($login,$password);
		$errorString = $identity->authenticate();
			
		if(is_string($errorString))
			return false;
		
		switch($identity->errorCode)
		{
			case UUserIdentity::ERROR_NONE:
				app()->user->login($identity,0);
				break;
			case UUserIdentity::ERROR_USERNAME_INVALID:
				return false;
				break;
			case UUserIdentity::ERROR_PASSWORD_INVALID:
				return false;
				break;
		}

		if(!app()->user->checkAccess('administrator',array(),false))
			return false;
			
		return true;
	}
	
	public function taskRenderOutput()
	{
		$output = $this->t('Running Crons on {date}.',array('{date}'=>app()->getDateFormatter()->formatDateTime(time(),'full','full')))."\n\n";
		$modules = app()->getModules();
		foreach($modules as $m=>$d)
		{
			if($m == 'admin')
				continue;
			$w = wm()->get($m.'.cron');
			if($w)
				$output.= $w->run();
		}
		echo app()->format->ntext($output);
	}
	
	public function taskBreadCrumbs()
	{
		return array(
			$this->t('Tools') => url('/admin/tools'),
			wm()->get('admin.tools.cron')->title() => url('/admin/tools/cron'),
			$this->title()
		);
	}
}
<?php
class WUserVerify extends UWidgetWorklet
{	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function title()
	{
		return $this->t('Email Verification');
	}
	
	public function taskConfig()
	{
		if(isset($_GET['h'], $_GET['e'])) {
			$hash = MHash::model()->find('hash=? AND type=? AND expire>=?', array($_GET['h'], 1, time()));
			$user = MUser::model()->find('email=?', array($_GET['e']));
			if($hash && $user && $hash->id == $user->id) {
				$user->role = $this->getModule()->params['approveNewAccounts'] ? 'unapproved' : 'user';
				$user->save();
				$hash->delete();
				return true;
			}
		}
		throw new CHttpException(403, $this->t('Invalid verification link! Email not verified.'));
	}
	
	public function taskRenderOutput()
	{
		echo $this->t('Email has been successfully verified! You can now {login} to your account.', array(
			'{login}' => CHtml::link($this->t('login'), url('/user/login'))
		));
	}
}
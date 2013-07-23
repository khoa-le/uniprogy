<?php
class WUserReset extends UFormWorklet
{	
	public $modelClassName = 'MUserResetForm';
	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function title()
	{
		return $this->t('Reset Password');
	}
	
	public function properties()
	{
		return array(
			'action' => array('/user/reset'),
			'elements' => array(
				'hash' => array('type' => 'hidden'),
				'user' => array('type' => 'hidden'),
				'newPassword' => array('type' => 'password'),
				'passwordRepeat' => array('type' => 'password'),
			),
			'buttons' => array(
				'submit' => array('type' => 'submit',
					'label' => $this->t('Reset Password'))
			),
			'model' => $this->model
		);
	}
	
	public function successUrl()
	{
		return url('/user/login');
	}
	
	public function taskProcess()
	{
		parent::taskProcess();
		if(!$this->form->submitted() && isset($_GET['h']) && isset($_GET['u'])) {
			$this->form->model->hash = $_GET['h'];
			$this->form->model->user = $_GET['u'];
		}
	}
	
	public function taskSave()
	{
		$valid = parent::taskSave();
		
		$data = $this->form->model;
		
		$hash = MHash::model()->find('hash=?', array($data->hash));
		$user = MUser::model()->findByPk($hash->id);
		
		if($user instanceOf MUser) {
			$user->password = $data->newPassword;
			$user->scenario = 'password';
			$user->save();
			
			$hash->delete();
			
			// send login info changed email			
			app()->mailer->send($user, 'loginInfoChangedEmail', array(
					'email' => $user->email,
					'password' => $data->newPassword
				)
			);
			
			app()->user->setFlash('info', $this->t('Password has been changed.'));
		}
		else
			$this->model->addError('hash', $this->t('Account not found.'));
		
		return $valid && true;
	}
}
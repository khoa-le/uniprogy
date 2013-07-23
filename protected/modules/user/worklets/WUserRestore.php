<?php
class WUserRestore extends UFormWorklet
{	
	public $modelClassName = 'MUserRestoreForm';
	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function title()
	{
		return $this->t('Restore Password');
	}
	
	public function properties()
	{
		return array(
			'action' => array('/user/restore'),
			'elements' => array(
				'email' => array('type' => 'text'),
			),
			'buttons' => array(
				'submit' => array('type' => 'submit',
					'label' => $this->t('Restore Password'))
			),
			'model' => $this->model
		);
	}
	
	public function successUrl()
	{
		return url('/user/login');
	}
	
	public function taskSave()
	{
		$valid = parent::taskSave();
		$user = MUser::model()->find('LOWER(email)=:u', array(
			':u' => strtolower($this->form->model->email)
		));
		
		if($user instanceOf MUser) {
			$hash = UHelper::hash();
			$model = new MHash;
			$model->hash = $hash;
			$model->type = 2;
			$model->id = $user->id;
			$model->expire = m('user')->params['passwordResetTimeLimit']
				? time() + m('user')->params['passwordResetTimeLimit'] * 3600
				: 0;
			$model->save();
			
			// send password restore email
			app()->mailer->send($user, 'passwordRestoreEmail', array('link' => aUrl(
				'/user/reset', array(
					'h' => $hash,
					'u' => $user->id,
				)))
			);
			app()->user->setFlash('info', $this->t('We have emailed you a link that will help you to reset your password.'));
		}
		else
			$this->form->model->addError('email', $this->t('Account not found.'));
		return $valid && true;
	}
}
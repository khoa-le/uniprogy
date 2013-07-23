<?php
class WUserResend extends UFormWorklet
{	
	public $modelClassName = 'MUserRestoreForm';
	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function title()
	{
		return $this->t('Resend Verification Email');
	}
	
	public function properties()
	{
		return array(
			'action' => array('/user/resend'),
			'elements' => array(
				'email' => array('type' => 'text'),
			),
			'buttons' => array(
				'submit' => array('type' => 'submit',
					'label' => $this->t('Resend'),
					'class' => 'wide')
			),
			'model' => $this->model
		);
	}
	
	public function taskSave()
	{
		$valid = parent::taskSave();
		$user = MUser::model()->find('LOWER(email)=:u', array(
			':u' => $this->form->model->email
		));
		
		if($user->role != 'unverified') {
			$this->form->model->addError('email', $this->t('Your account doesn\'t need to be verified.'));
			return false;
		}
		
		if($user instanceOf MUser) {			
			$hash = MHash::model()->find('id=? AND type=? AND expire >= ?',array(
				$user->id, 1, time()
			));
			
			// send verification email
			app()->mailer->send($user, 'verificationEmail', array('link' => aUrl(
				'/user/verify', array(
					'h' => $hash->hash,
					'e' => $user->email,
				)))
			);
			
			app()->user->setFlash('info', $this->t('Your verification email has been resent.'));
		}
		return $valid && true;
	}
}
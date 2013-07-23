<?php
class WBaseContact extends UFormWorklet
{
	public $modelClassName = 'MBaseContactForm';
	
	public function accessRules()
	{
		return array(array('allow','users'=>array('*')));
	}
	
	public function title()
	{
		return $this->t('Contact Us');
	}
	
	public function properties()
	{
		return array(
			'elements' => array(
				'name' => array('type' => 'text'),
				'email' => array('type' => 'text'),
				'subject' => array('type' => 'text'),
				'message' => array('type' => 'textarea'),				
			),
			'buttons' => array(
				'submit' => array('type' => 'submit',
					'label' => $this->t('Send'))
			),
			'model' => $this->model
		);
	}
	
	public function beforeBuild()
	{
		$this->attachBehavior('base.captcha','base.captcha');
	}
	
	public function taskSave()
	{
		app()->mailer->send(param('systemEmail'), 'contactEmail', $this->model->attributes);
		return parent::taskSave();
	}
	
	public function ajaxSuccess()
	{
		wm()->get('base.init')->addToJson(array('info' => array(
			'replace' => $this->t('Your message has been successfully sent!'),
			'fade' => 'target',
			'focus' => true
		)));
	}
}
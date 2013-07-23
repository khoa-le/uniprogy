<?php
class MBaseContactForm extends UFormModel
{
	public $name;
	public $email;
	public $subject;
	public $message;
	
	public function module()
	{
		return 'base';
	}
	
	public function rules()
	{
		return array(
			array(implode(',',array_keys(get_object_vars($this))),'required'),
			array('email','email'),
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'name' => $this->t('Full Name'),
			'email' => $this->t('Email Address'),
			'subject' => $this->t('Subject'),
			'message' => $this->t('Message'),
		);
	}
}
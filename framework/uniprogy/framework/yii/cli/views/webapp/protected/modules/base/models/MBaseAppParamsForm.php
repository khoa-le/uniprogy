<?php
class MBaseAppParamsForm extends UFormModel
{
	public $adminUrl;
	public $name;
	public $publicAccess;
	public $maxCacheDuration;
	public $systemEmail;
	public $contactEmail;
	public $newsletterEmail;
	public $htmlEmails;
	public $keywords;
	public $description;
	
	public function module()
	{
		return 'base';
	}
	
	public function rules()
	{
		return array(
			array(implode(',',array_keys(get_object_vars($this))),'safe')
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'name' => $this->t('Site Name'),
			'adminUrl' => $this->t('Admin Panel URL'),
			'publicAccess' => $this->t('Visitor Access'),
			'maxCacheDuration' => $this->t('Maximum Cache Duration'),
			'systemEmail' => $this->t('Site "no-reply" Email'),	
			'contactEmail' => $this->t('Site "Contact Us" Email'),	
			'newsletterEmail' => $this->t('Newsletter Email'),	
			'htmlEmails' => $this->t('Send HTML Emails'),	
			'keywords' => $this->t('Keywords (meta tag)'),
			'description' => $this->t('Description (meta tag)'),
		);
	}
}
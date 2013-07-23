<?php
class WBaseAdminAppParams extends UParamsWorklet
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
		return $this->t('General Settings');
	}
	
	public function properties()
	{
		return array(
			'elements' => array(
				'name' => array('type' => 'text'),
				'adminUrl' => array('type' => 'text', 'hint' => aUrl('/') . '/', 'layout' => "{label}\n<span class='hintInlineBefore'>{hint}\n{input}</span>"),
				'publicAccess' => array('type' => 'radiolist', 'items' => array(
					1 => $this->t('Allow visitors to browse the whole site'),
					0 => $this->t('Allow visitors to access only home and registration pages')
				), 'layout' => "{label}\n<fieldset>{input}\n{hint}</fieldset>"),
				'maxCacheDuration' => array('type' => 'text', 'hint' => $this->t('seconds'),
					'class' => 'short', 'layout' => "{label}\n<span class='hintInlineAfter'>{input}\n{hint}</span>"),
				'systemEmail' => array('type' => 'text'),
				'contactEmail' => array('type' => 'text'),
				'newsletterEmail' => array('type' => 'text'),
				'htmlEmails' => array('type' => 'radiolist', 'items' => array(
					1 => $this->t('Send HTML emails'),
					0 => $this->t('Send plain text emails only')
				), 'layout' => "{label}\n<fieldset>{input}\n{hint}</fieldset>"),
				'keywords' => array('type' => 'textarea'),
				'description' => array('type' => 'textarea'),
			),
			'buttons' => array(
				'submit' => array('type' => 'submit', 'label' => $this->t('Save'))
			),
			'model' => $this->model
		);
	}
	
	public function taskConfig()
	{
		$modelClassName = 'MBaseAppParamsForm';
		$this->model = new $modelClassName;
		$config = require($GLOBALS['config']);
		$this->model->attributes = $config['params'];
		$this->model->name = $config['name'];
		parent::taskConfig();
	}
	
	public function taskSave()
	{
		$models = $this->form->getModels();
		foreach($models as $model)
		{
			$params = $model->attributes;
			$config=array();
			if(isset($params['name'])) {
				$config['name'] = $params['name'];
				unset($params['name']);
			}
			$config['params'] = $params;
			UHelper::saveConfig(app()->basePath .DS. 'config' .DS. 'public' .DS. 'modules.php',$config);
		}
	}
}
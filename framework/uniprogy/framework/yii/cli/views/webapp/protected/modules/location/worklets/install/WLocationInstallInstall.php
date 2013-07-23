<?php
class WLocationInstallInstall extends UInstallWorklet
{
	public $modelClassName = 'MLocationParamsForm';
	public $autoSubmit = false;
	
	public function init()
	{
		Yii::import('application.modules.location.models.MLocationParamsForm');
		Yii::import('application.modules.location.models.MLocationForm');
		return parent::init();
	}
	
	public function properties()
	{
		return array(
			'elements' => array(
				'<h4>Default Location</h4>',
			),
			'buttons' => array(
				'submit' => array('type' => 'submit',
					'label' => $this->t('Save'))
			),
			'model' => $this->model
		);
	}
	
	public function beforeBuild()
	{
		$b = $this->attachBehavior('location.form','location.form');
		$b->insert = array('after' => '__BOTTOM__');
	}
	
	public function taskModuleParams()
	{
		return CMap::mergeArray(parent::taskModuleParams(),array(
			'defaultCountry' => '*',
	        'location' => $this->model->location,
		));
	}
}
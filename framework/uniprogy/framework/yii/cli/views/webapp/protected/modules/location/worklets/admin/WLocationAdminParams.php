<?php
class WLocationAdminParams extends UParamsWorklet
{
	public function title()
	{
		return txt()->format(ucfirst($this->module->name),' ',$this->t('Module'));
	}
	
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('administrator')),
			array('deny', 'users'=>array('*'))
		);
	}
	
	public function properties()
	{
		$countries = CHtml::listData(wm()->get('location.helper')->loadCountries(),'code','name');
		$countries = array('*' => $this->t('All Countries')) + $countries;
		return array(
			'elements' => array(
				'<h4>Other Settings</h4>',
				'defaultCountry' => array('type' => 'dropdownlist',
					'items' => $countries,
					'label' => $this->t('Supported Country')),
			),
			'buttons' => array(
				'submit' => array('type' => 'submit', 'label' => $this->t('Save'))
			),
			'model' => $this->model
		);
	}
	
	public function beforeBuild()
	{
		$b = $this->attachBehavior('location.form','location.form');
	}
	
	public function beforeCreateForm()
	{
		$this->insertBefore('location', array('locTitle'=>array('type' => 'UForm','elements'=>array('<h4>Default Location</h4>'))));
	}
}
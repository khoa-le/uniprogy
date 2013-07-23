<?php
class WAdminToolsCron extends UFormWorklet
{	
	public $modelClassName = 'UDummyModel';
	
	public $files;
	public $data;
	public $types = array('css','js');
	
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('administrator')),
			array('deny', 'users'=>array('*'))
		);
	}
	
	public function title()
	{
		return $this->t('Manually Run Crons');
	}
	
	public function description()
	{
		return $this->t('If for any reason you can\'t setup crons to run automatically on your server you can use this tool to execute them manually.');
	}
	
	public function properties()
	{
		return array(
			'activeForm' => array(
				'class'=>'UActiveForm',
				'ajax'=>false
			),
			'description' => $this->description(),
			'elements' => array(),
			'model' => $this->model,
			'buttons' => array(
				'submit' => array('type' => 'submit', 'label' => $this->t('Run Crons')),
			),
		);
	}
	
	public function taskSave()
	{
		wm()->add('admin.cron');
		$this->show = false;
	}
	
	public function beforeSuccess()
	{
		return false;
	}
	
	public function taskBreadCrumbs()
	{
		return array(
			$this->t('Tools') => url('/admin/tools'),
			$this->title()
		);
	}
}
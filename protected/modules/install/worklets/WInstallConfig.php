<?php
class WInstallConfig extends UFormWorklet
{
	public $modelClassName = 'UDummyModel';
	public $permissions;
	
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('administrator')),
			array('deny', 'users'=>array('*'))
		);
	}
	
	public function title()
	{
		return $this->t('Installation');
	}
	
	public function properties()
	{
		return array(
			'elements' => array(				
				'attribute' => array('type' => 'hidden'),
			),
			'buttons' => array(
				'submit' => array('type' => 'submit', 'label' => $this->t('Continue'))
			),
			'model' => $this->model
		);
	}
	
	public function taskConfig()
	{
		$this->permissions = array(
			Yii::getPathOfAlias('webroot.assets') => '0777',
			Yii::getPathOfAlias('webroot.storage') => '0777',
			Yii::getPathOfAlias('application.runtime') => '0777',
			Yii::getPathOfAlias('application.config.auth') . '.php' => '0777',
			Yii::getPathOfAlias('application.config.modules') . '.php' => '0777',
		);
		return parent::taskConfig();
	}
	
	public function taskRenderOutput()
	{
		$this->render('config');
		return parent::taskRenderOutput();
	}
	
	public function taskSave()
	{
		foreach($this->permissions as $item=>$p)
		{
			if(app()->file->set($item)->permissions != $p)
			{
				$this->model->addError('attribute',$this->t('Item {item} permissions are set incorrectly.', array(
					'{item}' => $item
				)));
			}
		}
		
		try
		{
			app()->db->active;
		}
		catch(Exception $e)
		{
			$this->model->addError('attribute', $this->t('Unable to connect to database. Please verify your configuration.'));
		}
	}
	
	public function successUrl()
	{
		return url('/install/process');
	}
}
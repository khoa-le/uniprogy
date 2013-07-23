<?php
/**
 * UParamsWorklet class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UParamsWorklet is a special worklet which is used on admin/setup page.
 * It renders a form which includes all settings of a particular module which
 * administrator can modify.
 *
 * @version $Id: UParamsWorklet.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UParamsWorklet extends UFormWorklet
{
	/**
	 * @var CModel model object.
	 */
	public $model;
	/**
	 * @var string model class name.
	 */
	public $modelClassName;
	
	/**
	 * @return string worklet title.
	 */
	public function title()
	{
		$title = txt()->format($this->module->title,' ',$this->t('Module'));
		if(!$this->moduleEnabled())
			$title.= ' ['.$this->t('Disabled').']';
			
		return $title;
	}
	
	/**
	 * Allow access to administrator only.
	 * @return boolean
	 */
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('administrator')),
			array('deny', 'users'=>array('*'))
		);
	}
	
	/**
	 * Configures worklet.
	 */
	public function taskConfig()
	{
		if($this->model===null)
		{
			$modelClassName = $this->modelClassName===null
				? 'M' . ucfirst($this->module->name) . 'ParamsForm'
				: $this->modelClassName;
			Yii::import($this->module->name.'.models.'.$modelClassName);
			$this->model = new $modelClassName;
			$this->model->attributes = $this->module->getParams()->toArray();
		}
		parent::taskConfig();
		$this->properties['action'] = array('/'.str_replace('.','/',$this->id));
	}
		
	/**
	 * Overrides {@link UFormWorklet::taskSave} task.
	 * Saves submitted data into a configuration file, rather then DB.
	 */
	public function taskSave()
	{
		$configTrace = '';
		$m = $this->module;
		while($m)
		{
			$configTrace = '["modules"]["'.$m->name.'"]' . $configTrace;
			$m = $m->getParentModule();
		}
		$configTrace = '$config' . $configTrace . '["params"] = ';
		
		$models = $this->form->getModels();
		foreach($models as $model)
		{
			eval($configTrace.'$model->attributes;');			
			UHelper::saveConfig(app()->basePath .DS. 'config' .DS. 'public' .DS. 'modules.php',$config);
		}
	}
	
	/**
	 * Overrides {@link UFormWorklet::ajaxSuccess} method.
	 * Adds 'info' JSON command instead of redirecting user.
	 */
	public function ajaxSuccess()
	{
		wm()->get('base.init')->addToJson(array('info' => array(
			'replace' => $this->t('Settings have been successfully saved!'),
			'fade' => 'target',
			'focus' => 'true',
		)));
	}
	
	/**
	 * @return string success URL.
	 */
	public function successUrl()
	{
		return url('/admin/setup');
	}
	
	public function taskModuleEnabled()
	{
		$alias = UFactory::getModuleAlias($this->module);
		$trace = '$config["modules"]["'.str_replace('.','"]["',$alias).'"]["enabled"]';
		$command = 'isset('.$trace.') ? '.$trace.' : true';
		return $this->evaluateExpression($command, 
			array('config' => require(Yii::getPathOfAlias('application.config.public.modules').'.php')));
	}
}
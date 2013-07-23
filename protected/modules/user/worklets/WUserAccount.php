<?php
class WUserAccount extends UFormWorklet
{
	public $modelClassName = 'MUserAccountForm';
	public $space = 'inside';
	
	public function beforeBuild()
	{
		if(!app()->request->isAjaxRequest)
		{
			$w = wm()->add('user.accountTabs');
			$w->select = 1;
			$this->show = false;
			return false;
		}
		else
			wm()->add('base.dialog');
	}
	
	public function title()
	{
		return $this->t('My Account');
	}
	
	public function accessRules()
	{
		return array(
			array('deny', 'users'=>array('?'))
		);
	}
	
	public function properties()
	{		
		$avatar = null;
		if($this->model->avatar)
		{
			$bin = app()->storage->bin($this->model->avatar);
			if($bin)
			{
				$avatar = $this->render('imageWithControls', array(
					'src' => $bin->getFileUrl('original').'?_r='.time(),
					'controls' => array(
						$this->t('Delete') => url('/user/avatar', array('delete'=>1))
					),
				), true);
			}
		}
		
		$pwdBlockId = 'pwdB_'.CHtml::$count++;
		
		return array(
			'elements' => array(
				'avatar' => array('type' => 'UUploadField', 'attributes' => array(
					'content' => $avatar, 
					'label' => $this->t('Upload'),
					'url' => url('/user/avatar',
						array(
							'binField'=>CHtml::getIdByName(CHtml::activeName($this->model,'avatar')),
						)),
				), 'layout' => "{label}\n<fieldset>{input}</fieldset>\n{hint}"),
				'firstName' => array('type'=>'text'),
				'lastName' => array('type'=>'text'),
				'email' => array('type'=>'text'),
				'password' => array('type'=>'UJsButton', 'attributes'=>array(
					'label'=>$this->t('Change'),
					'callback'=>'$("#' . $pwdBlockId . '").toggle();'
				)),
				'<div id="' .$pwdBlockId. '" style="display:none">',
				'newPassword' => array('type'=>'password'),
				'passwordRepeat' => array('type'=>'password'),
				'</div>',
				'timeZone' => array('type'=>'dropdownlist',
					'items' => include(app()->basePath.DS.'data'.DS.'timezones.php')),
			),
			'buttons' => array(
				'submit' => array('type' => 'submit', 'label' => 'Update'),
			),
			'model' => $this->model
		);
	}
	
	public function taskModel()
	{
		if($this->modelClassName===null)
			return false;
			
		$id = app()->user->id;
		$this->model = $id
			? CActiveRecord::model($this->modelClassName)->findByPk($id)
			: new $this->modelClassName;
	}
	
	public function ajaxSuccess()
	{
		wm()->get('base.init')->addToJson(array('info' => array(
			'replace' => $this->t('Account has been successfully updated.'),
			'fade' => 'target',
			'focus' => true,
		)));
	}
}
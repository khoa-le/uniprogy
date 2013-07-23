<?php
class WUserAdminUpdate extends UFormWorklet
{
	public $modelClassName = 'MUserAccountForm';
	public $primaryKey='id';
	private $_tmp;
	
	public function title()
	{
		return $this->isNewRecord
			? $this->t('Create User')
			: $this->t('Edit User');
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
		$avatar = null;
		if($this->model->avatar)
		{
			$bin = app()->storage->bin($this->model->avatar);
			if($bin)
			{
				$avatar = $this->render('imageWithControls', array(
					'src' => $bin->getFileUrl('original').'?_r='.time(),
					'controls' => array(
						$this->t('Delete') => url('/user/avatar', array('id'=>app()->request->getParam('id',null),'delete'=>1))
					),
				), true);
			}
		}
		
		$pwdBlockId = 'pwdB_'.CHtml::$count++;
		
		return array(
			'elements' => array(
				'role' => array('type'=>'dropdownlist',
					'items'=>wm()->get('user.admin.helper')->roles()),
				'avatar' => array('type' => 'UUploadField', 'attributes' => array(
					'content' => $avatar, 
					'label' => $this->t('Upload'),
					'url' => url('/user/avatar',
						array(
							'binField'=>CHtml::getIdByName(CHtml::activeName($this->model,'avatar')),
							'id'=>app()->request->getParam('id',null),
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
	
	public function beforeBuild()
	{
		wm()->add('base.dialog');
		wm()->add('user.admin.menu');
	}
	
	public function afterConfig()
	{
		if($this->isNewRecord)
			unset($this->properties['elements']['avatar']);
	}
	
	public function taskBreadCrumbs()
	{
		return array(
			$this->t('Users') => url('/user/admin/list'),
			$this->title
		);
	}
	
	public function afterModel()
	{
		$this->model->setScenario('admin');
		$this->_tmp = $this->model->role;
	}
	
	public function beforeSave()
	{
		if($this->_tmp == 'administrator'
			&& $this->form->model->role != 'administrator')
		{
			if(MUser::model()->count('role=?',array('administrator'))==1)
			{
				$this->model->addError('role',
					$this->t('This is the last administrator in the system. You can\'t assign this account to another role.'));
				return false;
			}
		}
	}
	
	public function ajaxSuccess()
	{
		$message = $this->isNewRecord
			? $this->t('User has been successfully created.')
			: $this->t('User account has been successfully updated.');
		wm()->get('base.init')->addToJson(array('info' => array(
			'replace' => $message,
			'fade' => 'target',
			'focus' => true,
		)));
		if($this->isNewRecord)
			wm()->get('base.init')->addToJson(array('redirect' => url('/user/admin/update',array('id'=>$this->model->id))));
	}
}
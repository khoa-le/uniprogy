<?php
class MAdminToolsThemeModel extends UFormModel
{
	public $name;
	
	public function module()
	{
		return 'admin';
	}
	
	public function rules()
	{
		return array(
			array('name','required'),
			array('name','alphanumeric'),
		);
	}
	
	public function beforeValidate()
	{
		$this->name = ucfirst(strtolower($this->name));
		return parent::beforeValidate();
	}
	
	public function alphanumeric($attribute,$params)
	{
		if(preg_match("/^[^A-Za-z]/",$this->name))
			$this->addError('name','Name must start with a letter.');
		if(preg_match("/[^a-zA-Z|0-9|-|_]/",$this->name))
			$this->addError('name','Name can contain latin letters, numbers, "-" and "_" symbols only.');
	}
	
	public function attributeLabels()
	{
		return array(
			'name' => $this->t('Theme Name'),
		);
	}
}
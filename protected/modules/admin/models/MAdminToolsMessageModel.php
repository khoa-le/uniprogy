<?php
class MAdminToolsMessageModel extends UFormModel
{
	public $path;
	public $language;
	
	public function module()
	{
		return 'admin';
	}
	
	public function rules()
	{
		return array(
			array('path','required'),
			array('language','safe')
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'path' => $this->t('Source Path'),
			'language' => $this->t('Language')
		);
	}
}
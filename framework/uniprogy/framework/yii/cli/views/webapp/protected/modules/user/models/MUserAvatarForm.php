<?php
class MUserAvatarForm extends UFormModel
{
	public $avatar;
	
	public function module()
	{
		return 'user';
	}
	
	public function rules()
	{
		return array(
			array('avatar', 'file',
				'types'=>m('user')->params['fileTypes'],
				'maxSize'=>m('user')->params['fileSizeLimit'] * 1024 * 1024,
			),
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'avatar' => $this->t('Select Image to Upload'),
		);
	}
}
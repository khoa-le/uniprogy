<?php
class MLocationForm extends UFormModel
{
	public $country;
	public $state;
	public $city;
	
	public function module()
	{
		return 'location';
	}
	
	public function rules()
	{
		return array(
			array('country,state,city','safe'),
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'country' => $this->t('Country'),
			'state' => $this->t('State'),
			'city' => $this->t('City'),
		);
	}
}
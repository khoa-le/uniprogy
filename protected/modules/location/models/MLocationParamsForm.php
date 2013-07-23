<?php
class MLocationParamsForm extends UFormModel
{
	public $defaultCountry;
	public $location;
	
	public function module()
	{
		return 'location';
	}
	
	public function rules()
	{
		return array(
			array(implode(',',array_keys(get_object_vars($this))),'safe')
		);
	}
}
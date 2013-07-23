<?php
class MLocation extends UActiveRecord
{
	public function module()
	{
		return 'location';
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function tableName()
	{
		return '{{Location}}';
	}

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('country,state,city','safe'),
			array('id, country, state, city', 'safe', 'on'=>'search'),
		);
	}
}
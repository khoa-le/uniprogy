<?php
class MStorageFile extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{StorageFile}}';
	}

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, binId, name, hash, extension, created, size', 'safe', 'on'=>'search'),
		);
	}
	
	public function behaviors()
	{
		return array(
			'TimestampBehavior' => array(
				'class' => 'UTimestampBehavior',
				'modified' => null,
			)
		);
	}
}
<?php
class MStorageBin extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{StorageBin}}';
	}

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, owner, status, created', 'safe', 'on'=>'search'),
		);
	}
	
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'files' => array(self::HAS_MANY, 'MStorageFile', 'binId'),
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
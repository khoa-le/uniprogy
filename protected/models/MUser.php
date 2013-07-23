<?php
class MUser extends UActiveRecord
{
	public function module()
	{
		return 'user';
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{User}}';
	}

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, email, password, salt, changePassword, role, created, firstName, lastName, avatar, timeZone', 'safe', 'on'=>'search'),
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
	
	public function beforeSave()
	{
		if($this->scenario=='password')
		{
			$this->salt = UHelper::salt();
			$this->password = UHelper::password($this->password, $this->salt);
		}
		return parent::beforeSave();
	}
	
	public function relations()
	{
		return array(
			'avatarBin' => array(self::BELONGS_TO, 'MStorageBin', 'avatar', 'with' => 'files'),
		);
	}
	
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('email',$this->email,true);

		$criteria->compare('password',$this->password,true);

		$criteria->compare('salt',$this->salt,true);

		$criteria->compare('changePassword',$this->changePassword);

		if(!$this->role)
		{
			$criteria->compare('role','<>administrator',true);
		}
		else
			$criteria->compare('role',$this->role,true);
			
		$criteria->compare('firstName',$this->lastName,true);
		
		$criteria->compare('lastName',$this->lastName,true);

		$criteria->compare('created',$this->created);

		$criteria->compare('avatar',$this->avatar);

		$criteria->compare('timeZone',$this->timeZone);

		return new CActiveDataProvider('MUser', array(
			'criteria'=>$criteria,
		));
	}
	
	public function getName($full=false)
	{
		return $full || $this->scenario == 'fullName'
			? txt()->format($this->firstName,' ',$this->lastName)
			: txt()->format($this->firstName,' ',substr($this->lastName,0,1),'.');
	}
}
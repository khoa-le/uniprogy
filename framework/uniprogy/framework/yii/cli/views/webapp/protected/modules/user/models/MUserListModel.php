<?php
class MUserListModel extends MUser
{	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
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

		$criteria->compare('created',$this->created);

		$criteria->compare('avatar',$this->avatar);

		$criteria->compare('timeZone',$this->timeZone);
		
		$nameCriteria = new CDbCriteria;
		$nameCriteria->compare('firstName',$this->lastName,true);
		$nameCriteria->compare('lastName',$this->lastName,true,'OR');
		
		$criteria->mergeWith($nameCriteria);

		return new CActiveDataProvider(__CLASS__, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getName()
	{
		return $this->lastName
			? txt()->format($this->lastName,',',' ',$this->firstName)
			: $this->firstName;
	}
}
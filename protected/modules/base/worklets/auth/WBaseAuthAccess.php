<?php
class WBaseAuthAccess extends USystemWorklet
{	
	public function taskGranted()
	{
		return true;
	}
	
	public function taskDenied()
	{
		$message = Yii::t('yii', 'You are not authorized to perform this action.');    	
    	throw new CHttpException(403,$message);
	}
}
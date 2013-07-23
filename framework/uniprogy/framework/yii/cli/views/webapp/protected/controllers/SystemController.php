<?php
class SystemController extends UController
{	
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction'
			)
		);
	}
	
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	$this->layout = 'system';
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	    	{
	    		wm()->get('base.init')->reset()->renderPage(
	    			$this->renderPartial('error', $error, true)
	    		);
	    	}
	    }
	}
}
<?php
class BBaseCaptcha extends UWorkletBehavior
{
	public function properties()
	{
		return array(
			'captcha' => array('type' => 'UForm',
				'elements' => array(
					'<hr />',
					'verifyCode' => array(
						'type' => 'UCaptcha',
						'layout' => "<fieldset class='captcha'>{input}\n{label}\n{hint}</fieldset>",
						'afterLabel' => '',
					),
				),
				'model' => new MBaseCaptchaForm
			)
		);
	}
	
	public function afterConfig()
	{
		$this->getOwner()->insert('bottom', $this->properties());
	}
}
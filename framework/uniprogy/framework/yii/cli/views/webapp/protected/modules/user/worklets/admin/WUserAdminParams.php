<?php
class WUserAdminParams extends UParamsWorklet
{
	public function accessRules()
	{
		return array(
			array('allow', 'roles' => array('administrator')),
			array('deny', 'users'=>array('*'))
		);
	}
	
	public function properties()
	{
		return array(
			'elements' => array(			
				'<h4>Sign-Up Settings</h4>',
				
				'captcha' => array('type' => 'checkbox', 'label' => 'Require captcha verification during sign-up'),				
				'emailVerification' => array('type' => 'checkbox',
					'label' => $this->t('Email Verification')),
				'unverifiedAccess' => array('type' => 'radiolist', 'label' => 'Unverified Users',
					'items' => array(
						0 => $this->t('can login until email is verified'),
						1 => $this->t('can\'t login until email is verified')
					), 'layout' => "{label}\n<fieldset>{input}\n{hint}</fieldset>"
				),
				'verificationTimeLimit' => array('type' => 'text', 'hint' => $this->t('hours'),
					'label' => $this->t('Email must be verified within'),
					'class' => 'short', 'layout' => "{label}\n<span class='hintInlineAfter'>{input}\n{hint}</span>"),					
				'approveNewAccounts' => array('type' => 'checkbox',
					'label' => $this->t('Approve New Accounts')),
				'unapprovedAccess' => array('type' => 'radiolist', 'label' => 'Unapproved Users',
					'items' => array(
						0 => $this->t('can login until account is approved'),
						1 => $this->t('can\'t login until account is approved')
					), 'layout' => "{label}\n<fieldset>{input}\n{hint}</fieldset>"
				),
				
				'<h4>Time Limits</h4>',
				'passwordResetTimeLimit' => array('type' => 'text', 'hint' => $this->t('hours'),
					'label' => $this->t('When requested, password must be reset within'),
					'class' => 'short', 'layout' => "{label}\n<span class='hintInlineAfter'>{input}\n{hint}</span>"),

				'inactivityTimeLimit' => array('type' => 'text', 'hint' => $this->t('minutes of no activity'),
					'label' => $this->t('Mark user as offline after'),
					'class' => 'short', 'layout' => "{label}\n<span class='hintInlineAfter'>{input}\n{hint}</span>"),

				'<h4>Avatar Settings</h4>',
				'fileTypes' => array('type' => 'text', 'hint' => $this->t('ex.: jpg, gif, png'), 'label' => 'Supported Formats'),
				'fileSizeLimit' => array('type' => 'text', 'hint' => $this->t('MB'),
					'label' => $this->t('Maximum Filesize'),
					'class' => 'short', 'layout' => "{label}\n<span class='hintInlineAfter'>{input}\n{hint}</span>"),
				'fileResize' => array('type' => 'text', 'label' => $this->t('Resize Uploaded Images To'),
					'hint' => $this->t('ex.: 480x360'))

			),
			'buttons' => array(
				'submit' => array('type' => 'submit', 'label' => $this->t('Save'))
			),
			'model' => $this->model
		);
	}
}
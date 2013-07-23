<?php
/**
 * UConfirmWorklet class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UConfirmWorklet is a worklet class that renders a 'yes/no' confirmation form.
 *
 * @version $Id: UConfirmWorklet.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UConfirmWorklet extends UFormWorklet
{
	/**
	 * @var string label for the 'Yes' button.
	 */
	public static $yesButtonLabel='';
	/**
	 * @var string label for the 'No' button.
	 */
	public static $noButtonLabel='';
	
	/**
	 * @return CFormModel blank form model
	 */
	public function taskModel()
	{
		return new CFormModel;
	}
	
	/**
	 * Predefines form properties and yes/no buttons.
	 * @return array form properties
	 */
	public function properties()
	{
		return array(
			'elements' => array(),
			'description' => $this->description(),
			'buttons' => array(
				'no' => array('type' => 'submit',
					'label' => self::$noButtonLabel?self::$noButtonLabel:$this->t('No')),
				'yes' => array('type' => 'submit',
					'label' => self::$yesButtonLabel?self::$yesButtonLabel:$this->t('Yes')),
			),
			'model' => $this->model
		);
	}
	
	/**
	 * Sets success URL to HTTP referer.
	 * @return string URL.
	 */
	public function successUrl()
	{
		if(!app()->request->isAjaxRequest)
			app()->user->returnUrl = app()->request->urlReferrer;
		return app()->user->returnUrl;
	}
	
	/**
	 * Overrides process task from {@link UFormWorklet}.
	 * It will call appropriate function depending on which button (yes or no) has been clicked.
	 */
	public function taskProcess()
	{
		if($this->form->submitted())
		{
			$this->form->validate();
			if($this->form->hasErrors())
				$this->error();
			else
			{
				if($this->form->clicked('no'))
					$this->no();
				elseif($this->form->clicked('yes'))
					$this->yes();
				$this->success();
			}
		}
	}
	
	/**
	 * Confirmation description (question).
	 */
	public function taskDescription() {}
	
	/**
	 * This task is executed when 'no' button is clicked.
	 */
	public function taskNo() {}	
	
	/**
	 * This task is executed when 'yes' button is clicked.
	 */
	public function taskYes() {}
}
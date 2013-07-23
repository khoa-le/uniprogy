<?php
/**
 * UPhpMessageSource class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UPhpMessageSource is an extension of {@link http://www.yiiframework.com/doc/api/CPhpMessageSource CPhpMessageSource}.
 *
 * @version $Id: UPhpMessageSource.php 79 2010-12-10 10:07:00 GMT vasiliy.y $
 * @package system.yii.i18n
 * @since 1.0.0
 */
class UPhpMessageSource extends CPhpMessageSource
{
	/**
	 * Overrides initialization by attaching {@link missingTranslation} function
	 * to 'onMissingTranslation' event.
	 */
	public function init()
	{
		parent::init();
		$this->onMissingTranslation = array($this, 'missingTranslation');
	}
	
	/**
	 * If translation failed but the category where script was looking for it
	 * wasn't identical to 'uniprogy' we'll check in 'uniprogy' to make sure
	 * translation is actually missing.
	 * Also if translation fails for 'yii' category we'll also check if it will be able
	 * to translate if we loose the region part of the language code.
	 */
	public function missingTranslation($event)
	{
		if(($event->category == 'yii' || $event->category == 'zii') && $pos=strpos($event->language,'_')!==false)
			$event->message = $this->translateMessage($event->category, $event->message, substr($event->language,0,$pos+1));
		elseif($event->category !== 'uniprogy')
			$event->message = $this->translateMessage('uniprogy', $event->message, $event->language);
	}
}
<?php
/**
 * Yii/UniProgy bootstrap file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

require(dirname(__FILE__).'/../../../yii/framework/YiiBase.php');
require(dirname(__FILE__).'/../globals.php');

defined('UP_PATH') or define('UP_PATH',realpath(dirname(__FILE__).DS.'..'));

/**
 * Yii is a helper class serving common framework functionalities.
 *
 * It encapsulates {@link http://www.yiiframework.com/doc/api/YiiBase YiiBase} which provides the actual implementation.
 * By writing your own Yii class, you can customize some functionalities of YiiBase.
 *
 * @version $Id: yii.php 79 2010-12-10 13:56:00 GMT vasiliy.y $
 * @package system.yii
 * @since 1.0.0
 */
class Yii extends YiiBase
{
	/**
	 * @return string the version of UniProgy framework
	 */
	public static function getUniprogyVersion()
	{
		return '1.1.14';
	}
	
	/**
	 * Class autoload loader.
	 * This method is provided to be invoked within an __autoload() magic method.
	 * @param string class name
	 * @return boolean whether the class has been loaded successfully
	 */
	public static function autoload($className)
	{
		if(isset(self::$_uCoreClasses[$className]))
		{
			include(UP_PATH.self::$_uCoreClasses[$className]);
			return class_exists($className,false) || interface_exists($className,false);
		}
		elseif(isset(self::$_uCustomClasses[$className]))
		{
			include(self::$_uCustomClasses[$className]);
			return class_exists($className,false) || interface_exists($className,false);
		}
		return parent::autoload($className);
	}
	
	/**
	 * Imports the definition of a class or a directory of class files.
	 * More details: {@link http://www.yiiframework.com/doc/api/YiiBase#import-detail YiiBase::import}.
	 * We have to override this method because Yii's import doesn't attempt to autoload a class
	 * when forceInclude is set to 'true'.
	 * @param string path alias to be imported
	 * @param boolean whether to include the class file immediately. If false, the class file
	 * will be included only when the class is being used.
	 * @return string the class name or the directory that this alias refers to
	 */
	public static function import($alias,$forceInclude=false)
	{	
		if(!$forceInclude
			|| ($pos=strrpos($alias,'.'))!==false
			|| class_exists($alias,false) || interface_exists($alias,false))
			return parent::import($alias,$forceInclude);
			
		if(self::autoload($alias))
			return parent::import($alias,$forceInclude);
	}
	
	/**
	 * Adds custom classes into autoload array.
	 * @param array class name => path to file
	 */
	public static function addCustomClasses($classes)
	{
		self::$_uCustomClasses = array_merge(self::$_uCustomClasses,$classes);
	}
	
	private static $_uCustomClasses=array();
	
	private static $_uCoreClasses=array(
		'UFactory' => '/base/UFactory.php',
		'UModelConstructor' => '/base/UModelConstructor.php',
		'UTimestampBehavior' => '/behaviors/UTimestampBehavior.php',
		'UHelper' => '/helpers/UHelper.php',
		'UTimeWording' => '/helpers/UTimeWording.php',
		'UImageStorageFile' => '/storage/UImageStorageFile.php',
		'UStorage' => '/storage/UStorage.php',
		'UStorageBin' => '/storage/UStorageBin.php',
		'UStorageFile' => '/storage/UStorageFile.php',
		'UDummyModel' => '/UDummyModel.php',
		'UDynamicModel' => '/UDynamicModel.php',
		'UMailer' => '/UMailer.php',
		'UDateTimeField' => '/widgets/UDateTimeField.php',
		'UDateTimePicker' => '/widgets/UDateTimePicker.php',
		'UI18NField' => '/widgets/UI18NField.php',
		'UJsButton' => '/widgets/UJsButton.php',
		'UMenu' => '/widgets/UMenu.php',
		'UUploadField' => '/widgets/UUploadField.php',
		'UUploadify' => '/widgets/UUploadify.php',
		'UCKEditor' => '/widgets/UCKEditor.php',
		'UConfirmWorklet' => '/worklets/UConfirmWorklet.php',
		'UCronWorklet' => '/worklets/UCronWorklet.php',
		'UDeleteWorklet' => '/worklets/UDeleteWorklet.php',
		'UFormWorklet' => '/worklets/UFormWorklet.php',
		'UInstallWorklet' => '/worklets/UInstallWorklet.php',
		'UListWorklet' => '/worklets/UListWorklet.php',
		'UMenuWorklet' => '/worklets/UMenuWorklet.php',
		'UMetaWorklet' => '/worklets/UMetaWorklet.php',
		'UParamsWorklet' => '/worklets/UParamsWorklet.php',
		'USystemWorklet' => '/worklets/USystemWorklet.php',
		'UUploadWorklet' => '/worklets/UUploadWorklet.php',
		'UWidgetWorklet' => '/worklets/UWidgetWorklet.php',
		'UWorklet' => '/worklets/UWorklet.php',
		'UWorkletAccessRule' => '/worklets/UWorkletAccessRule.php',
		'UWorkletBehavior' => '/worklets/UWorkletBehavior.php',
		'UWorkletConstructor' => '/worklets/UWorkletConstructor.php',
		'UWorkletFilter' => '/worklets/UWorkletFilter.php',
		'UWorkletManager' => '/worklets/UWorkletManager.php',
		'UWorkletOrderer' => '/worklets/UWorkletOrderer.php',
		'UActiveRecord' => '/yii/db/ar/UActiveRecord.php',
		'UDateFormatter' => '/yii/i18n/UDateFormatter.php',
		'ULocale' => '/yii/i18n/ULocale.php',
		'UPhpMessageSource' => '/yii/i18n/UPhpMessageSource.php',
		'UTextFormatter' => '/yii/i18n/UTextFormatter.php',
		'UTimestamp' => '/yii/utils/UTimestamp.php',
		'UAlphaNumericValidator' => '/yii/validators/UAlphaNumericValidator.php',
		'UDateTimeValidator' => '/yii/validators/UDateTimeValidator.php',
		'UAction' => '/yii/web/actions/UAction.php',
		'UViewAction' => '/yii/web/actions/UViewAction.php',
		'UAccessFilter' => '/yii/web/auth/UAccessFilter.php',
		'UAuthManager' => '/yii/web/auth/UAuthManager.php',
		'UUserIdentity' => '/yii/web/auth/UUserIdentity.php',
		'UWebUser' => '/yii/web/auth/UWebUser.php',
		'UForm' => '/yii/web/form/UForm.php',
		'UFormInputElement' => '/yii/web/form/UFormInputElement.php',
		'UClientScript' => '/yii/web/UClientScript.php',
		'UController' => '/yii/web/UController.php',
		'UFormModel' => '/yii/web/UFormModel.php',
		'UHttpRequest' => '/yii/web/UHttpRequest.php',
		'UTheme' => '/yii/web/UTheme.php',
		'UThemeManager' => '/yii/web/UThemeManager.php',
		'UWebApplication' => '/yii/web/UWebApplication.php',
		'UWebModule' => '/yii/web/UWebModule.php',
		'UCaptcha' => '/yii/web/widgets/captcha/UCaptcha.php',
		'UActiveForm' => '/yii/web/widgets/UActiveForm.php',
	);
}
spl_autoload_unregister(array('YiiBase','autoload'));
spl_autoload_register(array('Yii','autoload'));
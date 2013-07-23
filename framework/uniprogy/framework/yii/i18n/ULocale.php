<?php
/**
 * ULocale class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * ULocale is an extension of {@link http://www.yiiframework.com/doc/api/CLocale CLocale}.
 *
 * @version $Id: ULocale.php 5 2010-09-16 12:50:00 GMT vasiliy.y $
 * @package system.yii.i18n
 * @since 1.0.0
 */
class ULocale extends CLocale
{
	/**
	 * @var string the directory that contains the additional locale data.
	 * If this property is not set, the locale data will be loaded from 'framework/yii/i18n/data'.
	 * Yii's default locale data doesn't contain some essential info, such as text direction
	 * (left-to-right or right-to-left) so that's why we have to use additional data source.
	 */
	public static $exDataPath;
	
	private $_exData;
	private $_textFormatter;
	private $_uDateFormatter;
	
	/**
	 * Returns the instance of the specified locale.
	 * @param string the locale ID (e.g. en_US)
	 * @return ULocale the locale instance
	 */
	public static function getInstance($id)
	{
		static $locales=array();
		if(isset($locales[$id]))
			return $locales[$id];
		else
			return $locales[$id]=new ULocale($id);
	}
	
	/**
	 * Constructor.
	 * Since the constructor is protected, please use {@link getInstance}
	 * to obtain an instance of the specified locale.
	 * @param string the locale ID (e.g. en_US)
	 */
	protected function __construct($id)
	{
		parent::__construct($id);
		
		$exDataPath=self::$exDataPath===null ? dirname(__FILE__).DIRECTORY_SEPARATOR.'data' : self::$exDataPath;
		$dataFile=$exDataPath.DIRECTORY_SEPARATOR.$this->id.'.php';
		if(is_file($dataFile))
			$this->_exData=require($dataFile);
	}
	
	/**
	 * @return UTextFormatter the text formatter for this locale
	 */
	public function getTextFormatter()
	{
		if($this->_textFormatter===null)
			$this->_textFormatter=new UTextFormatter($this);
		return $this->_textFormatter;
	}
	
	/**
	 * @since 1.0.2
	 * @return UDateFormatter the date formatter for this locale
	 */
	public function getDateFormatter()
	{
		if($this->_uDateFormatter===null)
			$this->_uDateFormatter=new UDateFormatter($this);
		return $this->_uDateFormatter;
	}
	
	/**
	 * @return array locale data as array
	 */
	public function getData()
	{
		return $this->_exData;
	}
	
	/**
	 * @return string text direction for the current locale: l2r - left-to-right; r2l - right-to-left.
	 */
	public function getTxtDirection()
	{
		return isset($this->_exData['txtDirection']) ? $this->_exData['txtDirection'] : 'l2r';
	}
}
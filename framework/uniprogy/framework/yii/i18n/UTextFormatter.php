<?php
/**
 * UTextFormatter class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UTextFormatter provides text localization functionalities.
 *
 * @version $Id: UTextFormatter.php 79 2010-12-10 10:07:00 GMT vasiliy.y $
 * @package system.yii.i18n
 * @since 1.0.0
 */
class UTextFormatter extends CComponent
{
	private $_locale;
	
	/**
	 * Constructor.
	 * @param ULocale locale
	 */
	public function __construct($locale)
	{
		if(is_string($locale))
			$this->_locale=ULocale::getInstance($locale);
		else
			$this->_locale=$locale;
	}
	
	/**
	 * Orders text according to locale's text direction.
	 * <pre>Yii:app()->getLocale()->getTextFormatter()->format('Hello','!');</pre>
	 * If text direction for current locale is set to l2r it will return:
	 * <pre>Hello!</pre>
	 * Otherwise it will return:
	 * <pre>!Hello</pre>
	 * @return string ordered text
	 */
	public function format()
	{
		$args = func_get_args();

		if(is_array($args[0]))
			$args = $args[0];
		
		if($this->_locale->txtDirection == 'r2l')
			$args = array_reverse($args);
		return implode("", $args);
	}
	
	public function utf8strlen($str)
	{
		return function_exists('mb_strlen')
			? mb_strlen($str,'UTF-8')
			: substr($str);
	}
	
	public function utf8substr($str,$start,$length)
	{
		return function_exists('mb_substr')
			? mb_substr($str,$start,$length,'UTF-8')
			: substr($str,$start,$length);
	}
}
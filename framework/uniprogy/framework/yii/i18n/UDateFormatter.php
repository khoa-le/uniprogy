<?php
/**
 * UDateFormatter class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UDateFormatter is an extension of {@link http://www.yiiframework.com/doc/api/CDateFormatter CDateFormatter}.
 *
 * @version $Id: UDateFormatter.php 1 2010-09-16 12:48:00 GMT vasiliy.y $
 * @package system.yii.i18n
 * @since 1.0.2
 */
class UDateFormatter extends CDateFormatter
{
	/**
	 * Formats a date according to a customized pattern.
	 * We are overriding parent method because all passed time will be in GMT time zone.
	 * @param string the pattern (See {@link http://www.unicode.org/reports/tr35/#Date_Format_Patterns})
	 * @param mixed UNIX timestamp or a string in strtotime format
	 * @return string formatted date time.
	 */
	public function format($pattern,$time)
	{
		if(is_string($time))
		{
			if(ctype_digit($time))
				$time=(int)$time;
			else
				$time=strtotime($time);
		}
		$date=UTimestamp::getDate($time,false,true);
		$tokens=$this->parseFormat($pattern);
		foreach($tokens as &$token)
		{
			if(is_array($token)) // a callback: method name, sub-pattern
				$token=$this->{$token[0]}($token[1],$date);
		}
		return implode('',$tokens);
	}
}
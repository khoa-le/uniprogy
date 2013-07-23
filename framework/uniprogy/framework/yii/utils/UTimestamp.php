<?php
/**
 * UTimestamp class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UTimestamp is an extension of {@link http://www.yiiframework.com/doc/api/CTimestamp CTimestamp}.
 *
 * @version $Id: UTimestamp.php 67 2010-10-20 13:56:00 GMT vasiliy.y $
 * @package system.yii.utils
 * @since 1.0.0
 */
class UTimestamp extends CTimestamp
{	
	/**
	 * Converts hours from 12h to 24h format.
	 * @param integer hours in 12h format
	 * @param string am/pm
	 * @return integer hours in 24h format
	 */
	public static function h12to24($h, $a)
	{
		if($h == 12 && $a == 'am') return 0;
		if($h != 12 && $a == 'pm') return $h+12;
		return $h;
	}
	
	/**
	 * Makes a timestamp out of date/time values array.
	 * @param array date/time values
	 * @param boolean whether this is GMT time
	 * @return integer timestamp
	 */
	public static function arrayToTimestamp($value,$is_gmt=true)
	{
		if(!is_array($value))
			return;
			
		list($h,$m,$M,$d,$y) = array(0,0,false,false,false);
		
		// date
		if(isset($value['date']) && $value['date']!='')
		{
			$positions = preg_split('/[^A-Za-z]+/',app()->locale->getDateFormat('short'));
			$parts = preg_split('/[^0-9]+/',$value['date']);
			foreach($positions as $k=>$v)
			{
				$v = strtolower(substr($v,0,1));
				switch($v)
				{
					case 'm':
						$value['M'] = $parts[$k];
						break;
					case 'd':
						$value['d'] = $parts[$k];
						break;
					case 'y':
						$value['y'] = $parts[$k];
						break;
				}
			}
		}
			
		// hour
		if(isset($value['h']) && $value['h'] !== '' && isset($value['a']) && $value['a'] !== '')
			$h = self::h12to24($value['h'], $value['a']);
		if(isset($value['H']) && $value['H'] !== '')
			$h = $value['H'];
		// minute
		if(isset($value['m']) && $value['m'] !== '') $m = $value['m'];		
		// month
		if(isset($value['M']) && $value['M'] !== '') $M = $value['M'];		
		// day
		if(isset($value['d']) && $value['d'] !== '') $d = $value['d'];		
		// year
		if(isset($value['y']) && $value['y'] !== '') $y = $value['y'];
		
		if($M===false || $d===false || $y===false)
			return;
		
		return self::getTimestamp($h, $m, 0, $M, $d, $y, $is_gmt);
	}
	
	/**
	 * Returns current timestamp.
	 * @return integer timestamp
	 */
	public static function getNow()
	{
		return time();
	}
	
	/**
	 * Applies GMT difference to the timestamp.
	 * @param integer source timestamp
	 * @param float offset in hours
	 * @param boolean whether to apply or un-apply the difference
	 * @param boolean whether to calculate DST difference or not
	 * @return integer timestamp with GMT difference applied
	 */
	public static function applyGMT($value,$offset,$apply=true,$dst=true)
	{
		$difference = $offset*3600;
		if($dst)
			$difference+= date('I',$value)*3600;
		return $apply ? $value - $difference : $value + $difference;
	}
	
	/**
	 * @param integer timestamp
	 * @return integer GMT timestamp
	 */
	public static function getGMT($value)
	{
		return self::getTimestamp(date('H',$value), date('i',$value), date('s',$value),
			date('n',$value), date('j',$value), date('Y',$value), true);
	}
}
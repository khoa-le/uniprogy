<?php
/**
 * UTimeWording class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2012 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UTimeWording is a static helper class that provides a collection of methods to convert time difference to word phrase.
 *
 * @version $Id: UTimeWording.php 1 2012-02-04 09:07:00 GMT vasiliy.y $
 * @package system.helpers
 * @since 1.1.12
 */
class UTimeWording
{
	/**
	* Returns either a relative date or a formatted date depending
	* on the difference between the current time and given datetime.
	* $datetime should be in a <i>strtotime</i>-parsable format, like MySQL's datetime datatype
	* or UNIX-timestamp.
	*
	* Options:
	*  'format' => a fall back format if the relative time is longer than the duration specified by end (short, medium, long, full)
	*  'end' =>  The end of relative time telling
	*
	* Relative dates look something like this:
	*	3 weeks, 4 days ago
	*	15 seconds ago
	* Formatted dates look like this:
	*	on 02/18/2004
	*
	* The returned string includes 'ago' or 'on' and assumes you'll properly add a word
	* like 'Posted ' before the function output.
	*
	* @param string $dateString Datetime string
	* @param array $options Default format if timestamp is used in $dateString
	* @return string Relative time string.
	*/
	public static function timeAgoInWords($dateTime, $options = array()) {
		$now = time();

		$inSeconds = is_numeric($dateTime)?$dateTime:strtotime($dateTime);
		$backwards = ($inSeconds > $now);

		$format = 'short';
		$end = '+1 month';

		if (is_array($options)) {
			if (isset($options['format'])) {
				$format = $options['format'];
				unset($options['format']);
			}
			if (isset($options['end'])) {
				$end = $options['end'];
				unset($options['end']);
			}
		} else {
			$format = $options;
		}

		if ($backwards) {
			$futureTime = $inSeconds;
			$pastTime = $now;
		} else {
			$futureTime = $now;
			$pastTime = $inSeconds;
		}
		$diff = $futureTime - $pastTime;

		// If more than a week, then take into account the length of months
		if ($diff >= 604800) {
			$current = array();
			$date = array();

			list($future['H'], $future['i'], $future['s'], $future['d'], $future['m'], $future['Y']) = explode('/', date('H/i/s/d/m/Y', $futureTime));

			list($past['H'], $past['i'], $past['s'], $past['d'], $past['m'], $past['Y']) = explode('/', date('H/i/s/d/m/Y', $pastTime));
			$years = $months = $weeks = $days = $hours = $minutes = $seconds = 0;

			if ($future['Y'] == $past['Y'] && $future['m'] == $past['m']) {
				$months = 0;
				$years = 0;
			} else {
				if ($future['Y'] == $past['Y']) {
					$months = $future['m'] - $past['m'];
				} else {
					$years = $future['Y'] - $past['Y'];
					$months = $future['m'] + ((12 * $years) - $past['m']);

					if ($months >= 12) {
						$years = floor($months / 12);
						$months = $months - ($years * 12);
					}

					if ($future['m'] < $past['m'] && $future['Y'] - $past['Y'] == 1) {
						$years --;
					}
				}
			}

			if ($future['d'] >= $past['d']) {
				$days = $future['d'] - $past['d'];
			} else {
				$daysInPastMonth = date('t', $pastTime);
				$daysInFutureMonth = date('t', mktime(0, 0, 0, $future['m'] - 1, 1, $future['Y']));

				if (!$backwards) {
					$days = ($daysInPastMonth - $past['d']) + $future['d'];
				} else {
					$days = ($daysInFutureMonth - $past['d']) + $future['d'];
				}

				if ($future['m'] != $past['m']) {
					$months --;
				}
			}

			if ($months == 0 && $years >= 1 && $diff < ($years * 31536000)) {
				$months = 11;
				$years --;
			}

			if ($months >= 12) {
				$years = $years + 1;
				$months = $months - 12;
			}

			if ($days >= 7) {
				$weeks = floor($days / 7);
				$days = $days - ($weeks * 7);
			}
		} else {
			$years = $months = $weeks = 0;
			$days = floor($diff / 86400);

			$diff = $diff - ($days * 86400);

			$hours = floor($diff / 3600);
			$diff = $diff - ($hours * 3600);

			$minutes = floor($diff / 60);
			$diff = $diff - ($minutes * 60);
			$seconds = $diff;
		}
		$relativeDate = '';
		$diff = $futureTime - $pastTime;

		if ($diff > abs($now - strtotime($end))) {
			$params = array('{date}' => app()->getDateFormatter()->formatDateTime($inSeconds, $format, $format));
			$relativeDate = Yii::t('uniprogy','on {date}',$params);
		} else {
			$txt = array();
			if ($years > 0) {
				// years and months and days				
				$txt[] = self::word('year',$years);
				$txt[] = $months > 0 ? self::word('month',$months) : null;
				$txt[] = $days > 0 ? self::word('day',$days) : null;
			} elseif (abs($months) > 0) {
				// months and days
				$txt[] = self::word('month',$months);
				$txt[] = $days > 0 ? self::word('day',$days) : null;
			} elseif (abs($weeks) > 0) {
				// weeks and days
				$txt[] = self::word('week',$weeks);
				$txt[] = $days > 0 ? self::word('day',$days) : null;
			} elseif (abs($days) > 0) {
				// days and hours
				$txt[] = self::word('day',$days);
				$txt[] = $hours > 0 ? self::word('hour',$hours) : null;
			} elseif (abs($hours) > 0) {
				// hours and minutes
				$txt[] = self::word('hour',$hours);
				$txt[] = $minutes > 0 ? self::word('minute',$minutes) : null;
			} elseif (abs($minutes) > 0) {
				// minutes only
				$txt[] = self::word('minute',$minutes);
			} else {
				// seconds only
				$txt[] = self::word('second',$seconds);
			}
			
			$txt = UHelper::unsetNulls($txt);
			if(app()->locale->getTxtDirection() == 'r2l')
				$txt = array_reverse($txt);
			$glue = txt()->format(',',' ');
			$relativeDate = implode($glue,$txt);

			if (!$backwards)
				$relativeDate = Yii::t('uniprogy','{time} ago',array('{time}'=>$relativeDate));
		}
		return $relativeDate;
	}
	
	public static function word($type,$num)
	{
		$m = '';
		switch($type)
		{
			case 'year':
				$m = Yii::t('uniprogy','1#year|n>1#year',array($num));
				break;
			case 'month':
				$m = Yii::t('uniprogy','1#month|n>1#months',array($num));
				break;
			case 'week':
				$m = Yii::t('uniprogy','1#week|n>1#weeks',array($num));
				break;
			case 'day':
				$m = Yii::t('uniprogy','1#day|n>1#days',array($num));
				break;
			case 'hour':
				$m = Yii::t('uniprogy','1#hour|n>1#hours',array($num));
				break;
			case 'minute':
				$m = Yii::t('uniprogy','1#minute|n>1#minutes',array($num));
				break;
			case 'second':
				$m = Yii::t('uniprogy','1#second|n>1#seconds',array($num));
				break;
		}
		return txt()->format($num,' ',$m);
	}
}
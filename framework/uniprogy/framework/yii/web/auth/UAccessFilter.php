<?php
/**
 * UAccessFilter class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UAccessFilter delegates authorization checks for the specified actions to the worklet.
 *
 * @version $Id: UAccessFilter.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.yii.web.auth
 * @since 1.0.0
 */
class UAccessFilter extends CFilter
{
	/**
	 * @var string the authorization access verification worklet id.
	 */
	public static $worklet = 'base.auth.access';
	
	/**
	 * Performs the pre-action filtering.
	 * @param CFilterChain the filter chain that the filter is on.
	 * @return boolean whether the filtering process should continue and the action
	 * should be executed.
	 */
	protected function preFilter($filterChain)
    {
    	return wm()->get(self::$worklet)->granted();
    }
}
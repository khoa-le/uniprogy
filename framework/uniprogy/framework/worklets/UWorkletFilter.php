<?php
/**
 * UWorkletFilter class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UWorkletFilter is a base class for worklet filters.
 *
 * @version $Id: UWorkletFilter.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UWorkletFilter extends CComponent
{
	/**
	 * @var UModule module instance
	 */
	public $module;
	
	public $_filters=array();
	
	/**
	 * Constructor.
	 * @param UModule module
	 */
	public function __construct($module)
	{
		$this->module = $module;
		$this->_filters = $this->filters();
	}
	
	/**
	 * Calls the named method which is not a class method.
	 * Do not call this method. This is a PHP magic method that we override.
	 * @param string the method name
	 * @param array method parameters
	 * @return mixed the method return value
	 */
	public function __call($name,$params)
	{
		return $this->filter($params[0],$name);
	}
	
	/**
	 * Returns filter configuration.
	 * Ex.:
	 * <pre>
	 * return array(
	 *     'base.index' => array(
	 *         'replace' => 'custom.index'
	 *     ),
	 *     'base.contact' => array(
	 *         'behaviors' => array('custom.behavior'),
	 *         'cacheKey' => 'CustomFunction'
	 *     ),
	 * );
	 * </pre>
	 * 
	 * Above configuration means that:
	 * 'base.index' worklet needs to be replaced with 'custom.index';
	 * 'custom.behavior' behavior needs to be attached to 'base.contact' worklet;
	 * when saving 'base.contact' into the cache the key should be appended with the result of
	 * CustomFunction call:
	 * <pre>
	 * public function CustomFunction()
	 * {
	 *     return 'add.custom.postfix';
	 * }
	 * </pre>
	 *
	 * @return array filter configuration
	 */
	public function filters()
	{
		return array();
	}
	
	/**
	 * Looks for a filter of specified type for a specified worklet id.
	 * @param string worklet id
	 * @param string type: replace, behaviors or cacheKey
	 * @return mixed filter info
	 */
	public function filter($id,$type)
	{
		if(!isset($this->_filters[$id][$type]))
			return;
		
		$filter = $this->_filters[$id][$type];
		return is_string($filter)
			? call_user_func(array($this,$filter))
			: $filter;
	}
}
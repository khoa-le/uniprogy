<?php
/**
 * UWorkletManager class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UWorkletManager is the main class that contains common worklets management functionalities.
 * Here are the main functions you will be likely to be using.
 * 
 * Specify custom initial worklet (in main configuration file):
 * <pre>
 * ...
 * 'components' => array(
 *     'workletManager' => array(
 *         'class' => 'UWorkletManager',
 *         'initialWorklet' => 'custom.init'
 *     ),
 * ),
 * ...
 * </pre>
 * 
 * Get worklet:
 * <pre>Yii::app()->workletManager->get('worklet.id');</pre>
 * 
 * Get worklet and add it to rendering:
 * <pre>Yii::app()->workletManager->add('worklet.id');</pre>
 *
 * @version $Id: UWorkletManager.php 73 2010-11-09 09:26:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UWorkletManager extends CComponent
{
	private $_initialWorklet='base.init';
	private $_worklets=null;
	private $_currentWorklet=null;
	
	/**
	 * Initialized component.
	 * You can override this function to add some custom initialization functionality.
	 */
	public function init() {
		$this->_worklets = new CAttributeCollection;
		$this->_worklets->caseSensitive = true;
	}
	
	/**
	 * Initial worklet getter.
	 * @return string initial worklet id
	 */
	public function getInitialWorklet()
	{
		return $this->_initialWorklet;
	}
	
	/**
	 * Initial worklet setter.
	 * @param string initial worklet id
	 */
	public static function setInitialWorklet($value)
	{
		self::$_initialWorklet = $value;
	}
	
	/**
	 * @param string worklet id
	 * @param array configuration
	 * @return mixed UWorklet instance or null if worklet cannot be created for any reason
	 */
	public function get($id,$config=array())
	{
		$worklet = UWorkletConstructor::get($id,$config);
		return $worklet;
	}
	
	/**
	 * @return UWorklet current active worklet instance
	 */
	public function getCurrentWorklet()
	{
		return $this->_currentWorklet;
	}
	
	/**
	 * Sets current worklet to the specified one.
	 * @param mixed worklet id or worklet instance
	 * @param mixed custom id to associate this worklet with
	 * @param array worklet configuration
	 * @return mixed UWorklet instance or boolean false
	 */
	public function addCurrent($worklet,$id=null,$config=array())
	{
		$worklet = $worklet instanceOf UWorklet ? $worklet : wm()->get($worklet,$config);
		$previous = $this->_currentWorklet?$this->_currentWorklet->id:null;		
		if($worklet = $this->add($worklet,$id,$config))
		{
			$current = $this->_currentWorklet?$this->_currentWorklet->id:null;
			if($current == $previous)
				$this->_currentWorklet = $worklet;
			return $worklet;
		}
		return false;
	}
	
	/**
	 * Adds a worklet to rendering stack.
	 * @param mixed worklet id or worklet instance
	 * @param mixed custom id to associate this worklet with
	 * @param array worklet configuration
	 * @return mixed UWorklet instance or boolean false
	 */
	public function add($worklet,$id=null,$config=array())
	{
		if(is_string($worklet))
		{
			$id = $id===null?$worklet:$id;
			$worklet = $this->get($worklet,$config);
		}

		if($worklet instanceOf UWorklet)	
		{
			if($worklet->access())
			{
				$worklet->init();
				$id = $id===null?$worklet->getId():$id;
				$this->_worklets->add($id,$worklet);
				return $worklet;
			}
		}
		return false;
	}
	
	/**
	 * Removes worklet from rendering stack.
	 * @param string worklet id
	 */
	public function remove($id)
	{
		$this->_worklets->remove($id);
	}
	
	/**
	 * @return array worklets rendering stack.
	 */
	public function getWorklets()
	{
		return $this->_worklets;
	}
	
	/**
	 * Clears worklets rendering stack and {@link UWorkletConstructor} instances array.
	 */
	public function clear()
	{
		$this->_worklets->clear();
		UWorkletConstructor::clear();
	}
}
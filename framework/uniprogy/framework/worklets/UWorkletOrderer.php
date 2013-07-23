<?php
/**
 * UWorkletOrderer class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UWorkletOrderer is a class that is used by initial worklet to order worklets according to their
 * {@link UWidgetWorklet::position} values.
 *
 * @version $Id: UWorkletOrderer.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UWorkletOrderer extends CComponent
{
	private $_list;
	private $_done;
	
	/**
	 * Constructor.
	 * @param CList unordered list of worklets
	 */
	public function __construct($list)
	{
		$this->_list = $list;
		$this->_done = array();
	}
	
	/**
	 * Orders the list.
	 * @return CList ordered list of worklets
	 */
	public function order()
	{
		$list = $this->_list->toArray();
		foreach($list as $dummy=>$id)
		{
			if(!in_array($id,$this->_done))
			{
				$this->position($id);
			}
		}
		return $this->_list;
	}
	
	/**
	 * Puts a single worklet in the right position.
	 * @param string worklet id
	 */
	public function position($id)
	{
		// add to done
		$this->_done[] = $id;
		// get worklet
		$worklet = wm()->worklets->itemAt($id);
		// do we need to reposition
		if(!$worklet->position)
			return;
			
		// remove from the list initially
		$this->_list->remove($id);
		// top position: pushing at the top of the list
		if($worklet->position == 'top')
			$this->_list->insertAt(0, $id);
		// bottom position: adding to the end of the list
		elseif($worklet->position == 'bottom')
			$this->_list->add($id);
		// complex position
		elseif(is_array($worklet->position))
		{
			list($where, $what) = each($worklet->position);
			$index = $this->_list->indexOf($what);
			
			// if corresponding worklet is not found
			// we just add to the end of the list
			if($index === -1)
				$this->_list->add($id);
			elseif(!in_array($this->_list[$index],$this->_done))
			{
				$this->position($this->_list[$index]);
				// refreshing index
				$index = $this->_list->indexOf($what);
			}
			
			if($where == 'before')
				$this->_list->insertAt($index, $id);
			elseif($where == 'after')
			{
				// if we need to add after the last element
				// we can just add to the end of the list
				if($index == count($this->_list) - 1)
					$this->_list[] = $id;
				else
					$this->_list->insertAt($index + 1, $id);
			}
		}
	}
}
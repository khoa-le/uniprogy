<?php
/**
 * UMenuWorklet class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UMenuWorklet is a worklet that renders a menu.
 *
 * @version $Id: UMenuWorklet.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UMenuWorklet extends UWidgetWorklet
{
	/**
	 * @var array menu properties
	 * @link http://www.yiiframework.com/doc/api/CMenu
	 */
	public $properties = array();
	/**
	 * @var boolean true if menu has submenus which should appear as dropdowns.
	 */
	public $dropdown = false;
	
	/**
	 * Configures menu.
	 */
	public function taskConfig()
	{
		$this->properties = CMap::mergeArray($this->properties, $this->properties());
		parent::taskConfig();
	}
	
	/**
	 * Renders menu.
	 */
	public function taskRenderOutput()
	{
		$this->render('menu');
	}
	
	/**
	 * @return array menu properties
	 * @link http://www.yiiframework.com/doc/api/CMenu
	 */
	public function properties()
	{
		return array('items'=>array());
	}
	
	/**
	 * Inserts items before specified index into the menu items array.
	 *
	 * @param string element index.
	 * @param array new items.
	 * @return boolean true
	 */
	public function insertBefore($index, $items)
	{
		$key = false;
		foreach($this->properties['items'] as $k=>$v)
			if($v['label'] === $this->t($index) || $v['url'] == $index)
			{
				$key = $k;
				break;
			}
		reset($this->properties['items']);
		
		if($key === false || $key === 0)
			return $this->insert('top', $items);
		
		$this->properties['items'] = array_merge(array_slice($this->properties['items'],0,$key),
			$items, array_slice($this->properties['items'],$key));
		
		return true;
	}
	
	/**
	 * Inserts items after specified index into the menu items array.
	 *
	 * @param string element index.
	 * @param array new items.
	 * @return boolean true
	 */
	public function insertAfter($index, $items)
	{
		$key = false;
		foreach($this->properties['items'] as $k=>$v)
			if($v['label'] === $this->t($index) || $v['url'] == $index)
			{
				$key = $k;
				break;
			}
		reset($this->properties['items']);
		
		if($key === false || $key == count($this->properties['items'])-1)
			return $this->insert('bottom', $items);
			
		$key++;
		$this->properties['items'] = array_merge(array_slice($this->properties['items'],0,$key),
			$items, array_slice($this->properties['items'],$key));
		return true;
	}
	
	/**
	 * Inserts items on top or bottom of the menu items array.
	 *
	 * @param string position 'top' or 'bottom'.
	 * @param array new elements to add.
	 * @return boolean true
	 */
	public function insert($position, $items)
	{
		if($position == 'top')
			$this->properties['items'] = array_merge($items,$this->properties['items']);
		elseif($position == 'bottom')
			$this->properties['items'] = array_merge($this->properties['items'],$items);
		return true;
	}
	
	/**
	 * Merges menu properties.
	 * @param array properties to merge with.
	 */
	public function merge($properties)
	{
		$this->properties = CMap::mergeArray($this->properties, $properties);
	}
}
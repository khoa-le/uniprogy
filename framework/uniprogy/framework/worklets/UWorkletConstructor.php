<?php
/**
 * UWorkletConstructor class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UWorkletConstructor is a class that handles worklet construction.
 *
 * @version $Id: UWorkletConstructor.php 65 2010-10-14 17:55:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UWorkletConstructor extends CComponent
{
	private static $_instances=array();
	
	/**
	 * @var string worklet id.
	 */
	public $id;
	/**
	 * @var UModule parent module
	 */
	public $parent;
	/**
	 * @var UModule worklet module.
	 */
	public $module;
	/**
	 * @var array worklet configuration.
	 */
	public $config=array();
	/**
	 * @var array worklet behaviors.
	 */
	public $behaviors=array();
	/**
	 * @var array worklet module and all it's parents combined filters
	 */
	public $filters=array();
	
	/**
	 * Constructor.
	 *
	 * @param string worklet id
	 * @param array worklet configuration
	 * @param mixed null or parent module instance
	 */
	public function __construct($id,$config,$parent=null)
	{
		$this->id = $id;
		$this->config=$config;
		if($parent)
			$this->parent = $parent;
	}
	
	/**
	 * Clears constructor instances array so any new attempt to get a worklet
	 * will actually create it from scratch.
	 */
	public function clear()
	{
		self::$_instances = array();
	}
	
	/**
	 * Factory.
	 *
	 * @param string worklet id
	 * @param array configuration array
	 * @param mixed null or parent module instance
	 * @return mixed {@link UWorklet} instance or boolean false or null
	 */
	public static function get($id,$config=array(),$parent=null)
	{
		if(!$id)
			return false;
		
		// first call to this worklet - construct it from scratch
		if(!isset(self::$_instances[$id]))
		{
			$c = new UWorkletConstructor($id,$config,$parent);
			self::$_instances[$id] = $c->run();
		}
		// simply apply new configuration and return worklet
		else
		{
			foreach($config as $k=>$v)
				self::$_instances[$id]->$k = $v;
		}
		return self::$_instances[$id];
	}
	
	/**
	 * Constructs a worklet.
	 * @return mixed {@link UWorklet} instance or boolean false or null
	 */
	public function run()
	{
		// first checking if we can get worklet from cache
		$worklet = $this->getFromCache();
		
		// build worklet
		if(!$worklet instanceOf UWorklet)
			$worklet = $this->build();
		return $worklet;
	}
	
	/**
	 * Builds a worklet.
	 * @return mixed {@link UWorklet} instance or boolean false or null
	 */
	public function build()
	{
		// extracting module
		$module = UFactory::getModuleFromAlias($this->id);
		// if module doesn't exist or it is disabled - returning false
		if(!$module)
			return false;	

		// gathering filters information from the current module and all it's parents
		$filters = array();
		$parent = $module;
		while($parent)
		{
			$filters = CMap::mergeArray($filters,$parent->filters);
			$parent = $parent->getParentModule();
		}
		
		// attaching filters to constructor
		$this->attachFilters($filters);

		// we have to check if worklet can be found in cache again, because now filters are attached
		$worklet = $this->getFromCache();
		if($worklet instanceOf UWorklet)
			return $worklet;
			
		// checking if this worklet needs to be replaced with another one
		$replace = $this->replace();
		if($replace)
		{
			$this->saveToCache(array('replace' => $replace));
			return $this->getReplace($replace);
		}
		
		// saving module and class alias into public variables
		$this->module = $module;
		
		// building a list of behaviors to be attached to the worklet
		$this->behaviors();
		
		// saving to cache
		$this->saveToCache();
		
		// creating an instance of worklet
		return $this->worklet();
	}
	
	/**
	 * Builds a replace worklet.
	 * @param mixed replace worklet. Can be a string or an array.
	 * Ex.:
	 * <pre>$this->getReplace('replace.worklet.id');</pre>
	 * <pre>$this->getReplace(array('replace.worklet.id',
	 *    'config1'=>'value1','config2'=>'value2'));</pre>
	 * @return UWorklet worklet instance
	 */
	public function getReplace($replace)
	{
		if(is_array($replace) && count($replace) > 1)
			return $this->get($replace[0],array_splice($replace,1),$this);
		elseif(is_array($replace))
			return $this->get($replace[0],$this->config,$this);
		else
			return $this->get($replace,$this->config,$this);
	}
	
	/**
	 * Creates a worklet.
	 * @return UWorklet worklet instance
	 */
	public function worklet()
	{
		if(isset($this->config['constructor']))
			unset($this->config['constructor']);
		if(!$worklet = UFactory::getWorklet($this->id,$this->config,$this->behaviors))
			return false;
		if(!$worklet->enabled())
			return false;
		
		return $worklet;
	}
	
	/**
	 * Goes through all filters and collects behaviors into an array.
	 */
	public function behaviors()
	{
		reset($this->filters);
		foreach($this->filters as $obj)
			if($b = $obj->behaviors($this->id))
				$this->behaviors = CMap::mergeArray($this->behaviors,$b);		

		if(isset($this->config['constructor'])
			&& isset($this->config['constructor']['inheritBehaviors'])
			&& $this->config['constructor']['inheritBehaviors']===false)
			return;
			
		$parent = $this->parent;
		while($parent!==null)
		{
			$this->behaviors = CMap::mergeArray($this->behaviors,$parent->behaviors);
			$parent = $parent->parent;
		}
	}
	
	/**
	 * Goes through all filters to find if this worklet has to be replaced by another one.
	 * @return mixed replace worklet info or boolean false
	 */
	public function replace()
	{
		reset($this->filters);
		foreach($this->filters as $obj)
			if($replace = $obj->replace($this->id))
				return $replace;
		return false;
	}
	
	/**
	 * Attaches filters to the constructor.
	 * @param array filters
	 */
	public function attachFilters($filters)
	{
		if(!is_array($filters) && !$filters instanceOf CList)
			return;
		foreach($filters as $f)
			if($filter=UFactory::getFilter($f))
				$this->filters[$f] = $filter;
	}
	
	/**
	 * Saves worklet data into the cache.
	 * @param mixed worklet data or null (in this case worklet data is collected from public variables)
	 */
	public function saveToCache($data=null)
	{
		$data = $data ? $data : array(
			'module' => UFactory::getModuleAlias($this->module),
			'behaviors' => $this->behaviors
		);
		app()->cache->set($this->cacheKey(),$data);
	}
	
	/**
	 * Tries to retrieve worklet info from the cache.
	 * @return mixed worklet instance or boolean false
	 */
	public function getFromCache()
	{
		$worklet = app()->cache->get($this->cacheKey());
		if(!is_array($worklet))
			return false;		
		if(isset($worklet['replace']))
			return $this->getReplace($worklet['replace']);
		elseif(isset($worklet['module'],$worklet['behaviors'])) {
			foreach($worklet as $k=>$v)
				$this->$k = $v;
			return $this->worklet();
		}
		return false;
	}
	
	/**
	 * Goes through all filters and builds a full cache key.
	 * @return string cache key
	 */
	public function cacheKey()
	{
		$cacheKey = 'UniProgy.UWorkletConstructor.' . $this->id;
		reset($this->filters);
		foreach($this->filters as $obj)
			if($addCacheKey = $obj->cacheKey($this->id))
				$cacheKey.= '.' . $addCacheKey;
		return $cacheKey;
	}
}
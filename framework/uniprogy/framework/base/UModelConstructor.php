<?php
/**
 * UModelConstructor class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UModelConstructor is the main factory class for creating such items as worklets, filters, behaviors, etc.
 *
 * @version $Id: UModelConstructor.php 66 2010-10-15 11:26:00 GMT vasiliy.y $
 * @package system.base
 * @since 1.1.2
 */
class UModelConstructor extends CComponent
{
	private static $_instances=array();
	/**
	 * @var string model class name
	 */
	public $class;
	/**
	 * @var UModule module instance
	 */
	public $module;
	/**
	 * @var array model behaviors
	 */
	public $behaviors=array();
	/**
	 * @var array model module and all it's parents combined filters
	 */
	public $filters=array();
	
	/**
	 * Constructor.
	 * @param string model class name
	 */
	public function __construct($class)
	{
		$this->class = $class;
		$moduleAlias = call_user_func(array($class,'module'));
		$this->module = UFactory::getModuleFromAlias($moduleAlias);
		$this->init();
	}
	
	/**
	 * Singleton.
	 * @param CModel {@link UActiveRecord} or {@link UFormModel} instance.
	 * @param string model class name
	 */
	public static function process($model,$class=null)
	{
		$class = $class?$class:get_class($model);
		if(!isset(self::$_instances[$class]))
			self::$_instances[$class] = new UModelConstructor($class);
		self::$_instances[$class]->apply($model);
	}
	
	/**
	 * Initializes model constructor
	 * by attaching filters and building behaviors list.
	 */
	public function init()
	{
		// first checking if we can get data from cache
		if($this->getFromCache())
			return;
		
		// gathering filters information from the current module and all it's parents
		$filters = array();
		$parent = $this->module;
		while($parent)
		{
			$filters = CMap::mergeArray($filters,$parent->filters);
			$parent = $parent->getParentModule();
		}
		
		// attaching filters to constructor
		$this->attachFilters($filters);
		
		if(!$this->getFromCache())
		{
			// building a list of behaviors to be attached to the model
			$this->behaviors();
			// saving to cache
			$this->saveToCache();
		}
	}
	
	/**
	 * Applies behaviors to the model.
	 * @param CModel {@link UActiveRecord} or {@link UFormModel} instance
	 */
	public function apply($model)
	{
		$model->attachBehaviors($this->behaviors);
		$parent = get_parent_class($this->class);
		if($parent && $parent!='UActiveRecord' && $parent!='UFormModel')
			self::process($model,$parent);
	}
	
	/**
	 * Goes through all filters and collects behaviors into an array.
	 */
	public function behaviors()
	{
		reset($this->filters);
		foreach($this->filters as $obj)
			if($b = $obj->behaviors($this->class))
				$this->behaviors = CMap::mergeArray($this->behaviors,$b);
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
	 * Saves behaviors array into the cache.
	 */
	public function saveToCache()
	{
		app()->cache->set($this->cacheKey(),$this->behaviors);
	}
	
	/**
	 * Tries to retrieve model info from the cache.
	 * @return boolean true or false
	 */
	public function getFromCache()
	{
		$data = app()->cache->get($this->cacheKey());
		if(!is_array($data))
			return false;
		$this->behaviors = $data;	
		return true;
	}
	
	/**
	 * Goes through all filters and builds a full cache key.
	 * @return string cache key
	 */
	public function cacheKey()
	{
		$cacheKey = 'UniProgy.UModelConstructor.' . $this->class;
		reset($this->filters);
		foreach($this->filters as $obj)
			if($addCacheKey = $obj->cacheKey($this->class))
				$cacheKey.= '.' . $addCacheKey;
		return $cacheKey;
	}
}
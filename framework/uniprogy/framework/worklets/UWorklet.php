<?php

/**
 * UWorklet class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UWorklet is the base class for all worklets.
 * It provides the common functionalities shared by worklets.
 * 
 * UWorklet behaviors work similar to {@link http://www.yiiframework.com/doc/api/CComponent}
 * but there's a little difference.
 * 
 * If you call a named method 'name' which doesn't exist in this class it will be captured by the {@link __call}
 * PHP magic method and it will try to execute the following:
 * <ul>
 * <li>beforeName method of all behaviors and current class: if any of 'before' methods
 * will return anything different from NULL the remaining chain of methods won't be executed
 * including the actual task method and the value returned from 'before' method will be returned
 * as the final result of task call.</li>
 * <li>taskName method of a current class</li>
 * <li>afterName method of all behaviors and current class: all 'after' methods get current result
 * returned by taskName as an additional function argument. Any 'after' method can override
 * current task execution result by returning anything different from NULL.</li>
 * </ul>
 * 
 * So when you're creating a new worklet name your methods like this:
 * <pre>public function taskMethod</pre>
 * but call them without 'task' prefix:
 * <pre>$worklet->method()</pre>
 * 
 * In this case you will be able to attach behavior to this worklet get all
 * beforeMethod and afterMethod methods executed automatically.
 *
 * @version $Id: UWorklet.php 82 2010-12-16 14:02:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 * @link http://www.yiiframework.com/doc/api/CBaseController
 */
class UWorklet extends CBaseController {

    private $_b;
    private $_id;
    private $_module;

    /**
     * @var string cache keys prefix
     */
    public static $cacheKeyPrefix = 'UniProgy.UWorklet.';

    /**
     * Constructor.
     * @param UModule module
     * @param string worklet id
     * @param array behaviors that need to be attached to worklet
     */
    public function __construct($module, $id, $behaviors) {
        $this->_id = $id;
        $this->_module = $module;
        $this->attachBehaviors($behaviors);
    }

    /**
     * Calls the named method which is not a class method.
     * Do not call this method. This is a PHP magic method that we override
     * to implement the behavior feature.
     * @param string the method name
     * @param array method parameters
     * @return mixed the method return value
     */
    public function __call($name, $parameters) {
        // behaviors before
        if ($this->_b !== null) {
            $before = null;
            foreach ($this->_b as $object)
                if ($object->getEnabled() && method_exists($object, 'before' . $name))
                    if (($before = call_user_func_array(array($object, 'before' . $name), $parameters)) !== null)
                        return $before;
        }

        // worklet before
        $before = null;
        if (method_exists($this, 'before' . $name))
            if (($before = call_user_func_array(array($this, 'before' . $name), $parameters)) !== null)
                return $before;
        // worklet task
        $result = method_exists($this, 'task' . $name) ? call_user_func_array(array($this, 'task' . $name), $parameters) : parent::__call('task' . $name, $parameters);
        // worklet after			
        $parameters['result'] = $result;
        if (method_exists($this, 'after' . $name))
            if (($after = call_user_func_array(array($this, 'after' . $name), $parameters)) !== null)
                $result = $after;

        // behaviors after
        if ($this->_b !== null) {
            reset($this->_b);
            foreach ($this->_b as $object)
                if ($object->getEnabled() && method_exists($object, 'after' . $name))
                    if (($after = call_user_func_array(array($object, 'after' . $name), $parameters)) !== null)
                        $parameters['result'] = $result = $after;
        }

        return $result;
    }

    /**
     * Initializes worklet.
     */
    public function init() {
        
    }

    /**
     * Runs worklet.
     */
    public function run() {
        
    }

    /**
     * Attaches behaviors to the worklet one by one.
     * @param array behaviors
     * @see UWorkletBehavior
     */
    public function attachBehaviors($behaviors) {
        foreach ($behaviors as $key => $behavior) {
            $name = is_numeric($key) ? $behavior : $key;
            $this->attachBehavior($name, $behavior);
        }
    }

    /**
     * Attaches behavior to the worklet.
     * @param string behavior name
     * @param mixed behavior instance or id
     * @return mixed behavior instance or boolean false
     */
    public function attachBehavior($name, $behavior) {
        if (!($behavior instanceof IBehavior)) {
            if (!is_array($behavior))
                $behavior = UFactory::getBehavior($behavior);
            else
                $behavior = UFactory::getBehavior($name, $behavior);
        }
        if ($behavior && $behavior->valid($this)) {
            parent::attachBehavior($name, $behavior);
            return $this->_b[$name] = $behavior;
        }
        return false;
    }

    /**
     * Detaches behavior from the worklet.
     * @param string behavior name
     * @return mixed behavior instance or boolean false
     */
    public function detachBehavior($name) {
        if (isset($this->_b[$name])) {
            $this->_b[$name]->detach($this);
            $behavior = $this->_b[$name];
            unset($this->_b[$name]);
            parent::detachBehavior($name);
            return $behavior;
        }
        return false;
    }

    /**
     * Worklet id getter.
     * @return string worklet id
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * Worklet id setter.
     * @param string worklet id
     */
    public function setId($value) {
        $this->_id = $value;
    }

    /**
     * Module getter.
     * @return UModule module object.
     */
    public function getModule() { 
        if(is_array($this->_module)){
            return $this->_module[0];
        }
        return $this->_module;
    }

    /**
     * Worklet name getter.
     * @return string worklet name.
     */
    public function getName() {
        static $name;
        if (!isset($name))
            $name = substr($this->getId(), strrpos($this->getId(), '.') + 1);
        return $name;
    }

    /**
     * Worklet parent path getter.
     * @return worklet parent path.
     */
    public function getParentPath() {
        static $path;
        if (!isset($path))
            $path = substr($this->getPath(), 0, strrpos($this->getPath(), '/'));
        return $path;
    }

    /**
     * Worklet path getter.
     * @return string worklet path
     */
    public function getPath() {
        static $path;
        if (!isset($path))
            $path = str_replace('.', '/', $this->getId());
        return $path;
    }

    /**
     * Shortcut to {@link UWebModule::t}.
     * @param string message
     * @param array parameters
     * @param string source
     * @param string language
     * @return string translated message
     */
    public function t($message, $params = array(), $source = null, $language = null) {             
        return $this->module->t($message, $params, $source, $language);
    }

    /**
     * Worklet session getter.
     * @return array worklet session
     */
    public function getSession() {
        if (!isset(app()->session[$this->id]))
            app()->session[$this->id] = array();

        return app()->session[$this->id];
    }

    /**
     * Worklet session setter.
     * @param array value to be stored in a session
     */
    public function setSession($value) {
        if ($value === null) {
            if (isset(app()->session[$this->id]))
                unset(app()->session[$this->id]);
            return;
        }
        $session = $this->getSession();
        foreach ($value as $k => $v) {
            if ($v == null)
                unset($session[$k]);
            else
                $session[$k] = $v;
        }
        app()->session[$this->id] = $session;
    }

    /**
     * Saves data into cache.
     * @param string item name
     * @param mixed item value
     * @param boolean vary cache by currently attached behaviors
     * @param CCacheDependency cache dependency as described here: {@link http://www.yiiframework.com/doc/api/CCacheDependency}
     * @param integer cache duration in seconds
     */
    public function cacheSet($name, $value, $varyByBehaviors = false, $dependency = null, $duration = 0) {
        $duration = app()->params['maxCacheDuration'];
        app()->cache->set($this->getCacheKeyPrefix($varyByBehaviors) . $name, $value, $duration, $dependency);
    }

    /**
     * Retrieves data from cache.
     * @param string item name
     * @param boolean vary cache by currently attached behaviors
     * @return mixed cached item value (if found)
     */
    public function cacheGet($name, $varyByBehaviors = false) {
        return app()->cache->get($this->getCacheKeyPrefix($varyByBehaviors) . $name);
    }

    /**
     * Returns cache prefix. If $varyByBehaviors is true it will also go through all
     * behaviors and add their prefixes to the result.
     * @param boolean vary cache by currently attached behaviors
     * @return string cache prefix
     */
    public function getCacheKeyPrefix($varyByBehaviors = false) {
        $prefix = self::$cacheKeyPrefix . $this->getId() . '.';
        if ($varyByBehaviors && is_array($this->_b))
            $prefix.= md5(serialize(array_keys($this->_b))) . '.';
        return $prefix;
    }

    /**
     * @return CChainedCacheDependency current class and all attached behaviors combined cache dependency
     */
    public function cacheDependency() {
        // going through all behaviors and getting their cache dependencies
        $d = array();
        if (is_array($this->_b)) {
            foreach ($this->_b as $alias => $obj) {
                if (($cD = $obj->cacheDependency()) !== false)
                    $d[] = $cD;
            }
            reset($this->_b);
        }
        if (!count($d))
            return null;

        // adding all found cache dependencies into a chain
        $dependency = new CChainedCacheDependency;
        $dependency->setDependencies($d);
        return $dependency;
    }

    /**
     * This method is required by CBaseComponent.
     * @param string view name
     * @return boolean false
     */
    public function getViewFile($viewName) {
        return false;
    }

    /**
     * A shortcut to {@link http://www.yiiframework.com/doc/api/CModule#getParams-detail}
     * @param string parameter name
     * @return mixed parameter value
     */
    public function param($name) {
        return isset($this->module->params[$name]) ? $this->module->params[$name] : null;
    }

    /**
     * Denies access to worklet.
     * @param mixed null or Yii::app()->getUser() instance
     * @param string error message to be shown
     * @throws CHttpException when user is not a guest
     */
    protected function accessDenied($user = null, $message = null) {
        $user = $user === null ? app()->user : $user;
        if ($user->getIsGuest())
            $user->loginRequired();
        else
            throw new CHttpException(403, $message !== null ? $message : Yii::t('yii', 'You are not authorized to perform this action.'));
    }

    /**
     * Override this function to disable worklet permanently or
     * under certain circumstances.
     * @return boolean
     */
    public function taskEnabled() {
        return true;
    }

    /**
     * Override this function to allow/disallow access to the worklet.
     * @return boolean
     */
    public function taskAccess() {
        return true;
    }

}
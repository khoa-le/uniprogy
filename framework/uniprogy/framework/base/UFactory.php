<?php
/**
 * UFactory class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UFactory is the main factory class for creating such items as worklets, filters, behaviors, etc.
 *
 * @version $Id: UFactory.php 66 2010-10-15 11:26:00 GMT vasiliy.y $
 * @package system.base
 * @since 1.1.2
 */
class UFactory
{
    private static $_instances = array();
    /**
     * @var string cache key prefix
     */
    public static $cacheKeyPrefix = 'UniProgy.UFactory';

    /**
     * Tries to retrieve item from local instances param
     * and cache.
     * @param string item id
     * @return mixed item or null
     */
    public static function getItem($key) {
        if (!isset(self::$_instances[$key])) {
            $data = self::getFromCache($key);
            if($data)
                return self::$_instances[$key] = $data;
        }

        return null;
    }

    /**
     * Saves the item in local instances param and cache (optionally).
     *
     * @param string item id
     * @param mixed item
     * @param boolean whether save it to the cache or not
     */
    public static function setItem($key, $value, $toCache = true) {
        self::$_instances[$key] = $value;
        if ($toCache)
            self::saveToCache($key, $value);
    }

    /**
     * Creates a component of special type.
     *
     * @param string alias
     * @param string item type
     * @param array configuration
     * @return mixed component or null
     */
    public static function getComponent($alias, $type, $config = array()) {
        $key = 'component.' . $alias . '.' . $type;
        if (isset(self::$_instances[$key]))
            return self::$_instances[$key];

        $c = self::getFromCache($key);
        if (is_array($c)) {
            extract($c);
            $module = self::getModuleWithParents($module);
        } else {
            list($module, $alias) = self::getModuleWithParents($alias);
            if (!$module)
                return null;

            $alias = '.' . rtrim($alias, '.');
            $paths = explode('.', substr($alias, 0, strrpos($alias, '.')));
            $itemName = substr($alias, strrpos($alias, '.') + 1);

            $className = '';

            $id = explode('/', $module->getId());
            $id = implode('', array_map('ucfirst', $id));
            $className = $id . $className;

            $path = 'application.modules.'
                    . str_replace('.', '.modules.', self::getModuleAlias($module)) . '.' . $type;
            if (count($paths)) {
                $path.= implode('.', $paths);
                $className.= implode('', array_map('ucfirst', $paths));
            }
            $letter = strtoupper(substr($type, 0, 1));
            $path.= '.' . $letter . $className . ucfirst($itemName);

            self::saveToCache($key, array('module' => self::getModuleAlias($module),
                'path' => $path));
        }

        if (!file_exists(Yii::getPathOfAlias($path) . '.php'))
            return null;

        $config['class'] = $path;
        $args = array_slice(func_get_args(), 3);
        array_unshift($args, $module);
        array_unshift($args, $config);

        $component = call_user_func_array(array('Yii', 'createComponent'), $args);

        self::$_instances[$key] = $component;
        return $component;
    }

    /**
     * @param string worklet alias
     * @param array configuration
     * @return UWorklet worklet instance or null
     */
    public static function getWorklet($alias, $config = array()) {
        $args = array_slice(func_get_args(), 2);
        array_unshift($args, $alias);
        array_unshift($args, $config);
        array_unshift($args, 'worklets');
        array_unshift($args, $alias);
        return call_user_func_array(array('UFactory', 'getComponent'), $args);
    }

    /**
     * @param string filter alias
     * @param array configuration
     * @return UWorkletFilter filter instance or null
     */
    public static function getFilter($alias, $config = array()) {
        return self::getComponent($alias, 'filters', $config);
    }

    /**
     * @param string behavior alias
     * @param array configuration
     * @return UWorkletBehavior behavior instance or null
     */
    public static function getBehavior($alias, $config = array()) {
        return self::getComponent($alias, 'behaviors', $config);
    }

    /**
     * @param string module alias
     * @return UModule module instance or null
     */
    public static function getModuleFromAlias($alias) {
        list($module, $alias) = self::getModuleWithParents($alias);
        return $module;
    }

    /**
     * Builds a module with all it's parents out of an alias.
     * @param string alias
     * @param UModule parent module
     * @return array first element - module instance; second element - alias with all found modules stripped from it
     */
    public static function getModuleWithParents($alias, $module = null) {
        if ($module === null)
            if (($c = self::getItem('module.' . $alias)) !== null)
                return $c;

        if ($module === null)
            $module = app();
        $alias = $alias . '.';
        if (($pos = strpos($alias, '.')) !== false) {
            $id = substr($alias, 0, $pos);
            if (($child = $module->getModule($id)) === null)
                if (!file_exists($module->basePath . DS . 'controllers' . DS . ucfirst($id) . 'Controller.php'))
                    $child = $module->getModule($id);
            if ($child) {
                $alias = substr($alias, $pos + 1);
                $result = self::getModuleWithParents($alias, $child);
                if ($module === app())
                    self::setItem('module.' . $alias, $result, false);
                return $result;
            }
        }

        if ($module === app())
            $module = null;
        return array($module, $alias);
    }

    /**
     * @param UModule module instance
     * @return string full alias to the module with all it's parents
     */
    public static function getModuleAlias($module) {
        if (($c = self::getItem('moduleAlias' . $module->getId())) !== null)
            return $c;

        $alias = '';
        $parent = $module;
        while ($parent !== null) {
            $alias = ($parent === app() ? 'app' : $parent->name) . '.' . $alias;
            $parent = $parent->getParentModule();
        }
        $result = trim($alias, '.');
        self::setItem('moduleAlias' . $module->getId(), $result);
        return $result;
    }

    /**
     * Tries to retrieve item info from the cache.
     * @return mixed item info or null
     */
    public static function getFromCache($key) {
        $key = self::$cacheKeyPrefix . $key;
        return app()->cache->get($key);
    }

    /**
     * Saves item to the cache.
     * @param string item id
     * @param mixed item data
     */
    public static function saveToCache($key, $value) {
        $key = self::$cacheKeyPrefix . $key;
        app()->cache->set($key, $value);
    }
}
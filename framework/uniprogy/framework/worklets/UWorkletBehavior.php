<?php

/**
 * UWorkletBehavior class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UWorkletBehavior is a base class for worklet behaviors.
 *
 * @version $Id: UWorkletBehavior.php 65 2010-10-14 17:55:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UWorkletBehavior extends CComponent implements IBehavior {

    private $_enabled;
    private $_owner;
    private $_module;

    /**
     * Constructor.
     * @param CModule module
     */
    public function __construct($module) {
        $this->_module = $module;
    }

    /**
     * Attaches the behavior object to the worklet.
     * The default implementation will set the {@link owner} property.
     * Make sure you call the parent implementation if you override this method.
     * @param UWorklet the worklet that this behavior is to be attached to.
     */
    public function attach($owner) {
        $this->_owner = $owner;
    }

    /**
     * Detaches the behavior object from the worklet.
     * The default implementation will unset the {@link owner} property.
     * Make sure you call the parent implementation if you override this method.
     * @param UWorklet the worklet that this behavior is to be detached from.
     */
    public function detach($owner) {
        $this->_owner = null;
    }

    /**
     * @return UWorklet the owner worklet that this behavior is attached to.
     */
    public function getOwner() {
        return $this->_owner;
    }

    /**
     * @return boolean whether this behavior is enabled.
     */
    public function getEnabled() {
        return $this->_enabled;
    }

    /**
     * @param boolean whether this behavior is enabled.
     */
    public function setEnabled($value) {
        $this->_enabled = $value;
    }

    /**
     * @param UWorklet the owner worklet.
     * @return boolean whether this behavior is valid and can be attached.
     */
    public function valid($owner) {
        return true;
    }

    /**
     * @return mixed {@link http://www.yiiframework.com/doc/api/CCacheDependency CCacheDependency} or boolean false.
     */
    public function cacheDependency() {
        return false;
    }

    /**
     * @return UModule behavior module object.
     */
    public function getModule() {
        if (is_array($this->_module)) {
            return $this->_module[0];
        }
        return $this->_module;
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

}
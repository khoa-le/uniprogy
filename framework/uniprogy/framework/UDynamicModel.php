<?php
/**
 * UDynamicModel class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2012 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UDynamicModel is a model class with dynamic attributes.
 * 
 * Form model requires attributes to be declared as class properties, active record
 * gets attributes from DB table column names.
 * 
 * Using UDynamicModel you can import attribute names, rules and labels.
 * 
 * This is useful in a situation when some model attributes have to be dynamic.
 *
 * @version $Id: UDynamicModel.php 1 2011-05-26 10:07:00 GMT vasiliy.y $
 * @package system
 * @since 1.4.0
 */
class UDynamicModel extends CModel
{
	private $_attributes = array();
    private $_names = array();
	private $_rules = array();
	private $_labels = array();

	/**
	 * @return array imported rules.
	 */
    public function rules()
    {
        return $this->_rules;
    }
	
	/**
	 * Imports model attributes name, rules and labels.
	 * @var array attributes names
	 * @var array attributes rules
	 * @var array attributes labels
	 */
	public function import($names, $rules, $labels = array())
	{
		$this->_names = $names;
        foreach($names as $k)
            $this->_attributes[$k] = null;
		$this->_rules = $rules;
		$this->_labels = $labels;
	}

	/**
	 * Returns the list of attribute names of the model.
	 * @return array list of attribute names.
	 */
    public function attributeNames() {
        return $this->_names;
    }
	
	/**
	 * Returns the attribute labels.
	 * @return array attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return $this->_labels;
	}

	/**
	 * Setter: tries to set an attribute value. If fails - calls parent method.
	 * @param string attribute name
	 * @param mixed attribute value
	 */
    public function __set($name, $value) {
        if($this->setAttribute($name,$value)===false)
			parent::__set($name,$value);
    }

	/**
	 * Sets a value for model attribute.
	 * @param string attribute name
	 * @param mixed attribute value
	 * @return bool whether attribute has been successfully set or not.
	 */
    public function setAttribute($name,$value)
	{
		if(property_exists($this,$name))
			$this->$name=$value;
        elseif(in_array($name, $this->_names))
            $this->_attributes[$name] = $value;
		else
			return false;
		return true;
	}

	/**
	 * Attribute getter. If fails to find attribute - calls parent function.
	 * @param string attribute name
	 * @return mixed attribute value
	 */
    public function __get($name)
	{
		if(in_array($name, $this->_names))
			return $this->_attributes[$name];
		else
			return parent::__get($name);
	}
}
<?php
/**
 * UDateTimeValidator class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UDateTimeValidator is a validator class for date and time field input validation.
 *
 * @version $Id: UDateTimeValidator.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.validators
 * @since 1.0.0
 */
class UDateTimeValidator extends CValidator
{
	/**
	 * @var string required fields as a string of latin characters.
	 * h - hour; m - minute; d - day; M - month; y - year
	 */
	public $require = 'hmdMy';
	/**
	 * @var boolean whether the attribute value can be null or empty. Defaults to true,
	 * meaning that if the attribute is empty, it is considered valid.
	 */
	public $allowEmpty = true;
	
	/**
	 * Validates the attribute of the object.
	 * If there is any error, the error message is added to the object.
	 * @param CModel the object being validated
	 * @param string the attribute being validated
	 */
	public function validateAttribute($object,$attribute)
	{
		$value = $object->$attribute;
		if($this->allowEmpty && $this->isEmpty($value))
			return;
		if(!$this->validateValue($value))
		{
			$message=$this->message!==null?$this->message:t('Please input correct date.');
			$this->addError($object,$attribute,$message);
		}
	}
	
	/**
	 * Validates a static value to see if it is a valid email.
	 * Note that this method does not respect {@link allowEmpty} property.
	 * This method is provided so that you can call it directly without going through the model validation rule mechanism.
	 * @param mixed the value to be validated
	 * @return boolean whether the value is a valid email
	 */
	public function validateValue($value)
	{
		for($i=0;$i<strlen($this->require);$i++) {
			if($this->require[$i] == 'h')
				return 
					(isset($value['h']) && $value['h'] !== '' && isset($value['a']) && $value['a'] !== '')
					|| (isset($value['H']) && $value['H'] !== '');
			else
				return isset($value[$this->require[$i]]) && $value[$this->require[$i]] !== '';
		}
	}
	
	/**
	 * Checks if the given value is empty.
	 * A value is considered empty if it is null, an empty array, or the trimmed result is an empty string.
	 * Note that this method is different from PHP empty(). It will return false when the value is 0.
	 * Additionally if array is not empty, but all it's values are - it will still return false.
	 * @param mixed the value to be checked
	 * @param boolean whether to perform trimming before checking if the string is empty. Defaults to false.
	 * @return boolean whether the value is empty
	 */
	public function isEmpty($value,$trim=false)
	{
		if(!is_array($value))
			return parent::isEmpty($value,$trim);
		else
			foreach($value as $k=>$v)
				if($v !== '')
					return false;
		return true;
	}
}
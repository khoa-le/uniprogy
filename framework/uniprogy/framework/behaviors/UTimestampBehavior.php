<?php
/**
 * UTimestampBehavior class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UTimestampBehavior automatically addes created/modified timestamps to active record it is attached to.
 *
 * @version $Id: UTimestampBehavior.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.behaviors
 * @since 1.0.0
 */
class UTimestampBehavior extends CActiveRecordBehavior {
	/**
	* The field that stores the creation time.
	*/
	public $created = 'created';
	/**
	* The field that stores the modification time.
	*/
	public $modified = 'modified';	
	
	/**
	 * Updates "created" field if this is a new record
	 * and "modified" if this is an existing one.
	 * @return boolean true.
	 */
	public function beforeValidate($on) {
		if ($this->Owner->getIsNewRecord()) {
			if ($this->created)
				$this->Owner->{$this->created} = time();
		} else {
			if ($this->modified)
				$this->Owner->{$this->modified} = time();
		}			
		return true;	
	}
}
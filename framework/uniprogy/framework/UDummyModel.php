<?php
/**
 * UDummyModel class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UDummyModel is an empty model class.
 *
 * @version $Id: UDummyModel.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system
 * @since 1.0.0
 */
class UDummyModel extends CFormModel
{
	/**
	 * @var mixed preset attribute
	 */
	public $attribute;
	
	/**
	 * Declares preset attribute as safe.
	 * @return array rules
	 */
	public function rules()
	{
		return array(
			array('attribute','safe')
		);
	}
}
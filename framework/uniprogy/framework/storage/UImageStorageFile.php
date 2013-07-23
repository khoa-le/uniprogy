<?php
/**
 * UImageStorageFile class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UImageStorageFile is an extension of {@link UStorageFile} specially designed
 * to work with image files which are currenty being processed by Image extension.
 *
 * @version $Id: UImageStorageFile.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.storage
 * @since 1.0.0
 */
class UImageStorageFile extends UStorageFile
{
	/**
	 * Saves image into a file.
	 * @param string destination path.
	 * @return boolean file save result.
	 */
	public function save($path)
	{
		$path.= '.' . strtolower($this->getSource()->ext);
		$this->getSource()->save($path);
		return app()->file->set($path);
	}
}
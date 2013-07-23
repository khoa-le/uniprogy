<?php
/**
 * UStorageFile class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UStorageFile is a wrapper class which an actual file is put in for further processing by a storage bin.
 *
 * @version $Id: UStorageFile.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.storage
 * @since 1.0.0
 * @see UStorageBin
 * @see UStorage
 */
class UStorageFile
{
	private $_source = null;
	
	/**
	 * Constructor.
	 * @param mixed file source.
	 */
	public function __construct($source)
	{
		$this->_source = $source;
	}
	
	/**
	 * File source getter.
	 * @return file source.
	 */
	public function getSource()
	{
		return $this->_source;
	}
	
	/**
	 * Saves a file to specified path.
	 * @param string path where file needs to be saved.
	 * @return boolean result.
	 */
	public function save($path)
	{
		$file = app()->file->set($this->_source);
		$path.= '.' . strtolower($file->extension);
		return $file->isUploaded ? $file->copy($path) : $file->rename($path);
	}
}
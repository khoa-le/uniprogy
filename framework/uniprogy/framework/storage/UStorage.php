<?php
/**
 * UStorage class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UStorage is the main class that helps to work with framework file storage.
 * By default it is loaded as application component and therefore can be called
 * as <code>app()->storage</code> anywhere in the script.
 * 
 * Usually you would call UStorage only to get or create a storage bin with a particular ID.
 * 
 * Create new bin:
 * <pre>
 * $bin = app()->storage->bin();
 * </pre>
 * 
 * Get existing bin:
 * <pre>
 * $bin = app()->storage->bin($id);
 * </pre>
 *
 * @version $Id: UStorage.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.storage
 * @since 1.0.0
 */
class UStorage extends CComponent
{
	/**
	 * @var string path alias to storage directory.
	 */
	private $_path = 'webroot.storage';
	/**
	 * @var string the name of the class for representing a storage bin. Defaults to 'UStorageBin'.
	 */
	public $storageBinClass = 'UStorageBin';
	/**
	 * @var string the name of the class for storage bin model. Defaults to 'MStorageBin'.
	 */
	public $storageBinModelClass = 'MStorageBin';
	/**
	 * @var string the name of the class for storage file model. Defaults to 'MStorageFile'.
	 */
	public $storageFileModelClass = 'MStorageFile';
	
	/**
	 * Initializes the application component.
	 */
	public function init()
	{
	}
	
	/**
	 * Storage directory setter.
	 * @param string path alias.
	 */
	public function setPath($value)
	{
		$this->_path = $value;
	}
	
	/**
	 * Storage directory getter.
	 * @return string current storage directory path alias.
	 */
	public function getPath()
	{
		return $this->_path;
	}
	
	/**
	 * Returns storage bin using {@link storageBinClass}.
	 * @param integer bin ID; if empty - new bin will be created.
	 * @return instance of {@link storageBinClass}.
	 */
	public function bin($id=null)
	{
		return call_user_func(array($this->storageBinClass, 'bin'), $this, $id);
	}
}
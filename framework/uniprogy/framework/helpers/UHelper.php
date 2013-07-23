<?php
/**
 * UHelper class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UHelper is a static helper class that provides a collection of helper methods for commonly used tasks.
 *
 * @version $Id: UHelper.php 78 2010-11-24 13:28:00 GMT vasiliy.y $
 * @package system.helpers
 * @since 1.0.0
 */
class UHelper
{
	/**
	 * Creates a random salt of a fixed length.
	 * @param integer length of the salt to return.
	 * @return string random salt.
	 */
	public static function salt($length=3)
	{
		srand((double)microtime()*1000000);
		
		$randSym  = "!@#$%^&*()_+=-';:,<.>`~?[]{}";
		$randChar = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"; 
		
		$salt = array();
		
		while($length > 0) {
			$str = $length%3==0 ? $randSym : $randChar;
			$salt[] = $str[rand() % strlen($str) - .04];
			$length--;
		}
		
		shuffle($salt);
		
		// reseed
		mt_srand(); 
		
		return(implode("", $salt));
	}
	
	/**
	 * Finds a unique md5-encrypted hash based on a Model provided.
	 * @param string class name of the model to check uniqueness in.
	 * @param integer length of hash to return.
	 * @return string unique hash.
	 */
	public static function hash($className='MHash',$length=32)
	{
		$exists = true;
		$hash = null;
		while($exists) {
			$hash = md5( self::salt(5) . time() );
			if($length < 32)
				$hash = substr($hash,0,$length);
			if($className !== null)
				$exists = CActiveRecord::model($className)->exists('hash=?',array($hash));
			else
				$exists = false;
		}
		return $hash;
	}
	
	/**
	 * Encrypts password.
	 * @param string un-encrypted password.
	 * @param string salt.
	 * @return string encrypted password.
	 */
	public static function password($str, $salt)
	{
		return md5(md5($str) . $salt);
	}
	
	/**
	 * Turns a string into camel-case format.
	 * @param string source string.
	 * @return string string in a camel-case format.
	 */
	public static function camelize($str)
	{
		$words = preg_split("/[^A-Za-z0-9]/", $str);
		return implode("",array_map("ucfirst", $words));
	}
	
	/**
	 * Returns data from a file if one exists in a special data directory.
	 * @param string file name without extension.
	 * @return mixed file contents.
	 * @throws CException if file doesn't exist.
	 */
	public static function data($name)
	{
		$dataFile = Yii::getPathOfAlias('application.data.' . $name).'.php';
		if(is_file($dataFile))
			return require($dataFile);
		else
			throw new CException(500,t('Unrecognized data source "{name}".', 'yii',
				array('{name}'=>$name)));
	}
	
	/**
	 * Turns dot-separated path into URL.
	 * @param string dot-separated path.
	 * @param string schema to use (e.g. http, https). If empty, the schema used for the current request will be used.
	 * @return string URL.
	 */
	public static function pathToUrl($path,$schema='')
	{
		return aUrl('/',array(),$schema) . str_replace(".", "/", str_replace("webroot", "", $path));
	}
	
	/**
	 * Extracts width and height into an array from a "WIDTHxHEIGHT" string.
	 * @param string width-height string.
	 * @return array width and height as elements of array.
	 */
	public static function dims($str)
	{
		return explode('x', $str);
	}
	
	/**
	 * Saves configuration into a file.
	 * There's no need to provide full array, just the part that needs to be updated.
	 * 
	 * Ex.:
	 * current configuration:
	 * <pre>
	 * array(
	 *     'key1'=>'value1',
	 *     'key2'=>'value2'
	 * )
	 * </pre>
	 * configuration, that needs to be updated:
	 * <pre>
	 * array(
	 *     'key1'=>'newValue'
	 * )
	 * </pre>
	 * @param string configuration file name.
	 * @param array configuration to be saved.
	 * @param boolean whether to replace matching configuration recrods or not
	 * @return boolean true if configuration has been successfully saved.
	 */
	public static function saveConfig($file,$config,$replace=true)
	{
		$old = require($file);
		$new = $replace?CMap::mergeArray($old,$config):CMap::mergeArray($config,$old);
		$new = self::unsetNulls($new);
		return app()->file->set($file)->setContents("<?php\n return " . var_export($new,true) . ';');
	}
	
	/**
	 * @param array to parse
	 * @return array with NULL values unset
	 */
	public static function unsetNulls($value)
	{
		if(is_array($value))
		{
			foreach($value as $k=>$v)
			{
				if(is_array($v))
					$value[$k] = self::unsetNulls($v);
				elseif($v === NULL)
					unset($value[$k]);
			}
		}
		return $value;
	}
}
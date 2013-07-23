<?php
/**
 * UWebAppCommand class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UWebAppCommand is an extension of Yii's WebAppCommand.
 *
 * @version $Id: UWebAppCommand.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.yii.cli.commands
 * @since 1.0.0
 */
require(YII_PATH.'/cli/commands/WebAppCommand.php');
class UWebAppCommand extends WebAppCommand
{
	/**
	 * Execute the action.
	 * @param array command line parameters specific for this command
	 */
	public function run($args)
	{
		if(!isset($args[0]))
			$this->usageError('the Web application location is not specified.');
		$path=strtr($args[0],'/\\',DIRECTORY_SEPARATOR);
		if(strpos($path,DIRECTORY_SEPARATOR)===false)
			$path='.'.DIRECTORY_SEPARATOR.$path;
		$dir=rtrim(realpath(dirname($path)),'\\/');
		if($dir===false || !is_dir($dir))
			$this->usageError("The directory '$path' is not valid. Please make sure the parent directory exists.");
		if(basename($path)==='.')
			$this->_rootPath=$path=$dir;
		else
			$this->_rootPath=$path=$dir.DIRECTORY_SEPARATOR.basename($path);
		echo "Create a Web application under '$path'? [Yes|No] ";
		if(!strncasecmp(trim(fgets(STDIN)),'y',1))
		{
			$sourceDir=realpath(dirname(__FILE__).'/../views/webapp');
			if($sourceDir===false)
				die('Unable to locate the source directory.');
			$list=$this->buildFileList($sourceDir,$path);
			$list['index.php']['callback']=array($this,'generateIndex');
			$this->copyFiles($list);
			@chmod($path.'/assets',0777);
			@chmod($path.'/protected/runtime',0777);
			@chmod($path.'/protected/data',0777);
			@chmod($path.'/storage',0777);
			echo "\nYour application has been created successfully under {$path}.\n";
		}
	}
}
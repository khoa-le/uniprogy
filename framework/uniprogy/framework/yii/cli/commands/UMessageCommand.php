<?php
/**
 * UMessageCommand class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UMessageCommand is an extension of Yii's MessageCommand.
 *
 * @version $Id: UMessageCommand.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.yii.cli.commands
 * @since 1.0.0
 */
require(YII_PATH.'/cli/commands/MessageCommand.php');
class UMessageCommand extends MessageCommand
{
	protected function extractMessages($fileName,$translator)
	{
		echo "Extracting messages from $fileName...\n";
		$subject=file_get_contents($fileName);
		$n=preg_match_all('/\b'.$translator.'\s*\(\s*(\'.*?(?<!\\\\)\'|".*?(?<!\\\\)")\s*[,\)]/s',$subject,$matches,PREG_SET_ORDER);
		$messages=array();
		for($i=0;$i<$n;++$i)
		{
			$category = 'uniprogy';
			$message=$matches[$i][1];
			$messages[$category][]=eval("return $message;");  // use eval to eliminate quote escape
		}
		return $messages;
	}
}
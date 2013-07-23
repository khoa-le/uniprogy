<?php
/**
 * UModuleCommand class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UModuleCommand is an extension of Yii's ModuleCommand.
 *
 * @version $Id: UModuleCommand.php 20 2010-09-15 07:18:00 GMT vasiliy.y $
 * @package system.yii.cli.commands.shell
 * @since 1.0.0
 */
require(YII_PATH.'/cli/commands/shell/ModuleCommand.php');
class UModuleCommand extends ModuleCommand
{	
	/**
	 * Execute the action.
	 * @param array command line parameters specific for this command
	 */
	public function run($args)
	{
		$this->templatePath = UP_PATH.'/yii/cli/views/shell/module';
		return parent::run($args);
	}
}
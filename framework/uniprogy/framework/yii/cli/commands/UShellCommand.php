<?php
/**
 * UShellCommand class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UShellCommand is an extension of Yii's ShellCommand.
 *
 * @version $Id: UShellCommand.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.yii.cli.commands
 * @since 1.0.0
 */
require(YII_PATH.'/cli/commands/ShellCommand.php');
class UShellCommand extends ShellCommand
{
	protected function runShell()
	{
		// disable E_NOTICE so that the shell is more friendly
		error_reporting(E_ALL ^ E_NOTICE);

		$_runner_=new CConsoleCommandRunner;
		$_runner_->addCommands(dirname(__FILE__).'/shell');
		$_runner_->addCommands(Yii::getPathOfAlias('application.commands.shell'));
		if(($_path_=@getenv('YIIC_SHELL_COMMAND_PATH'))!==false)
			$_runner_->addCommands($_path_);
		$_commands_=$_runner_->commands;

		while(($_line_=$this->readline("\n>> "))!==false)
		{
			$_line_=trim($_line_);
			try
			{
				$_args_=preg_split('/[\s,]+/',rtrim($_line_,';'),-1,PREG_SPLIT_NO_EMPTY);
				if(isset($_args_[0]) && isset($_commands_[$_args_[0]]))
				{
					$_command_=$_runner_->createCommand($_args_[0]);
					array_shift($_args_);
					$_command_->run($_args_);
				}
				else
					echo eval($_line_.';');
			}
			catch(Exception $e)
			{
				if($e instanceof ShellException)
					echo $e->getMessage();
				else
					echo $e;
			}
		}
	}
}
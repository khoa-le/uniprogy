<?php
/**
 * UModelCommand class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UModelCommand is an extension of Yii's ModelCommand.
 *
 * @version $Id: UModelCommand.php 20 2010-09-15 07:18:00 GMT vasiliy.y $
 * @package system.yii.cli.commands.shell
 * @since 1.0.0
 */
require(YII_PATH.'/cli/commands/shell/ModelCommand.php');
class UModelCommand extends ModelCommand
{
	/**
	 * @var string the directory that contains test fixtures.
	 * Defaults to null, meaning using 'protected/tests/fixtures'.
	 * If this is false, it means fixture file should NOT be generated.
	 */
	public $fixturePath = false;
	/**
	 * @var string the directory that contains unit test classes.
	 * Defaults to null, meaning using 'protected/tests/unit'.
	 * If this is false, it means unit test file should NOT be generated.
	 */
	public $unitTestPath = false;
	
	/**
	 * Execute the action.
	 * @param array command line parameters specific for this command
	 */
	public function run($args)
	{
		$this->templatePath = UP_PATH.'/yii/cli/views/shell/model';
		return parent::run($args);
	}
	
	/**
	 * Generates model class name based on a table name
	 * @param string the table name
	 * @return string the generated model class name
	 */
	protected function generateClassName($tableName)
	{
		return 'M'.parent::generateClassName($tableName);
	}
}
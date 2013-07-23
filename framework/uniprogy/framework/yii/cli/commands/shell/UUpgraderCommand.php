<?php
/**
 * UUpgraderCommand class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UUpgraderCommand generates upgrader worklets.
 *
 * @version $Id: UUpgraderCommand.php 72 2010-11-09 09:26:00 GMT vasiliy.y $
 * @package system.yii.cli.commands.shell
 * @since 1.1.2
 */
class UUpgraderCommand extends CConsoleCommand
{
	public function getHelp()
	{
		return <<<EOD
USAGE
  uupgrader <module(s)> <version_from> <version_to>

DESCRIPTION
  This command generates basic upgrade worklet for a module.

PARAMETERS
 * module(s): required, module or modules (separated by colon) names.
 
 * version_from: required, which version should worklet upgrade from.
 
 * version_to: required, which version should worklet upgrade to.

EXAMPLES
 * Generates the upgrader for all application modules from 1.0.0 to 1.0.1 version:
        uupgrader app 1.0.0 1.0.1
        
 * Generates the upgrader for 'base' module from 1.0.0 to 1.0.1 version:
        uupgrader base 1.0.0 1.0.1

 * Generates the upgraders for 'base' and 'user' modules from 1.0.0 to 1.0.1 version:
        uupgrader base:user 1.0.0 1.0.1
        
EOD;
	}
	
	/**
	 * Execute the action.
	 * @param array command line parameters specific for this command
	 */
	public function run($args)
	{
		if(!isset($args[0]))
		{
			echo "Error: module name is required.\n";
			echo $this->getHelp();
			return;
		}
		$module = $args[0];
		
		if(!isset($args[1]))
		{
			echo "Error: version from is required.\n";
			echo $this->getHelp();
			return;
		}
		$fromVersion = $args[1];
		
		if(!isset($args[2]))
		{
			echo "Error: version to is required.\n";
			echo $this->getHelp();
			return;
		}
		$toVersion = $args[2];
		
		$modules = array();
		if($module == 'app')
		{
			$modules = app()->getAppModules();
			$modules[] = 'app';
		}
		elseif(strpos($module,':')!==false)
			$modules = explode(':',$module);
		else
			$modules[] = $module;
			
		$cfg = require(app()->basePath.'/config/public/modules.php');
			
		foreach($modules as $m)
			$this->buildUpgrader($m,$fromVersion,$toVersion,$cfg);
			
		echo "Upgrader successfully created.";
		return;
	}
	
	/**
	 * Builds upgrader.
	 * @param string alias
	 * @param string version from
	 * @param string version to
	 */
	public function buildUpgrader($alias,$from,$to,$cfg)
	{
		$filters = array();
		$temp = $cfg['modules'];
		while(is_array($temp) && count($temp))
		{
			foreach($temp as $id=>$m)
			{
				if(isset($m['filters']))
					foreach($m['filters'] as $f=>$d)
						if(strstr($f,$alias))
							$filters[$id] = $f;
			}
			$temp = isset($temp['modules'])?$temp['modules']:null;
		}
		
		if($alias == 'app')
		{
			$className = 'WInstallAppUpgrade_'.str_replace('.','_',$to);
			$targetPath = UFactory::getModuleFromAlias('install')->basePath
				. '/worklets/app/'.$className.'.php';
			$installer = 'install.app.install';
		}
		else
		{
			$className = 'W'.implode('',array_map('ucfirst',explode('.',$alias)))
				. 'InstallUpgrade_'.str_replace('.','_',$to);
			$targetPath = UFactory::getModuleFromAlias($alias)->basePath
				. '/worklets/install/'.$className.'.php';
			$installer = $alias.'.install.install';
		}
		
		if(strpos($alias,'.')!==false)
		{
			$trace = explode('.',$alias);
			foreach($trace as $s)
				$cfg = $cfg['modules'][$s];
		}
		elseif($alias != 'app')
			$cfg = $cfg['modules'][$alias];
		
		$installer = wm()->get($installer);
		$params = array_diff_key($cfg['params'],$installer->moduleParams());
		
		$filters = array_diff_key($filters,$installer->moduleFilters());
		
		$data = array(
			'className' => $className,
			'fromVersion'=>$from,
			'toVersion'=>$to,
			'params'=>$params,
			'filters'=>$filters,
			'sql'=>file_exists($installer->module->basePath.'/data/'.$from.'-'.$to.'.mysql.sql'),
		);
		$this->createUpgrader($targetPath,$data);
	}
	
	/**
	 * Creates upgrader.
	 * @param string target path
	 * @param array parameters
	 */
	public function createUpgrader($targetPath,$params)
	{
		$list = array(
			'upgrader' => array(
				'source' => UP_PATH.'/yii/cli/views/shell/upgrader/upgrader.php',
				'target' => $targetPath,
				'callback' => array($this,'generateUpgrader'),
				'params' => $params,
			),
		);
		$this->copyFiles($list);
	}
	
	/**
	 * Renders upgrader from a template.
	 * @param string template file path
	 * @param array parameters
	 * @return string template rendering result
	 */
	public function generateUpgrader($source,$params)
	{
		return $this->renderFile($source,$params,true);
	}
}
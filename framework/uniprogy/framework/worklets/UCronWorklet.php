<?php
/**
 * UCronWorklet class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UCronWorklet is a cron worklet class.
 *
 * @version $Id: UCronWorklet.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UCronWorklet extends USystemWorklet
{
	private $_result;
	
	/**
	 * Runs this cron.
	 * @return string cron execution report.
	 */
	public function run()
	{
		$this->build();
		return ucfirst($this->module->name)." Module:\n".$this->getResult()."\n\n";
	}
	
	/**
	 * Empty 'build' task. Override this method and add tasks you need this cron to do.
	 */
	public function taskBuild()
	{		
	}
	
	/**
	 * Saves execution report.
	 * @param string report to be saved.
	 */
	public function addResult($text)
	{
		$this->_result.= $text."\n";
	}
	
	/**
	 * @return string execution result.
	 */
	public function getResult()
	{
		return $this->_result;
	}
}
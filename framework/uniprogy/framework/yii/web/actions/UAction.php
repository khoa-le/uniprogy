<?php
/**
 * UAction class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UAction is a base action class for overriding Yii's default behavior.
 * When the script processes request it will find a module, a controller and will try to find an action.
 * If it is found - it will execute it (normal Yii behavior).
 * If it is not found - UAction will be loaded.
 *
 * @version $Id: UAction.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.yii.web.actions
 * @since 1.0.0
 */

class UAction extends CAction
{	
	/**
	 * UAction does only one thing - it launches an initial worklet.
	 */
	public function run()
	{
		wm()->get(wm()->getInitialWorklet())->build();
	}
}
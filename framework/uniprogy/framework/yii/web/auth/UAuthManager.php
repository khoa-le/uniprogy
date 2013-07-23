<?php
/**
 * UAuthManager class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UAuthManager is an extension of {@link http://www.yiiframework.com/doc/api/CPhpAuthManager CPhpAuthManager}.
 *
 * @version $Id: UAuthManager.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.yii.web.auth
 * @since 1.0.0
 */
class UAuthManager extends CPhpAuthManager
{
	/**
	 * Initializes the component.
	 * It first calls parent method and then assigns a role to the current user.
	 */
	public function init()
	{
		parent::init();
		if(!Yii::app()->user->isGuest)
            $this->assign(Yii::app()->user->role, Yii::app()->user->id);
	}
}
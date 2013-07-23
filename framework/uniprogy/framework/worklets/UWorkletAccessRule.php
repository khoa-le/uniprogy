<?php
/**
 * UWorkletAccessRule class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UWorkletAccessRule is an extension of {@link http://www.yiiframework.com/doc/api/CAccessRule CAccessRule}
 * optimized for worklets.
 *
 * @version $Id: UWorkletAccessRule.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
Yii::import('system.web.auth.CAccessControlFilter',true);
class UWorkletAccessRule extends CAccessRule
{
	/**
	 * We simply override {@link http://www.yiiframework.com/doc/api/CAccessRule#isUserAllowed-detail CAccessRule::isUserAllowed}
	 * method cause we need to omit several non-applicable rules checking.
	 * @param CWebUser the user object
	 * @param string the request IP address
	 * @param string the request verb (GET, POST, etc.)
	 * @return integer 1 if the user is allowed, -1 if the user is denied, 0 if the rule does not apply to the user
	 */
	public function isUserAllowed($user,$ip,$verb)
	{
		if($this->isUserMatched($user)
			&& $this->isRoleMatched($user)
			&& $this->isIpMatched($ip)
			&& $this->isVerbMatched($verb)
			&& $this->isExpressionMatched($user))
			return $this->allow ? 1 : -1;
		else
			return 0;
	}
}
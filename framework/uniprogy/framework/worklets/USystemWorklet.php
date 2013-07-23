<?php
/**
 * USystemWorklet class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * USystemWorklet is an extension of {@link UWorklet}.
 * USystemWorklet cannot be accessed from the web. It should contain only helper methods.
 *
 * @version $Id: USystemWorklet.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class USystemWorklet extends UWorklet
{
	/**
	 * Overrides {@link UWorklet::taskAccess} method.
	 * @return boolean false always - not accessable from the web.
	 */
	public function taskAccess()
	{
		$this->accessDenied();
			return false;
	}
}
<?php
/**
 * UViewAction class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UViewAction is an extension of {@link http://www.yiiframework.com/doc/api/CViewAction CViewAction}.
 *
 * @version $Id: UViewAction.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.yii.web.actions
 * @since 1.0.0
 */
class UViewAction extends CViewAction
{
	/**
	 * This is almost a copy of original run() function.
	 * The only difference is that when controller's layout property is 'false'
	 * then we prefer to partially render the view, because we don't need any post-processing
	 * in this case.
	 */
	public function run()
	{
		$this->resolveView($this->getRequestedView());
		$controller=$this->getController();
		if($this->layout!==null)
		{
			$layout=$controller->layout;
			$controller->layout=$this->layout;
		}

		$this->onBeforeRender($event=new CEvent($this));
		if(!$event->handled)
		{
			if($this->renderAsText)
			{
				$text=file_get_contents($controller->getViewFile($this->view));
				$controller->renderText($text);
			}
			else
			{
				if($controller->layout === false)
					$controller->renderPartial($this->view);
				else
					$controller->render($this->view);
			}
			$this->onAfterRender(new CEvent($this));
		}

		if($this->layout!==null)
			$controller->layout=$layout;
	}
}
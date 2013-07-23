<?php
/**
 * UDeleteWorklet class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UDeleteWorklet is a delete worklet class.
 *
 * @version $Id: UDeleteWorklet.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UDeleteWorklet extends UWidgetWorklet
{
	/**
	 * @var boolean usually you don't need to render anything when item is deleted, so
	 * by default this worklet won't be rendered.
	 */
	public $show = false;
	/**
	 * @var mixed the name of the model or an array of models and associated IDs
	 * where the script is supposed to delete item from.
	 * <pre>$modelClassName = 'MUser';</pre>
	 * worklet will delete an entry using MUser model and it's primary key as id column
	 * <pre>$modelClassName = array('MUser'=>'id','MUserStuff'=>'userId');</pre>
	 * worklet will delete all entries using MUser model and 'id' as id column;
	 * MUserStaff model and 'userId' as id column.
	 */
	public $modelClassName;
	
	/**
	 * Executes delete task on every item that has to be deleted.
	 * @see modelClassName
	 */
	public function taskConfig()
	{
		if(!$this->modelClassName)
			return false;
			
		if($this->isMass)
		{
			foreach($_POST['id'] as $id)
			{
				$this->delete($id);
			}
		}
		elseif($this->isSingle){
			$this->delete($_GET['id']);
		}
	}
	
	/**
	 * Deletes an item
	 * @param integer item ID
	 * @see modelClassName
	 */
	public function taskDelete($id)
	{
		if(!is_array($this->modelClassName))
			CActiveRecord::model($this->modelClassName)->deleteByPk($id);
		else
			foreach($this->modelClassName as $className=>$idCol)
				CActiveRecord::model($className)->deleteAll($idCol.'=?', array($id));
	}
	
	/**
	 * @return boolean true if worklet has to delete multiple items
	 */
	public function getIsMass()
	{
		return isset($_POST['id']) && is_array($_POST['id']);
	}
	
	/**
	 * @return boolean true if worklet has to delete single item
	 */
	public function getIsSingle()
	{
		return isset($_GET['id']) && $_GET['id'];
	}
}
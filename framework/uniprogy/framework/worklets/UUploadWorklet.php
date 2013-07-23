<?php
/**
 * UUploadWorklet class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UUploadWorklet is a worklet that processes file uploads.
 *
 * @version $Id: UUploadWorklet.php 73 2010-11-09 09:26:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UUploadWorklet extends UFormWorklet
{
	/**
	 * @var UStorageBin storage bin object
	 */
	public $bin;
	
	/**
	 * @var string which file label to use to store the uploaded file
	 */
	public $fileLabel = 'original';
	
	/**
	 * Worklet builder.
	 * If $_GET['delete'] variable is set it will call {@link taskDelete}.
	 */
	public function taskBuild()
	{
		if(isset($_GET['delete']))
		{
			$this->delete();
			$this->show = false;
		}
		else
			parent::taskBuild();
	}
	
	/**
	 * The field which tells our script that form has been submitted
	 * sometimes is missing from uploadify post data.
	 * We have to add it manually.
	 */
	public function beforeProcess()
	{
		if(isset($_POST['field']))
			$_POST['yform_'.$this->form->id] = true;
	}
	
	/**
	 * Attempts to save uploaded file into a storage bin.
	 * @return boolean
	 */
	public function taskSave()
	{
		$file = CUploadedFile::getInstance($this->model,$_POST['field']);
		if($file) {
			$bin = $this->bin();
			if($bin && $bin->put($file, $this->fileLabel))
			{
				$this->bin = $bin;
				return true;
			}
		}
		$this->model->addError(CHtml::activeName($this->model,$_POST['field']), $this->t('File didn\'t upload. Please try again later.'));
		return false;
	}
	
	/**
	 * @return MStorageBin storage bin object
	 */
	public function taskBin()
	{
		return app()->storage->bin($this->resolveBinId());
	}
	
	/**
	 * Deletes the file and it's containing bin.
	 */
	public function taskDelete()
	{
		$this->bin = $this->bin();
		if($this->bin)
			$this->bin->purge();
	}
	
	/**
	 * Resolves bin id.
	 * @return mixed bin id.
	 */
	public function resolveBinId()
	{
		return app()->request->getParam('bin',null);
	}
	
	/**
	 * Overrides {@link UFormWorklet::ajaxSuccess}.
	 * Simply returns bin id via JSON command.
	 */
	public function ajaxSuccess()
	{
		wm()->get('base.init')->addToJson(array('bin' => $this->bin->id));
	}
}
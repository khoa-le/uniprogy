<?php
/**
 * UUploadify class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UUploadify is an input widget that renders {@link http://www.uploadify.com/ Uploadify-powered} upload field.
 * Example of how to use it in a form elements array:
 * <pre>
 * 'elements' => array(
 *     'avatar' => array(
 *         'type' => 'UUploadify',
 *         'options' => array(
 *             'script'=>url('/user/avatar',array('id' => app()->request->getParam('id',null))),
 *             'auto'=>true,
 *             'multi'=>false,
 *             'binField' => isset($_GET['binField']) ? $_GET['binField'] : '',
 *         ),
 *         'layout' => "{label}\n<fieldset>{input}</fieldset>\n{hint}",
 *     ),
 * ),
 * </pre>
 *
 * @version $Id: UUploadify.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.widgets
 * @since 1.0.0
 */
Yii::import('uniprogy.extensions.uploadify.EuploadifyWidget');
class UUploadify extends EuploadifyWidget
{
	/**
	 * @var boolean allow multiple uploads.
	 */
	public $multi=false;
	/**
	 * @var boolean automatically start uploading as soon as the file has been selected.
	 */
	public $auto=false;
	
	/**
	 * Extends EuploadifyWidget extension by adding 'binField' to valid options.
	 * @var array uploadify options
	 */
	public function setOptions($value)
    {
    	$this->validOptions['binField'] = array('type' => 'string');
        parent::setOptions($value);
    }
	
    /**
     * Renders widget.
     */
	public function run()
	{
		list($name, $id) = $this->resolveNameID();
		
		$options = $this->options;
		$options['fileDataName'] = $name;
		$options['scriptData']['field'] = $this->attribute;
		$options['scriptData']['PHPSESSID'] = session_id();
		$this->options = $options;
		
		cs()->registerScriptFile(asma()->publish(dirname(__FILE__).'/assets/jquery.uniprogy.uploadify.js'));
		parent::run();
		
		$options = $this->makeOptions();
        $js =<<<EOP
$("#{$id}").uUploadify({$options});
EOP;
		cs()->registerScript('Yii.'.get_class($this).'#'.$id, $js, CClientScript::POS_READY);		
	}
}
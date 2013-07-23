<?php
/**
 * UDateTimePicker class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UDateTimePicker is an input widget class that is used to display date field as jQuery picker.
 * Time fields are rendered by {@link UDatTimeField} widget.
 * It expects the value of this field to be a GMT UNIX timestamp.
 * It automatically renders date and time fields in the right order according to the currently active locale.
 * {@link http://www.yiiframework.com/doc/guide/topics.i18n}
 *
 * @version $Id: UDateTimePicker.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.widgets
 * @since 1.0.0
 */
Yii::import('zii.widgets.jui.CJuiDatePicker',true);
class UDateTimePicker extends CJuiDatePicker
{
	/**
	 * Renders date and time fields.
	 */
	public function run()
	{
		$pattern = app()->locale->getDateFormat('short');
		$value = $this->model->{$this->attribute};
		if($value && !preg_match('/[^0-9]/',$value))
		{
			$value = UTimestamp::getDate($value,true,true);
			$replaces = array(
				'/[m]+/i'=>$value['mon'],
				'/[d]+/i'=>$value['mday'],
				'/[y]+/i'=>$value['year'],
			);
			$this->htmlOptions['value'] = preg_replace(array_keys($replaces),
				array_values($replaces),$pattern);
		}
		
		$replaces = array(
			'/[m]+/i'=>'mm',
			'/[d]+/i'=>'dd',
			'/[y]+/i'=>'yy',
		);
		$this->options = array('dateFormat' => preg_replace(array_keys($replaces),
				array_values($replaces),$pattern));
		
		$language = $this->language();
		if($language)
			$this->language = $language;
			
		list($name,$id)=$this->resolveNameID();

		if(isset($this->htmlOptions['id']))
			$id=$this->htmlOptions['id'];
		else
			$this->htmlOptions['id']=$id;
		if(isset($this->htmlOptions['name']))
			$name=$this->htmlOptions['name'];
		else
			$this->htmlOptions['name']=$name;
			
		$this->htmlOptions['name'] = $name.'[date]';
		$this->htmlOptions['id'] = $id.'Date';
			
		parent::run();
			
		$this->widget('UDateTimeField', 
			array('showDate'=>false,'showHiddenField'=>false,
				'model'=>$this->model,'attribute'=>$this->attribute,
				'htmlOptions'=>array('class'=>'autoWidth')));
	}
	
	/**
	 * Converts current application language to jQuery UI date picker format.
	 * @return string language in jQuery UI date picker format.
	 */
	public function language()
	{
		static $supportedLangs;
		if(!isset($supportedLangs))
			$supportedLangs = explode(',','af,ar,az,bg,bs,ca,cs,da,de,el,en-GB,eo,es,et,eu,fa,fi,fo,fr-CH,fr,he,hr,hu,hy,id,is,it,ja,ko,lt,lv,ms,nl,no,pl,pt-BR,ro,ru,sk,sl,sq,sr-SR,sr,sv,ta,th,tr,uk,vi,zh-CN,zh-HK,zh-TW');
		
		$vers = array();
		$lang = explode('_',app()->language);
		if(count($lang)>1)
			$vers[] = $lang[0].'-'.strtoupper($lang[1]);
		$vers[] = $lang[0];
		
		foreach($vers as $v)
			if(in_array($v,$supportedLangs))
				return $v;
		return false;
	}
}
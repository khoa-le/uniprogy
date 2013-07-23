<?php
/**
 * UDateTimeField class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UDateTimeField is an input widget class that is used to display date and time field.
 * It expects the value of this field to be a GMT UNIX timestamp.
 * It automatically renders date and time fields in the right order according to the currently active locale.
 * {@link http://www.yiiframework.com/doc/guide/topics.i18n}
 *
 * @version $Id: UDateTimeField.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.widgets
 * @since 1.0.0
 */
class UDateTimeField extends CInputWidget
{	
	/**
	 * @var boolean render date field.
	 */
	public $showDate = true;
	/**
	 * @var boolean render time field.
	 */
	public $showTime = true;
	/**
	 * @var boolean render hidden field with an empty value so this attribute doesn't
	 * appear as unfilled when form is processed.
	 */
	public $showHiddenField = true;
	/**
	 * @var array range of years to render in a year field.
	 */
	public $yearRange = array(1970,2010);
	/**
	 * @var array HTML attributes which will be applied to each field.
	 */
	public $htmlOptions = array();
	
	/**
	 * Renders date and time fields.
	 */
	public function run()
	{
		$value = null;
		if($this->model->{$this->attribute})
			$value = UTimestamp::getDate($this->model->{$this->attribute},true,true);
		
		if($this->showDate && $this->showTime)
		{
			$pattern = app()->locale->getDateTimeFormat();
			$pattern = strtr($pattern, array(
				'{0}' => app()->locale->getTimeFormat('short'),
				'{1}' => app()->locale->getDateFormat('short'),
			));
		}
		elseif($this->showDate)
			$pattern = app()->locale->getDateFormat('short');
		elseif($this->showTime)
			$pattern = app()->locale->getTimeFormat('short');
			
		$order = array(
			'd' => strpos($pattern, 'd'),
			'M' => strpos($pattern, 'M'),
			'y' => strpos($pattern, 'y'),
			'H' => strpos($pattern, 'H'),
			'h' => strpos($pattern, 'h'),
			'm' => strpos($pattern, 'm'),
			'a' => strpos($pattern, 'a')
		);
		asort($order);
		$nameId = $this->resolveNameID();
		
		if($this->showHiddenField)
			echo CHtml::hiddenField($nameId[0], '', array('id' => $nameId[1]));
		
		foreach($order as $k=>$render)
		{
			if($render !== false)
				echo $this->field($k, $nameId, $value);
		}
	}
	
	/**
	 * Renders a field.
	 *
	 * @param string field label
	 * <ul>
	 * <li>d - day</li>
	 * <li>M - month</li>
	 * <li>y - year</li>
	 * <li>h - hour (12h format)</li>
	 * <li>H - hour (24h format)</li>
	 * </ul>
	 * @param array field name and id as elements of array.
	 * @param array date and time array in a {@link http://php.net/getdate getdate} function format.
	 * @return string field render result.
	 */
	public function field($what, $nameId, $value)
	{
		$valueVar = null;
		switch($what)
		{
			case 'd':
				$data = range(1,31);
				$data = array_combine(array_values($data), array_values($data));
				$data = array('' => txt()->format(Yii::t('uniprogy','Day'), ':')) + $data;
				$valueVar = 'mday';
				break;
			case 'M':
				$data = array('' => txt()->format(Yii::t('uniprogy','Month'), ':')) + locale()->getMonthNames('wide', true);
				$valueVar = 'mon';
				break;
			case 'y':
				$data = range($this->yearRange[0], $this->yearRange[1]);
				$data = array_combine(array_values($data), array_values($data));
				$data = array('' => txt()->format(Yii::t('uniprogy','Year'), ':')) + $data;
				$valueVar = 'year';
				break;
			case 'h':
				$data = range(1,12);
				$data = array_combine(array_values($data), array_values($data));
				foreach($data as $k=>$v)
					if($v<10) $data[$k] = '0'.$v;
				$data = array('' => txt()->format(Yii::t('uniprogy','Hour'), ':')) + $data;
				$valueVar = 'hours';
				if(isset($value['hours'])) {
					if($value['hours'] > 12)
						$select = $value['hours'] - 12;
					elseif($value['hours'] == 0)
						$select = 12;
				}
				break;
			case 'H':
				$data = range(0,23);
				$data = array_combine(array_values($data), array_values($data));
				foreach($data as $k=>$v)
					if($v<10) $data[$k] = '0'.$v;
				$data = array('' => txt()->format(Yii::t('uniprogy','Hour'), ':')) + $data;
				$valueVar = 'hours';
				break;
			case 'm':
				$data = range(0,59);
				$data = array_combine(array_values($data), array_values($data));
				foreach($data as $k=>$v)
					if($v<10) $data[$k] = '0'.$v;
				$data = array('' => txt()->format(Yii::t('uniprogy','Minute'), ':')) + $data;
				$valueVar = 'minutes';
				break;
			case 'a':
				$data = array(
					'' => txt()->format(
						locale()->getAMName(), '/', locale()->getPMName(), ':'
					),
					'am' => locale()->getAMName(),
					'pm' => locale()->getPMName()
				);
				if(isset($value['hours']))
					$select = $value['hours'] >= 12 ? 'pm' : 'am';
				break;
		}
		list($name, $id) = $nameId;
		$name.= '[' . $what . ']';
		$id.= '_' . $what;
		
		if(!isset($select))
			$select = isset($value[$valueVar]) ? $value[$valueVar] : null;
		$htmlOptions = array('id' => $id) + $this->htmlOptions;
		
		return CHtml::dropDownList($name, $select, $data, $htmlOptions);
	}
}
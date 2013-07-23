<?php
class WUserAccountTabs extends UWidgetWorklet
{	
	public $tabs;
	public $select=0;
	
	public function taskConfig()
	{
		$this->tabs = array(
			$this->t('My Account') => array('ajax'=>array('/user/account')),
		);
	}
	
	public function taskRenderOutput()
	{
		$id = 'uTabs_'.$this->getDOMId().'_'.CHtml::$count++;
		
		$this->widget('zii.widgets.jui.CJuiTabs', array(
			'id' => $id,
		    'tabs' => $this->tabs,
		    // additional javascript options for the tabs plugin
		    'options'=>array(
		       
		    ),
		));
		
		cs()->registerScript(__CLASS__.'#'.$id,"jQuery('#{$id}').tabs().tabs('select', ".$this->select.");");
	}
}
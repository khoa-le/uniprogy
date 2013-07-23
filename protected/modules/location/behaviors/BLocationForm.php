<?php
class BLocationForm extends UWorkletBehavior
{
	public static $cacheKeyPrefix = 'UniProgy.BLocationForm.';
	public $insert;
	public $model;
	public $elementsKey;
	public $required=true;
	
	public function properties()
	{
		$data = app()->cache->get(self::$cacheKeyPrefix . 'data');
		if($data === false)
		{
			$elements = array();
			list($countries,$states,$cities) = wm()->get('location.helper')->locations();
			if(count($countries)>1)
				$elements['country'] = array(
					'type' => 'dropdownlist',
					'items' => CHtml::listData($countries,'code','name'),
					'required' => $this->required,
				);
			else
				$elements['country'] = array('type' => 'hidden', 'value' => array_pop(array_keys($countries)));
				
			foreach($states as $c=>$s)
				if($s===true || (is_array($s) && count($s)))
					$elements['state'] = array('type' => 'dropdownlist',
						'required' => $this->required);
				else
					unset($states[$c]);
			
			if(count($cities)===0)
				$elements['city'] = array('type' => 'text', 'required' => $this->required);
			else
			{
				foreach($cities as $k=>$c)
					if($c===true || (is_array($c) && count($c) > 1))
						$elements['city'] = array('type' => 'text', 'required' => $this->required);
					else
						unset($cities[$k]);
			}
			
			$data = array('elements' => $elements, 
				'json' => CJavaScript::jsonEncode(array('states' => $states,'cities' => $cities)));
			app()->cache->set(self::$cacheKeyPrefix . 'data', $data, app()->params['maxCacheDuration']);
		}
		
		$selectedLocation = wm()->get('location.helper')->locationToData(
			$this->getOwnerModel()->location);
			
		if(isset($selectedLocation['country']) && !array_key_exists($selectedLocation['country'],$countries))
			$selectedLocation = array();
			
		if(!isset($selectedLocation['country']))
		{
			if($this->getModule()->param('defaultCountry'))
				$selectedLocation['country'] = $this->getModule()->param('defaultCountry');
			else
				$selectedLocation['country'] = 'US';
		}
		
		$locationModel = new MLocationForm;
		$locationModel->attributes = $selectedLocation;
		
		$selects = CJavaScript::jsonEncode($selectedLocation);
		
		$jsFile = asma()->publish($this->getModule()->basePath .DS. 'js' .DS. 'jquery.uniprogy.loc.js');
		cs()->registerScriptFile($jsFile,CClientScript::POS_HEAD);
		$script = '$("#' .$this->getOwner()->getFormId(). '").uLoc('. $data['json'] . ', ' . $selects . ');';
		cs()->registerScript('UniProgy.location.form',$script);
		return array(
			'location' => array('type' => 'UForm',
				'elements' => $data['elements'],
				'model' => $locationModel
			)
		);
	}
	
	public function getOwnerModel()
	{
		return $this->model?$this->model:$this->getOwner()->model;
	}
	
	public function afterConfig()
	{
		if(!$this->insert)
			$this->insert = array('before'=>'address');
			
		if(key($this->insert) == 'before')
			$this->getOwner()->insertBefore(current($this->insert),$this->properties(),$this->elementsKey);
		else
			$this->getOwner()->insertAfter(current($this->insert),$this->properties(),$this->elementsKey);
	}
	
	public function beforeSave()
	{
		$model = $this->getOwnerModel();
		$form = $this->getOwner()->form;
		if(is_array($this->elementsKey))
		{
			reset($this->elementsKey);
			while(($k = current($this->elementsKey))!==false)
			{
				if(isset($form[$k]))
				{
					$form = $form[$k];
					next($this->elementsKey);
				}
				else
					break;
			}
		}
		
		$locModel = isset($form['location'])?$form['location']->model:null;		
		if(!$locModel)
			return;
		
		$location = wm()->get('location.helper')->dataToLocation($locModel->attributes);
		if(!$location && $this->required)
			$locModel->addError('country', $this->getModule()->t('Invalid location selected. Please verify.'));
		else
			$model->location = $location;
	}
}
<?php
class WLocationHelper extends USystemWorklet
{
	public function taskLocations()
	{
		if(($locations = $this->cacheGet('locations'))!==false)
			return $locations;
		
		$countries = array();
		$states = array();
		$cities = array();
		
		$countries = $this->loadCountries();
		if($this->param('defaultCountry') && $this->param('defaultCountry')!=='*')
			$countries = array_intersect_key($countries,array($this->param('defaultCountry')=>true));
		
		foreach($countries as $code=>$name)
		{
			$st = $this->loadStates($code);
			if($st)
			{
				$states[$code] = $st;
				foreach($states[$code] as $c=>$s)
					$cities[$code.'_'.$c] = true;
			}
			else
				$cities[$code.'_0'] = true;
		}
		
		$locations = array($countries,$states,$cities);
		$this->cacheSet('locations',$locations);
		return $locations;
	}
	
	public function taskDataToLocation($data,$addMissing=true)
	{
		static $locations = array();
		
		if(!is_array($data))
			return;
		
		$key = serialize($data);
		if(!isset($locations[$key]))		
		{
			$location = MLocation::model()->findByAttributes($data);
			if(!$location && $addMissing)
			{
				if(!$data['state'])
					$data['state'] = '0';
				$location = new MLocation;
				$location->attributes = $data;
				$location->save();
			}
			$locations[$key] = $location?$location->id:false;
		}
		return $locations[$key];
	}
	
	public function taskLocationToData($location)
	{
		static $locations = array();
		
		if(!$location)
			return;
		
		if(!isset($locations[$location]))
		{		
			$model = MLocation::model()->findByPk($location);
			$locations[$location] = $model ? $model->attributes : array();
		}
		return $locations[$location];
	}
	
	public function taskDefaultCountry()
	{
		if($this->param('defaultCountry')!=='*')
			return $this->param('defaultCountry');
		return false;
	}
	
	public function taskDefaultLocation()
	{
		if($this->param('location')!=='*')
			return $this->param('location');
		return false;
	}
	
	public function taskCountry($id)
	{
		$countries = $this->loadCountries();
		return isset($countries[$id])?$countries[$id]['name']:null;
	}
	
	public function taskState($country,$id)
	{
		$states = $this->loadStates($country);
		return isset($states[$id])?$states[$id]:null;
	}
	
	public function loadCountries()
	{
		$countriesFile = Yii::getPathOfAlias('application.data.countries') . '.php';
		return include($countriesFile);
	}
	
	public function loadStates($country)
	{
		$statesFile = Yii::getPathOfAlias('application.data.states.'.$country).'.php';
		if(file_exists($statesFile))
			return include($statesFile);
		else
			return false;
	}
}
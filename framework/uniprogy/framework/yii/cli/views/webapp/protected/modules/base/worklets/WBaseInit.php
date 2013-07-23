<?php
class WBaseInit extends USystemWorklet
{
	public $show = false;
	private $_json = array();
	private $_renderType = 'normal';
	private $_states;
	
	public function js()
	{
		return array(
			'jquery.scrollTo-min.js',
			'jquery.uniprogy.js',
			'jquery.uniprogy.binds.js',
		);
	}
	
	public function taskBuild()
	{
		$this->config();
		$this->worklet();
		$this->renderPage();
	}
	
	public function taskConfig()
	{
		if(app()->request->isAjaxRequest)
			$this->setRenderType('ajax');
		else
			$this->registerScripts();
	}
	
	public function taskWorklet()
	{
		$route = app()->getController()->getRouteEased();
		$worklet = wm()->get(str_replace("/", ".", $route));
		if($worklet)
			wm()->addCurrent($worklet);
		else
			app()->controller->missingAction(app()->controller->action->id);
	}
	
	public function taskRenderPage($content=null)
	{
		switch($this->getRenderType())
		{
			case 'normal':
				$this->metaData();
				$this->recordClips();
				app()->controller->renderText($content);
				break;
			case 'ajax':
				$this->recordClips();
				app()->controller->layout = 'ajax';				
				app()->controller->renderText('');
				break;
			case 'json':
				app()->controller->renderFile(
					app()->controller->getLayoutFile('json')
				);
				break;
		}
	}
	
	public function taskMetaData()
	{
		$metaData = array();
		if($w = wm()->getCurrentWorklet())
			$metaData = $w->meta();
		
		$metaData = app()->controller->module
			&& ($w = wm()->get(UFactory::getModuleAlias(app()->controller->module).'.meta'))!==false
				? CMap::mergeArray($w->get(),$metaData)
				: CMap::mergeArray(UMetaWorklet::defaultMetaData(),$metaData);
		
		// set page title
		$title = null;
		if(strpos(app()->controller->pageTitle, '*')===0)
			$metaData['title'] = substr(app()->controller->pageTitle, 1);
		else{
			if(empty($metaData['title']))
				$metaData['title'] = array(app()->name);
			else
				$metaData['title'] = array($metaData['title'],' - ',app()->name);
				
			if(app()->params['poweredBy'])
			{
				$metaData['title'][] = ' - ';
				$metaData['title'][] = strip_tags(app()->params['poweredBy']);
			}
			
			$metaData['title'] = app()->locale->textFormatter->format($metaData['title']);
		}
			
		app()->controller->pageTitle = $metaData['title'];
		
		// add keywords and description meta tags
		cs()->registerMetaTag($metaData['keywords'], 'keywords');
		cs()->registerMetaTag($metaData['description'], 'description');
	}
	
	public function taskRegisterScripts()
	{
		$cs = app()->clientScript;
		$am = app()->getAssetManager();
		$cs->registerCoreScript('jquery');
		foreach($this->js() as $file) {
			$pubFile = $am->publish(UP_PATH.DS.'js'.DS.$file);
			$cs->registerScriptFile($pubFile);
		}
	}
	
	public function taskRecordClips()
	{
		$pieces = array();
		foreach(wm()->worklets as $id=>$dummy)
		{
			$worklet = wm()->get($id);
			if(!$worklet->show)
				continue;

			if(app()->theme)
				$worklet = app()->theme->applyToWorklet($worklet);
			$space = $worklet->space;			
			
			if(!isset($pieces[$space]))
				$pieces[$space] = new CList;
			
			$pieces[$space]->add($id);			
		}
				
		foreach($pieces as $space => $ids)
		{
			$orderer = new UWorkletOrderer($ids);
			$ids = $orderer->order();
			
			app()->controller->beginClip($space);
			foreach($ids as $id)
				wm()->get($id)->run();
			app()->controller->endClip();
		}
	}
	
	public function taskReset()
	{
		wm()->clear();
		app()->controller->getClips()->clear();
		return $this;
	}
	
	public function taskUrlRules()
	{
		return array('page/<view>' => 'base/page');
	}
	
	public function taskCreateController($route,$owner=null)
	{
		return null;
	}
	
	public function clearJson()
	{
		$this->_json = array();
	}
	
	public function addToJson($data)
	{
		$this->_json = CMap::mergeArray($this->_json, $data);
		$this->setRenderType('json');
	}
	
	public function getJson()
	{
		return $this->_json;
	}
	
	public function setRenderType($value)
	{
		$this->_renderType = $value;
	}
	
	public function getRenderType()
	{
		return $this->_renderType;
	}
	
	public function getStates()
	{
		if(!isset($this->_states))
			$this->_states = new CAttributeCollection;
		return $this->_states;
	}
	
	public function setState($key,$value)
	{
		$states = $this->getStates();
		$states->add($key,$value);		
	}
}
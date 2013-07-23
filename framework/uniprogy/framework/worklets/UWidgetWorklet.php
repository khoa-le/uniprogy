<?php
/**
 * UWidgetWorklet class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UWidgetWorklet is an extension of {@link UWorklet}.
 * Widget worklet is any worklet that is supposed to be accessable from the web and render any output.
 *
 * @version $Id: UWidgetWorklet.php 79 2010-12-10 17:55:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UWidgetWorklet extends UWorklet
{	
	/**
	 * @var string space in the layout worklet output should be placed into.
	 */
	public $space='content';
	/**
	 * @var mixed position (order) in which worklet should be rendered
	 * <pre>
	 * public $position = 'top';
	 * public $position = 'bottom';
	 * public $position = array('before' => 'another.worklet.id');
	 * public $position = array('after' => 'another.worklet.id');
	 * </pre>
	 */
	public $position;
	/**
	 * @var string worklet title.
	 */
	public $title;
	/**
	 * @var boolean render worklet output or no.
	 */
	public $show=true;
	/**
	 * @var string layout of this worklet. 
	 * Defaults to 'worklet'.
	 */
	public $layout='worklet';
	
	/**
	 * Initializes worklet.
	 * By default it only executes {@link taskBuild} task.
	 */
	public function init()
	{
		$this->build();
	}
	
	/**
	 * Renders worklet output.
	 * @param boolean return output or print it out.
	 * @return string worklet output.
	 */
	public function run($return=false)
	{	
		ob_start(); ob_implicit_flush(false);
		$this->renderOutput();
		$output = ob_get_clean();
		
		$this->registerScripts();
		
		if(($layoutFile=$this->getLayoutFile($this->layout))!==false)
			$output=$this->renderFile($layoutFile,array('id' => $this->getDOMId(), 'title' => $this->title, 'content'=>$output), true);
		
		if($return)
			return $output;
		else
			echo $output;
	}
	
	/**
	 * @return string worklet title.
	 */
	public function title()
	{
		return '';
	}
	
	/**
	 * @return array worklet meta data.
	 */
	public function meta()
	{
		$md = array();
		if($this->title)
			$md['title'] = strip_tags($this->title);
		elseif($bc = $this->breadCrumbs())
		{
			$t = array_pop(array_keys($bc));
			if(is_numeric($t))
				$t = array_pop(array_values($bc));
			$md['title'] = strip_tags($t);
		}
		return $md;
	}
	
	/**
	 * @return array worklet access rules. Identical to {@link http://www.yiiframework.com/doc/api/CController#accessRules-detail}.
	 */
	public function accessRules()
	{
		return array();
	}
	
	/**
	 * Goes through {@link accessRules} and either allows or denies access.
	 * @return boolean
	 */
	public function taskAccess()
	{
		$user=app()->user;
		$request=app()->getRequest();
		$verb=$request->getRequestType();
		$ip=$request->getUserHostAddress();
		
		$rules = $this->accessRules();
		if(!param('publicAccess'))
			$rules[] = array('deny','users'=>array('?'));
			
		foreach($rules as $rule)
		{
			if(is_array($rule) && isset($rule[0]))
			{
				$r=new UWorkletAccessRule;
				$r->allow=$rule[0]==='allow';
				foreach(array_slice($rule,1) as $name=>$value)
				{
					if($name==='expression' || $name==='roles' || $name==='message')
						$r->$name=$value;
					else
						$r->$name=array_map('strtolower',$value);
				}
				if(($allow=$r->isUserAllowed($user,$ip,$verb))>0) // allowed
					break;
				else if($allow<0) // denied
				{
					$this->accessDenied($user,$r->message);
					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * Worklet builder.
	 */
	public function taskBuild()
	{
		$this->config();
	}
	
	/**
	 * Configures worklet.
	 */
	public function taskConfig()
	{
		if(!isset($this->title))
			$this->title = $this->title();
		if($bc = $this->breadCrumbs())
			app()->controller->breadcrumbs = $bc;
	}
	
	/**
	 * Renders worklet content.
	 */
	public function taskRenderOutput() {}
	
	/**
	 * Looks for CSS and JavaScript files with the same file name
	 * as worklet id and if found - registers them.
	 */
	public function taskRegisterScripts()
	{
		$themeCss['path'] = null;
		if(app()->theme)
		{
			$themeCss = array(
				'path' => app()->theme->basePath . DS . 'css' . DS . $this->id . '.css',
				'url' => app()->theme->baseUrl . '/css/' . $this->id . '.css',
			);
		}
		$moduleCss = $this->module->basePath . DS . 'css' . DS . $this->id . '.css';
		
		if(is_file($themeCss['path']))
			cs()->registerCssFile($themeCss['url']);
		elseif(is_file($moduleCss))
			cs()->registerCssFile(app()->getAssetManager()->publish($moduleCss));
			
		$js = $this->module->basePath . DS . 'js' . DS . $this->id . '.js';
		if(is_file($js))
			cs()->registerScriptFile(app()->getAssetManager()->publish($js));
	}
	
	/**
	 * @return mixed null or bread crumbs array.
	 */
	public function taskBreadCrumbs()
	{
		return null;
	}
	
	/**
	 * @return string worklet id in a DOM optimized format.
	 */
	public function getDOMId()
	{
		return 'wlt-' . UHelper::camelize($this->getId());
	}
	
	/**
	 * Looks for the view file according to the given view name.
	 * This method implements UniProgy's templates inheritance feature:
	 * there are 10 levels of template hierarchy and script tries to find
	 * appropriate view file in each of them.
	 * 
	 * Here's the template hierarchy starting from the top level (if theme is disabled
	 * all theme-related levels are ignored):
	 * <ul>
	 * <li>theme view path / worklets / parent module(s) / module / controller / worklet name</li>
	 * <li>module view path / worklets / controller / worklet name</li>
	 * <li>theme view path / worklets / parent module(s) / module / controller</li>
	 * <li>module view path / worklets / controller</li>
	 * <li>theme view path / worklets / parent module(s) / module</li>
	 * <li>module view path / worklets</li>
	 * <li>theme view path / worklets</li>
	 * <li>application view path / worklets</li>
	 * </ul>
	 * 
	 * Note: default controller is always ommited.
	 * 
	 * Ex.:
	 * Worklet id is 'base.test'.
	 * This means that it belongs to 'base' module, 'default' controller and the name of the worklet is 'test'.
	 * <pre>$worklet->getViewFile('someTemplate');</pre>
	 * will be searching for the file in these locations:
	 * theme view path / worklets / base /  test / someTemplate.php
	 * module view path / worklets / test / someTemplate.php
	 * ...
	 *
	 * @param string view name.
	 * @return mixed path to the view file or boolean false or null.
	 */
	public function getViewFile($viewName)
	{
		if(!$viewName)
			return false;
			
		if(($value = $this->cacheGet('views.'.$viewName))!==false)
			return $value;
			
		// alias
		if(($pos=strrpos($viewName,'.'))!==false)
		{
			$path = Yii::getPathOfAlias(substr($viewName,0,$pos));
			$viewName = substr($viewName,$pos+1);
			return $this->resolveViewFile($viewName,$path);
		}

		list($module,$alias) = UFactory::getModuleWithParents($this->id);
		$subpaths = explode('.',trim($alias,'.'));

		$paths = new CList;
		for($i=count($subpaths);$i>=1;$i--)
			$paths->add($this->module->viewPath . DS . 'worklets' . DS . implode(DS, array_slice($subpaths,0,$i)));
		$paths->add($this->module->viewPath . DS . 'worklets');
		$paths->add(app()->viewPath .DS. 'worklets');
		
		if(app()->theme)
		{
			$module = $this->module;
			while($module)
			{
				array_unshift($subpaths, $module->id);
				$module = $module->parentModule;
			}
			
			$pos = 0;
			for($i=count($subpaths);$i>=1;$i--)
			{
				$paths->insertAt($pos*2, app()->theme->viewPath . DS . 'worklets' . DS . implode(DS, array_slice($subpaths,0,$i)));
				$pos++;
			}
			$paths->insertAt($pos*2-1, app()->theme->viewPath . DS . 'worklets');
		}
		
		$viewFile = null;
		foreach($paths as $p)
			if($viewFile = $this->resolveViewFile($viewName, $p))
				break;
		$this->cacheSet('views.'.$viewName,$viewFile);		
		return $viewFile;
	}
	
	/**
	 * Resolves layout file.
	 * @param string layout name.
	 * @return mixed path to the view file or boolean false or null.
	 */
	public function getLayoutFile($layoutName)
	{
		if($layoutName===false)
			return false;
		return $this->getViewFile($layoutName);
	}
	
	/**
	 * A shortcut to {@link http://www.yiiframework.com/doc/api/CController#resolveViewFile-detail}
	 * @param string view name
	 * @param string view path
	 */
	public function resolveViewFile($viewName,$viewPath)
	{
		return app()->controller->resolveViewFile($viewName,$viewPath,$viewPath);
	}
	
	/**
	 * Renders a view.
	 * @param string name of the view to be rendered. See {@link getViewFile} for details
	 * about how the view script is resolved.
	 * @param array data to be extracted into PHP variables and made available to the view script
	 * @param boolean whether the rendering result should be returned instead of being displayed to end users.
	 * @return string the rendering result. Null if the rendering result is not required.
	 * @link http://www.yiiframework.com/doc/api/CBaseController#renderFile-detail
	 * @see getLayoutFile
	 */
	public function render($view,$data=null,$return=false)
	{
		if(($viewFile=$this->getViewFile($view))!==false)
		{
			$output=$this->renderFile($viewFile,$data,true);
			if($return)
				return $output;
			else
				echo $output;
		}
		else
			throw new CException(Yii::t('yii','{controller} cannot find the requested view "{view}".',
				array('{controller}'=>get_class($this), '{view}'=>$view)));
	}
}
<?php
/**
 * UTheme class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UTheme is an extension of {@link http://www.yiiframework.com/doc/api/CTheme CTheme}.
 *
 * @version $Id: UTheme.php 79 2010-12-10 13:18:00 GMT vasiliy.y $
 * @package system.yii.web
 * @since 1.0.0
 */
class UTheme extends CTheme
{
	/**
	 * @var string some custom path to be added to the theme view path.
	 * For ex.: if the request is coming from a mobile browser theme view path will be appended
	 * with '/mobile'.
	 */
	public $customPath = null;
	/**
	 * Resolves controller layout according to theme route special settings.
	 * @param UController controller
	 * @return string layout name
	 * @see routes
	 */
	public function resolveControllerLayout($controller)
	{
		$route = $controller->getRouteEased();
		$routes = $this->routes();
		return isset($routes[$route]) ? $routes[$route] : $controller->layout;
	}
	
	/**
	 * Modifies worklet space if theme requires it.
	 * @param UWorklet worklet object
	 * @return UWorklet worklet object
	 * @see worklets
	 * @see resolveSpace
	 */
	public function applyToWorklet($worklet)
	{
		$worklets = $this->worklets();
		if(isset($worklets[$worklet->id]))
			foreach($worklets[$worklet->id] as $s=>$v)
				$worklet->$s = $v;
		if(($space=$this->resolveSpace($worklet->space))!==false)
			$worklet->space = $space;
		else
			throw new CException(t('Space "{space}" is not available for layout "{layout}" and theme "{theme}".',null,
				array('{space}' => $worklet->space, '{layout}' => app()->controller->getLayoutName(), '{theme}' => $this->name)));
		return $worklet;
	}
	
	/**
	 * Resolves worklet space according to theme settings.
	 * @param string space name
	 * @return string space name or boolean false if space doesn't exist in this theme
	 */
	public function resolveSpace($space)
	{
		$spaces = $this->spaces();
		$layout = app()->controller->getLayoutName();
		
		$layout = strstr($layout,'/')
			? substr($layout, strrpos($layout,'/')+1)
			: $layout;
		
		if(!isset($spaces[$layout]) || !is_array($spaces[$layout]))
			return false;
		
		$spaces = $spaces[$layout];
			
		if(in_array($space, $spaces)) return $space;
		if(isset($spaces[$space])) return $spaces[$space];
		if(isset($spaces['default'])) return $spaces['default'];
		
		return false;
	}
	
	/**
	 * Finds the layout file for the specified controller's layout.
	 * @param UController the controller
	 * @param string the layout name
	 * @return string the layout file path. False if the file does not exist.
	 */
	public function getLayoutFile($controller,$layoutName)
	{
		if($layoutFile = parent::getLayoutFile($controller,$layoutName))
			return $layoutFile;
		$layoutName = $controller->getLayoutName($layoutName);		
		return $controller->resolveViewFile($layoutName,$this->getViewPath().'/layouts',$this->getViewPath());
	}
	
	/**
	 * Finds the view file for the specified controller's view.
	 * @param UController the controller
	 * @param string the view name
	 * @return string the view file path. False if the file does not exist.
	 */
	public function getViewFile($controller,$viewName)
	{
		if($viewFile = parent::getViewFile($controller,$viewName))
			return $viewFile;
		if($controller->getModule()!==null)
			return $controller->resolveViewFile($viewName,$this->getViewPath().'/'.$controller->getModule()->name,$this->getViewPath());
		return false;
	}
	
	/**
	 * Lists all spaces which current theme supports in different layouts.
	 * <pre>
	 * return array(
     *     'main' => array(
     *         'inside',
     *         'outside',
     *         'header',
     *         'menu',
     *         'content',
     *         'sidebar',
     *         'footer',
     *         'default' => 'content'
     *     ),
     *     'email' => array(
     *         'content'
     *     ),
     *     'print' => array(
     *         'content'
     *     ),
     * );
	 * </pre>
	 * Above configuration means that:
	 * <ul>
	 *     <li>This theme supports 'inside', 'outside', 'header', etc. spaces for 'main' layout</li>
	 *     <li>When space is not supported in 'main' layout 'default' one is 'content'.</li>
	 *     <li>'email' and 'print' layouts support only 'content' space.</li>
	 * </ul>
	 * @return array spaces configuration
	 */
	public function spaces()
	{
		return array();
	}
	
	/**
	 * Routes-layout configuration.
	 * <pre>
	 * return array(
	 *     'base/index' => 'special'
	 * );
	 * </pre>
	 * Above means that 'special' layout should be used for 'base/index' route.
	 * @return array routes-layout configuration
	 */
	public function routes()
	{
		return array();
	}
	
	/**
	 * Worklets special settings.
	 * <pre>
	 * return array(
	 *     'base.menu' => array('space' => 'menu'),
	 *     'user.account' => array('layout' => 'worklet-special'),
	 * );
	 * </pre>
	 * Above means that 'base.menu' worklet should be placed into 'menu' space
	 * and 'user.account' worklet layout should be set to 'worklet-special'
	 * no matter what are the current settings of these worklet.
	 * @return array worklet special settings
	 */
	public function worklets()
	{
		return array();
	}
	
	/**
	 * @return string theme name
	 * @since 1.1.0
	 */
	public static function getThemeName()
	{
		return 'Unnamed Theme';
	}
	
	/**
	 * @return string the path for controller views. We have to override
	 * parent method so it changes view path directory according to the current
	 * render type set in initial worklet.
	 */
	public function getViewPath($ignoreCustom=false)
	{
		$bP = parent::getViewPath();
		return $this->customPath && file_exists($bP.DS.$this->customPath) && !$ignoreCustom
			? $bP.DS.$this->customPath
			: $bP;
	}
}
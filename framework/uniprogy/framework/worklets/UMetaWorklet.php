<?php
/**
 * UMetaWorklet class file.
 *
 * @link http://www.uniprogy.com/
 * @copyright Copyright &copy; 2009-2010 UniProgy Limited
 * @license http://www.uniprogy.com/license/
 */

/**
 * UMetaWorklet is a system worklet that holds meta data for a particular module.
 *
 * @version $Id: UMetaWorklet.php 4 2010-08-26 10:07:00 GMT vasiliy.y $
 * @package system.worklets
 * @since 1.0.0
 */
class UMetaWorklet extends USystemWorklet
{
	/**
	 * @return array meta data
	 * Ex.:
	 * <pre>
	 * return array(
	 *     'index' => array(
	 *         'title' => 'This is the index page',
	 *         'keywords' => 'index,page',
	 *         'description' => 'This is the description of the index page.'
	 *     )
	 * );
	 * </pre>
	 */
	public function metaData()
	{
		return array();
	}
	
	/**
	 * @return array default meta data.
	 */
	public static function defaultMetaData()
	{
		if(app()->controller->module)
			$title = app()->locale->textFormatter->format(
				ucfirst(basename(app()->controller->module->name)),
				' - ',
				ucfirst(basename(app()->controller->action->id))
			);
		else
			$title = ucfirst(basename(app()->controller->action->id));
		
		return array(
			'title' => $title,
			'keywords' => app()->params['keywords'],
			'description' => app()->params['description'],
		);
	}
	
	/**
	 * Combines {@link metaData worklet-level} and {@link defaultMetaData default} meta data
	 * into a single array.
	 * @return array meta data.
	 */
	public function taskGet()
	{
		$metaData = $this->metaData();
		$key = app()->controller->id != 'default'
			? app()->controller->id . '/' . app()->controller->action->id
			: app()->controller->action->id;
		return isset($metaData[$key]) ? CMap::mergeArray(self::defaultMetaData(), $metaData[$key])  : self::defaultMetaData();
	}
}
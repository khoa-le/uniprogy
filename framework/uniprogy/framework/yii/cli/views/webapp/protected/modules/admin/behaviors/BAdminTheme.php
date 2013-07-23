<?php
class BAdminTheme extends UWorkletBehavior
{
	public function afterWorklet()
	{
		if(app()->user->checkAccess('administrator')
			&& (app()->controller->id == 'admin'
				|| (app()->controller->module && app()->controller->module->name == 'admin')
				|| wm()->get('base.init')->states['admin']))
			app()->theme = $this->getModule()->param('theme')?$this->getModule()->param('theme'):null;
	}
}
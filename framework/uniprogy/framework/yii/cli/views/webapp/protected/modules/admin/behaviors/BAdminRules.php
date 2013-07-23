<?php
class BAdminRules extends UWorkletBehavior
{
	public function afterUrlRules($rules)
	{
		$rules[app()->params['adminUrl']] = 'admin';
		$rules[app()->params['adminUrl'].'/<_a>/*'] = 'admin/<_a>';
		
		if(!app()->user->checkAccess('administrator',array(),false)
			&& isset(app()->params['adminUrl']) && app()->params['adminUrl'] !== 'admin')
				$rules['admin*'] = 'base';
		return $rules;
	}
}
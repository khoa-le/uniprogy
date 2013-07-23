<?php
class WUserCron extends UCronWorklet
{
	public function taskBuild()
	{
		$this->expired();
	}
	
	public function taskExpired()
	{
		$models = MUser::model()->findAll("role=? AND created<=?",array('unverified',time()-$this->param('verificationTimeLimit')*3600));
		foreach($models as $m)
			wm()->get('user.admin.delete')->delete($m->id);
		$this->addResult($this->t('{num} unverified users have been deleted.', array('{num}'=>count($models))));
	}
}
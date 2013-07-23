<?php
class WBaseCron extends UCronWorklet
{	
	public function taskBuild()
	{
		$this->expiredBins();
	}
	
	public function taskExpiredBins()
	{
		$models = MStorageBin::model()->findAll("status=? AND created<?",array(0,time()-3*3600));
		foreach($models as $m)
		{
			$bin = app()->storage->bin($m->id);
			if($bin)
				$bin->purge();
		}
		$this->addResult($this->t('{num} temporary bins have been removed.', array('{num}'=>count($models))));
	}
}
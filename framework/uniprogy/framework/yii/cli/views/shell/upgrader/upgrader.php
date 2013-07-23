<?php echo "<?php\n"; ?>
class <?php echo $className; ?> extends UInstallWorklet
{
	public $fromVersion = '<?php echo $fromVersion; ?>';
	public $toVersion = '<?php echo $toVersion; ?>';
	
<?php if(count($params)) { ?>
	public function taskModuleParams()
	{
return CMap::mergeArray(parent::taskModuleParams(),<?php var_export($params); ?>);
	}
<?php } if(count($filters)) { ?>
	public function taskModuleFilters()
	{
return <?php var_export($filters); ?>;
	}
<?php } if($sql) { ?>
	public function taskSuccess()
	{
		parent::taskSuccess();
	}
<?php } ?>
}
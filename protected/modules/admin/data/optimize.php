<?php echo "<?php\n"; ?>
class BBaseOptimize extends UWorkletBehavior
{
	public function beforeBuild()
	{
<?php foreach($types as $type) { ?>
		$all = asma()->publish(app()->basePath .DS. 'assets' .DS. 'all.<?php echo $type; ?>');
		cs()-><?php if($type=='js'){ ?>registerScriptFile<?php } elseif($type == 'css') { ?>registerCssFile<?php } ?>($all);
		$scriptMap = array(
<?php foreach($files[$type] as $file) { ?>
			'<?php echo $file; ?>' => $all,
<?php  } ?>
		);
		cs()->scriptMap = CMap::mergeArray(cs()->scriptMap,$scriptMap);
<?php } ?>

		Yii::addCustomClasses(array(<?php
		foreach($classes as $k=>$v)
		{
			echo "'$k' => app()->basePath.'$v',\n";
		}
		?>));

	}
}
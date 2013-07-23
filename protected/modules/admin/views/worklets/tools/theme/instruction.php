<h4>Instructions</h4>
<ul>
	<li>Create theme package using the above form</li>
	<li>Unzip</li>
	<li>Upload to <?php echo Yii::getPathOfAlias('webroot'); ?></li>
	<li>To switch your site to this new theme edit this file
	<?php echo app()->basePath.DS.'config'.DS.'public'.DS.'instance.php'; ?>.<br />
	Just change 'themes' => 'classic' to 'theme' => '{THEME_ID}'<br />
	{THEME_ID} is your theme name from the from above in a lower case.<br />
	Ex.: <div class='box'><?php
	$str = <<<EOD
<?php
 return array (
  ...
  'theme' => '[s]mynewtheme[/s]',
);
EOD;
	$str = highlight_string($str,true);
	echo strtr($str,array('[s]'=>'<strong>','[/s]'=>'</strong>'));
	?></div>
	</li>
	<li>Please note that <?php echo UApp::getName(); ?> uses templates "inheritance" technique.
	So you can safely delete templates which you don't want to edit from your theme and 
	script will automatically use default ones in this case.</li>
</ul>
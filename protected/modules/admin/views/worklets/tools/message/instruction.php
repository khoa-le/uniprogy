<h4>Instructions</h4>
<ul>
	<li>Create language file using the above form</li>
	<li>Translate it</li>
	<li>Upload to <?php echo app()->basePath.DS.'messages'.DS.'{LANGUAGE_ID}'.DS.'uniprogy.php'; ?><br />
	Make sure to replace {LANGUAGE_ID} with ISO 639:1988/ISO 3166 language/region codes
	(ex.: en_us, fr, zn_ch)</li>
	<li>To switch your site to this new language edit this file
	<?php echo app()->basePath.DS.'config'.DS.'public'.DS.'instance.php'; ?>.<br />
	Insert: 'language' => '{LANGUAGE_ID}'<br />
	After: return array (<br />
	Ex.: <div class='box'><?php
	$str = <<<EOD
<?php
 return array (
  'language' => '[s]fr[/s]',
  ...
  'theme' => 'classic',
);
EOD;
	$str = highlight_string($str,true);
	echo strtr($str,array('[s]'=>'<strong>','[/s]'=>'</strong>'));
	?></div>
	</li>
</ul>
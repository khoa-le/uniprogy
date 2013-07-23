<?php
// This is the configuration for 
// yiic console message collector application
return array(
	array(
		'regex' => '/\bthis-\>t\s*\(\s*(\'.*?(?<!\\\\)\'|".*?(?<!\\\\)")\s*[,\)]/s',
		'index' => array('category' => 'uniprogy', 'message' => 1),
		'fileTypes' => array('php'),
		'exclude'=> array('.svn'),
	),
	array(
		'regex' => '/\bYii::t\s*\(\s*(\'.*?(?<!\\\\)\'|".*?(?<!\\\\)")\s*,\s*(\'.*?(?<!\\\\)\'|".*?(?<!\\\\)")\s*[,\)]/s',
		'index' => array('category' => 1, 'message' => 2),
		'fileTypes' => array('php'),
		'exclude'=> array('.svn'),
	),
);
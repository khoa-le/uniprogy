<?php
return CMap::mergeArray(
	require(dirname(__FILE__) . DS . 'public' . DS . 'modules.php'),
	CMap::mergeArray(
		require(dirname(__FILE__) . DS . 'public' . DS . 'instance.php'),
		require(dirname(__FILE__) . DS . 'main.php')
	)
);
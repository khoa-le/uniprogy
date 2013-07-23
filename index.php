<?php
// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/uniprogy/framework/yii/yii.php';
$config=dirname(__FILE__).'/protected/config/config.php';

// remove the following line when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once($yii);
require_once(dirname(__FILE__).'/protected/components/UApp.php');

Yii::createApplication('UApp',$config)->run();

<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

//Set global scale variables
defined('APP_PATH') or define('APP_PATH',dirname(__FILE__));
defined('BASE_URL') or define('BASE_URL',dirname($_SERVER['PHP_SELF']));

// Cache Control - We use the assets directory as this is recreated on every deploy
$cacheControlVersionFile = __DIR__.'/assets';
clearstatcache(true, $cacheControlVersionFile); // Clear PHP File Cache
defined('APP_VERSION') or define('APP_VERSION', filectime($cacheControlVersionFile));

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();

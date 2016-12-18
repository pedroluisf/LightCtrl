<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yii.php';
$configFile=dirname(__FILE__).'/protected/config/console.php';

//Set global scale variables
defined('APP_PATH') or define('APP_PATH',dirname(__FILE__));
defined('BASE_URL') or define('BASE_URL',dirname($_SERVER['PHP_SELF']));

require_once($yii);

// creating and running console application
Yii::createConsoleApplication($configFile)->run();
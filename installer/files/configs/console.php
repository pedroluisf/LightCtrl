<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>'C:\\xampp\\htdocs\\lcheadend\\protected\\config\\..',
	'name'=>'Lighting Controls Headend Console Application',

	// preloading 'log' component
	'preload'=>array('log'),

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.models.activeRecords.*',
        'application.models.commands.*',
        'application.models.forms.*',
        'application.models.jsonHandlers.*',
        'application.models.updaters.*',
        'application.components.*',
        'application.transfers.*',
    ),

	// application components
	'components'=>array(
        'db'=>array(
            'class'=>'CDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=lcheadend',
            'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
            'charset' => 'utf8',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'requests.log',
                    'categories'=>'request.*',
                    'levels'=>'info',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'cron.log',
                    'levels'=>'error, warning',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'cron_trace.log',
                    'levels'=>'trace',
                ),
            ),
        ),
	),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>array(
        'communications_class_name'=>'TcpConnection',
        'communications_port'=>'6661',
        'time_wait_cronjob_seconds'=>'20',
        'minimum_delay_save_emergency_response_in_minutes' => '5',
        'exclusions_on_status_save'=>'FFFFFFFF,00000000,FFFFF0FF',
        'maximum_retry_ett' => '3',
        'default_wattage'=>10,
        'delete_old_status_hist'=>1,
        'maximum_days_status_hist'=>90,
        'delete_old_queue_commands'=>0,
        'maximum_days_command_queue'=>90,
    ),
);
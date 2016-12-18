<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>'C:\\xampp\\htdocs\\lcheadend\\protected\\config\\..',
	'name'=>'Lighting Controls Headend',

    // Application Theme
    'theme' => 'intsys',

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

	'modules'=>array(
	),

	// application components
	'components'=>array(
		'user'=>array(
            'class' => 'WebUser',
            'loginUrl'=>array('user/login'),
			// set true to enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>'
			),
		),
        'configuration'=>array(
            'class'=>'WebConfiguration'
        ),
		// Database connection
		'db'=>array(
			'class'=>'CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=lcheadend',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		'errorHandler'=>array(
			'errorAction'=>'index/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
        'request'=>array(
            'enableCsrfValidation'=>true,
            'enableCookieValidation'=>true,
        ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
        'companyName'=>'Lighting Controls Ltd',
        'plansFolder'=>'/data/plans/',
        'configsFolder'=>'/data/configs/',
        'hashKey'=>'xxx',
        'timer_status_delay'=>'30000',
        'communications_class_name'=>'TcpConnection',
        'communications_port'=>'6661',
        'minimum_delay_save_emergency_response_in_minutes' => '5',
        'exclusions_on_status_save'=>'FFFFFFFF,00000000,FFFFF0FF',
        'maximum_retry_ett' => '3',
        'main_logo' => '/themes/intsys/images/lightctr.png',
		'footer_note' => 'Copyright &copy; ' . date('Y') . ' by The Lighting Controls Ltd.<br/>All Rights Reserved.',
        'default_page_size'=>20,
        'default_wattage'=>10,
	),
);
<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Application template',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'test',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
    */
		
	),

	// application components
	'components'=>array(
    
		'Smtpmail'=>array(
			'class'=>'application.extensions.smtpmail.PHPMailer',
			'Host'=>"localhost",
			'Username'=>'',
			'Password'=>'',
			'Mailer'=>'smtp',
			'Port'=>25,
			'SMTPAuth'=>false,
      'CharSet' => 'UTF-8'
		),
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),

		// uncomment the following to enable URLs in path-format

		'urlManager'=>array(
			'urlFormat'=>'path',
//      'showScriptName'=> false,
			'rules'=>array(

				'/api/test'                               => '/site/page/view/resttest',
				'/<page:[\w\-]+>'                         => '/site',
				'/<page:[\w\-]+>/<id:\d+>'                => '/site',
				'/api/upload-file/<model:\w+>/'           => '/base/uploadFile',
				'/api/<model:\w+>'                        => '/base',
				'/api/<model:\w+>/<id:\d+>'               => '/base',
				'/api/<model:\w+>*'                       => '/base',
				'<controller:\w+>/<id:\d+>'               => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'  => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>'           => '<controller>/<action>',
			),
		),


		// database settings are configured in database.php
		'db'=>require(dirname(__FILE__).'/db.php'),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),

	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
		'hideDemo'=>false,
	),
);

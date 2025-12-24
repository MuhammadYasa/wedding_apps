<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'Asia/Jakarta',
    'aliases' => [
        '@bower' => '@vendor/yidas/yii2-bower-asset/bower',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'wedding_1234567890',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Auth routes (must be before generic rules)
                'auth/login' => 'auth/login',
                'auth/logout' => 'auth/logout',
                
                // RSVP routes
                'rsvp/<id:\d+>' => 'rsvp/index',
                'rsvp/submit' => 'rsvp/submit',
                'rsvp/confirmation/<token:[\w\-]+>' => 'rsvp/confirmation',
                
                // Gallery routes
                'gallery' => 'gallery/index',
                'gallery/<id:\d+>' => 'gallery/index',
                
                // Admin routes
                'admin-rsvp' => 'admin-rsvp/index',
                'admin-rsvp/<action:\w+>' => 'admin-rsvp/<action>',
                'admin-rsvp/<action:\w+>/<id:\d+>' => 'admin-rsvp/<action>',
                
                'admin-guest' => 'admin-guest/index',
                'admin-guest/<action:\w+>' => 'admin-guest/<action>',
                'admin-guest/<action:\w+>/<id:\d+>' => 'admin-guest/<action>',
                
                'admin-gallery' => 'admin-gallery/index',
                'admin-gallery/<action:\w+>' => 'admin-gallery/<action>',
                'admin-gallery/<action:\w+>/<id:\d+>' => 'admin-gallery/<action>',
                
                // Invitation friendly URLs
                'invitation/<slug:[\w\-]+>' => 'invitation/view',
                
                // Default rules
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;

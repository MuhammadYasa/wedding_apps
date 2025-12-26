<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$security = require __DIR__ . '/security.php';

$config = [
    'id' => 'basic',
    'name' => 'Yadevs',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone' => 'Asia/Jakarta',
    'params' => array_merge(
        $params,
        ['security' => $security]
    ),
    'aliases' => [
        '@bower' => '@vendor/yidas/yii2-bower-asset/bower',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY') ?: 'wedding_1234567890',
            'enableCsrfValidation' => true,
            'csrfParam' => '_csrf-wedding',
            'enableCsrfCookie' => true,
            'csrfCookie' => [
                'httpOnly' => true,
                'secure' => !YII_DEBUG,
            ],
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'directoryLevel' => 2,
            'defaultDuration' => 3600, // 1 hour default cache
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
            'identityCookie' => [
                'name' => '_identity-wedding',
                'httpOnly' => true,
                'secure' => !YII_DEBUG,
            ],
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'timeout' => 3600, // 1 hour
            'cookieParams' => [
                'httpOnly' => true,
                'secure' => !YII_DEBUG,
                'sameSite' => 'Lax',
            ],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => [
                    'class' => 'yii\authclient\clients\Google',
                    'clientId' => getenv('GOOGLE_CLIENT_ID') ?: 'YOUR_GOOGLE_CLIENT_ID',
                    'clientSecret' => getenv('GOOGLE_CLIENT_SECRET') ?: 'YOUR_GOOGLE_CLIENT_SECRET',
                    'returnUrl' => 'http://localhost/wedding_apps/web/auth/callback',
                ],
            ],
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'bundles' => YII_ENV_PROD ? [
                'yii\web\JqueryAsset' => [
                    'js' => ['jquery.min.js']
                ],
                'yii\bootstrap5\BootstrapAsset' => [
                    'css' => ['bootstrap.min.css']
                ],
                'yii\bootstrap5\BootstrapPluginAsset' => [
                    'js' => ['bootstrap.bundle.min.js']
                ],
            ] : [],
            'appendTimestamp' => true, // Add timestamp to assets for cache busting
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // Use file transport in dev, SMTP in production
            'useFileTransport' => YII_ENV_DEV,
            'transport' => YII_ENV_DEV ? null : [
                'scheme' => getenv('MAIL_SCHEME') ?: 'smtp',
                'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
                'username' => getenv('MAIL_USERNAME') ?: 'your-email@gmail.com',
                'password' => getenv('MAIL_PASSWORD') ?: 'your-app-password',
                'port' => getenv('MAIL_PORT') ?: 587,
                'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
                'streamOptions' => [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ],
            ],
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
            'enableStrictParsing' => false,
            'cache' => 'cache', // Enable URL rule caching
            'rules' => [
                // Auth routes (must be before generic rules)
                'auth/login' => 'auth/login',
                'auth/logout' => 'auth/logout',
                'auth/callback' => 'auth/callback',
                
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
                'admin-gallery/<action:\w+>' => 'admin-gallery/<action>',
                'admin-gallery/<action:\w+>/<id:\d+>' => 'admin-gallery/<action>',
                
                // Invitation friendly URLs
                'invitation/<slug:[\w\-]+>/qrcode/<token:[\w\-]+>' => 'invitation/qrcode',
                'invitation/<slug:[\w\-]+>/print' => 'invitation/print',
                'invitation/<slug:[\w\-]+>/thankyou' => 'invitation/thankyou',
                'invitation/<slug:[\w\-]+>/send-message' => 'invitation/send-message',
                'invitation/<slug:[\w\-]+>/get-messages' => 'invitation/get-messages',
                'invitation/<slug:[\w\-]+>/rsvp' => 'invitation/rsvp',
                'invitation/<slug:[\w\-]+>' => 'invitation/view',

                
                // Default rules
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
    ],
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

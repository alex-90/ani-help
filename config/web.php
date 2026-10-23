<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$routes = require __DIR__ . '/routes.php';

$config = [
    'id' => 'basic',
    'name' => 'My Application',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'sourceLanguage' => 'en-US',
    'timeZone' => 'Asia/Yekaterinburg',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'view' => [
            // Тема «Приют для животных»: тёплое дружелюбное оформление.
            // Для view-файлов без тематической версии используется оригинал (fallback).
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@app/themes/animals',
                ],
                'baseUrl' => '@web/themes/animals',
            ],
        ],
        'request' => [
            // ключ из окружения (COOKIE_VALIDATION_KEY); старый был в публичном репозитории
            'cookieValidationKey' => $_ENV['COOKIE_VALIDATION_KEY'] ?? getenv('COOKIE_VALIDATION_KEY') ?: 'dev-only-insecure-key',
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
            'rules' => $routes,
        ],
        'b2' => [
            'class'    => 'app\components\B2Storage',

            'keyId'    => $_ENV['B2_KEY_ID'] ?? getenv('B2_KEY_ID') ?: '',
            'appKey'   => $_ENV['B2_APP_KEY'] ?? getenv('B2_APP_KEY') ?: '',

            'endpoint' => 'https://s3.eu-central-003.backblazeb2.com',
            'region'   => 'eu-central-003',
            'bucket'   => 'kkkk-site',
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

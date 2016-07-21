<?php
return array(
    'language' => 'zh_cn',
    'timeZone' => 'Asia/Chongqing',
    'charset' => 'utf-8',
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'commonAuth',
    'preload' => array('log'),
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.extensions.*'
    ),
    'components' => array(
        'urlManager' => array(
            'caseSensitive' => true,
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                    'logFile' => 'error.log',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'trace, profile, info',
                    'logFile' => 'access.log',
                ),
            ),
        ),
        'cache' => array(
            'class' => 'system.caching.CFileCache',
            //'directoryLevel' => 2
        ),
        'session' => array(
            'sessionName' => 'SITESESSID',
            'class' => 'CCacheHttpSession',
            'cacheID' => 'cache',
            'cookieMode' => 'only',
            'timeout' => 1200,
        ),
        'db' => array(
            'connectionString' => 'mysql:host=127.0.0.1;dbname=auth',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'tablePrefix' => '',
            'emulatePrepare' => true,
            'enableParamLogging' => true,
            'enableProfiling' => true,
        ),
    ),
    'language' => 'zh_cn',
    'params' => require(dirname(__FILE__) . '/params.php'),
);
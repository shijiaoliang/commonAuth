<?php
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

//全局常量
require_once dirname(__FILE__) . '/constants.php';

$yii = dirname(__FILE__) . '/framework/yii.php';
require_once($yii);

$config = require(dirname(__FILE__) . '/config/main.php');
Yii::createWebApplication($config)->run();
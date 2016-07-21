<?php
// 项目路径
define('PROJECT_PATH', realpath(dirname(__FILE__) . '/../') . '/');

//网站地址
define('SROOT', (@$_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/');

//上传文件地址
define('UPLOAD_DIR', realpath(dirname(__FILE__) . '/uploads/'));

//上传路劲域名地址
define('HTTP_UPLOAD', SROOT . 'uploads/');

//静态资源地址
define('STATICS_DIR', SROOT . 'statics/');

//公共域配置
define('STATIC_COOKIES', 'auth.com');

//系统key
define('KEY', '24dff7fdd5719b07a0b1e56058764e25');//md5('commonAuth18')
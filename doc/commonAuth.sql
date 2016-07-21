-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        5.5.40 - MySQL Community Server (GPL)
-- 服务器操作系统:                      Win32
-- HeidiSQL 版本:                  9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出 auth 的数据库结构
CREATE DATABASE IF NOT EXISTS `auth` /*!40100 DEFAULT CHARACTER SET gbk */;
USE `auth`;


-- 导出  表 auth.app 结构
CREATE TABLE IF NOT EXISTS `app` (
  `app_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_name` varchar(255) DEFAULT NULL COMMENT '应用名称',
  `app_code` varchar(255) DEFAULT NULL COMMENT '应用唯一编码',
  `app_status` tinyint(3) unsigned DEFAULT NULL COMMENT '应用状态, 1:开启，2:禁用',
  `app_url` varchar(255) DEFAULT NULL COMMENT '应用网址',
  `app_create_time` int(10) unsigned DEFAULT NULL COMMENT '应用创建时间',
  PRIMARY KEY (`app_id`),
  UNIQUE KEY `code` (`app_code`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='应用';

-- 正在导出表  auth.app 的数据：0 rows
/*!40000 ALTER TABLE `app` DISABLE KEYS */;
/*!40000 ALTER TABLE `app` ENABLE KEYS */;


-- 导出  表 auth.module 结构
CREATE TABLE IF NOT EXISTS `module` (
  `module_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(255) DEFAULT NULL COMMENT '模块名称',
  `module_code` varchar(255) DEFAULT NULL COMMENT '模块编码',
  `module_status` tinyint(4) DEFAULT NULL COMMENT '模块状态,1:启用2:禁用',
  `module_create_time` int(10) unsigned DEFAULT NULL COMMENT '模块创建时间',
  `app_id` int(10) unsigned DEFAULT NULL COMMENT '应用id',
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `module_code` (`module_code`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='应用模块表';

-- 正在导出表  auth.module 的数据：0 rows
/*!40000 ALTER TABLE `module` DISABLE KEYS */;
/*!40000 ALTER TABLE `module` ENABLE KEYS */;


-- 导出  表 auth.permission 结构
CREATE TABLE IF NOT EXISTS `permission` (
  `p_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `p_name` varchar(255) DEFAULT NULL,
  `p_code` varchar(255) DEFAULT NULL,
  `p_type` tinyint(3) unsigned DEFAULT '10' COMMENT '权限类型, 10:普通权限 20:数据权限',
  `p_status` tinyint(3) unsigned DEFAULT NULL COMMENT '权限状态, 1:开启 2:禁用',
  `p_app_id` int(10) unsigned DEFAULT NULL,
  `p_module_id` int(10) unsigned DEFAULT NULL,
  `p_data_url` varchar(255) DEFAULT NULL,
  `p_data_id` int(11) DEFAULT NULL,
  `p_create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`p_id`),
  UNIQUE KEY `p_code` (`p_code`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='权限表';

-- 正在导出表  auth.permission 的数据：0 rows
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;
/*!40000 ALTER TABLE `permission` ENABLE KEYS */;


-- 导出  表 auth.role 结构
CREATE TABLE IF NOT EXISTS `role` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) DEFAULT NULL,
  `role_code` varchar(255) DEFAULT NULL,
  `role_status` tinyint(3) unsigned DEFAULT '10' COMMENT '10:启用 20:禁用',
  `permission_codes` text COMMENT '角色拥有权限编码, 多个权限用 "," 隔开',
  `data_role_codes` text COMMENT '角色拥有的数据权限编码, {data_id}_{data_code}',
  `role_create_time` int(11) unsigned zerofill DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_code` (`role_code`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='角色表';

-- 正在导出表  auth.role 的数据：0 rows
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
/*!40000 ALTER TABLE `role` ENABLE KEYS */;


-- 导出  表 auth.user 结构
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) DEFAULT NULL,
  `user_no` varchar(255) DEFAULT NULL,
  `user_pwd` varchar(255) DEFAULT NULL,
  `user_status` tinyint(4) DEFAULT '10' COMMENT '10:启用 20:禁用',
  `role_codes` varchar(1024) DEFAULT NULL,
  `last_login_ip` varchar(100) DEFAULT NULL,
  `last_login_time` int(11) DEFAULT NULL,
  `user_create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_no` (`user_no`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk COMMENT='用户表';

-- 正在导出表  auth.user 的数据：0 rows
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`user_id`, `user_name`, `user_no`, `user_pwd`, `user_status`, `role_codes`, `last_login_ip`, `last_login_time`, `user_create_time`) VALUES
	(1, 'admin', '605724193', 'e5a1748e7863b603bc947be49a7fd631', 10, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

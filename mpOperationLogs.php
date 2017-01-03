<?php
/*
Plugin Name: MpOperationLogs
Plugin URI: http://www.ipy8.com/
Description: 后台管理员操作日志、后台登陆IP记录插件(administrator operation logs and IP record Plugin).
Version: 1.0.0
Author: Mrpeng
Author URI: http://www.ipy8.com/
*/
defined( 'ABSPATH' )  or exit;
date_default_timezone_set(get_option('timezone_string'));
define("MRPENG_ROOT", dirname(__FILE__) . '/');
require_once(MRPENG_ROOT.'common.php');
require_once(MRPENG_ROOT.'dispatcher.php');
require_once(MRPENG_ROOT.'lib/MpLogs.class.php');
require_once(MRPENG_ROOT.'lib/MpIpLogs.class.php');
register_activation_hook(__FILE__, 'operationlog_activation_createtable');
register_deactivation_hook(__FILE__, 'operationlog_activation_deletetable');


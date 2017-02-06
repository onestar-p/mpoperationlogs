<?php
/*
Plugin Name: MpOperationLogs
Plugin URI: https://wordpress.org/plugins/mpoperationlogs/
Description: 后台管理员操作日志、后台登陆IP记录插件(administrator operation logs and IP record Plugin).
Version: 1.0.1
Author: Mrpeng
Author URI: http://www.ipy8.com/
*/
defined( 'ABSPATH' )  or exit;
$timezoneString = get_option('timezone_string');
$timezoneString = !empty($timezoneString)?
                    $timezoneString:'Asia/Shanghai';
date_default_timezone_set($timezoneString);

$mrpengRoot = plugin_dir_path(__FILE__ );
require_once($mrpengRoot.'common.php');
require_once($mrpengRoot.'dispatcher.php');
require_once($mrpengRoot.'lib/MpLogs.class.php');
require_once($mrpengRoot.'lib/MpIpLogs.class.php');
register_activation_hook(__FILE__, 'operationlog_activation_createtable');
register_deactivation_hook(__FILE__, 'operationlog_activation_deletetable');


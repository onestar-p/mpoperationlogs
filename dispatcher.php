<?php
add_action('publish_post', 'mpoplogs_postAddLogs');
add_action('xmlrpc_public_post', 'mpoplogs_postAddLogs');
add_action('deleted_post','mpoplogs_deletePostLogs');
add_action('wp_login','mpUserLoginWriteIp');
add_action('admin_menu','mpoplogs_menuIndex');
function mpoplogs_menuIndex()
{
    add_menu_page('管理员操作日志','管理员操作日志','administrator','mpoplogs_menuIndex','mpoplogs_logListIndex');
    add_submenu_page('mpoplogs_menuIndex','操作日志列表','日志列表','administrator','operationLog','mpoplogs_getLogsListView');
    add_submenu_page('mpoplogs_menuIndex','IP地址列表','IP列表','administrator','iplist','mpoplogs_getIpListView');
}

function mpoplogs_logListIndex()
{

    echo '<h1>欢迎使用</h1><hr>';
    echo '<pre>
本插件主要用于后台管理员、编辑等角色对文章操作的记录(发布\更新\删除)以及用户登录的IP地址记录（This plugin is mainly used for post operation records (release, update, delete) and the user login IP address record.）。
如果在使用中发现BUG或者有任何建议或意见可以发送邮件至“root@ipy8.com”。

注意：在启用本插件时，会在你的数据库中新建两张数据表，1：日志记录表；2：IP地址记录表，如果停用插件将会删除这两张表（如需清空数据停用再启用即可）。
</pre>';
}

// 加载IP列表页面
function mpoplogs_getIpListView()
{
    wp_enqueue_script('jquery');
    try
    {
        $admin_ajax_url = admin_url('admin-ajax.php');
        $obj = new MpLogs();
        $obj -> assign('admin_ajax_url',$admin_ajax_url);
        $obj -> display('ipsList');
    }
    catch(Exception $e)
    {
        wp_die('MpOperationLogs Error:'.$e->getMessage());
    }

}

// 加载日志列表页面
function mpoplogs_getLogsListView()
{
    wp_enqueue_script('jquery');
    try
    {
        $admin_ajax_url = admin_url('admin-ajax.php');
        $obj = new MpLogs();
        $obj -> assign('admin_ajax_url',$admin_ajax_url);
        $obj -> display('logsList');
    }
    catch(Exception $e)
    {
        wp_die('MpOperationLogs Error:'.$e->getMessage());
    }

}

// 异步获取日志列表
function mpoplogs_ajaxGetLogsList()
{
    try
    {
        $page = isset($_POST['page'])?(int)trim($_POST['page']):1;
        $limit = isset($_POST['limit'])?(int)trim($_POST['limit']):10;
        $page = ($page <= 0)?1:$page;
        $log = new MpLogs();
        $log -> order('op_time','desc');
        $res = mpoplogs_pagerQuery($log,function($list){
            foreach($list as $k => $v)
            {
                $mpIp = new MpIpLogs();
                $ip = $mpIp->getIpAddressById($v['op_ip_id']);
                $list[$k]['op_time'] = date('Y-m-d H:i:s',$v['op_time']);
                $list[$k]['op_type'] = Mplogs::$opTypeText[$v['op_type']];
                $list[$k]['op_ip_id'] = $ip;
            }
            return $list;
        },$page,$limit);
        $html = $log->fetch('loglist_td',$res['list']);
        $res['list'] = $html;
        mpoplogs_die_ok('ok',$res);
    }
    catch(Exception $e)
    {
        mpoplogs_die_err($e->getMessage());
    }
}

// 异步获取IP列表
function mpoplogs_ajaxGetIpsList()
{
    try
    {
        $page = isset($_POST['page'])?(int)trim($_POST['page']):1;
        $limit = isset($_POST['limit'])?(int)trim($_POST['limit']):10;
        $page = ($page <= 0)?1:$page;
        $ipObj = new MpIpLogs();
        $ipObj -> order('id','DESC');
        $res = mpoplogs_pagerQuery($ipObj,function($list){
            foreach($list as $k => $v)
            {
                $list[$k]['first_write_time'] = date('Y-m-d H:i:s',$v['first_write_time']);
                $list[$k]['last_write_time'] = date('Y-m-d H:i:s',$v['last_write_time']);

            }
            return $list;
        },$page,$limit);
        $html = $ipObj->fetch('ipslist_td',$res['list']);
        $res['list'] = $html;
        mpoplogs_die_ok('ok',$res);
    }
    catch (Exception $e)
    {
        mpoplogs_die_err($e->getMessage());
    }
}



add_action('wp_ajax_mpoplogs_ajaxGetIpsList','mpoplogs_ajaxGetIpsList');
add_action('wp_ajax_mpoplogs_ajaxGetLogsList','mpoplogs_ajaxGetLogsList');

// 新增、修改文章保存日志
function mpoplogs_postAddLogs($post_id)
{

    try
    {
        MpLogs::doActionPost($post_id);
    }
    catch(Exception $e)
    {
//        wp_die('MpOperationLogs Error:'.$e->getMessage());
    }
}

// 文章删除日志操作
function mpoplogs_deletePostLogs($postId)
{
    try
    {
        MpLogs::doActionPost($postId,true);
    }
    catch(Exception $e)
    {
//        wp_die('MpOperationLogs Error:'.$e->getMessage());
    }
}

// 用户登录保存IP地址
function mpUserLoginWriteIp($userName)
{
    try
    {
        $ip = mpoplogs_getUserIp();
        $userId = wp_cache_get($userName,'userlogins');
        $ipObj = new MpIpLogs();
        $res = $ipObj->checkoutUseIp($userId,$ip);
        if(!$res)
            $ipObj->addUserIp($userId,$ip);
        else
            $ipObj->updateUserRecoredNums($userId,$ip);
    }
    catch (Exception $e)
    {
    }

}

function operationlog_activation_createtable()
{
    global $mrpengRoot;
    require_once($mrpengRoot.'lib/cls.MpDb.php');
    $db = new MpDbOperationlog();
    $db->setTableName(array('mp_ips','mp_logs'));
    $db->checkTables();
}

function operationlog_activation_deletetable()
{
    global $mrpengRoot;
    require_once($mrpengRoot.'lib/cls.MpDb.php');
    $db = new MpDbOperationlog();
    $db->setTableName(array('mp_ips','mp_logs'));
    $db->delete();
}
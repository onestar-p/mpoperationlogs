<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2016/12/16
 * Time: 0:33
 */


function mpoplogs_getUserIp()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"),
            "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']
        && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "0.0.0.0";
    return $ip;
}


function ETHROW($msg, $e_code=0) {
    /*$debug_backtrace = debug_backtrace();
    $File_Path = $debug_backtrace[0]["file"];
    $file_name = basename($File_Path);
    $line = $debug_backtrace[0]["line"];

    $md5 = md5($file_name . "line:" . $line);
    $base36 = strtoupper(base_convert($md5, 16, 36));

    $ERROR_CODE = strtoupper(substr($base36, 0, 4));
    $request_id = $ERROR_CODE . ':' . (strtoupper(base_convert(time(), 10, 36)));
    $log = '【' . $request_id . '】' . $msg ."\r\n" . "{$File_Path}:{$line}" . "\r\n" .get_http_raw() . "\r\n";

    Log::write($log, Log::ERR, '', exceptioin_log_dir() . date('y_m_d') . '.BLException.log');
    */
    $e = new Exception($msg, $e_code);

    throw $e;

}

function mpoplogs_die_err($msg='',$arr=array())
{
    $arr = array(
        'status' =>0,
        'msg' => $msg,
        'data'=>$arr,
    );
    exit(json_encode($arr));
}

function mpoplogs_die_ok($msg='',$arr=array())
{
    $arr = array(
        'status' =>1,
        'msg' => $msg,
        'data'=>$arr,
    );
    exit(json_encode($arr));
}


// 检测是否开启output_buffering
function checkOutputBuffering()
{
    if(ini_get('output_buffering')<=0)
        return false;
    else
        return true;

}

// 分页获取数据
function mpoplogs_pagerQuery($model,$fun,$page=1,$limit=10)
{
    $data = array();

    $total = $model->count();
    $list = $model->select($page,$limit);
    if(isset($fun) && $list)
    {
        $list = $fun($list);
        if($list === null)
        {
            $list = array();
        }
    }
    $data['list'] = $list;
    $data['total'] = $total;
    $data['pageSum'] = ceil(($total/$limit));
    $data['page'] = $page;
    $data['pageHtml'] = mpoplogs_pageHtml($data);
    return $data;


}

function mpoplogs_pageHtml($data=array())
{
    $html = '';
    $html .= ' <a href="javascript:;" page="1"  class="page">首页</a> ';
    $upPage = ($data['page']<=1)?1:$data['page']-1;
    $nextPage = ($data['page']<$data['pageSum'])?$data['page']+1:$data['pageSum'];
    if($data['page'] == 1)
        $html .= " <span> 上一页</span> ";
    else
        $html .= ' <a href="javascript:;" page="'.$upPage.'"  class="page">上一页</a> ';

    for($i=1;$i<=$data['pageSum'];$i++)
    {
        if($i == $data['page'])
            $html .= " <u><span>{$i}</span></u> ";
        else
            $html .= ' <a href="javascript:;" page="'.$i.'"  class="page">'.$i.'</a> ';
    }

    if($data['page'] == $data['pageSum'])
        $html .= " <span>下一页</span> ";
    else
        $html .= ' <a href="javascript:;" page="'.$nextPage.'"  class="page">下一页</a> ';
    $html .= ' <a href="javascript:;" page="'.$data["pageSum"].'"  class="page">尾页</a> ';
    $html .= ' 当前页:<span>'.$data["page"].'</span> ';
    $html .= ' 总页数:<span>'.$data["pageSum"].'</span> ';
    return $html;
}
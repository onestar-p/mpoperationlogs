<?php
require_once($mrpengRoot.'lib/OperationlogBase.php');
class MpLogs extends OperationlogBase
{
    private $op_type;
    private $allow_op_type = array('create','update','delete');
    private $data = array();
    private $format = '';

    static public $opTypeText = array(
        'create'=>'发布文章',
        'update'=>'更新文章',
        'delete'=>'删除文章',
    );

    public function __construct()
    {
        parent::__construct();
        if(!$this->wpdb)
        {
            global $wpdb;
            $this->wpdb = $wpdb;
        }
        $wpdb -> mrpengtable = $wpdb->prefix.'mp_logs';
    }

    public function action($type)
    {
        if(!in_array($type,$this->allow_op_type)) ETHROW('不存在该日志操作类型');
        $this->op_type = $type;
        return $this;
    }

    public function data($data=array())
    {
        if(empty($data)) ETHROW('缺少插入数据');
        $this->checkAction();
        $this->data = array(
            'class' => $this->isValue('class',$data),
            'op_uid' => $this->isValue('op_uid',$data,0),
            'op_data_id' => $this->isValue('op_data_id',$data,0),
            'data_name' => $this->isValue('data_name',$data,''),
            'op_time' => $this->isValue('op_time',$data,0),
            'op_ip_id' => $this->isValue('op_ip_id',$data,0),
            'other_info' => $this->isValue('other_info',$data),
            'op_user_name' => $this->isValue('op_user_name',$data),
        );
        $this->data['op_type'] = $this->op_type;
        $this->format = '%s,%d,%d,%s,%d,%d,%s,%s,%s';
        return $this;
    }

    public function insert()
    {
        $this->checkData();
        $fields = '`'.implode('`,`',array_keys($this->data)).'`';
        $sql = "INSERT INTO `{$this->wpdb->mrpengtable}` ({$fields}) VALUES ({$this->format})";
        $res = $this->wpdb->query($this->wpdb->prepare($sql,$this->data));
        return $res;
    }

    private function isValue($val='',$arr=array(),$re='')
    {
        if(isset($arr[$val]))
            return $arr[$val];
        else
            return $re;
    }

    private function checkData()
    {
        if(empty($this->data)) ETHROW('缺少保存数据');
    }

    private function checkAction()
    {
        if(empty($this->op_type)) ETHROW('缺少操作类型');
        return true;
    }


    public function count()
    {
        $sql = "SELECT COUNT(id) count FROM `{$this->wpdb->mrpengtable}`";
        $count = $this->wpdb->get_row($sql);
        if($count)
            return $count->count;
        else
            return 0;
    }

    static public function doActionPost($postId,$isDelete=false)
    {
        $post = wp_cache_get($postId,'posts');


        if(($post && $post->post_type == 'post') && $post->post_status != 'auto-draft')
        {
            $userInfo = wp_cache_get($post->post_author,'users');
            $postTitle = $post->post_title;
            $uid = $userInfo->ID;
            if($uid)
            {
                $ip = mpoplogs_getUserIp();
                $ipObj = new MpIpLogs();
                $ips = $ipObj->checkoutUseIp($uid,$ip);
                $ipId = 0;
                if($ips)
                    $ipId = $ips->id;
                else
                    $ipId = $ipObj ->addUserIp($uid,$ip);
                $logs = new self();
                $data = array(
                    'class' => 'post',
                    'op_uid' => $uid,
                    'op_data_id' => (int)$postId,
                    'data_name' => $postTitle,
                    'op_time' => time(),
                    'op_ip_id' => $ipId,
                    'other_info' => '',
                    'op_user_name' => $userInfo->user_login,
                );
                if(!$isDelete)
                {
                    if($post->post_date == $post->post_modified)
                        $logs->action('create');
                    else
                        $logs->action('update');
                }
                else
                {
                    $logs->action('delete');
                }
                $logs->data($data)->insert();
            }

        }
    }

}

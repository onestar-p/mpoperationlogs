<?php

abstract class OperationlogBase
{
    private $variableParams = array();
    private $order = '';
    protected $wpdb = null;

    protected $mrpengRoot;
    public function __construct()
    {
        global $mrpengRoot;
        $this->mrpengRoot = $mrpengRoot;
    }

    public function assign($variableName='',$params='')
    {
        $variableName = trim($variableName);
        if(empty($variableName)) ETHROW('变量名不能为空');
        $this->variableParams[$variableName] = $params;
    }

    public function fetch($temp='',$data=array())
    {

        if(!checkOutputBuffering()) ETHROW('ERROR:php output_buffering 未开启,不能正常使用本插件');
        ob_start();
        require_once($this->mrpengRoot.'template/'.$temp.'.php');
        return ob_get_clean();
    }

    public function display($tName='')
    {
        if(!empty($this->variableParams))
            extract($this->variableParams);
        include_once($this->mrpengRoot.'template/'.$tName.'.php');
        exit;
    }

    public function order($f,$d='')
    {
        $this->order = "ORDER BY {$f} {$d}";
        return $this;
    }

    public function select($page=1,$limit=10)
    {
        $offset = ($page-1)*$limit;
        $sql = "SELECT * FROM `{$this->wpdb->mrpengtable}` {$this->order} LIMIT %d OFFSET %d";
        $data = $this->wpdb->get_results($this->wpdb->prepare($sql,$limit,$offset),ARRAY_A);
        return $data;
    }

}
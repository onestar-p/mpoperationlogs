<?php

class MpDbOperationlog
{
    private $tableName = array();
    private $wpdb = null;
    private $prefix;
    private $charsetCollate='';
    private $db_version = '1.0.0';
    private $dbKey = 'mp_operation_db_version';

    public function __construct()
    {
        if(!$this->wpdb)
        {
            global $wpdb;
            $this->wpdb = $wpdb;
        }
        $this->prefix = $this->wpdb->prefix;
        if(!empty($this->wpdb->charset))
            $this->charsetCollate .= " DEFAULT CHARACTER SET {$this->wpdb->charset} ";
        if(!empty($this->wpdb->collate))
            $this->charsetCollate .= " COLLATE {$this->wpdb->collate} ";

    }

    public function addOptionDbVersion()
    {
        return add_option($this->dbKey,$this->db_version);
    }

    public function getOptionDbVersion()
    {
        return get_option($this->dbKey);
    }

    public function updateOptionDbVersion()
    {
        return update_option($this->dbKey,$this->db_version);
    }

    public function deleteOptionDbVersion()
    {
        return delete_option($this->dbKey);
    }

    public function setTableName($name=array())
    {
        if(!is_array($name)) ETHROW('参数格式错误');
        $this->tableName = $name;
        return $this;
    }

    private function checkTableIsSet()
    {
        if(empty($this->tableName) || !is_array($this->tableName))
            ETHROW('未设置需创建的表名');
        else
            return true;
    }

    public function createTable()
    {
        $this->checkTableIsSet();
        require_once(ABSPATH.'wp-admin/includes/upgrade.php' );
        foreach($this->tableName as $k => $v)
        {
            $fun = 'create_sql_'.$v;
            $sql = $this->{$fun}($v);
            dbDelta($sql);
        }
    }

    private function create_sql_mp_ips($name='')
    {
        $tableName = $this->prefix.$name;
        $sql = "CREATE TABLE `{$tableName}` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `uid` int(10) unsigned NOT NULL DEFAULT '0',
              `user_name` varchar(62) COLLATE utf8mb4_unicode_ci DEFAULT '',
              `ip_address` varchar(62) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
              `session_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
              `first_write_time` int(10) NOT NULL DEFAULT '0',
              `last_write_time` int(10) NOT NULL DEFAULT '0',
              `record_nums` int(10) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `uid` (`uid`),
              KEY `ip_adderss` (`ip_address`)
            ){$this->charsetCollate};";

        return $sql;
    }

    private function create_sql_mp_logs($name='')
    {
        $tableName = $this->prefix.$name;
        $sql = "CREATE TABLE `{$tableName}` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `op_uid` int(10) NOT NULL DEFAULT '0',
                  `op_user_name` varchar(62) COLLATE utf8mb4_unicode_ci DEFAULT '',
                  `op_type` varchar(10) CHARACTER SET utf8 DEFAULT '',
                  `op_data_id` int(10) DEFAULT '0',
                  `data_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
                  `class` varchar(10) CHARACTER SET utf8 DEFAULT '',
                  `op_time` int(10) NOT NULL DEFAULT '0',
                  `op_ip_id` int(10) DEFAULT '0',
                  `other_info` text CHARACTER SET utf8,
                  PRIMARY KEY (`id`),
                  KEY `op_uid` (`op_uid`)
                ){$this->charsetCollate};";
        return $sql;
    }

    public function checkTables()
    {
        $version = $this->getOptionDbVersion();
        if($version)
        {
            $ver = $this->diffVersion($version);
            if(($ver != 'eq') && ($ver !== false))
            {
                $this->createTable();
                $this->updateOptionDbVersion();
            }
        }
        else
        {
            if($this->addOptionDbVersion())
            {
                $this->createTable();
            }
        }
    }

    /// return gt : > ; lt : < ; eq : ==
    private function diffVersion($version)
    {
        $theVersion = explode('.',$this->db_version);
        $oldVersion = explode('.',$version);
        if(count($theVersion) != count($oldVersion)) return false;
        foreach($theVersion as $k => $v)
        {
            if($v == $oldVersion[$k])
                continue;

            if($v > $oldVersion[$k])
                return 'gt';
            if($v < $oldVersion[$k])
                return 'lt';
        }
        return 'eq';
    }

    public function delete()
    {
        $this->checkTableIsSet();

        foreach($this->tableName as $k => $v)
        {
            $sql = "DROP TABLE IF EXISTS ";
            $tableName = $this->prefix.$v;
            $sql .= $tableName;
            $res = $this->wpdb->query($sql);
        }
        $this->deleteOptionDbVersion();

    }

}
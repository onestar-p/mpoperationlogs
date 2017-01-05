<?php
require_once($mrpengRoot.'lib/OperationlogBase.php');
class MpIpLogs extends OperationlogBase
{

   public function __construct()
   {
       parent::__construct();
      if(!$this->wpdb)
      {
         global $wpdb;
         $this->wpdb = $wpdb;
      }
      $wpdb -> mrpengtable = $wpdb->prefix.'mp_ips';
   }

   public function getIpAddressById($ipId)
   {
      $sql = "SELECT `ip_address` FROM {$this->wpdb->mrpengtable} WHERE `id` = %d";
      $res = $this->wpdb->get_row($this->wpdb->prepare($sql,$ipId));
      if($res)
         return $res->ip_address;
//      else
//         $this->
      return 0;

   }

   public function checkoutUseIp($userId,$ip)
   {
      $sql = "SELECT * FROM {$this->wpdb->mrpengtable} WHERE `ip_address` = '%s' AND `uid` = %d ORDER BY last_write_time DESC";
      $res = $this->wpdb->get_row($this->wpdb->prepare($sql,array($ip,$userId)));
      return $res;
   }

   public function updateUserRecoredNums($userId,$ip)
   {
      $data = array(
          wp_get_session_token(),
          time(),
          $userId,
          $ip,
      );
      $sql = "UPDATE `{$this->wpdb->mrpengtable}` SET
            `record_nums` = `record_nums` + 1 , `session_token` = '%s' , `last_write_time` = %d
            WHERE `uid` = %d AND `ip_address` = '%s'";
      $res = $this->wpdb->query($this->wpdb->prepare($sql,$data));
      return $res;
   }

   public function addUserIp($userId,$ip)
   {
      $userInfo = wp_cache_get($userId,'users');

      $data = array(
          'uid'=>$userId,
          'ip_address'=>$ip,
          'session_token'=>wp_get_session_token(),
          'firest_write_time'=>time(),
          'last_write_time'=>time(),
          'record_nums'=>1,
          'user_name'=>$userInfo->user_login,
      );
      $sql = "INSERT INTO `{$this->wpdb->mrpengtable}`
              (`uid`,`ip_address`,`session_token`,`first_write_time`,`last_write_time`,`record_nums`,`user_name`)
              VALUES(%d,'%s','%s',%d,%d,%d,'%s')";
      $res = $this->wpdb->query($this->wpdb->prepare($sql,$data));
      if($res)
      {
         $check = $this->checkoutUseIp($userId,$ip);
         if($check) return $check->id;
      }
      return $res;

   }

   public function count()
   {
      $sql = "SELECT count(id) count FROM `{$this->wpdb->mrpengtable}`";
      $ip = $this->wpdb->get_row($sql);
      if($ip)
         return $ip->count;
      else
         return 0;
   }


}
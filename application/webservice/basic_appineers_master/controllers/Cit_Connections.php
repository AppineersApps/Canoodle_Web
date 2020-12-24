<?php
/**
 * Description of Resend Otp Extended Controller
 * 
 * @module Extended Resend Otp
 * 
 * @class Cit_Resend_otp.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_Resend_otp.php
 * 
 * @author CIT Dev Team
 * 
 * @date 18.09.2019
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_Connections extends Connections {
  public function __construct()
  {
      parent::__construct();
  }

  public function checkConnectionExist($input_params=array()){

      $return_arr['message']='';
      $return_arr['status']='1';
       if(false == empty($input_params['user_id']))
       {
          $this->db->from("user_connections AS usc");
          $this->db->select("usc.iConnectionId AS conn_id");
          $this->db->select("usc.eConnectionType AS conn_type");
          $this->db->select("usc.eConnectionResult AS conn_result");
          $this->db->where_in("iConnectionUserId", $input_params['connection_user_id']);
          $this->db->where_in("iUserId", $input_params['user_id']);
          
          $review_data=$this->db->get()->result_array();
          
          if(true == empty($review_data)){
            $return_arr['checkconnectionexist']['0']['message']="No connection available";
            $return_arr['checkconnectionexist']['0']['status'] = "0";
            return  $return_arr;   
          }else{
            $return_arr['connection_id']=$review_data[0]['conn_id'];
            $return_arr['conn_type']=$review_data[0]['conn_type'];
            $return_arr['conn_result']=$review_data[0]['conn_result'];
          } 
      }
      return $return_arr;
    
  }

    public function checkOtherConnectionExist($input_params=array()){

      $return_arr['message']='';
      $return_arr['status']='1';
       if(false == empty($input_params['user_id']))
       {
          $this->db->from("user_connections AS usc");
          $this->db->select("usc.iConnectionId AS conn_id");
          $this->db->select("usc.eConnectionType AS conn_type");
          $this->db->select("usc.eConnectionResult AS conn_result");
          $this->db->where_in("iUserId", $input_params['connection_user_id']);
          $this->db->where_in("iConnectionUserId", $input_params['user_id']);
          $review_data=$this->db->get()->result_array();
          
          if(true == empty($review_data)){
            $return_arr['checkotherconnectionexist']['0']['message']="No connection available";
            $return_arr['checkotherconnectionexist']['0']['status'] = "0";
            return  $return_arr;   
          }else{
            $return_arr['old_connection_type']=$review_data[0]['conn_type'];
            $return_arr['old_connection_result']=$review_data[0]['conn_result'];
            $return_arr['connection_id']=$review_data[0]['conn_id'];
          } 
      }
      return $return_arr;
  }

  public function checkBlockExist($input_params=array()){

      $return_arr['message']='';
      $return_arr['status']='1';
       if(false == empty($input_params['user_id']))
       {
          $this->db->from("user_block AS usb");
          $this->db->select("usb.iBlockId AS block_id");
          $this->db->where_in("iUserId", $input_params['user_id']);
          $this->db->where_in("iConnectionUserId", $input_params['connection_user_id']);
          $review_data=$this->db->get()->result_array();
          
          if(true == empty($review_data)){
            $return_arr['checkblockexist']['0']['message']="No connection available";
            $return_arr['checkblockexist']['0']['status'] = "0";
            return  $return_arr;   
          }else{
            $return_arr['block_id']=$review_data[0]['block_id'];
          } 
      }
      return $return_arr;
  }

  public function PrepareHelperMessage($input_params=array()){
    // print_r($input_params);exit;
    $this->db->select('nt.tMessage');
    $this->db->from('mod_push_notify_template as nt');
    $strConnectionType = (isset($input_params['connection_result']) && $input_params['connection_result'] == 'Match') ?  'matched_user':'like_user' ;
    $this->db->where('nt.vTemplateCode',$strConnectionType);
    $notification_text=$this->db->get()->result_array();
    $notification_text=$notification_text[0]['tMessage'];

    $notification_text = str_replace("|sender_name|",ucfirst($input_params['get_user_details_for_send_notifi'][0]['s_name']), $notification_text);
    $return_array['notification_message']=$notification_text;
   // print_r($return_array);exit;
    return $return_array;
    
}

}
?>

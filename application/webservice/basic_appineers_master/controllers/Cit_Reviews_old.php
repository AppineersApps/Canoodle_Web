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
 
Class Cit_Reviews extends Reviews {
  public function __construct()
  {
      parent::__construct();
  }
  public function checkReviewExist($input_params=array()){
      $return_arr['message']='';
     	$return_arr['status']='1';
     	 if(false == empty($input_params['review_id']) && is_array($input_params['review_id']))
     	 {
          $this->db->select('iReviewId');
          $this->db->from('review');
          //print_r($input_params['review_id']); exit;
          $this->db->where_in("iReviewId", $input_params['review_id']);
          $review_data=$this->db->get()->result_array();
          if(true == empty($review_data)){
             $return_arr['message']="No reviews available";
             $return_arr['status'] = "0";
             return  $return_arr;
          }else{
          	$return_arr['review_id']=$review_data;
          }
      }
      return $return_arr;
    
  }
}
?>

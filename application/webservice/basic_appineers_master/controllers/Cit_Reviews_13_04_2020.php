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
      //print_r($input_params); exit;
     	 if(false == empty($input_params['review_id']))
     	 {
            $this->db->from("review AS r");
           // $this->db->select("r.iReviewId AS review_id");
            $this->db->select("r.vFirstName AS first_name");
            $this->db->select("r.vLastName AS last_name");
            $this->db->select("r.vMobileNo AS mobile_number");            
            $this->db->select("r.vEmail AS email"); 
            $this->db->select("r.vProfileImage AS profile_image");            
            $this->db->select("r.iStateId AS state"); 
            $this->db->select("r.vPlaceId AS place_id");
            $this->db->select("r.dLatitude AS latitude");
            $this->db->select("r.dLongitude AS longitude");
            $this->db->select("r.vCity AS city");
            $this->db->select("r.vZipCode AS zipcode");
            $this->db->select("r.tAddress AS street_address");
            $this->db->select("r.vBussinessName AS business_name");
            $this->db->select("r.vReviewType AS review_type");
            $this->db->select("r.vPosition AS position");
            $this->db->select("r.iBussinessType AS business_typeid");
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
      foreach ($return_arr as $value) {
        $return_arr = $value;
        $return_arr['status']='1';
      }
      return $return_arr;
    
  }
}
?>

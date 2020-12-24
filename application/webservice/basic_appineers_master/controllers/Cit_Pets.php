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
 
Class Cit_Pets extends Pets {
  public function __construct()
  {
      parent::__construct();
  }
  public function checkPetExist($input_params=array()){

        // print_r($input_params);exit;
      $return_arr['message']='';
     	$return_arr['status']='1';
      //print_r($input_params); exit;
     	 if(false == empty($input_params['pet_id']))
     	 {
            $this->db->from("pet AS h");
            $this->db->select("h.iPetId AS pet_id");
            $this->db->where_in("iPetId", $input_params['pet_id']);
            $pet_data=$this->db->get()->result_array();
            // echo $this->db->last_query();exit;
            if(true == empty($pet_data)){
               $return_arr['checkpetexist']['0']['message']="No pet available";
               $return_arr['checkpetexist']['0']['status'] = "0";
               return  $return_arr;
            }else{
            	$return_arr['pet_id']=$pet_data;
            }
        }
        foreach ($return_arr as $value) {
          $return_arr = $value;
          $return_arr['status']='1';
        }
        return $return_arr;
        
      }

      /*public function getProviderForService($input_params = array()){
        $this->db->from("service AS s");
        $this->db->select("s.iServiceProviderId AS provider_id");
        $this->db->where_in("iServiceId", $input_params['service_id']);
        $pet_data=$this->db->get()->result_array();
        return $pet_data;
      }*/

      public function uploadQueryImages($input_params=array()){
        $result_arr = array();
        $user_id=$input_params['user_id'];
        $img_name="profile_pic";
        $pet_id=$input_params['pet_id'];
        $folder_name="canoodle/pet_image/".$pet_id."/";
      
        $return_arr = array();
        $insert_arr = array();
        $temp_var   = 0;
        

          $new_file_name=$img_name;
        
          if($_FILES[$new_file_name]['name']!='')
          {
            $file_name = $_FILES[$new_file_name]['name'];
            $arrExp = explode('.', $file_name);
            $ext = strtolower(end($arrExp));
            $filename = strtolower($arrExp['0']);
            $actual_image_name = $filename. "_" .uniqid() . "." . $ext;
            $temp_file = $_FILES[$new_file_name]['tmp_name'];
            $res = $this->general->uploadAWSData($temp_file, $folder_name, $actual_image_name );
            if($res)
            {
              $insert_arr['vProfileImage'] = $actual_image_name;
              $temp_var++;
            }
          
        }

       if(is_array($insert_arr) && !empty($insert_arr))
        {
          $this->db->where('iPetId', $pet_id);
          $strFinalResult = $this->db->update("pet",$insert_arr);
         // echo $this->db->last_query();exit;
          $affected_rows = $this->db->affected_rows();
          if (!$strFinalResult || $affected_rows == -1)
          {
              throw new Exception("Failure in updation.");
          }
          $result_param = "affected_rows";
          $result_arr[0][$result_param] = $affected_rows;
          $return["success"]  = true;
        }
        //sleep(10);
        return $return;
    }

}

?>

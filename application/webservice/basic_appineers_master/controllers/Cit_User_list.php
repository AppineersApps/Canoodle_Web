<?php

   
/**
 * Description of User Sign Up Email Extended Controller
 * 
 * @module Extended User Sign Up Email
 * 
 * @class Cit_User_sign_up_email.php
 * 
 * @path application\webservice\basic_appineers_master\controllers\Cit_User_sign_up_email.php
 * 
 * @author CIT Dev Team
 * 
 * @date 10.02.2020
 */        

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
Class Cit_User_list extends User_list {
        public function __construct()
{
    parent::__construct();
}
public function checkUniqueUser($input_params=array()){
    $return_arr['message']='';
    $return_arr['status']='1';
    if(!empty($input_params['user_id'])){
      $this->db->select('iUserId');
      $this->db->from('users');
      $this->db->where('iUserId',$input_params['user_id']);
      $mobile_number_data=$this->db->get()->result_array();
      if (!is_array($mobile_number_data) || count($mobile_number_data) == 0)
      {
         $return_arr['status'] = "0";
      }
    }
   return  $return_arr; 
    
}

public function prepareDistanceQuery($input_params=array()){

    $this->db->select('dLatitude,dLongitude');
        $this->db->from('users');
        $this->db->where('iUserId', $input_params['user_id']);
        $user_data=$this->db->get()->row_array();

 
      $user_latitude    =   $user_data['dLatitude'];
      $user_longitude   =   $user_data['dLongitude'];
      if(!empty($user_longitude) && !empty($user_latitude))
      {

        $distance = "
            3959 * acos (
              cos ( radians($user_latitude) )
              * cos( radians( u.dLatitude ) )
              * cos( radians( u.dLongitude ) - radians($user_longitude))
              + sin ( radians($user_latitude) )
              * sin( radians( u.dLatitude ))
            )";
        
      }else{
           //distance filter
        $distance= 'IF(1=1,"","")'; 
      }
      
      $return_arr['distance']=$distance;

  
    
      return $return_arr;
}

public function uploadMediaImages($input_params=array()){
    
    $user_id=$input_params['user_id'];
    $img_name="media_file_";
    $folder_name="canoodle/media_images/".$user_id."/";
    //$folder_name="public/upload/media_images/".$user_id."/";
    $return_arr = array();
    $insert_arr = array();
    $temp_var   = 0;
    $upper_limit = 3;
    //echo $input_params['media_file_count']; exit;
    if($input_params['media_file_count'] > 0)
    {
      $upper_limit = $input_params['media_file_count'];
    }else{
      if(false == empty($input_params['media_url'])){
        $insert_arr[$temp_var]['iUserId']=$user_id;
        $insert_arr[$temp_var]['vMediaUrl']=(false == empty($input_params['media_url']))?$input_params['media_url']:'';
        $insert_arr[$temp_var]['vMediaName']=(false == empty($input_params['media_name']))?$input_params['media_name']:'';
        $insert_arr[$temp_var]['dtAddedAt']=date('Y-m-d H:i:s');
        $insert_arr[$temp_var]['eStatus']="Active";

      }
    }
    for($i=1; $i<=$upper_limit; $i++)
    {

    $new_file_name=$img_name.$i;
    //$_FILES[$new_file_name]["ext"] = implode(',', $this->config->item('IMAGE_EXTENSION_ARR'));
    //if(!empty($_FILES[$new_file_name]["name"]) && $this->general->validateFileFormat($_FILES[$new_file_name]["ext"], $_FILES[$new_file_name]["name"]))
     if(!empty($_FILES[$new_file_name]["name"]))
    {
        $file_name= $_FILES[$new_file_name]["name"];
        $arrExp = explode('.', $file_name);
        $ext = strtolower(end($arrExp));
        $filename = strtolower($arrExp['0']);
        $file_name = $filename. "_" .uniqid() . "." . $ext;
      
        $temp_file = $_FILES[$new_file_name]['tmp_name'];
          $res = $this->general->uploadAWSData($temp_file, $folder_name, $file_name );
       
        if($res)
        {          
          $insert_arr[]=array(
            'iUserId'=>$user_id,
            'vMediaFile'=>$file_name,
            'vMediaUrl'=>(false == empty($input_params['media_url']))?$input_params['media_url']:'',
            'vMediaType'=>$_FILES[$new_file_name]['type'],
            'vMediaName'=>(false == empty($input_params['media_name']))?$input_params['media_name']:'',
            'dtAddedAt'=>date('Y-m-d H:i:s'),
            'eStatus'=>"Active"
          );
        }
    
    } 
  }
////////////////
    if($input_params['media_count'] > 0)
    {
       $upper_limit = $input_params['media_count'];
    }
    /* for($i=1; $i<=$upper_limit; $i++)
    {

    $new_file_name=$img_name.$i;
    //$_FILES[$new_file_name]["ext"] = implode(',', $this->config->item('IMAGE_EXTENSION_ARR'));
    //if(!empty($_FILES[$new_file_name]["name"]) && $this->general->validateFileFormat($_FILES[$new_file_name]["ext"], $_FILES[$new_file_name]["name"]))
    if(!empty($_FILES[$new_file_name]["name"]))
    {
        $file_name= $_FILES[$new_file_name]["name"];
        $arrExp = explode('.', $file_name);
        $ext = strtolower(end($arrExp));
        $filename = strtolower($arrExp['0']);
        $file_name = $filename. "_" .uniqid() . "." . $ext;
        $temp_file = $_FILES[$new_file_name]['tmp_name'];
          $res = '';$this->general->uploadAWSData($temp_file, $folder_name, $file_name );
        if($res)
        {          
         $insert_arr[]=array(
         'iUserId'=>$user_id,
            'vMediaFile'=>$file_name,
            'vMediaUrl'=>'',
            'vMediaType'=>$_FILES[$new_file_name]['type'],
            'vMediaName'=>(false == empty($input_params['media_name']))?$input_params['media_name']:'',
           'dtAddedAt'=>date('Y-m-d H:i:s'),
           'eStatus'=>"Active"
         );        
        }
    }
  } */


///////////////////
   if(is_array($insert_arr) && !empty($insert_arr))
    {
      $this->db->insert_batch("user_media",$insert_arr);
    }
  
  
  $return["success"]  = true;
  return $return;
    
}

}

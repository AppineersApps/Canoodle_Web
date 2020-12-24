<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User Sign Up Email Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module User Sign Up Email
 *
 * @class User_sign_up_email.php
 *
 * @path application\webservice\basic_appineers_master\controllers\User_sign_up_email.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 12.02.2020
 */

class Mad_collab_user extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
    public $multiple_keys;
    public $block_result;

    /**
     * __construct method is used to set controller preferences while controller object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            "set_profile",
            "get_users_list_details",
        );
        $this->multiple_keys = array(
            "custom_function",
            "query_images",
            "formatting_images",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->library('flowplayer');
        $this->load->model('mad_collab_user_model');
    }

     /**
     * rules_set_profile method is used to validate api input params.
     * @created priyanka chillakuru | 09.09.2019
     * @modified priyanka chillakuru | 11.12.2019
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_set_profile($request_arr = array())
    {
        $valid_arr = array(            
            
            "user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "user_id_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "set_profile");

        return $valid_res;
    }

    /**
     * rules_user_sign_up_email method is used to validate api input params.
     * @created kavita Sawant | 25-05-2020
     * @modified kavita Sawant | 12.02.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_users_list($request_arr = array())
    {
        if(true==empty($request_arr['other_user_id'])){
            $valid_arr = array(
                
                "location" => array(
                    array(
                        "rule" => "minlength",
                        "value" => 1,
                        "message" => "location_minlength",
                    ),
                    array(
                        "rule" => "maxlength",
                        "value" => 80,
                        "message" => "location_maxlength",
                    )
                ),

                "min_age" => array(
                    array(
                        "rule" => "number",
                        "value" => TRUE,
                        "message" => "min_age_number",
                    )
                ),

                "min_age" => array(
                    array(
                        "rule" => "number",
                        "value" => TRUE,
                        "message" => "min_age_number",
                    )
                )
            );
            }
            else{
                $valid_arr = array(
                    "other_user_id" => array(
                        array(
                            "rule" => "required",
                            "value" => TRUE,
                            "message" => "other_user_id_required",
                        )
                    ),
                );
            }
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_users_list");
        return $valid_res;
    }
    

    /**
     * start_user_sign_up_email method is used to initiate api execution flow.
     * @created kavita Sawant | 25-05-2020
     * @modified kavita Sawant | 12.02.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_mad_collab_user($request_arr = array(), $inner_api = FALSE)
    {

        // get the HTTP method, path and body of the request
        $method = $_SERVER['REQUEST_METHOD'];

        $output_response = array();
        switch ($method) {
          case 'GET':
             $output_response = $this->get_users_list($request_arr);
             return  $output_response;
             break;
        case 'POST':
             $output_response = $this->set_profile($request_arr);
             return  $output_response;
             break;  
         case 'DELETE':
             $output_response = $this->delete_media_images($request_arr);
             return  $output_response;
             break; 
              
        }
    }
    /**
     * set_profile method is used to initiate api execution flow.
     * @created kavita sawant | 02-06-2020
     * @modified priyanka chillakuru | 02-06-2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function set_profile($request_arr = array(), $inner_api = FALSE)
    {



        try
        {
            $validation_res = $this->rules_set_profile($request_arr);
            if ($validation_res["success"] == "-5")
            {
                if ($inner_api === TRUE)
                {
                    return $validation_res;
                }
                else
                {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }            
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

            $condition_res = $this->checkuniqueuser($input_params);

            if($condition_res['status']){
            $input_params = $this->update_profile($input_params);

            $condition_res = $this->is_updated($input_params);
                if ($condition_res["success"])
                {
                    
                   
                   $input_params = $this->custom_function($input_params);
                    $input_params = $this->get_posted_details_v1($input_params);                
                    $input_params = $this->start_loop($input_params);

                    
                    
                    
                    if ($input_params["success"])
                    {                
                        $output_response = $this->user_query_finish_success($input_params);
                        return $output_response;
                    }else{

                    }
                }
                else
                {
                    $output_response = $this->user_query_finish_success_1($input_params);
                    return $output_response;
                }
            }

            else
            {

                $output_response = $this->user_query_finish_success_1($input_params);
                return $output_response;
            }
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }
     /**
     * get_posted_details_v1 method is used to process query block.
     * @created CIT Dev Team
     * @modified saikumar anantham | 10.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_posted_details_v1($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $this->block_result = $this->mad_collab_user_model->get_posted_details_v1($user_id);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_posted_details_v1"] = $this->block_result["data"];
       // print_r($this->block_result["data"]);
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }



      /**
     * post_images method is used to process query block.
     * @created priyanka chillakuru | 30.04.2019
     * @modified priyanka chillakuru | 30.04.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function post_videos($input_params = array())
    {

        $this->block_result = array();
        try
        {

             $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $this->block_result = $this->mad_collab_user_model->post_video_v1($user_id);
            //print_r( $this->block_result);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            //print_r($result_arr);
            if (is_array($result_arr) && count($result_arr) > 0)
            {
              //  $input_params['user_id']
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                {
                    
                    
               //     if(trim($data_arr["video_thumbnail"])!=""){
                    
                if(false == empty($data_arr["media_file"])){
                    
                    
                    
                    $data = $data_arr["media_file"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $p_key = $input_params["user_id"];
                    $image_arr["pk"] = $p_key;
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $image_arr["path"] = "canoodle/media_images";
                    // $image_arr["path"] = $this->general->getImageNestedFolders("golden_opportunity/post_image_names");
                    $data = $this->general->get_image_aws($image_arr);
                    $result_arr[$data_key]["video_url"] = $data;

                    //$video_thumb = $data_arr["video_thumb"]; user_id
 
                    $arrExp = explode('.', $data_arr["media_file"]);
                    $ext = strtolower(end($arrExp));
                    if($ext == 'mp4'){
                    $arrFile=$this->flowplayer->getThumnails($data,500,500);
                   
                    
                     $folder_name="canoodle/media_images/".$p_key."/";
                    
                    $res = $this->general->uploadAWSData($arrFile['temppath'],$folder_name, $arrFile['filename'] );
                    
                    if(false== empty($arrFile['filename'])){
                      $post_media_id=$data_arr["media_id"];
                     
                      if(false == empty($post_media_id)){
                         $arrUpdate= $this->mad_collab_user_model->update_thumbnail_image($post_media_id, $arrFile['filename']);
                         if($arrUpdate['success']==1){
                            $thumb_image_arr = array();
                            $thumb_image_arr["image_name"] = $arrFile['filename'];
                            $thumb_image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                            $p_key = ($data_arr["user_id"] != "") ? $data_arr["user_id"] : $input_params["user_id"];
                            $thumb_image_arr["pk"] = $p_key;
                            $thumb_image_arr["color"] = "FFFFFF";
                            $thumb_image_arr["path"] = "chinwag/post_image_names";
                            // $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                            $data_arr["pm_video_thumbnail"] = $this->general->get_image_aws($thumb_image_arr);
                        }
                      }
                   
                    }
                    }
                    
                    
                    
                  //  }
                }                   

                    $i++;
                }
                $this->block_result["data"] = $result_arr;
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["post_videos"] = $this->block_result["data"];

        return $input_params;
    }

      /**
     * start_loop method is used to process loop flow.
     * @created priyanka chillakuru | 30.04.2019
     * @modified priyanka chillakuru | 30.04.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function start_loop($input_params = array())
    {
        $this->iterate_start_loop($input_params["get_posted_details_v1"], $input_params);
        return $input_params;
    }
     /**
     * iterate_start_loop method is used to iterate loop.
     * @created priyanka chillakuru | 30.04.2019
     * @modified priyanka chillakuru | 30.04.2019
     * @param array $get_posted_details_v1_lp_arr get_posted_details_v1_lp_arr array to iterate loop.
     * @param array $input_params_addr $input_params_addr array to address original input params.
     */
    public function iterate_start_loop(&$get_posted_details_v1_lp_arr = array(), &$input_params_addr = array())
    {

        $input_params_loc = $input_params_addr;
        $_loop_params_loc = $get_posted_details_v1_lp_arr;
        $_lp_ini = 0;
        $_lp_end = count($_loop_params_loc);
        for ($i = $_lp_ini; $i < $_lp_end; $i += 1)
        {
            $get_posted_details_v1_lp_pms = $input_params_loc;

            unset($get_posted_details_v1_lp_pms["get_posted_details_v1"]);
            if (is_array($_loop_params_loc[$i]))
            {
                $get_posted_details_v1_lp_pms = $_loop_params_loc[$i]+$input_params_loc;
            }
            else
            {
                $get_posted_details_v1_lp_pms["get_posted_details_v1"] = $_loop_params_loc[$i];
                $_loop_params_loc[$i] = array();
                $_loop_params_loc[$i]["get_posted_details_v1"] = $get_posted_details_v1_lp_pms["get_posted_details_v1"];
            }

            $get_posted_details_v1_lp_pms["i"] = $i;
            $input_params = $get_posted_details_v1_lp_pms;

            //$input_params = $this->post_images($input_params);
            $input_params = $this->post_videos($input_params);

            $get_posted_details_v1_lp_arr[$i] = $this->wsresponse->filterLoopParams($input_params, $_loop_params_loc[$i], $get_posted_details_v1_lp_pms);
        }
    }

     /**
     * update_profile method is used to process query block.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 11.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_profile($input_params = array())
    {

        $this->block_result = array();
        try
        {
            $params_arr = array();
            $params_arr["_dtupdatedat"] = "NOW()";
            $params_arr["_estatus"] = "Active";
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["description"]))
            {
                $params_arr["description"] = $input_params["description"];
            }  
               
            $this->block_result = $this->mad_collab_user_model->set_profile($params_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["set_profile"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }
	public function get_users_list($request_arr = array(), $inner_api = FALSE)
   { 
		try
		{
            //http://18.211.58.235/mad_collab/WS/mad_collab_user?gender_type=All
			$validation_res = $this->rules_get_users_list($request_arr);
			if ($validation_res["success"] == "-5")
			{
				if ($inner_api === TRUE)
				{
					return $validation_res;
				}
				else
				{
					$this->wsresponse->sendValidationResponse($validation_res);
				}
			}
			$output_response = array();
			$input_params = $validation_res['input_params'];
			$output_array = $func_array = array();

			$input_params = $this->get_users_list_details($input_params);
			$condition_res = $this->is_posted($input_params);
			if ($condition_res["success"])
			{
				
				$output_response = $this->users_finish_success($input_params);
				return $output_response;
			}

			else
			{

				$output_response = $this->users_finish_success_1($input_params);
			
				return $output_response;
			}
		}
		catch(Exception $e)
		{
			$message = $e->getMessage();
		}
		return $output_response;
}
 
    /**
     * get_users_list method is used to process query block.
     * @created kavita sawant | 27-05-2020
     * @modified kavita sawant  | 01.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_users_list_details($input_params = array())
    {

        $this->block_result = array();
        try
        {
			$arrParams=array();
            $arrParams['user_id'] = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
			$arrParams['gender_type'] = isset($input_params["gender_type"]) ? $input_params["gender_type"] : "";
			$arrParams['city'] = isset($input_params["city"]) ? $input_params["city"] : "";
            $arrParams['state'] = isset($input_params["state"]) ? $input_params["state"] : "";
			$arrParams['min_age'] = isset($input_params["min_age"]) ? $input_params["min_age"] : "";
			$arrParams['max_age'] = isset($input_params["max_age"]) ? $input_params["max_age"] : "";
            $arrParams['other_user_id'] = isset($input_params["other_user_id"]) ? $input_params["other_user_id"] : "";
            $arrParams['breed_list'] = isset($input_params["breed_list"]) ? $input_params["breed_list"] : "";
            $this->block_result = $this->mad_collab_user_model->get_users_list_details($arrParams);
			
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            //print_r($result_arr);exit;
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $arrBreed =array();
				$arrMedia =array();
                
                foreach ($result_arr as $data_key => $data_arr)
                {
                    if(false == empty($data_arr["user_id"])){
                    $strConnectionType  ='';
                    #echo $data_arr["user_id"]; exit;
                    if($data_arr["user_id"] == $arrParams['user_id']){
                        $data_arr["user_id"] = $arrParams['other_user_id'];
                    }
					$arrConnectionType = $this->get_users_connection_details($arrParams['user_id'],$data_arr["user_id"],$arrParams['other_user_id']);
                   // print_r($arrConnectionType);
                   // $arrConnectionType = $this->get_users_connection_details('8','24',$arrParams['other_user_id']);
                  

                    if(false == empty($arrConnectionType['0']['connection_type'])){
                        $strConnectionType =$arrConnectionType['0']['connection_type'];
                        $result_arr[$data_key]["connection_type_by_receiver_user"] =  $strConnectionType ;
                    }else{
                        $result_arr[$data_key]["connection_type_by_receiver_user"] =  '' ;
                    }
                     if(false == empty($arrConnectionType['0']['connection_type_by_logged_user'])){
                        $strConnectionType =$arrConnectionType['0']['connection_type_by_logged_user'];
                        $result_arr[$data_key]["connection_type_by_logged_user"] =  $strConnectionType ;
                    }else{
                        $result_arr[$data_key]["connection_type_by_logged_user"] =  '';
                    }
                     if(false == empty($arrConnectionType['0']['connection_type_by_receiver_user'])){
                        $strConnectionType =$arrConnectionType['0']['connection_type_by_receiver_user'];
                        $result_arr[$data_key]["connection_type_by_receiver_user"] =  $strConnectionType ;
                    }

//print_r($data_arr);exit;
                    
                    $arrBreedlist = explode(',',$data_arr["breed_name"]);
                    $result_arr[$data_key]["breeds_name"] = $arrBreedlist;
					$arrMedia = $this->get_query_details($data_arr["media_id"]);
                  
					foreach ($arrMedia as $arrMedia_key => $arrMedia_val)
					{
						
                        $data = $arrMedia_val["media_file"];
                        $image_arr = array();
                        $image_arr["image_name"] = $data;
                        $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                        $p_key = ($arrMedia_val["m_user_id"] != "") ? $arrMedia_val["m_user_id"] : $input_params["user_id"];
                        $image_arr["pk"] = $p_key;
                        $image_arr["color"] = "FFFFFF";
                        $image_arr["no_img"] = FALSE;
                        $image_arr["path"] = "canoodle/media_images";
                       // $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                       $data = $this->general->get_image_aws($image_arr);




//////////////////
                        $data_im="";
                        if($arrMedia_val["video_thumbnail"]!=""){
                            
                            $data_im = $arrMedia_val["video_thumbnail"];
                            $image_arr_vi = array();
                            $image_arr_vi["image_name"] = $data_im;
                            $image_arr_vi["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                            $p_key = ($arrMedia_val["m_user_id"] != "") ? $arrMedia_val["m_user_id"] : $input_params["user_id"];
                            $image_arr_vi["pk"] = $p_key;
                            $image_arr_vi["color"] = "FFFFFF";
                            $image_arr_vi["no_img"] = FALSE;
                            $image_arr_vi["path"] = "canoodle/media_images";
                           // $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                           $data_im = $this->general->get_image_aws($image_arr_vi);
                        }
//////////////



                        $result_arr[$data_key]['media'][$arrMedia_key]["media_images"] = $data;
                        $result_arr[$data_key]['media'][$arrMedia_key]["media_id"] = $arrMedia_val["media_id"];
                        $result_arr[$data_key]['media'][$arrMedia_key]["media_name"] = $arrMedia_val["media_name"];
                        $result_arr[$data_key]['media'][$arrMedia_key]["m_user_id"] = $arrMedia_val["m_user_id"];
                        $result_arr[$data_key]['media'][$arrMedia_key]["media_type"] = $arrMedia_val["media_type"];
                        $result_arr[$data_key]['media'][$arrMedia_key]["media_url"] = $arrMedia_val["media_url"];
                        
                        
                        
                        
                        $result_arr[$data_key]['media'][$arrMedia_key]["video_thumbnail"] = $data_im;
					}
					
                    $data = $data_arr["user_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $dest_path = "canoodle/user_profile";
                    $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                     $data = $this->general->get_image_aws($image_arr);

                    $result_arr[$data_key]["user_image"] = $data;
                    }
                }
				//print_r($result_arr);exit;
                $this->block_result["data"] = $result_arr;
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_users_list_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

     /**
     * get_users_list method is used to process query block.
     * @created kavita sawant | 27-05-2020
     * @modified kavita sawant  | 01.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_users_connection_details($user_id = '',$connection_id='',$other_user_id='')
    {

        $this->block_result = array();
        try
        {
            
            $this->block_result = $this->mad_collab_user_model->get_users_connection_details($user_id,$connection_id,$other_user_id);
            
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        return $result_arr;
    }
	 /**
     * is_posted method is used to process conditions.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_posted($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["user_id"];
            $cc_ro_0 = 0;

            $cc_fr_0 = ($cc_lo_0 > $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0)
            {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }
     /**
     * is_updated method is used to process conditions.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_updated($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["affected_rows"];
            $cc_ro_0 = 0;

            $cc_fr_0 = ($cc_lo_0 > $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0)
            {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }
	
	/**
     * get_query_details method is used to process query block.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 11.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_query_details($media_id = array())
    {
        $this->block_result = array();
        try
        {

            $arrMediaId = isset($media_id) ? $media_id : "";
           if(is_array($media_id)){
             $media_id = implode(",",$arrMediaId);
           }else{
            $media_id =  $arrMediaId;
           }
	
            $this->block_result = $this->mad_collab_user_model->media_images($media_id);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            /* if (is_array($result_arr) && count($result_arr) > 0)
            {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                {

                    $data = $data_arr["uq_note"];
                    if (method_exists($this, "get_Limit_characters_feedback"))
                    {
                        $data = $this->get_Limit_characters_feedback($data, $result_arr[$data_key], $i, $input_params);
                    }
                    $result_arr[$data_key]["uq_note"] = $data;

                    $i++;
                }
                 $this->block_result["data"] = $result_arr;
            }*/
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
		//print_r($result_arr);exit;
        //$input_params["get_query_details"] = $result_arr;
        //$input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $result_arr;
    }
     /**
     * checkuniqueusername method is used to process custom function.
     * @created priyanka chillakuru | 25.09.2019
     * @modified saikumar anantham | 08.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function checkuniqueuser($input_params = array())
    {
        if (!method_exists($this, "checkUniqueUser"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkUniqueUser($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkuniqueuser"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }
     /**
     * custom_function method is used to process custom function.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 31.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function($input_params = array())
    {
        if (!method_exists($this, "uploadMediaImages"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->uploadMediaImages($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

     /**
     * delete_media_images method is used to initiate api execution flow.
     * @created aditi billore | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function delete_media_images($request_arr = array())
    {
      try
        {
           
            $output_response = array();
            $output_array = $func_array = array();
            $input_params = $request_arr;
		
            $input_params = $this->get_query_details($input_params['media_id']);

            if (false == empty($input_params))
            {

               $input_params = $this->delete_media($input_params);

               if ($input_params["affected_rows"])
                {
                    $output_response = $this->delete_media_finish_success($input_params);
                        return $output_response;
                }else{
                    $output_response = $this->delete_media_finish_success_1($input_params);
                    return $output_response;
                }              
            }
            else
            {
                $output_response = $this->delete_media_finish_success_1($input_params);
                return $output_response;
            }
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }
    /**
     * delete review method is used to process review block.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_media($input_params = array())
    {
     //print_r($input_params);exit;
      $this->block_result = array();
     
        try
        {
            $arrResult = array();
            foreach($input_params as $value){
                 $arrResult['media_id'] =$value['media_id'];
                 $this->block_result = $this->mad_collab_user_model->delete_media($arrResult);

                    $data = $value["media_file"];
                    $folder_name="public/upload/media_images/".$value['m_user_id']."/".$data;
                    $image_arr["path"] = $this->general->getImageNestedFolders($folder_name);
                    $data = unlink($image_arr["path"]);                    
            }
            $result_arr = $this->block_result["data"];
           
          $this->block_result["data"] = $result_arr;
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_item"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;

    }
	 
	 /**
     * users_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified kavita Sawant | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function user_query_finish_success($input_params = array())
    {
       
        $setting_fields = array(
            "success" => "1",
            "message" => "Set profile successfully",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_users_list_details";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
	
	 /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function user_query_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "No data found",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_users_list_details";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
     /**
     * users_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified kavita Sawant | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success($input_params = array())
    {
       
        $setting_fields = array(
            "success" => "1",
            "message" => "get_users_list_details",
        );
        $output_fields = array(
            'user_id',
            'user_name',
            'user_image',
            'description',
            'city',
            'age',
            'state',
            'connection_type_by_logged_user',
            'connection_type_by_receiver_user',
            'breeds_name',
            'media',
        );
        $output_keys = array(
            'get_users_list_details',
        );

        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_users_list_details";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
    
     /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "No data found",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_users_list_details";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
      /**
     * users_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified kavita Sawant | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_media_finish_success($input_params = array())
    {
       
        $setting_fields = array(
            "success" => "1",
            "message" => "Deleted successfully",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_media";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;


        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
    
     /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_media_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "No data found",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_media";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

  
}

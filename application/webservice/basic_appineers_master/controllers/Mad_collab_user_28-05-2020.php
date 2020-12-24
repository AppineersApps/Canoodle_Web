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
            "get_users_list_details",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('mad_collab_user_model');
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
        $valid_arr = array(
            "gender_type" => array(

                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "gender_type_required",
                )
            ),
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
              
        }
    }
	public function get_users_list($request_arr = array(), $inner_api = FALSE)
   { 
		try
		{
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
			$arrParams['location'] = isset($input_params["location"]) ? $input_params["location"] : "";
			$arrParams['min_age'] = isset($input_params["min_age"]) ? $input_params["min_age"] : "";
			$arrParams['max_age'] = isset($input_params["max_age"]) ? $input_params["max_age"] : "";
            $this->block_result = $this->mad_collab_user_model->get_users_list_details($arrParams);
			
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $arrBreed =array();
				$arrMedia =array();
                foreach ($result_arr as $data_key => $data_arr)
                {
					print_r($data_arr["m_media_id"]);
					$arrMedia = $this->get_query_details($data_arr["m_media_id"]);
					
					foreach ($arrMedia as $arrMedia_key => $arrMedia_val)
					{
						//echo $arrMedia_key;
						$data = $arrMedia_val["m_media_name"];
						$image_arr = array();
						$image_arr["image_name"] = $data;
						$image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
						$image_arr["color"] = "FFFFFF";
						$image_arr["no_img"] = FALSE;
						$dest_path = "media_images";
						$image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
						$data = $this->general->get_image($image_arr);
						$result_arr[$data_key]['media'][$arrMedia_key]["m_media_name"] = $data;
						$result_arr[$data_key]['media'][$arrMedia_key]["m_user_id"] = $arrMedia_val["m_user_id"];
						$result_arr[$data_key]['media'][$arrMedia_key]["m_media_type"] = $arrMedia_val["m_media_type"];
						$result_arr[$data_key]['media'][$arrMedia_key]["m_media_url"] = $arrMedia_val["m_media_url"];
					}
					
                    $data = $data_arr["u_profile_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $dest_path = "user_profile";
                    $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                    $data = $this->general->get_image($image_arr);

                    $result_arr[$data_key]["u_profile_image"] = $data;
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

            $cc_lo_0 = $input_params["u_user_id"];
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

            $media_id = isset($media_id) ? $media_id : "";
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
            'u_user_id',
            'u_first_name',
            'u_last_name',
            'u_profile_image',
            'u_age',
            'i_breeds_name',
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

  
}

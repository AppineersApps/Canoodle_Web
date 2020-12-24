<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Post a Feedback Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module Set store review
 *
 * @class set_store_review.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Set_store_review.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Pets extends Cit_Controller
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
            "set_pet",
            "get_pet_details",
        );

        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('pets_model');
        
    }


    /**
     * start_set_store_review method is used to initiate api execution flow.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_pets($request_arr = array(), $inner_api = FALSE)
    {
        // get the HTTP method, path and body of the request
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();

        switch ($method) {
          /*case 'GET':
           if(isset($request_arr['offer_id'])){
            $output_response =  $this->get_pet_by_offerid($request_arr);     

           }else{

            $output_response =  $this->get_pet($request_arr);  
            }   
            return  $output_response;
             break;*/
          case 'POST':
            if(isset($request_arr['pet_id'])){
                $output_response =  $this->update_pet($request_arr);
            }
            else{
                $output_response =  $this->add_pet($request_arr);
            }
            return  $output_response;
            break;

           /* case 'DELETE':
            $output_response = $this->get_deleted_pet($request_arr);
            return  $output_response;
            break;*/
        }
    }


    /**
     * rules_set_store_review method is used to validate api input params.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_add_pet($request_arr = array())
    {       
        $valid_arr = array(
            "pet_name" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "pet_name_required",
                )
            ),
            "breed" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "breed_required",
                )
            ),
            );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "add_pet");

        return $valid_res;
    }

    public function add_pet($input_params){

        try
        {
        
            $validation_res = $this->rules_add_pet($input_params);
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
            // $input_params = $validation_res['input_params'];
            
            

            $output_response = array();
           
            $output_array = $func_array = array();

            $input_params = $this->set_pet($input_params);

            $condition_res = $this->is_posted($input_params);

            if ($condition_res["success"])
            {
                $input_params = $this->custom_image_function($input_params);
                $output_response = $this->user_pet_finish_success($input_params);
                return $output_response;
            }

            else
            {

                $output_response = $this->user_pet_finish_success_1($input_params);
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
     * custom_function method is used to process custom function.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 31.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_image_function($input_params = array())
    {
       if (!method_exists($this, "uploadQueryImages"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->uploadQueryImages($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_image_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

    /**
     * set_store_review method is used to process review block.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function set_pet($input_params = array())
    {
        $this->block_result = array();
        try
        {
            $params_arr = array();
            
            /*if (isset($input_params["added_date"]))
            {
                $params_arr["_dtaddedat"] = $input_params["added_date"];
            }else{
               $params_arr["_dtaddedat"] = "NOW()"; 
            }
            $params_arr["_estatus"] = "Active";*/
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["pet_name"]))
            {
                $params_arr["pet_name"] = $input_params["pet_name"];
            }
            if (isset($input_params["breed"]))
            {
                $params_arr["breed"] = $input_params["breed"];
            }
	    if (isset($input_params["pet_age"]))
            {
                $params_arr["pet_age"] = $input_params["pet_age"];
            }

            if (isset($input_params["pet_category"]))
            {
                $params_arr["pet_category"] = $input_params["pet_category"];
            }

            if (isset($input_params["pet_description"]))
            {
                $params_arr["pet_description"] = $input_params["pet_description"];
            }

            if (isset($input_params["pet_akc_registered"]))
            {
                $params_arr["pet_akc_registered"] = $input_params["pet_akc_registered"];
            }
 	         
            $this->block_result = $this->pets_model->set_pet($params_arr);

            if (!$this->block_result["success"])
            {
                throw new Exception("Insertion failed.");
            }
            
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["set_pet"] = $this->block_result["data"];
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
            $cc_lo_0 = (is_array($input_params["pet_id"])) ? count($input_params["pet_id"]):$input_params["pet_id"];
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
     * is_posted method is used to process conditions.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_fetched($input_params = array())
    {
        $this->block_result = array();
        try
        {
            $cc_lo_0 = $input_params["pet_id"];
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
     * user_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function user_pet_finish_success($input_params = array())
    {
        $output_arr['settings']['success'] = "1";
        $output_arr['settings']['message'] = "Pet added successfully";
        // $output_arr['data'] = "";
        $responce_arr = $this->wsresponse->sendWSResponse($output_arr, array(), "add_pet");

        return $responce_arr;
    }

    /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function user_pet_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "user_pet_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "add_pet";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }




     /**
     * rules_set_store_review method is used to validate api input params.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_update_pet($request_arr = array())
    {
        
         $valid_arr = array(            
            "pet_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "pet_id_required",
                )
            ),
            "pet_name" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "pet_name_required",
                )
            ),
            "breed" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "breed_required",
                )
            )
            );
        
        
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "update_pet");

        return $valid_res;
    }
    /**
     * rules_set_store_review method is used to validate api input params.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
   /* public function rules_get_pet($request_arr = array())
    {
        
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "get_pet");

        return $valid_res;
    }




    public function get_pet_by_offerid($request_arr = array(), $inner_api = FALSE)
    {
       try
        {
            $validation_res = $this->rules_get_pet($request_arr);
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
           
            $result_params = $this->get_all_pet_offer($input_params);
            // print_r($result_params); exit; 
            $condition_res = $this->is_posted($result_params);
            if ($condition_res["success"])
            {
               
                $output_response = $this->get_pet_finish_success($input_params,$result_params);
                return $output_response;
            }

            else
            {
 
                $output_response = $this->get_pet_finish_success_1($result_params);
                return $output_response;
            }
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    *
     * start_set_store_review method is used to initiate api execution flow.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     
    public function get_pet($request_arr = array(), $inner_api = FALSE)
    {
       try
        {
            $validation_res = $this->rules_get_pet($request_arr);
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
           
            $result_params = $this->get_all_pet($input_params);
             //print_r($result_params); exit; 
            $condition_res = $this->is_posted($result_params);
            if ($condition_res["success"])
            {
               
                $output_response = $this->get_pet_finish_success($input_params,$result_params);
                return $output_response;
            }

            else
            {
 
                $output_response = $this->get_pet_finish_success_1($result_params);
                return $output_response;
            }
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }
*/
     /**
     * start_edit_profile method is used to initiate api execution flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 23.12.2019
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function update_pet($request_arr = array(), $inner_api = FALSE)
    {

        try
        {
            $validation_res = $this->rules_update_pet($request_arr);
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

            $input_params = $this->check_pet_exist($input_params);
            if ($input_params["checkpetexist"]["status"])
            {



                if(false == empty($input_params['profile_pic'])){
                    $input_params = $this->get_pet_image($input_params);
                    $input_params = $this->custom_image_function($input_params);
                    $input_params = $this->get_image_details($input_params); 
                     
                }


                $input_params = $this->update_exist_pet($input_params);
                if ($input_params["affected_rows"])
                {
                    $output_response = $this->get_update_finish_success($input_params);
                    return $output_response;
                    
                }else{
                    $output_response = $this->get_update_finish_success_1($input_params);
                    return $output_response;
                }
            }
            else
            {

                $output_response = $this->get_update_finish_success_1($input_params);
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
     * get_updated_details method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 23.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_image_details($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $arrResult['pet_id'] = isset($input_params["pet_id"]) ? $input_params["pet_id"] : "";
            $this->block_result = $this->pets_model->get_pet_details($arrResult);

            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            
            $arrImageArray =array();
            if (is_array($result_arr) && count($result_arr) > 0)
            {
             
                $data_1 = $data_arr["pet_image"];
                $arrImageArray[$data_key]["pet_image"] = (false == empty($data_1))?$data_1:'';
         
                $this->block_result["data"] = $arrImageArray;
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_image_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }


     /**
     * get_updated_details method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 23.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_pet_image($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $arrResult['pet_id'] = isset($input_params["pet_id"]) ? $input_params["pet_id"] : "";
            $this->block_result = $this->pets_model->get_pet_details($arrResult);

            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            //print_r( $result_arr); exit;
                $data =array();
                $folder_name="canoodle/pet_image/".$arrResult['pet_id']."/";
                $insert_arr = array();
                $new_file_name="pet_image";
                  if(false == empty($_FILES[$new_file_name]['name']))
                  {
                    if(false == empty($result_arr['0'][$new_file_name]))
                    {
                        $file_name = $result_arr['0'][$new_file_name];
                        $res = $this->general->deleteAWSFileData($folder_name,$file_name);
                        if($res)
                        {
                          $insert_arr['vProfileImage'] = '';
                        }
                    }
                  
                  }                       
                
                if(is_array($insert_arr) && false == empty($insert_arr))
                {
                  $this->db->where('iPetId', $arrResult['pet_id']);
                  $this->db->update("pet",$insert_arr);
                }
                $this->block_result["data"] = $result_arr;
            
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        //$input_params["get_updated_details"] = $this->block_result["data"];
        //$input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }



     /**
     * update_profile method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_exist_pet($input_params = array())
    {
        $this->block_result = array();
        try
        {

            $params_arr = array();

            if (isset($input_params["pet_id"]))
            {
                 $where_arr["pet_id"] = $input_params["pet_id"];
            }

            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
           
            
           /* $params_arr["_dtupdatedat"] = "NOW()";            
            $params_arr["_estatus"] = "Active";*/
            
            
            if (isset($input_params["pet_name"]))
            {
                $params_arr["pet_name"] = $input_params["pet_name"];
            }
            
            if (isset($input_params["breed"]))
            {
                $params_arr["breed"] = $input_params["breed"];
            }

            if (isset($input_params["pet_age"]))
            {
                $params_arr["pet_age"] = $input_params["pet_age"];
            }

            if (isset($input_params["pet_category"]))
            {
                $params_arr["pet_category"] = $input_params["pet_category"];
            }

            if (isset($input_params["pet_description"]))
            {
                $params_arr["pet_description"] = $input_params["pet_description"];
            }

            if (isset($input_params["pet_akc_registered"]))
            {
                $params_arr["pet_akc_registered"] = $input_params["pet_akc_registered"];
            }
            

            $this->block_result = $this->pets_model->update_pet($params_arr, $where_arr);
            if (!$this->block_result["success"])
            {
                throw new Exception("updation failed.");
            }

            
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_pet"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }

    /**
     * checkuniqueusername method is used to process custom function.
     * @created priyanka chillakuru | 25.09.2019
     * @modified saikumar anantham | 08.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_pet_exist($input_params = array())
    {

        if (!method_exists($this, "checkPetExist"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkPetExist($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkpetexist"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        // print_r($input_params);
        return $input_params;
    }
    

 /**
     * checkuniqueusername method is used to process custom function.
     * @created priyanka chillakuru | 25.09.2019
     * @modified saikumar anantham | 08.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    /*public function get_provider_for_service($input_params = array())
    {

        if (!method_exists($this, "getProviderForService"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->getProviderForService($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["provider_id"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        // print_r($input_params);
        return $input_params;
    }*/


    /*public function get_all_pet_offer($input_params = array())
    {
        //print_r($input_params); exit;
        $this->block_result = array();
        try
        {
               
            $this->block_result = $this->pets_model->get_pet_details_offer($input_params,$this->settings_params);
        
            $result_arr = $this->block_result["data"];


           if (is_array($result_arr) && count($result_arr) > 0)
            {
                
                foreach ($result_arr as $data_key => $data_arr)
                {
                    $selected = array();
                    $data =array();
                    $result_arr[$data_key]["pet_image"] ="";
                    $data = $data_arr["pet_image"];
                    if(false == empty($data)){
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $p_key = ($data_arr["pet_id"] != "") ? $data_arr["pet_id"] : $input_params["pet_id"];
                    $image_arr["pk"] = $p_key;
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $dest_path = "side_jobs/pet_image";
                    $image_arr["path"] = "side_jobs/pet_image";
                    $data = $this->general->get_image_aws($image_arr);
                    if(false == empty($data)){                    
                      $result_arr[$data_key]["pet_image"] = $data;
                    }
                  }
                     
                }
                    $this->block_result["data"] = $result_arr;
            }

            if (is_array($result_arr) && count($result_arr) > 0)
            {
               
                $this->block_result["data"] = $result_arr;
            }
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
        $input_params["get_all_pet"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;
    }*/


    /**
     * get_review_details method is used to process review block.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    /*public function get_all_pet($input_params = array())
    {
        //print_r($input_params); exit;
        $this->block_result = array();
        try
        {
          
         
               
            if(isset($input_params['service_id'])){

                if(isset($input_params['provider_id']) == FALSE){
                    $input_params = $this->get_provider_for_service($input_params);
                }
            }

          

            $this->block_result = $this->pets_model->get_pet_details($input_params,$this->settings_params);
        
            $result_arr = $this->block_result["data"];


           if (is_array($result_arr) && count($result_arr) > 0)
            {
                
                foreach ($result_arr as $data_key => $data_arr)
                {
                    $selected = array();
                    $data =array();
                    $result_arr[$data_key]["pet_image"] ="";
                    $data = $data_arr["pet_image"];
                     if(false == empty($data)){
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $p_key = ($data_arr["pet_id"] != "") ? $data_arr["pet_id"] : $input_params["pet_id"];
                    $image_arr["pk"] = $p_key;
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $dest_path = "side_jobs/pet_image";
                    $image_arr["path"] = "side_jobs/pet_image";
                    $data = $this->general->get_image_aws($image_arr);
                    if(false == empty($data)){
                      $result_arr[$data_key]["pet_image"] = $data;
                    }
                  }
                     
                }
                    $this->block_result["data"] = $result_arr;
            }

            if (is_array($result_arr) && count($result_arr) > 0)
            {
               
                $this->block_result["data"] = $result_arr;
            }
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
        $input_params["get_all_pet"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;
    }*/

     /**
     * user_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_pet_finish_success($input_params = array(),$result_params = array())
    {
       // print_r($input_params); exit;
        $setting_fields = array(
            "success" => "1",
            "message" => "get_pet_finish_success",
        );
      
        $output_fields = array(
            "pet_id",
            "pet_name",
            "breed",
            //"pet_image",
        );
        $output_keys = array(
            'get_all_pet',
        );
        $ouput_aliases = array(

            "pet_id" => "pet_id",
            "pet_name" => "pet_name",
            "breed" => "breed",
            //"pet_image" => "pet_image",
        );

        $output_array["settings"] = array_merge($this->settings_params, $setting_fields);
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $result_params;
        //print_r($input_params);exit;

        $func_array["function"]["name"] = "get_pet";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
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
    public function get_pet_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "get_pet_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_pet";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

     /**
     * user_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_update_finish_success($input_params = array())
    {
       
        $setting_fields = array(
            "success" => "1",
            "message" => "get_update_finish_success"
        );

        $output_array["settings"] = $setting_fields;
        //print_r($input_params);exit;

        $func_array["function"]["name"] = "update_pet";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
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
    public function get_update_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "get_update_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "update_pet";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

   /* public function get_deleted_pet($request_arr = array())
    {
      try
        {
           
            $output_response = array();
            $output_array = $func_array = array();
            $input_params = $request_arr;

            $condition_res = $this->check_pet_exist($input_params);
           
            if ($condition_res["checkpetexist"]["status"])
            {
         $condition_pet = $this->cheak_pet_in_service($input_params);

                  if(false==empty($condition_pet['cheak_pet_in_service']))
                  {
                    
                  $output_response = $this->delete_pet_finish_success_2($input_params);
                    return $output_response;
                  }
                  else{

                  $input_params = $this->delete_pet($input_params);
                  $output_response = $this->delete_pet_finish_success($input_params);
                    return $output_response;

                }
            }

            else
            {

                $output_response = $this->delete_pet_finish_success_1($input_params);
                return $output_response;
            }
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }

   public function cheak_pet_in_service($input_params = array())
    {
      $this->block_result = array();
        try
        {
            $arrResult = array();
           
            $arrResult['pet_id']  = isset($input_params["pet_id"]) ? $input_params["pet_id"] : "";
            $arrResult['dtUpdatedAt']  = "NOW()";
            
            $this->block_result = $this->pets_model->cheak_pet_in_service($arrResult);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
           
          $this->block_result["data"] = $result_arr;
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["cheak_pet_in_service"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;

       }




    public function delete_pet($input_params = array())
    {
      $this->block_result = array();
        try
        {
            $arrResult = array();
           
            $arrResult['pet_id']  = isset($input_params["pet_id"]) ? $input_params["pet_id"] : "";
            $arrResult['dtUpdatedAt']  = "NOW()";
            
            $this->block_result = $this->pets_model->delete_pet($arrResult);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
           
          $this->block_result["data"] = $result_arr;
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_pet"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;

    }*/

     /**
     * delete_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
   /* public function delete_pet_finish_success($input_params = array())
    {
     $setting_fields = array(
            "success" => "1",
            "message" => "delete_pet_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_pet";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }*/
    /**
     * delete_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
   /* public function delete_pet_finish_success_1($input_params = array())
    {
     $setting_fields = array(
            "success" => "0",
            "message" => "delete_pet_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_pet";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }


    public function delete_pet_finish_success_2($input_params = array())
    {
     $setting_fields = array(
            "success" => "0",
            "message" => "delete_pet_finish_success_2",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_pet";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }*/
   

}

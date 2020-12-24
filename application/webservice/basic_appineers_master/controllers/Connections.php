<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of States List Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module States List
 *
 * @class States_list.php
 *
 * @path application\webservice\basic_appineers_master\controllers\Category_list.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Connections extends Cit_Controller
{
    public $settings_params;
    public $output_params;
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
            "if_blocked",
            "check_chat_intiated_or_not",
            "update_message",
            "get_user_details_for_send_notifi",
            "post_notification",
            "get_sender_image",
            "add_message",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('connections_model');
        $this->load->model('friends/send_message_model');
        $this->load->model("friends/blocked_user_model");
        $this->load->model("comments/messages_model");
        $this->load->model("notifications/notification_model");
        $this->load->model("basic_appineers_master/users_model");
    }

   
     /**
     * start_states_list method is used to initiate api execution flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_connections($request_arr = array(), $inner_api = FALSE)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();
        switch ($method) {
        case 'GET':            
            $output_response =  $this->get_connection_detail($request_arr);
          break;
        case 'POST':
            $output_response =  $this->add_connection_status($request_arr);
            break;
        case 'PUT':
            // $output_response =  $this->update_technique_status($request_arr);
            break;
        }
        return $output_response;

    }

       /**
     * checkuniqueusername method is used to process custom function.
     * @created Aditi billore | 25.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_block_exist($input_params = array())
    {
        if (!method_exists($this, "checkBlockExist"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkBlockExist($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkblockexist"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }


    /**
     * checkuniqueusername method is used to process custom function.
     * @created Aditi billore | 25.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_other_connection_exist($input_params = array())
    {
        if (!method_exists($this, "checkOtherConnectionExist"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkOtherConnectionExist($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkotherconnectionexist"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }


    /**
     * checkuniqueusername method is used to process custom function.
     * @created Aditi billore | 25.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_connection_exist($input_params = array())
    {
        if (!method_exists($this, "checkConnectionExist"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->checkConnectionExist($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["checkconnectionexist"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }




    /**
     * rules_states_list method is used to validate api input params.
     * @created Aditi Billore | 18.09.2019
     * @modified Aditi Billore | 18.09.2019
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_get_connection_detail($request_arr = array())
    {
        $valid_arr = array(
            "connection_type" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "connection_type_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "connections");

        return $valid_res;
    }

    public function get_connection_detail($request_arr = array())
    {
            $validation_res = $this->rules_get_connection_detail($request_arr);
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
         
            $input_params = $this->get_like_count($input_params);
          $input_params = $this->get_likeme_count($input_params); 
            



            $input_params = $this->get_connection_detail_v1($input_params);

            //$input_params = $this->get_like_count($input_params);
            //$input_params = $this->get_likeme_count($input_params);


            $condition_res = $this->condition_connection($input_params);
            if ($condition_res["success"])
            {

                $output_response = $this->mod_connection_finish_success($input_params);
                return $output_response;
            }

            else
            {

                $output_response = $this->mod_connection_finish_success_1($input_params);
                return $output_response;
            }
            
          
        return $output_response;
        
    }




     /**
     * get_business_type_list_v1 method is used to process query block.
     * @created Aditi Billore | 18.09.2019
     * @modified Aditi Billore | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_like_count($input_params = array())
    {
        

        $this->block_result = array();
        try
        {
            $this->block_result = $this->connections_model->get_like_count($input_params);
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
        $input_params["get_like_count"] = $result_arr;
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $input_params["get_like_count"]);



        return $input_params;
    }


    public function get_likeme_count($input_params = array())
    {
        

        $this->block_result = array();
        try
        {
            $this->block_result = $this->connections_model->get_likeme_count($input_params);
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
        $input_params["get_likeme_count"] = $result_arr;
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $input_params["get_likeme_count"]);
        return $input_params;
    }


  
    /**
     * get_business_type_list_v1 method is used to process query block.
     * @created Aditi Billore | 18.09.2019
     * @modified Aditi Billore | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_connection_detail_v1($input_params = array())
    {
        

        $this->block_result = array();
        try
        {
            $this->block_result = $this->connections_model->connection_details($input_params);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }

            $result_arr = $this->block_result["data"];
            //print_r( $result_arr);exit;
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                { 

                    $data = $data_arr["user_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $image_arr["path"] = "canoodle/user_profile";
                   // $image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                    $data = $this->general->get_image_aws($image_arr);

                    $result_arr[$data_key]["user_image"] = $data;

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
        $input_params["get_connection_detail_v1"] = $result_arr;
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $input_params["get_connection_detail_v1"]);



        return $input_params;
    }

    /**
     * condition method is used to process conditions.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function condition_connection($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = (empty($input_params["get_connection_detail_v1"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
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
     * mod_state_finish_success method is used to process finish flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function mod_connection_finish_success($input_params = array())
    {
        $flag = 1;
        
        $setting_fields = array(
            "success" => "1",
            "message" => "Get connection details fetched successfully.",
            "total_connection_count"=> count($input_params["get_connection_detail_v1"]),
            "Like_count"=>count($input_params["get_like_count"]),
             "Likeme_count"=>count($input_params["get_likeme_count"])

        );
        $output_fields = array(
            'user_id',
            'user_name',
            'user_image',
        );
        $output_keys = array(
            'get_connection_detail_v1',

        );
        $ouput_aliases = array(
            "user_id" => "user_id",
            "user_name" => "user_name",
            "user_image" => "user_image",
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;
        // print_r($output_array);
        $func_array["function"]["name"] = "get_connection_detail";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;


        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        return $responce_arr;
    }

    /**
     * mod_state_finish_success_1 method is used to process finish flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function mod_connection_finish_success_1($input_params = array())
    {

         $flag = 1;
        
        $setting_fields = array(
            "success" => "1",
            "message" => "mod_connection_finish_success_1",
            "total_connection_count"=> count($input_params["get_connection_detail_v1"]),
             "Like_count"=>count($input_params["get_like_count"]),
             "Likeme_count"=>count($input_params["get_likeme_count"])

        );
        $output_fields = array(
            'user_id',
            'user_name',
            'user_image',
        );
        $output_keys = array(
            'get_connection_detail_v1',

        );
        $ouput_aliases = array(
            "user_id" => "user_id",
            "user_name" => "user_name",
            "user_image" => "user_image",
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;
        // print_r($output_array);
        $func_array["function"]["name"] = "get_connection_detail";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
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
    public function connection_finish_id_error($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "connection_not_existing_error",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_connection_detail";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }



/**
     * rules_set_store_item method is used to validate api input params.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_add_connection_status($request_arr = array())
    {        
        $valid_arr = array(
            "connection_user_id" => array(
                array(
                    "rule" => "required",
                    "value" => TRUE,
                    "message" => "connection_user_id_required",
                )
            ),
        );
        
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "add_technique_status");

        return $valid_res;
    }


    public function add_connection_status($input){
        // print_r($input);exit;
        try
        {
            
            $validation_res = $this->rules_add_connection_status($input);
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
            $input_params = $validation_res['input_params'];

            $checkConnection_params = $this->check_connection_exist($input_params);
            if ($checkConnection_params["checkconnectionexist"]["status"])
            {


                // print_r($checkConnection_params);exit;
                
                //user can : like- check if reverse connection exists, update the result to match for both if not update the connection status
                // unlike- update the connection type to unlike - done
                // block- check if reverse connection exists, change reverse connection result to blocked, delete current connection

                if($checkConnection_params["connection_type"]=="Unlike"){
                        $output_response = array();
                   
                        $output_array = $func_array = array();
						$checkOtherConnection_params = $this->check_other_connection_exist($input_params);
						 if ($checkOtherConnection_params["checkotherconnectionexist"]["status"] && $checkOtherConnection_params["checkotherconnectionexist"]["old_connection_result"] == 'Match')
						 {
						 	$output_response = $this->connection_update_finish_success_1($input_params);
	                            return $output_response;
	                        
                         }
                        else{
                        	$checkConnection_params["connection_result"] = "Unmatch";
                        	$input_params = $this->update_exist_user_connection_status($checkConnection_params);
	                        
	                        if ($input_params["affected_rows"])
	                        {
	                        
	                          $output_response = $this->connection_update_finish_success($input_params);
	                          return $output_response;
	                        }
	                        else
	                        {
	                            $output_response = $this->connection_update_finish_success_1($input_params);
	                            return $output_response;
	                        }
                        }    
                }//connection type unlike for existing connection
                else if($checkConnection_params["connection_type"]=="Like"){




                   
                    $checkOtherConnection_params = $this->check_other_connection_exist($input_params);
                    if ($checkOtherConnection_params["checkotherconnectionexist"]["status"] &&
                        $checkOtherConnection_params["checkotherconnectionexist"]["old_connection_type"] == "Like" &&
                        $checkOtherConnection_params["checkotherconnectionexist"]["old_connection_result"] == "Unmatch")
                    {



                        //if reverse row exists for like then turn current row and return row to match
                        $output_response = array();
                   
                        $output_array = $func_array = array();
                        //updating current row
                        $checkConnection_params["connection_result"] = "Match";
                        $input_params = $this->update_exist_user_connection_result_status($checkConnection_params);
                        
                        
                        //updating reverse row
                        $checkOtherConnection_params["connection_result"] = "Match";
                        $input_params = $this->update_exist_user_connection_result_status($checkOtherConnection_params);



                        if ($input_params["affected_rows"])
                        {
                        
                           
                           $input_params = $this->get_user_details_for_send_notifi($input_params);

                            $input_params = $this->custom_function($input_params);

                            $input_params = $this->post_notification($input_params);


                            $input_params = $this->get_sender_image($input_params);


                            $condition_res = $this->check_receiver_device_token($input_params);

                            if ($condition_res["success"])
                            {

                                $input_params = $this->push_notification($input_params);

                                $output_response = $this->messages_finish_success_1($input_params);
                                return $output_response;
                            }

                            else
                            {

                                $output_response = $this->connection_update_finish_success($input_params);
                                return $output_response;
                            }
                        }
                        else
                        {
                            $output_response = $this->connection_update_finish_success_1($input_params);
                            return $output_response;
                        }   

                    }//connection type "Like" and reverse row exists

                    else{


                                 $output_response = array();
                           
                                $output_array = $func_array = array();

                                $input_params = $this->update_exist_user_connection_status($checkConnection_params);
                                
                                if ($input_params["affected_rows"])
                                {

                                    
                                   $input_params = $this->get_user_details_for_send_notifi($input_params);



                                    $input_params = $this->custom_function($input_params);

                                    $input_params = $this->post_notification($input_params);

                                    $input_params = $this->get_sender_image($input_params);

                                     


                                    $condition_res = $this->check_receiver_device_token($input_params);

                                    if ($condition_res["success"])
                                    {
                                        
                                         if($input_params['r_premium_status']==1){
                                        $input_params = $this->push_notification($input_params);
                                       }
                                             

                                        $output_response = $this->messages_finish_success_1($input_params);
                                        return $output_response;
                                    }

                                    else
                                    {

                                        $output_response = $this->connection_update_finish_success($input_params);
                                        return $output_response;
                                    }
                                }
                                else
                                {
                                    $output_response = $this->connection_update_finish_success_1($input_params);
                                    return $output_response;
                                }  

                      

                    }//connection type "Like" and reverse row does not exists
                }//connection type "like" with connection exist
                else if($checkConnection_params["connection_type"]=="Block"){
                    //user can block only after match so reverse row will always exist
                    //remove existing connection from user side and add in blocked table
                    //update reverse row with blocked result
                     $checkOtherConnection_params = $this->check_other_connection_exist($input_params);
                    if ($checkOtherConnection_params["checkotherconnectionexist"]["status"] )
                    {
                        $input_params = $this->delete_connection($checkConnection_params);
                        $input_params = $this->set_blocked_status($input_params);
                        if ($input_params["affected_rows"])
                        {
                            $checkOtherConnection_params["connection_type"] = "Like";
                            $checkOtherConnection_params["connection_result"] = "Block";
                            $input_params = $this->update_exist_user_connection_result_status($checkOtherConnection_params);

                            if ($input_params["affected_rows"])
                            {
                            $output_response = $this->delete_connection_finish_success($input_params);
                            return $output_response;
                            }


                            else
                            {
                                $output_response = $this->delete_connection_finish_success_1($input_params);
                                return $output_response;
                            }
                        }
                    }

                }//connection exist and block connection type appeared
                else{
                    $output_response = $this->connection_update_finish_success($input_params);
                          return $output_response;

                }
            }//end of connection exists
            else
            {


                    $checkOtherConnection_params = $this->check_other_connection_exist($input_params);
                    if ($checkOtherConnection_params["checkotherconnectionexist"]["status"])
                    {

                        // for connection type "Like" below task, for connection type "Block" remove block entry from user block table and change existing entry to Unmatch
                        //for connection type unlike..  update the new connection with "unlike" type and unmatch result
                        //if only reverse row exists for like, then add new row for current user and update reverse row with match result
                        //if reverse row exists for like then change current row and return row to match
                        
                        if($input_params["connection_type"]=="Unblock"){
                            //find block id and remove it
                            $checkBlockConnection_params = $this->check_block_exist($input_params);

                            if ($checkBlockConnection_params["checkblockexist"]["status"]){
                                $input_params = $this->delete_block_connection($checkBlockConnection_params);
                            }


                            $checkOtherConnection_params["connection_type"] = "Like";
                            $checkOtherConnection_params["connection_result"] = "Unmatch";
                            //updating reverse row
                            $input_params = $this->update_exist_user_connection_result_status($checkOtherConnection_params);

                            if ($input_params["affected_rows"])
                            {
                            
                              $output_response = $this->connection_update_finish_success($input_params);
                              return $output_response;
                            }
                            else
                            {
                                $output_response = $this->connection_update_finish_success_1($input_params);
                                return $output_response;
                            }   
                        }

                         if($input_params["connection_type"]=="Unlike"){
                            //find block id and remove it
                                $input_params["connection_type"] = "Unlike";
                                $input_params["connection_result"] = "Unmatch";
                                $input_params = $this->set_connection_status($input_params);
                                $condition_res = $this->is_posted($input_params);
                                if ($condition_res["success"])
                                        {

                                
                                            $output_response = $this->messages_finish_success_1($input_params);
                                            return $output_response;
                                        }

                                        else
                                        {

                                            $output_response = $this->connection_update_finish_success($input_params);
                                            return $output_response;
                                        }
                                                                
                         
                            
                        }
                        else if($input_params["connection_type"] == "Like"){

                      
                         $input_params = $this->check_eligibility_of_logedin_user($input_params);
                        $condition_res = $this->condition_2($input_params);

                         if($condition_res["success"]){

                                $output_response = array();
                                
                                $output_array = $func_array = array();
                                //adding row for current user
                                $input_params["connection_type"] = "Like";
                                $input_params["connection_result"] = "Match";
                                $input_params = $this->set_connection_status($input_params);
                                $condition_res = $this->is_posted($input_params);

                                if ($condition_res["success"])
                                {

                                   $input_params = $this->like_count_management($input_params);

                                    $checkOtherConnection_params["connection_type"] = "Like";
                                    $checkOtherConnection_params["connection_result"] = "Match";
                                    //updating reverse row
                                    $input_params = $this->update_exist_user_connection_result_status($checkOtherConnection_params);
                                    
                                    if ($input_params["affected_rows"])
                                    {
                                        $input_params = $this->get_user_details_for_send_notifi($input_params);

                                        $input_params = $this->custom_function($input_params);

                                        $input_params = $this->post_notification($input_params);

                                        $input_params = $this->get_sender_image($input_params);

                                


                                        $condition_res = $this->check_receiver_device_token($input_params);

                                        if ($condition_res["success"])
                                        {

                                            $input_params = $this->push_notification($input_params);

                                            $output_response = $this->messages_finish_success_1($input_params);
                                            return $output_response;
                                        }

                                        else
                                        {

                                            $output_response = $this->connection_update_finish_success($input_params);
                                            return $output_response;
                                        }
                                    }
                                    else
                                    {
                                        $output_response = $this->connection_update_finish_success_1($input_params);
                                        return $output_response;
                                    }   
                                }
                                else
                                {
                                    $output_response = $this->connection_finish_success_1($input_params);
                                    return $output_response;
                                } 

                        }else
                        {
                            $output_response = $this->connection_finish_success_4($input_params);
                            return $output_response;

                        }  

                        }
                      

                    }// if reverse connection exists for any new connection request
                    else{

                    

                          $input_params = $this->check_eligibility_of_logedin_user($input_params);

                          $condition_res = $this->condition_2($input_params);

                          if($condition_res["success"]){

                            

                            $output_response = array();
                           
                            $output_array = $func_array = array();
                            
                            $input_params = $this->set_connection_status($input_params);  
                          
                            

                            $condition_res = $this->is_posted($input_params);


                            if ($condition_res["success"])
                            {
                                
                                if($input_params['connection_type']=="Unlike"){
                                    
                                   $output_response = $this->connection_finish_success($input_params);
                                   return $output_response;
                                }
                                else{


                                    $input_params = $this->get_user_details_for_send_notifi($input_params);

                                    

                                        $input_params = $this->custom_function($input_params);

                                        $input_params = $this->post_notification($input_params);

                                        $input_params = $this->get_sender_image($input_params);

                                        $input_params = $this->like_count_management($input_params);

                                     
                                    

                                        $condition_res = $this->check_receiver_device_token($input_params);


                                        // print_r($condition_res);exit;
                                        if ($condition_res["success"])
                                        {
                                             if($input_params['r_premium_status']==1){
                                            $input_params = $this->push_notification($input_params);
                                         }

                                            $output_response = $this->messages_finish_success_1($input_params);
                                            return $output_response;
                                        }

                                        else
                                        {

                                            $output_response = $this->connection_finish_success($input_params);
                                            return $output_response;
                                        }
                                }
                                   
                            }
                            else
                            {
                                $output_response = $this->connection_finish_success_1($input_params);
                                return $output_response;
                            }

                        }else
                        {
                            $output_response = $this->connection_finish_success_4($input_params);
                    return $output_response;

                        }



                        }//receiving connection request for the first time

            }
        }
        catch(Exception $e)
        {
            $message = $e->getMessage();
        }
        return $output_response;
    }

/**
     * condition_2 method is used to process conditions.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 25.07.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function condition_2($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["u_is_subscribed"];
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;

            $cc_lo_2 = $input_params["connection_type"];
            $cc_ro_2 = 'Unlike';

            $cc_fr_2 = ($cc_lo_2 == $cc_ro_2) ? TRUE : FALSE;

            $cc_lo_1 = $input_params["u_likes_per_day"];
            $cc_ro_1 = 15;

            $cc_fr_1 = ($cc_lo_1 < $cc_ro_1) ? TRUE : FALSE;
            if (!($cc_fr_0 || $cc_fr_1|| $cc_fr_2))
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
     * check_eligibility_of_liking method is used to process query block.
     * @created saikrishna bellamkonda | 25.07.2019
     * @modified saikrishna bellamkonda | 29.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_eligibility_of_logedin_user($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $this->block_result = $this->users_model->check_eligibility_of_logedin_user($user_id);
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
        $input_params["check_eligibility_of_logedin_user"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }
  

  /**
     * set_store_review method is used to process review block.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function set_blocked_status($input_params = array())
    {
        
        $this->block_result = array();
        try
        {
            $params_arr = array();
            if (isset($input_params["timestamp"]))
            {
                $params_arr["_dtaddedat"] = $input_params["timestamp"];
            }else{
               $params_arr["_dtaddedat"] = 'NOW()'; 
            }
            $params_arr["_estatus"] = "Active";
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["connection_user_id"]))
            {
                $params_arr["connection_user_id"] = $input_params["connection_user_id"];
            }
            $this->block_result = $this->connections_model->add_blocked_status($params_arr);

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
        $input_params["add_connection_status"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }


     /**
     * like_count_management method is used to process query block.
     * @created saikrishna bellamkonda | 29.07.2019
     * @modified Devangi Nirmal | 30.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function like_count_management($input_params = array())
    {

        $this->block_result = array();
        try
        {


            $params_arr = $where_arr = array();
            if (isset($input_params["user_id"]))
            {
                $where_arr["u_users_id_1"] = $input_params["user_id"];
            }
            $params_arr["u_likes_per_day"] = "".$input_params["u_likes_per_day"]." +1";
            $this->block_result = $this->users_model->like_count_management($params_arr, $where_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["like_count_management"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }


    /**
     * set_store_review method is used to process review block.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function set_connection_status($input_params = array())
    {
        
        $this->block_result = array();
        try
        {
            $params_arr = array();
            
            if (isset($input_params["timestamp"]))
            {
                $params_arr["_dtaddedat"] = $input_params["timestamp"];
            }else{
               $params_arr["_dtaddedat"] = 'NOW()'; 
            }
            $params_arr["_estatus"] = "Active";
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            if (isset($input_params["connection_user_id"]))
            {
                $params_arr["connection_user_id"] = $input_params["connection_user_id"];
            }
            if (isset($input_params["connection_result"]))
            {
                $params_arr["connection_result"] = $input_params["connection_result"];
            }
            if (isset($input_params["connection_type"]))
            {
                $params_arr["connection_type"] = $input_params["connection_type"];
            }
            
            $this->block_result = $this->connections_model->add_connection_status($params_arr);

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
        $input_params["add_connection_status"] = $this->block_result["data"];
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
            
            $cc_lo_0 = (empty($input_params["add_connection_status"]) ? 0 : 1);
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
            $cc_lo_0 = $input_params["connection_id"];
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
    public function connection_finish_success($input_params = array())
    {
        $output_arr['settings']['success'] = "1";
        $output_arr['settings']['message'] = "Connection added successfully";
        $output_arr['data'] = "";
        $responce_arr = $this->wsresponse->sendWSResponse($output_arr, array(), "add_connection_status");

        return $responce_arr;
    }

    /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function connection_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "connection_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "add_connection_status";
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
    public function connection_finish_success_4($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "connection_finish_success_4",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "add_connection_status";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }


/**
     * update_profile method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_exist_user_connection_result_status($input_params = array())
    {
        $this->block_result = array();
        try
        {
            $params_arr = array();
            
            $where_arr = array();
            if (isset($input_params["timestamp"]))
            {
                $params_arr["_dtupdatedat"] = $input_params["timestamp"];
            }else{
               $params_arr["_dtupdatedat"] = "NOW()"; 
            }
            if (isset($input_params["connection_id"]))
            {
                $where_arr["connection_id"] = $input_params["connection_id"];
            }
            if (isset($input_params["connection_type"]))
            {
                $params_arr["connection_type"] = $input_params["connection_type"];
            }
            if (isset($input_params["connection_result"]))
            {
                $params_arr["connection_result"] = $input_params["connection_result"];
            }

           
            
            $this->block_result = $this->connections_model->update_exist_connection_result_status($params_arr,$where_arr);

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
        $input_params["update_connection_result_status"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        // print_r($input_params);exit;
        return $input_params;
    }



 /**
     * update_profile method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_exist_user_connection_status($input_params = array())
    {
        // print_r($input_params);exit;
        $this->block_result = array();
        try
        {
            $params_arr = array();
            
            $where_arr = array();
            if (isset($input_params["timestamp"]))
            {
                $params_arr["_dtupdatedat"] = $input_params["timestamp"];
            }else{
               $params_arr["_dtupdatedat"] = "NOW()"; 
            }
            if (isset($input_params['checkconnectionexist']["connection_id"]))
            {
                $where_arr["connection_id"] = $input_params['checkconnectionexist']["connection_id"];
            }
            if (isset($input_params["connection_type"]))
            {
                $params_arr["connection_type"] = $input_params["connection_type"];
            }

           
            
            $this->block_result = $this->connections_model->update_exist_connection_status($params_arr,$where_arr);

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
        $input_params["update_connection_status"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        // print_r($input_params);exit;
        return $input_params;
    }

  

     /**
     * user_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function connection_update_finish_success($input_params = array())
    {
        $output_arr['settings']['success'] = "1";
        $output_arr['settings']['message'] = "connection event updated successfully";
        $output_arr['data'] = "";
        $responce_arr = $this->wsresponse->sendWSResponse($output_arr, array(), "update_connection_status");

        return $responce_arr;
    }

    /**
     * user_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function connection_update_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "0",
            "message" => "connection_update_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "update_connection_status";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }


    /**
     * delete review method is used to process review block.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_connection($input_params = array())
    {
      $this->block_result = array();
        try
        {
            $arrResult = array();
           
            $arrResult['connection_id']  = isset($input_params['checkconnectionexist']["connection_id"]) ? $input_params['checkconnectionexist']["connection_id"] : "";
            $arrResult['dtUpdatedAt']  = "NOW()";
            
            $this->block_result = $this->connections_model->delete_connection($arrResult);
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
        $input_params["delete_connection"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;

    }
  

    /**
     * delete review method is used to process review block.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function delete_block_connection($input_params = array())
    {
      $this->block_result = array();
        try
        {
            $arrResult = array();
           
            $arrResult['block_id']  = isset($input_params['checkblockexist']["block_id"]) ? $input_params['checkblockexist']["block_id"] : "";
            $this->block_result = $this->connections_model->delete_block_connection($arrResult);
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
        $input_params["delete_block_connection"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       return $input_params;

    }
  

     /**
     * delete_review_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_connection_finish_success($input_params = array())
    {
     $setting_fields = array(
            "success" => "1",
            "message" => "delete_connection_finish_success"
        );
        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_connection";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }
    /**
     * delete_review_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_connection_finish_success_1($input_params = array())
    {
     $setting_fields = array(
            "success" => "0",
            "message" => "delete_connection_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_connection";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

     /**
     * get_other_user_details_for_send_notifi method is used to process query block.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 27.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_other_user_details_for_send_notifi($input_params = array())
    {
        
        $this->block_result = array();
        try
        {

            $connection_type = isset($input_params["connection_result"]) ? $input_params["connection_result"] : "";
            // if(false ==empty($connection_type) && $connection_type == 'Like'){
                 $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $receiver_id = isset($input_params["connection_user_id"]) ? $input_params["connection_user_id"] : "";

            /*}
            if(false ==empty($connection_type) && $connection_type == 'Match'){
                $user_id = isset($input_params["connection_user_id"]) ? $input_params["connection_user_id"] : "";
                $receiver_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            }*/
            
            $this->block_result = $this->connections_model->get_user_details_for_send_notifi($user_id, $receiver_id,$connection_type);
            //print_r($this->block_result);exit;
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
        $input_params["get_other_user_details_for_send_notifi"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

     /**
     * get_user_details_for_send_notifi method is used to process query block.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 27.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_details_for_send_notifi($input_params = array())
    {
        
        $this->block_result = array();
        try
        {

            $connection_type = isset($input_params["connection_type"]) ? $input_params["connection_type"] : "";
            // if(false ==empty($connection_type) && $connection_type == 'Like'){
                 $user_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            $receiver_id = isset($input_params["connection_user_id"]) ? $input_params["connection_user_id"] : "";

            /*}
            if(false ==empty($connection_type) && $connection_type == 'Match'){
                $user_id = isset($input_params["connection_user_id"]) ? $input_params["connection_user_id"] : "";
                $receiver_id = isset($input_params["user_id"]) ? $input_params["user_id"] : "";
            }*/
            
            $this->block_result = $this->connections_model->get_user_details_for_send_notifi($user_id, $receiver_id,$connection_type);
            //print_r($this->block_result);exit;
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
        $input_params["get_user_details_for_send_notifi"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * custom_function method is used to process custom function.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function custom_function($input_params = array())
    {

        if (!method_exists($this, "PrepareHelperMessage"))
        {
            $result_arr["data"] = array();
        }
        else
        {
            $result_arr["data"] = $this->PrepareHelperMessage($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["custom_function"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

    /**
     * post_notification method is used to process query block.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function post_notification($input_params = array())
    {
        
        $this->block_result = array();
        try
        {

            $params_arr = array();
            if (isset($input_params["notification_message"]))
            {
                $params_arr["notification_message"] = $input_params["notification_message"];
            }
            if (isset($input_params["connection_user_id"]))
            {
                $params_arr["receiver_id"] = $input_params["connection_user_id"];
            }
            if(isset($input_params["connection_result"]))
            {
                $params_arr["_enotificationtype"] = $input_params["connection_result"];    
            }
            else{
                $params_arr["_enotificationtype"] = $input_params["connection_type"];       
            }
            $params_arr["_dtaddedat"] = "NOW()";
            $params_arr["_dtupdatedat"] = "NOW()";
            $params_arr["_estatus"] = "Unread";
            if (isset($input_params["user_id"]))
            {
                $params_arr["user_id"] = $input_params["user_id"];
            }
            $this->block_result = $this->notification_model->post_notification($params_arr);
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["post_notification"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * get_sender_image method is used to process query block.
     * @created Devangi Nirmal | 18.06.2019
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_sender_image($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $s_users_id = isset($input_params["s_users_id"]) ? $input_params["s_users_id"] : "";
            $this->block_result = $this->users_model->get_sender_image($s_users_id);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                {

                    $data = $data_arr["ui_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["height"] = "100";
                    $image_arr["width"] = "100";
                    $p_key = ($data_arr["ui_users_id"] != "") ? $data_arr["ui_users_id"] : $input_params["ui_users_id"];
                    $image_arr["pk"] = $p_key;
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["path"] = $this->general->getImageNestedFolders("user_profile");
                    $data = $this->general->get_image($image_arr);

                    $result_arr[$data_key]["ui_image"] = $data;

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
        $input_params["get_sender_image"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * check_receiver_device_token method is used to process conditions.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 27.06.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_receiver_device_token($input_params = array())
    {   
        // print_r($input_params);exit;

        $this->block_result = array();
        try
        {

            $cc_lo_0 = $input_params["r_device_token"];

            $cc_fr_0 = (!is_null($cc_lo_0) && !empty($cc_lo_0) && trim($cc_lo_0) != "") ? TRUE : FALSE;
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
     * push_notification method is used to process mobile push notification.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 18.06.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function push_notification($input_params = array())
    {
        $this->block_result = array();
        try
        {

            $device_id = $input_params["r_device_token"];
            
            $type = (false == empty($input_params["connection_type"])) ? $input_params["connection_type"] : "connection"; 
            $code = "USER";
            $sound = "";
            $badge = "";
            $title = "";
            $send_vars = array(
                array(
                    "key" => "type",
                    "value" => $type,
                    "send" => "Yes",
                ),
                array(
                    "key" => "receiver_id",
                    "value" => $input_params["r_users_id"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_id",
                    "value" => $input_params["s_users_id"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_name",
                    "value" => $input_params["s_name"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_profile",
                    "value" => $input_params["s_profile_image"],
                    "send" => "Yes",
                ),
                array(
                    "key" => "user_image",
                    "value" => $input_params["ui_image"],
                    "send" => "Yes",
                )
            );
            $push_msg = "".$input_params["notification_message"]."";
            $push_msg = $this->general->getReplacedInputParams($push_msg, $input_params);
            $send_mode = "runtime";

            $send_arr = array();
            $send_arr['device_id'] = $device_id;
            $send_arr['code'] = $code;
            $send_arr['sound'] = $sound;
            $send_arr['badge'] = intval($badge);
            $send_arr['title'] = $title;
            $send_arr['message'] = $push_msg;
            $send_arr['variables'] = json_encode($send_vars);
            $send_arr['send_mode'] = $send_mode;
            $uni_id = $this->general->insertPushNotification($send_arr);
            if (!$uni_id)
            {
                throw new Exception('Failure in insertion of push notification batch entry.');
            }

            $success = 1;
            $message = "Push notification send succesfully.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        $input_params["push_notification"] = $this->block_result["success"];

        return $input_params;
    }

    /**
     * messages_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_finish_success_1($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "messages_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * messages_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function messages_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "messages_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * blocked_user_finish_success method is used to process finish flow.
     * @created Devangi Nirmal | 30.05.2019
     * @modified Devangi Nirmal | 31.07.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function blocked_user_finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "3",
            "message" => "blocked_user_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "send_message";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }


}

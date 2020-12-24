<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of States List Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module States List
 *
 * @class States_list_model.php
 *
 * @path application\webservice\basic_appineers_master\models\States_list_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Connections_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * get_states_list_v1 method is used to execute database queries for States List API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param string $STATES_LIST_COUNTRY_ID STATES_LIST_COUNTRY_ID is used to process query block.
     * @param string $STATES_LIST_COUNTRY_CODE STATES_LIST_COUNTRY_CODE is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function connection_details($params_arr = array())
    {
        try {
            if($params_arr['connection_type']=="Match"){
		
		$result_arr = array();
                $this->db->from("user_connections AS usc");            
                $this->db->join("users AS u", "u.iUserId = usc.iConnectionUserId AND u.eStatus = 'Active'", "left");
                $this->db->select("u.iUserId AS user_id");
                $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS user_name", FALSE);
                $this->db->select("u.vProfileImage AS user_image");
                $this->db->where("usc.eStatus", 'Active');
                $this->db->where("usc.eConnectionType", $params_arr['connection_type']);
                $this->db->where("usc.iUserId", $params_arr['user_id']);
                
                $result_obj = $this->db->get();
                $result_arr_user = is_object($result_obj) ? $result_obj->result_array() : array();
            
                
                $this->db->from("user_connections AS usc");            
                $this->db->join("users AS u", "u.iUserId = usc.iUserId AND u.eStatus = 'Active'", "left");
                $this->db->select("u.iUserId AS user_id");
                $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS user_name", FALSE);
                $this->db->select("u.vProfileImage AS user_image");
                $this->db->where("usc.eStatus", 'Active');
                $this->db->where("usc.eConnectionType", $params_arr['connection_type']);
                $this->db->where("usc.iConnectionUserId", $params_arr['user_id']);
                
                $result_obj = $this->db->get();
                $result_arr_connection = is_object($result_obj) ? $result_obj->result_array() : array();
            
                $result_arr = array_merge($result_arr_user, $result_arr_connection);
               	
		// echo $this->db->last_query();exit;
                // print_r($result_arr);exit;

            }
            else{
                $result_arr = array();
                $this->db->from("user_connections AS usc");            
                $this->db->join("users AS u", "u.iUserId = usc.iConnectionUserId AND u.eStatus = 'Active'", "left");
                $this->db->select("u.iUserId AS user_id");
                $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS user_name", FALSE);
                $this->db->select("u.vProfileImage AS user_image");
                $this->db->where("usc.eStatus", 'Active');
                $this->db->where("usc.eConnectionType", $params_arr['connection_type']);
                $this->db->where("usc.iUserId", $params_arr['user_id']);
                $this->db->order_by("usc.dtAddedAt","DESC");

                $result_obj = $this->db->get();
               // echo $this->db->last_query();exit;
                $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
                
            }
            if(!is_array($result_arr) || count($result_arr) == 0){
                    throw new Exception('No records found.');
            }
            
            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        
        //print_r($return_arr);
        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        // print_r($return_arr);
        return $return_arr;
    }

     /**
     * post_a_feedback method is used to execute database queries for Post a Feedback API.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $params_arr params_arr array to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function add_connection_status($params_arr = array())
    {
        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            $this->db->set("eStatus", $params_arr["_estatus"]);
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["connection_user_id"]))
            {
                $this->db->set("iConnectionUserId", $params_arr["connection_user_id"]);
            }
            if (isset($params_arr["connection_type"]))
            {
                $this->db->set("eConnectionType", $params_arr["connection_type"]);
            }
        
            $this->db->insert("user_connections");
            $insert_id = $this->db->insert_id();

            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "connection_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        
        // echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }



 /**
     * post_a_feedback method is used to execute database queries for Post a Feedback API.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $params_arr params_arr array to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function update_exist_connection_status($params_arr = array(), $where_arr = array())
    {

        try
        {
            $result_arr = array();
            
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }
            $this->db->start_cache();
            if (isset($where_arr["connection_id"]) && $where_arr["connection_id"] != "")
            {
                $this->db->where("iConnectionId =", $where_arr["connection_id"]);
            }
            $this->db->where_in("eStatus", array('Active'));  
            $this->db->stop_cache(); 

            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
                
            
            if (isset($params_arr["connection_type"]))
            {
                $this->db->set("eConnectionType", $params_arr["connection_type"]);
            }
            
            
            $res = $this->db->update("user_connections");
            
            $affected_rows = $this->db->affected_rows();
            // echo $this->db->last_query();exit;
            
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;

        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->flush_cache();
        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

     /**
     * delete review method is used to execute database queries for Edit Profile API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function delete_connection($params_arr = array())
    {
        // print_r($params_arr);exit;
        try
        {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($params_arr["connection_id"]))
            {
                $this->db->where("iConnectionId =", $params_arr["connection_id"]);
            }
            $this->db->stop_cache();
           
            $res = $this->db->delete("user_connections");

            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in deletion.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;

        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
     * get_user_details_for_send_notifi method is used to execute database queries for Send Message API.
     * @created CIT Dev Team
     * @modified Devangi Nirmal | 27.06.2019
     * @param string $user_id user_id is used to process query block.
     * @param string $receiver_id receiver_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_user_details_for_send_notifi($user_id = '', $receiver_id = '',$connection_type='')
    {
        try
        {
            $result_arr = array();

            $this->db->from("user_connections AS uc");
            if(false ==empty($connection_type) && $connection_type == 'Like'){
            $this->db->join("users AS s", "uc.iUserId = s.iUserId", "left");
            $this->db->join("users AS r", "uc.iConnectionUserId  = r.iUserId", "left");
            }
             if(false ==empty($connection_type) && $connection_type == 'Match'){
                 $this->db->join("users AS s", "uc.iConnectionUserId = s.iUserId", "left");
                $this->db->join("users AS r", "uc.iUserId  = r.iUserId", "left");
             }

            $this->db->select("s.iUserId AS s_users_id");
            $this->db->select("r.iUserId AS r_users_id");
            $this->db->select("r.vDeviceToken AS r_device_token");
            $this->db->select("CONCAT(s.vFirstName,\" \",s.vLastName) AS s_name");
           // $this->db->select("r.eNotificationType AS r_notification");
            //$this->db->where("(uc.iUserId = ".$user_id." ) OR (uc.iConnectionUserId = ".$receiver_id.")", FALSE, FALSE);
            //if(false ==empty($connection_type) && $connection_type == 'Like'){
                $this->db->where("(uc.iUserId = ".$user_id." AND uc.iConnectionUserId = ".$receiver_id.") OR (uc.iUserId = ".$receiver_id." AND uc.iConnectionUserId = ".$user_id.")", FALSE, FALSE);

           /* }
             if(false ==empty($connection_type) && $connection_type == 'Match'){
                $this->db->where("(uc.iUserId = ".$receiver_id." AND uc.iConnectionUserId = ".$user_id.") OR (uc.iUserId = ".$user_id." AND uc.iConnectionUserId = ".$receiver_id.")", FALSE, FALSE);

            }*/
            

            $this->db->limit(1);

            $result_obj = $this->db->get();
            //echo $this->db->last_query();exit;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            if (!is_array($result_arr) || count($result_arr) == 0)
            {
                throw new Exception('No records found.');
            }
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
       // echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
}

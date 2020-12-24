<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User Sign Up Email Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module User Sign Up Email
 *
 * @class User_sign_up_email_model.php
 *
 * @path application\webservice\basic_appineers_master\models\User_sign_up_email_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 12.02.2020
 */

class User_list_model extends CI_Model
{
    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
    }
     
      /**
     * update_thumbnail_image method is used to execute database queries for Delete Account API.
     * @created priyanka chillakuru | 22.03.2019
     * @modified priyanka chillakuru | 22.03.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_thumbnail_image($post_media_id = "", $thubnail_image_name = array())
    {

        try
        {
            $result_arr = array();

            $this->db->where("iMediaId", $post_media_id);

            $this->db->set("vVideoThumbNail", $thubnail_image_name);
            $res = $this->db->update("user_media");
            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows1";
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
     * post_image_v1 method is used to execute database queries for Post Listing API.
     * @created priyanka chillakuru | 30.04.2019
     * @modified priyanka chillakuru | 30.04.2019
     * @param string $p_post_id_1 p_post_id_1 is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function post_video_v1($user_id = '')
    {
        try
        {
            $result_arr = array();

            $this->db->from("user_media AS p");
          
           
            $this->db->select("p.iMediaId AS media_id");  //
            $this->db->select("p.iUserId AS user_id");  //
            $this->db->select("p.vMediaType AS media_type");  //
            $this->db->select("p.vMediaUrl AS media_url");  //
            $this->db->select("p.vMediaName AS media_name");  //
            $this->db->select("p.vMediaFile AS media_file");  //
            $this->db->select("p.vVideoThumbnail AS video_thumbnail");  //
            
            
            $this->db->select("p.dtAddedAt AS p_updated_date");
            $this->db->select("(DATE_FORMAT(p.dtAddedAt,\"%b %d %Y\")) AS added_at", FALSE); ///
           
           
           
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("p.iUserId =", $user_id);
                $this->db->where("p.vMediaType =", 'video/mp4');
                $this->db->where("p.vMediaUrl =", '');
                $this->db->where("p.vVideoThumbnail =", '');
            }




            $result_obj = $this->db->get();
            #echo $this->db->last_query();
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
        #echo $this->db->last_query();exit;
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

     
      public function get_posted_details_v1($user_id = '')
    {
        try
        {
            $result_arr = array();


            $this->db->from("user_media AS p");
          
           
            $this->db->select("p.iMediaId AS media_id");  //
            $this->db->select("p.vMediaType AS media_type");  //
            $this->db->select("p.vMediaUrl AS media_url");  //
            $this->db->select("p.vMediaName AS media_name");  //
            $this->db->select("p.vMediaFile AS media_file");  //
            
            
            $this->db->select("p.dtAddedAt AS p_updated_date");
            $this->db->select("(DATE_FORMAT(p.dtAddedAt,\"%b %d %Y\")) AS added_at", FALSE); ///
           
           
           
            if (isset($user_id) && $user_id != "")
            {
                $this->db->where("p.iUserId =", $user_id);
            }

//            $this->db->limit(1);

            $result_obj = $this->db->get();
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

     
     
    /**
     * get_users_list method is used to execute database queries for User Sign Up Email API.
     * @created Kavita sawant | 27.05.2020
     * @modified Kavita sawant | 27.05.2020
     * @param string $insert_id insert_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_users_list_details($arrParams = '')
    {
        try
        {
            $result_arr = $old_arr = $tmp_arr = array();

            $this->db->from("users AS u");
			$this->db->join("user_breed AS ui", "ui.iUserId = u.iUserId", "left");
			$this->db->join("breeds AS i", "i.iBreedsId = ui.iBreedsId", "left");
			$this->db->join("user_media AS m", "m.iUserId = u.iUserId", "left");

            $this->db->join("pet AS p", "p.iUserId = u.iUserId", "left");
            // $this->db->join("mod_state AS ms", "u.iStateId =   ms.iStateId", "left");
            // $this->db->join("user_connections AS uc", "uc.iConnectionUserId = '".$arrParams['user_id']."'", "left");
			
			$this->db->select("m.vVideoThumbnail AS video_thumbnail");
			$this->db->select("u.iUserId AS user_id");
            $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS user_name", FALSE);
            $this->db->select("u.vFirstName AS u_first_name");			
            $this->db->select("u.vLastName AS u_last_name");
            $this->db->select("u.vEmail AS u_email");
            $this->db->select("u.vMobileNo AS u_mobile_no");
            $this->db->select("u.vProfileImage AS user_image");
            $this->db->select("u.vDescription AS description");
            $this->db->select("u.dDob AS u_dob");
            $this->db->select("u.tAddress AS u_address");
            $this->db->select("u.vCity AS city");
            $this->db->select("u.dLatitude AS u_latitude");
            $this->db->select("u.dLongitude AS u_longitude");
            $this->db->select("u.iStateId AS u_state_id");
            $this->db->select("u.vStateName AS state");
            $this->db->select("u.vZipCode AS u_zip_code");
			$this->db->select("u.iAge AS age");
            $this->db->select("u.eStatus AS u_status");
			$this->db->select("GROUP_CONCAT(DISTINCT  i.vBreedsName SEPARATOR ', ') AS breed_name");
            // $this->db->select("ms.vState AS state");
			$this->db->select("GROUP_CONCAT( DISTINCT `m`.`iMediaId` SEPARATOR ', ')  AS media_id");
            $this->db->select("(".$this->db->escape("").") AS connection_type_by_logged_user", FALSE);
            $this->db->select("(".$this->db->escape("").") AS connection_type_by_receiver_user", FALSE);

            $this->db->select("p.iPetId AS pet_id");
            $this->db->select("p.vPetName AS pet_name");
            $this->db->select("p.vBreed AS breed");
            $this->db->select("p.vPetAge AS pet_age");
            $this->db->select("p.vPetCategory AS pet_category");
            $this->db->select("p.vPetDescription AS pet_description");
            $this->db->select("p.vAKCRegistered AS akc_registered");
            
            if($tmp_arr = filterEmptyValues($arrParams['breed_list'])){
                $old_arr = $arrParams['breed_list'];
                $arrParams['breed_list'] = $tmp_arr;
                $this->db->where_in("ui.iBreedsId", $arrParams['breed_list']);
                $arrParams['breed_list'] = $old_arr;
            }
            
			if (isset($arrParams['gender_type']) && $arrParams['gender_type'] != "" && $arrParams['gender_type'] !='All')
            {
                $this->db->where("u.vGender =", $arrParams['gender_type']);
               
            }
             if (isset($arrParams['search_radius']) && $arrParams['search_radius'] != "")
            {

            $this->db->where("FLOOR(".$arrParams['distance'].") <=", $arrParams['search_radius']); 
            }
			if (isset($arrParams['city']) && $arrParams['city'] != "")
            {
                $this->db->like("u.vCity", $arrParams['city'],'after');
            }
            if (isset($arrParams['state']) && $arrParams['state'] != "")
            {
                $this->db->like("u.iStateId", $arrParams['state']);
            }
			if ((isset($arrParams['min_age']) && $arrParams['min_age'] != "") && (isset($arrParams['max_age']) && $arrParams['max_age'] != ""))
            {
			 $this->db->where('u.iAge BETWEEN "'. $arrParams['min_age']. '" and "'. $arrParams['max_age'].'"');
			}

            if ((isset($arrParams['other_user_id']) && $arrParams['other_user_id'] != ""))
            {
             $this->db->where("u.iUserId =", $arrParams['other_user_id']);
            }else{
                 $strWhere = "u.iUserId not in (SELECT DISTINCT(u.iUserId) AS user_id
                FROM users AS u
                LEFT JOIN user_connections AS uc ON uc.iConnectionUserId = u.iUserId 
                LEFT JOIN user_connections AS ub ON ub.iUserId = u.iUserId 
                WHERE u.eStatus = 'Active' AND ( uc.iUserId = '".$arrParams['user_id']."' OR (ub.eConnectionResult='Block' AND ub.iConnectionUserId = '".$arrParams['user_id']."'))) AND u.iUserId <> '".$arrParams['user_id']."'"; 
                if (isset($strWhere) && $strWhere != "")
                {
                    $this->db->where($strWhere);
                }
               $this->db->group_by('user_id'); 
            }
             $this->db->where("u.eStatus =", 'Active');
             //$this->db->where("uc.eConnectionType","Like");
             //$this->db->or_where("uc.eConnectionType","Match");
            
			
            $result_obj = $this->db->get();
			//echo $this->db->last_query();exit;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
			//print_r( $result_arr);exit;
			
   
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
        // echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
     /**
     * get_users_list method is used to execute database queries for User Sign Up Email API.
     * @created Kavita sawant | 27.05.2020
     * @modified Kavita sawant | 27.05.2020
     * @param string $insert_id insert_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_users_connection_details($user_id = '',$connection_id='',$other_user_id='')
    {
         try
        {

            $result_arr = array();
           
               $strSql=
               "SELECT '' AS connection_type, 
                        (SELECT eConnectionType 
                        FROM user_connections 
                        WHERE eStatus='active' AND iUserId=".$user_id." AND  iConnectionUserId = ".$connection_id.") AS connection_type_by_logged_user,
                        (SELECT eConnectionResult 
                        FROM user_connections 
                        WHERE eStatus='active' AND iUserId=".$user_id." AND  iConnectionUserId = ".$connection_id.") AS connection_result_by_logged_user,
                        (SELECT eConnectionType 
                        FROM user_connections 
                        WHERE eStatus='active' AND iUserId=".$connection_id." AND  iConnectionUserId = ".$user_id.") AS connection_type_by_receiver_user,
                        (SELECT eConnectionResult 
                        FROM user_connections 
                        WHERE eStatus='active' AND iUserId=".$connection_id." AND  iConnectionUserId = ".$user_id.") AS connection_result_by_receiver_user
                        
                        FROM user_connections LIMIT 1";



                $result_obj =  $this->db->query($strSql);
            
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();

            if((isset($result_arr[0]['connection_result_by_logged_user']) && $result_arr[0]['connection_result_by_logged_user'] == "Block")){

                $result_arr[0]['connection_type_by_receiver_user'] = $result_arr[0]['connection_result_by_logged_user'];
            }
            else if((isset($result_arr[0]['connection_result_by_receiver_user']) && $result_arr[0]['connection_result_by_receiver_user'] == "Block")){

                $result_arr[0]['connection_type_by_logged_user'] = $result_arr[0]['connection_result_by_receiver_user'];
            }
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
      /**
     * get_users_list method is used to execute database queries for User Sign Up Email API.
     * @created Kavita sawant | 27.05.2020
     * @modified Kavita sawant | 27.05.2020
     * @param string $insert_id insert_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_message_connection_details($user_id = '',$connection_id='',$other_user_id='')
    {
        try
        {

            $result_arr = array();
            $this->db->from("user_connections");
            $this->db->select("eConnectionType  AS connection_type");
            //$this->db->join("user_connections AS uc", "uc.iConnectionUserId = u.iUserId OR uc.iUserId = u.iUserId", "left");
           // $this->db->select("distinct(u.iUserId) AS user_id");
            if(false == empty($other_user_id)){
              /*  $this->db->where("iUserId",$user_id);
                $this->db->where("iConnectionUserId",$connection_id);
                $this->db->or_where("iConnectionUserId",$user_id);
                 $this->db->where("iUserId",$connection_id);*/

                 $this->db->where("((iUserId = ".$user_id." AND iConnectionUserId = ".$connection_id.") OR (iUserId = ".$connection_id." AND iConnectionUserId = ".$user_id."))", FALSE, FALSE);

            }else{
                $this->db->where("iUserId",$connection_id);
                $this->db->where("iConnectionUserId",$user_id);

            }
            
           
            $result_obj = $this->db->get();
            //echo $this->db->last_query();exit;
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            //print_r( $result_arr);exit;
            
   
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
	/**
     * query_images method is used to execute database queries for Post a Feedback API.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param string $query_id query_id is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function media_images($media_id = '')
    {

        try
        {
            $result_arr = array();

            $this->db->from("user_media AS m");
            $this->db->select("m.iMediaId AS media_id");
			$this->db->select("m.iUserId AS m_user_id");
            $this->db->select("m.vMediaType AS media_type");
			$this->db->select("m.vVideoThumbnail  AS video_thumbnail");
			$this->db->select("m.vMediaUrl  AS media_url");
			$this->db->select("m.vMediaName AS media_name");
            $this->db->select("m.vMediaFile AS media_file");
            
			if(strpos($media_id, ',') !== false){
                    $strWhere = "m.iMediaId IN ('" . str_replace(",", "','", $media_id) . "')";            
            }else{
				 $strWhere = "m.iMediaId ='" .$media_id. "'";   
			}
			if (isset($strWhere) && $strWhere != "")
            {
				$this->db->where($strWhere);
            }

            $result_obj = $this->db->get();
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
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
     public function delete_media($arrResult = '')
    {
       
        try
        {
            $result_arr = array();

           
           /* $this->db->set("m.vMediaType", '');
            $this->db->set("m.vMediaUrl", '');
            $this->db->set("m.vMediaName", '');
            $this->db->set("m.vMediaFile", '');*/
            
            if(strpos($arrResult['media_id'], ',') !== false){
                    $strWhere = "iMediaId IN ('" . str_replace(",", "','", $media_id) . "')";            
            }else{
                 $strWhere = "iMediaId ='" .$arrResult['media_id']. "'";   
            }
            if (isset($strWhere) && $strWhere != "")
            {
                $this->db->where($strWhere);
            }
             $this->db->delete("user_media");
            $affected_rows = $this->db->affected_rows();
            /*if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in updation.");
            }*/
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
    /**
     * set_profile method is used to execute database queries for Post a Feedback API.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 11.12.2019
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function set_profile($params_arr = array())
    {
        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }

            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            
            if (isset($params_arr["user_id"]) && $params_arr["user_id"] != "")
            {
                $this->db->where("iUserId =", $params_arr["user_id"]);
            }
            if (isset($params_arr["description"]))
            {
                $this->db->set("vDescription", $params_arr["description"]);
            }           
            $res = $this->db->update("users");
            $affected_rows = $this->db->affected_rows();
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

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
  
}

<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User review Model
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage models
 *
 * @module User review
 *
 * @class User_review_model.php
 *
 * @path application\webservice\basic_appineers_master\models\User_review_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 18.09.2019
 */

class Pets_model extends CI_Model
{
    public $default_lang = 'EN';

    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        //$this->load->pet('listing');
        $this->default_lang = $this->general->getLangRequestValue();
    }

    /**
     * post_a_feedback method is used to execute database queries for Post a Feedback API.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $params_arr params_arr array to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function set_pet($params_arr = array())
    {
        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }
            /*$this->db->set("dtAddedAt", $params_arr["_dtaddedat"]);
            $this->db->set("eStatus", $params_arr["_estatus"]);*/
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["pet_name"]))
            {
                $this->db->set("vPetName", $params_arr["pet_name"]);
            }
            if (isset($params_arr["breed"]))
            {
                $this->db->set("vBreed", $params_arr["breed"]);
            } 
            if (isset($params_arr["pet_age"]))
            {
                $this->db->set("vPetAge", $params_arr["pet_age"]);
            }
             if (isset($params_arr["pet_category"]))
            {
                $this->db->set("vPetCategory", $params_arr["pet_category"]);
            }
             if (isset($params_arr["pet_description"]))
            {
                $this->db->set("vPetDescription", $params_arr["pet_description"]);
            }
            if (isset($params_arr["pet_akc_registered"]))
            {
                $this->db->set("vAKCRegistered", $params_arr["pet_akc_registered"]);
            }
 

            
            $this->db->insert("pet");
            $insert_id = $this->db->insert_id();
            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "pet_id";
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


   /* public function get_pet_details_offer($arrResult)
    {
        try
        { 
           $sql="SELECT h.iPetId AS pet_id,h.vFirstName AS pet_first_name,h.vLastName AS pet_last_name,h.vProfileImage AS pet_image from pet h INNER JOIN offer_pet AS of ON of.iPetId=h.iPetId WHERE of.iOfferId='".$arrResult['offer_id']."' AND h.eStatus='Active'";


            $results = $this->db->query($sql);
            //echo $this->db->last_query();exit;
            $result_arr = is_object($results) ? $results->result_array() : array();
            
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
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
      // print_r($return_arr["data"]); exit;
        return $return_arr;
    }*/



    /**
     * get_review_details method is used to execute database queries for Post a Feedback API.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param string $review_id review_id is used to process review block.
     * @return array $return_arr returns response of review block.
     */
     /*public function get_pet_details($arrResult)
    {
        try
        { 
            $result_arr = array();
            if(true == empty($arrResult)){
                return false;
            }
            $strWhere ='';

            
            if(isset($arrResult['service_id'])){*/
                //$this->db->join("offer_pet AS oh", "oh.iPetId = h.iPetId " , "right");
                //$this->db->join("offer AS o", "o.iOfferId = oh.iOfferId AND o.iProviderId = '".$arrResult['provider_id']."' AND  o.iServiceId = '" .$arrResult['service_id'] ."'", "right");
               /*  $this->db->join("offer_pet AS oh", "oh.iPetId = h.iPetId","right");
                 $this->db->where('oh.iServiceId',$arrResult['service_id']);*/
                 /*$this->db->start_cache();
                 $this->db->from("service AS s");
                 $this->db->join("offer AS o", "o.iServiceId = s.iServiceId" , "inner");
                 $this->db->join("offer_pet AS oh", "oh ON oh.iOfferId = o.iOfferId" , "inner");
                 $this->db->join("pet AS h", "h.iPetId = oh.iPetId" , "inner");
                  $strWhere .= "s.iServiceId = '".$arrResult['service_id']."' AND o.eOfferStatus='Accepted' AND h.eStatus= 'Active'" ;
                  $this->db->stop_cache();

            }else
            {
                $this->db->start_cache();
                $this->db->from("pet AS h");

                $this->db->stop_cache();
                $strWhere = "h.eStatus= 'Active'";              
                if(false == empty($arrResult['pet_id']))
                {
                   $strWhere .= " AND h.iPetId = '" . $arrResult['pet_id'] . "' ";
                }
                if(false == empty($arrResult['provider_id']))
                {
                   $strWhere .= " AND h.iProviderId = '" . $arrResult['provider_id'] . "' ";
                }
                else 
                {
                   $strWhere .= " AND h.iProviderId = '" . $arrResult['user_id'] . "' ";
                }
            }            
            
            if(false == empty($strWhere)){
              $this->db->where($strWhere); 
            } 
            $this->db->select("h.iPetId AS pet_id");
            $this->db->select("h.vFirstName AS pet_first_name");
            $this->db->select("h.vLastName AS pet_last_name"); 
            $this->db->select("h.vProfileImage AS pet_image");
            
            $this->db->order_by("h.dtAddedAt","desc");
            $this->db->limit($record_limit, $start_index);
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
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
      // print_r($return_arr["data"]); exit;
        return $return_arr;
    }*/

   /**
     * update_profile method is used to execute database queries for Edit Profile API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_pet($params_arr = array(), $where_arr = array())
    {
        try
        { 

            $result_arr = array();
            $this->db->start_cache();
             if (isset($params_arr["user_id"]))
            {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($where_arr["pet_id"]) && $where_arr["pet_id"] != "")
            {
                $this->db->where("iPetId =", $where_arr["pet_id"]);
            }
           /* $this->db->where_in("eStatus", array('Active'));*/
            $this->db->stop_cache();
            if (isset($params_arr["pet_name"]))
            {
                $this->db->set("vPetName", $params_arr["pet_name"]);
            }
            if (isset($params_arr["breed"]))
            {
                $this->db->set("vBreed", $params_arr["breed"]);
            }
            if (isset($params_arr["pet_age"]))
            {
                $this->db->set("vPetAge", $params_arr["pet_age"]);
            }
             if (isset($params_arr["pet_category"]))
            {
                $this->db->set("vPetCategory", $params_arr["pet_category"]);
            }
             if (isset($params_arr["pet_description"]))
            {
                $this->db->set("vPetDescription", $params_arr["pet_description"]);
            }
            if (isset($params_arr["pet_akc_registered"]))
            {
                $this->db->set("vAKCRegistered", $params_arr["pet_akc_registered"]);
            }
           /* $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);*/
          
            $res = $this->db->update("pet");
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
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }


    /*public function cheak_pet_in_service($params_arr = array())
    {
        try
        {
            $result_arr = array();
            
             $sql="select service.iServiceId from service join offer_pet of on of.iServiceId = service.iServiceId where of.iPetId='".$params_arr["pet_id"]."' AND service.eServiceStatus IN('Pending','Upcoming','Ongoing') ";

            $results = $this->db->query($sql);

          
            $result_arr = is_object($results) ? $results->result_array() : array();
            
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
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }


     public function delete_pet($params_arr = array())
    {
        try
        {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($params_arr["pet_id"]))
            {
                $this->db->where("iPetId =", $params_arr["pet_id"]);
            }
            $this->db->stop_cache();
            //$this->db->set("eStatus", 'InActive');
            //$this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["dtUpdatedAt"], FALSE);
           
            $res = $this->db->delete("pet");

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
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }*/

    

   
}

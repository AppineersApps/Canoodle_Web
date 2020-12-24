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

class Breed_type_list_model extends CI_Model
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
    public function breed_type_list()
    {
       // print_r("Text");exit;
            try {
            $result_arr = array();
                                
            $this->db->from("breeds");
            $this->db->select('iBreedsId AS breed_id');
            $this->db->select('vBreedsName AS breed_name');
            $this->db->select("vBreedsImage AS breed_image");
            $this->db->where("eStatus", 'Active');
            $this->db->order_by("iBreedsId", "asc");

            
            
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
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


    public function set_breed($params_arr = array())
    {
        try
        {
            $result_arr = array();
            $insert_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }

            if (isset($params_arr["breed_id"]))
            {
                $count=count(explode(",",$params_arr["breed_id"]));
               
                 if($count==1){
                    if (isset($params_arr["breed_id"]))
                    {
                        $this->db->set("iBreedsId", $params_arr["breed_id"]);
                    }
                    if (isset($params_arr["user_id"]))
                    {
                        $this->db->set("iUserId", $params_arr["user_id"]);
                    }
                    $this->db->set($this->db->protect("dAddedAt"), $params_arr["_addedat"], FALSE);
                    $this->db->insert("user_breed");
                    $insert_id = $this->db->insert_id();
                    //echo $this->db->last_query();exit;
                    if (!$insert_id)
                    {
                        throw new Exception("Failure in insertion.");
                    }
                     $result_param = "user_breed";
                     $result_arr[0][$result_param] = $insert_id;
                 }else if($count>1){
                    $arrBreedIds = explode(',',$params_arr["breed_id"]);
                    foreach($arrBreedIds as $key=>$intBreedValue){
                        $insert_arr[$key]['iUserId']=$params_arr["user_id"];
                        $insert_arr[$key]['iBreedsId']=$intBreedValue;
                        $insert_arr[$key]['dAddedAt']=date('Y-m-d H:i:s');
                    }

                     if(is_array($insert_arr) && !empty($insert_arr))
                        {
                            $res = $this->db->insert_batch("user_breed",$insert_arr);
                        }
                    $affected_rows = $this->db->affected_rows();
                    if (!$res || $affected_rows == -1)
                    {
                        throw new Exception("Failure in updation.");
                    }
                    $result_param = "affected_rows";
                    $result_arr[0][$result_param] = $affected_rows;

                }
            
            $success = 1;

        }
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

     public function get_breed_details($arrResult)
    {

        try
        {
            $result_arr = array();
            if(true == empty($arrResult)){
                return false;
            }
            $strWhere ='';            
            
            $this->db->from("breed AS i");
            /*$this->db->select("i.iItemId  AS item_id");
            $this->db->select("i.vItemName AS item_name");*/   

            $this->db->select("i.iBreedsId  AS breed_id");
            $this->db->select("i.vBreedsName AS breed_name");  
            //$this->db->select("i.dtAddedAt AS date_added");      

           

            if(false == empty($arrResult['breed_type']))
            {
              $this->db->where("eBreedStatus = '".$arrResult['breed_type']."' AND iBreedsId ='".$arrResult['breed_id']."'"); 
            }
            if(false == empty($arrResult['breed_id']))
            {
              $this->db->where("iBreedsId = '".$arrResult['breed_id']."'"); 
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
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
}

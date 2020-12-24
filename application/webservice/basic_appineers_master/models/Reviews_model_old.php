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

class Reviews_model extends CI_Model
{
    public $default_lang = 'EN';

    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
        $this->default_lang = $this->general->getLangRequestValue();
    }

    /**
     * post_a_feedback method is used to execute database queries for Post a Feedback API.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $params_arr params_arr array to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function set_review($params_arr = array())
    {

        try
        {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0)
            {
                throw new Exception("Insert data not found.");
            }

            $this->db->set("dAddedAt", $params_arr["_dtaddedat"]);
            $this->db->set("eStatus", $params_arr["_estatus"]);
            $this->db->set("bIsClaimed", $params_arr["is_claimed"]);
            if (isset($params_arr["user_id"]))
            {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["first_name"]))
            {
                $this->db->set("vFirstName", $params_arr["first_name"]);
            }
            if (isset($params_arr["last_name"]))
            {
                $this->db->set("vLastName", $params_arr["last_name"]);
            }
            if (isset($params_arr["mobile_number"]))
            {
                $this->db->set("vMobileNo", $params_arr["mobile_number"]);
            }
            if (isset($params_arr["email"]))
            {
                $this->db->set("vEmail", $params_arr["email"]);
            }
            if (isset($params_arr["position"]))
            {
                $this->db->set("vPosition", $params_arr["position"]);
            }
            if (isset($params_arr["street_address"]))
            {
                $this->db->set("tAddress", $params_arr["street_address"]);
            }
            if (isset($params_arr["city"]))
            {
                $this->db->set("vCity", $params_arr["city"]);
            }
            if (isset($params_arr["state"]))
            {
                $this->db->set("iStateId", $params_arr["state"]);
            }
            if (isset($params_arr["google_placeid"]))
            {
                $this->db->set("vPlaceId", $params_arr["google_placeid"]);
            }
            if (isset($params_arr["business_name"]))
            {
                $this->db->set("vBussinessName", $params_arr["business_name"]);
            }
            if (isset($params_arr["business_typeid"]))
            {
                $this->db->set("iBussinessType", $params_arr["business_typeid"]);
            }
            if (isset($params_arr["review_stars"]))
            {
                $this->db->set("iStars", $params_arr["review_stars"]);
            }
            if (isset($params_arr["description"]))
            {
                $this->db->set("vDescription", $params_arr["description"]);
            }
            if (isset($params_arr["review_type"]))
            {
                $this->db->set("vReviewType", $params_arr["review_type"]);
            }
            if (isset($params_arr["latitude"]))
            {
                $this->db->set("dLatitude", $params_arr["latitude"]);
            }
            if (isset($params_arr["longitude"]))
            {
                $this->db->set("dLongitude", $params_arr["longitude"]);
            }
             if (isset($params_arr["profile_image"]) && !empty($params_arr["profile_image"]))
            {
                $this->db->set("vProfileImage", $params_arr["profile_image"]);
            }
            
            $this->db->insert("review");
            $insert_id = $this->db->insert_id();
            if (!$insert_id)
            {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "review_id";
            $result_arr[0][$result_param] = $insert_id;
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


    /**
     * get_review_details method is used to execute database queries for Post a Feedback API.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param string $review_id review_id is used to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function get_updated_reviews($arrResult)
    {
       // print_r($arrResult); exit;
        try
        {
            $result_arr = array();
            if(true == empty($arrResult)){
                return false;
            }
            $strWhere ='';
           
            $this->db->from("review AS r");
            $this->db->join("mod_state AS ms", "r.iStateId = ms.iStateId", "left");
            $this->db->select("r.iReviewId AS review_id");
            $this->db->select("(concat(r.vFirstName,' ',r.vLastName)) AS consumer_full_name", FALSE);
            $this->db->select("r.vMobileNo AS consumer_mobile_numer");            
            $this->db->select("r.vEmail AS consumer_email_address");            
            $this->db->select("r.vBussinessName AS consumer_business_name");
            $this->db->select("r.vProfileImage AS consumer_profile_image");
            $this->db->select("r.iStars AS review_rating");
            $this->db->select("r.vDescription AS review_description");
            $this->db->select("r.dAddedAt AS review_adddate");
            $this->db->select("r.iUserId AS user_id");  
            $this->db->select("r.vClaimedEmail AS claimed_email");
            $this->db->select("r.bIsClaimed AS claimed_flag");
           if (isset($arrResult['updated_review_id'] ) && $arrResult['updated_review_id']  != "")
            {
               $this->db->where_in("iReviewId", $arrResult['updated_review_id']);
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

    /**
     * get_review_details method is used to execute database queries for Post a Feedback API.
     * @created priyanka chillakuru | 16.09.2019
     * @modified priyanka chillakuru | 16.09.2019
     * @param string $review_id review_id is used to process review block.
     * @return array $return_arr returns response of review block.
     */
    public function get_review_details($arrResult)
    {
        try
        {
            //print_r($arrResult); 
            $result_arr = array();
            if(true == empty($arrResult)){
                return false;
            }
            $strWhere ='';
            $page = (false == empty($arrResult['page_number'])) ?  $arrResult['page_number'] : 0;
            $rec_per_page =($page != '') ? 10 : 0;
            $start_from = ($page-1) * $rec_per_page;
            $strPaginationSql ='';

            $this->db->select("AVG(iStars)")->where("eStatus= 'Active' AND bIsClaimed = true AND vClaimedEmail = r.vClaimedEmail")->from("review"); 
            $subQuery =  $this->db->get_compiled_select();

            
            if (isset($arrResult['page_name'] ) && $arrResult['page_name']  != "" && ("consumer_listing" == $arrResult['page_name'] || "review_for_me" == $arrResult['page_name']))
            {
                if (isset($arrResult['email_address']) && false == empty($arrResult['email_address']) || true == empty($arrResult['mobile_number']))
            {

               $strWhere = "r.vEmail='" . $arrResult['email_address'] . "'";
                if(isset($arrResult['business_name']) && $arrResult['business_name'] != "")
                {
                    $strWhere .= " AND r.vBussinessName='" . $arrResult['business_name'] . "'";
                }
                if(isset($arrResult['fisrt_name']) && $arrResult['fisrt_name'] != "")
                {
                    $strWhere .= " OR r.vFirstName='" . $arrResult['fisrt_name'] . "'";
                }
                if(isset($arrResult['last_name']) && $arrResult['last_name'] != "")
                {
                    $strWhere .= " OR r.vLastName='" . $arrResult['last_name'] . "'";
                }
            }
            elseif(isset($arrResult['mobile_number']) && false == empty($arrResult['mobile_number'])  || true == empty($arrResult['email_address']))
            {
                $strWhere = "r.vMobileNo='" . $arrResult['mobile_number'] . "'";
                if(isset($arrResult['business_name']) && $arrResult['business_name'] != "")
                {
                    $strWhere .= " AND r.vBussinessName='" . $arrResult['business_name'] . "'";
                }
                if(isset($arrResult['fisrt_name']) && $arrResult['fisrt_name'] != "")
                {
                    $strWhere .= " OR r.vFirstName='" . $arrResult['fisrt_name'] . "'";
                }
                if(isset($arrResult['last_name']) && $arrResult['last_name'] != "")
                {
                    $strWhere .= " OR r.vLastName='" . $arrResult['last_name'] . "'";
                }
            }elseif((isset($arrResult['email_address']) && false == empty($arrResult['email_address']) ) && (isset($arrResult['mobile_number']) && false == empty($arrResult['mobile_number'])))
            {
                $strWhere = "r.vEmail='" . $arrResult['email_address'] . "' OR vMobileNo='" . $arrResult['mobile_number'] . "'";
                if(isset($arrResult['business_name']) && $arrResult['business_name'] != "")
                {
                    $strWhere .= " AND r.vBussinessName='" . $arrResult['business_name'] . "'";
                }
                if(isset($arrResult['fisrt_name']) && $arrResult['fisrt_name'] != "")
                {
                    $strWhere .= " OR r.vFirstName='" . $arrResult['fisrt_name'] . "'";
                }
                if(isset($arrResult['last_name']) && $arrResult['last_name'] != "")
                {
                    $strWhere .= " OR r.vLastName='" . $arrResult['last_name'] . "'";
                }
            }
                 $strWhere .= " AND r.eStatus= 'Active'";
                 if("consumer_listing" == $arrResult['page_name'])
                 {
                 $this->db->join("users AS u", "u.iUserId = r.iUserId", "left");
                 $this->db->select("(concat(u.vFirstName,' ',u.vLastName)) AS user_name", FALSE);
                 $this->db->select("u.vProfileImage AS user_profile_image");
                }
            }
            if (isset($arrResult['page_name'] ) && $arrResult['page_name']  != "" && "home" == $arrResult['page_name'])
            {
                 $strWhere = "r.eStatus= 'Active'";
            }
            if (isset($arrResult['page_name'] ) && $arrResult['page_name']  != "" && "search" == $arrResult['page_name'])
            {
                 $strWhere = "r.eStatus= 'Active'";
                 $arrName = array();
                 $strSearchName = strtolower($arrResult['search_string']);
                 $arrName = explode(' ', $strSearchName);
                 if(false == empty($arrName))
                 {
                    $arrResult['fisrt_name'] = $arrName['0'];
                    $arrResult['last_name'] = $arrName['1'];
                   
                 }
                  if(false == empty($arrResult['fisrt_name'])){
                        $strWhere .= " AND lower(r.vFirstName) LIKE '" . strtolower($arrResult['fisrt_name']) . "%' ";
                     }
                     if(false == empty($arrResult['last_name'])){
                       $strWhere .= " OR lower(r.vLastName) LIKE '" . strtolower($arrResult['last_name']) . "%' ";
                     }
                 
                   $strWhere .= " OR lower(r.vFirstName) LIKE '" . strtolower($strSearchName) . "%' OR lower(r.vLastName) LIKE '" . strtolower($strSearchName) . "%' OR lower(r.vEmail) LIKE '" . strtolower($strSearchName) . "%' OR lower(r.vMobileNo) LIKE '" . strtolower($strSearchName) . "%' OR lower(r.vBussinessName) LIKE '" . strtolower($strSearchName) . "%' OR lower(r.vMobileNo) LIKE '" . strtolower($strSearchName) . "%' OR lower(r.vBussinessName) LIKE '" . strtolower($strSearchName) . "%'";
                 
            }
            if (isset($arrResult['page_name'] ) && $arrResult['page_name']  != "" && "my_review" == $arrResult['page_name'])
            {
                  $strWhere = "r.eStatus= 'Active'";
                  $strWhere .= " AND r.iUserId='" . $arrResult['reviewer_id'] . "'";
            }
            
            $this->db->from("review AS r");
            $this->db->join("mod_state AS ms", "ms.iStateId = r.iStateId", "left");
            $this->db->join("business_type AS bt", "bt.iBusinessTypeId = r.iBussinessType", "left");
            $this->db->select("r.iReviewId AS review_id");
            $this->db->select("(concat(r.vFirstName,' ',r.vLastName)) AS consumer_full_name", FALSE);
            $this->db->select("r.vMobileNo AS consumer_mobile_numer");            
            $this->db->select("r.vEmail AS consumer_email_address");            
            $this->db->select("r.vBussinessName AS consumer_business_name");
            $this->db->select("r.vProfileImage AS consumer_profile_image");
            $this->db->select("r.iStars AS review_rating");
            $this->db->select("r.vReviewType AS review_type");
            $this->db->select("r.vDescription AS review_description");
            $this->db->select("r.dAddedAt AS review_adddate");
            $this->db->select("r.vPosition AS position");
            $this->db->select("ms.vState AS state"); 
            $this->db->select("r.vPlaceId AS place_id");
            $this->db->select("r.dLatitude AS latitude");
            $this->db->select("r.dLongitude AS longitude");
            $this->db->select("r.vCity AS city");
            $this->db->select("r.vZipCode AS zip_code");
            $this->db->select("r.tAddress AS street_address");
            $this->db->select("bt.vName AS business_type_name");
            $this->db->select("r.vClaimedEmail AS claimed_email");
            $this->db->select("($subQuery) AS average_rating");

            $this->db->select("r.iUserId AS user_id");
             $this->db->order_by("r.iReviewId Desc");
           
            
            if(false == empty($strWhere)){
               $this->db->where($strWhere); 
            }          
            
            $this->db->limit($rec_per_page, $start_from);
            $result_obj = $this->db->get();
            //echo $this->db->last_query();exit;
           $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
           // print_r($result_arr); exit; 
            //$this->db->select("AVG(r1.iStars) AS average_rating")->where("r1.eStatus= 'Active' AND r1.bIsClaimed = true AND r1.vClaimedEmail = r2.vClaimedEmail")->from("review AS r1, review AS r2");
           // $this->db->get()->row()->average_rating;
           // echo $this->db->last_query();exit;
           $this->db->select('COUNT(iReviewId) AS "total_count"')
            ->from('review');
            $result_arr['total_count'] = $this->db->get()->row()->total_count;
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
        //echo $this->db->last_review();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
      // print_r($return_arr["data"]); exit;
        return $return_arr;
    }
   /**
     * update_profile method is used to execute database queries for Edit Profile API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 25.09.2019
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_review($params_arr = array(), $where_arr = array())
    {
        try
        {
            $result_arr = array();
            $this->db->start_cache();
            if (false == empty($params_arr["claimed_email"]) && false == empty($params_arr["review_id"]))
            {
                $insert_arr = array();
                foreach($params_arr["review_id"] as $key => $review_id)
                {
                    $insert_arr[$key]['iReviewId']=$review_id["iReviewId"];
                    $insert_arr[$key]['vClaimedEmail']=$params_arr["claimed_email"];
                    $insert_arr[$key]['bIsClaimed']=True;
                    $insert_arr[$key]['dtUpdatedAt']=date('Y-m-d H:i:s');
                }
             
                if(is_array($insert_arr) && false == empty($insert_arr))
                {
                   $res=$this->db->update_batch("review",$insert_arr,'iReviewId');
                }  

            }
            else
            {
                $this->db->start_cache();
                if (isset($where_arr["review_id"]) && $where_arr["review_id"] != "")
                {
                    $this->db->where("iReviewId =", $where_arr["review_id"]);
                }
                $this->db->where_in("eStatus", array('Active'));
                $this->db->stop_cache();
                if (isset($params_arr["first_name"]))
                {
                    $this->db->set("vFirstName", $params_arr["first_name"]);
                }
                if (isset($params_arr["last_name"]))
                {
                    $this->db->set("vLastName", $params_arr["last_name"]);
                }
                if (isset($params_arr["user_profile"]) && !empty($params_arr["user_profile"]))
                {
                    $this->db->set("vProfileImage", $params_arr["user_profile"]);
                }
                if (isset($params_arr["dob"]))
                {
                    $this->db->set("dDob", $params_arr["dob"]);
                }
                if (isset($params_arr["address"]))
                {
                    $this->db->set("tAddress", $params_arr["address"]);
                }
                if (isset($params_arr["city"]))
                {
                    $this->db->set("vCity", $params_arr["city"]);
                }
                if (isset($params_arr["latitude"]))
                {
                    $this->db->set("dLatitude", $params_arr["latitude"]);
                }
                if (isset($params_arr["longitude"]))
                {
                    $this->db->set("dLongitude", $params_arr["longitude"]);
                }
                if (isset($params_arr["state_id"]))
                {
                    $this->db->set("iStateId", $params_arr["state_id"]);
                }
                if (isset($params_arr["zipcode"]))
                {
                    $this->db->set("vZipCode", $params_arr["zipcode"]);
                }
                $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
                if (isset($params_arr["user_name"]))
                {
                    $this->db->set("vUserName", $params_arr["user_name"]);
                }
                if (isset($params_arr["mobile_number"]))
                {
                    $this->db->set("vMobileNo", $params_arr["mobile_number"]);
                }
                if (isset($input_params["business_type_id"]))
                {
                    $this->db->set("iBusinessTypeId", $input_params["business_type_id"]);
                }
                if (isset($input_params["business_name"]))
                {
                    $this->db->set("vBusinessName", $input_params["business_name"]);
                }
                if (isset($input_params["position"]))
                {
                    $this->db->set("vPosition", $input_params["position"]);
                }
                $res = $this->db->update("review");
            }
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
}

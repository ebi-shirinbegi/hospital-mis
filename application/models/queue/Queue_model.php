<?php
/**
*@author: Aziz Matin
*@created Date: 20-Feb-2019
**/
defined('BASEPATH') OR exit('No direct script access allowed');
class Queue_model extends CI_Model {
    //construct function
	function __construct()
	{
		parent::__construct();
	}

	//insert and update function
	function update($table = "",$urn = 0, $data = array(), $user_id = 0, $action = "INSERT")
    {
		if (is_array($data) AND !empty($data)) {
			$table_urn = array('urn' => $urn);
			$data = array_merge($data,$table_urn);
			//echo "<pre>";print_r($data);exit;
			$this->db->trans_start();
			$log_data = array();
			if($user_id != 0){
				$log = array(
					'urn' => $urn,
					'user_id' => $user_id,
					'logid' => $urn,
					'action' => $action,
					'logdate' => date('Y-m-d H:i:s'),
					'logip' => $_SERVER['REMOTE_ADDR']
				);
			}else{
				$log = array();
				//$log = array('urn' => $urn);
			}
			$log_data = array_merge($data,$log);
			if($action == "UPDATE"){
				$this->db->where('urn',$urn);
				$this->db->update($table,$data);
			}elseif($action == "INSERT"){
				$this->db->insert($table,$data);
			}
			$this->db->insert($table.'_log',$log_data);
			$this->db->trans_complete();
			return TRUE;
		}else{
			return FALSE;
		}
	}
    
    //select todays queue records
    function queue_date()
    {
        $this->db->select('*');
        $this->db->from('queue_date');
        $this->db->where ('registerdate',date("Y-m-d"));
        $query = $this->db->get();  
        //echo $this->db->last_query();exit;
        if($query && $query->num_rows()>0){
            return $query->result();
        }  
        else{
            return false;
        }
    }
    
    //check if record exist;
    function checkdate($date = "")
    {
        $this->db->select('*');
        $this->db->from('queue_date');
        $this->db->where ('registerdate', $date);
        $query = $this->db->get();  
        if($query && $query->num_rows>0){
            return $query->result();
        }  
        else{
            return false;
        }
    }
    
    //check if queue exist
    function check_queue($name = "", $f_name = "")
    {
        $this->db->select('*');
        $this->db->from('queue');
        $this->db->where ('name', $name);
        $this->db->where ('f_name', $f_name);
        $query = $this->db->get();  
        if($query && $query->num_rows()>0){
            return $query->result();
        }  
        else{
            return false;
        }   
    }
    
    //getTodayQueue
    function getTodayQueue($list_type = "nothing",$per_page = 0,$ofset = 0)
    {
        if($list_type == "nothing"){
            $list_type = 0;
        }
        $this->db->select('*');
        $this->db->from('queue_date');
        $this->db->where ('registerdate',date("Y-m-d"));
        $query = $this->db->get();  
        if($query && $query->num_rows()>0){
            $parent_urn = $query->result()[0]->urn;
            $this->db->select('*');
            $this->db->from('queue');
            $this->db->where ("parent_urn",$parent_urn);
            $this->db->where ("visited",$list_type);
            if($per_page != 0){
                $this->db->limit($per_page,$ofset);
            }
            $q = $this->db->get();
            //echo $this->db->last_query();exit; 
            if($q){
                return $q->result();
            }else{
                return false;
            }
        }  
        else{
            return false;
        }   
    }
    
    //visit done
    function visit($urn = "")
    {                       
        if($urn != ""){
            $data= array(
                "visited" => 1
            );   
            $this->db->where("urn",$urn);
            $query = $this->db->update("queue", $data);
            if($query && $query->num_rows>0){
                return true;
            }else{
                return false;
            }
        }
    }
    
    //checkIfTaken function
    function checkIfTaken($no = "")
    {
        $this->db->select('*');
        $this->db->from('queue_date');
        $this->db->where ('registerdate',date("Y-m-d"));
        $query = $this->db->get();  
        //echo $this->db->last_query();exit;
        if($query){
            $parent_urn = $query->result()[0]->urn;
            $this->db->select('*');
            $this->db->from('queue');
            $this->db->where ("no",$no);
            $this->db->where ("parent_urn",$parent_urn);
            //$this->db->where ("visited",0);
            $q = $this->db->get();
            //echo $this->db->last_query();exit;
            if($q && $q->num_rows()>0){
                return true;
            }else{
                return false;
            }
            
        }  
        else{
            return false;
        }
    }
    
    //check if registered
    function checkIfRegistered($queue_urn = 0)
    {
        $this->db->select('*');
        $this->db->from('register');
        if($queue_urn != 0){
            $this->db->where ('queue_urn', $queue_urn);
        }
        $query = $this->db->get();  
        if($query && $query->num_rows()>0){
            return $query->result();
        }  
        else{
            return false;
        }
    }
    
}

 ?>
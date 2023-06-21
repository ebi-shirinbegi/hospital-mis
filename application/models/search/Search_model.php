<?php
/**
*@author: Aziz Matin
*@created Date: 20-Feb-2019
**/
defined('BASEPATH') OR exit('No direct script access allowed');
class Search_model extends CI_Model {
    //construct function
    function __construct()
    {
        parent::__construct();
    }

    
    //get all records
    function getAllrecords($per_page = 0, $ofset = 0)
    {
        $this->db->select('*');
        $this->db->from('register');
        if($per_page != 0){
            $this->db->limit($per_page,$ofset);
        }
        $this->db->order_by('id','DESC');
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
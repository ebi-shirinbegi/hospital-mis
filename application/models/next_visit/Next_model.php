<?php
/**
*@author: Aziz Matin
*@created Date: 20-Feb-2019
**/
defined('BASEPATH') OR exit('No direct script access allowed');
class Next_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    
    //select todays queue records
    function todayVisits()
    {
        $this->db->select('*');
        $this->db->from('register');
        $this->db->where ('next_visit',date("Y-m-d"));
        $this->db->order_by('next_time','ACS');
        $query = $this->db->get();  
        //echo $this->db->last_query();exit;
        if($query && $query->num_rows()>0){
            return $query->result();
        }  
        else{
            return false;
        }
    }
    
    //check if today visit is done
    function isDone($p_id = 0)
    {
        $this->db->select('*');
        $this->db->from('register');
        //$this->db->where ('next_visit',date("Y-m-d"));
        $this->db->where ('patient_id',$p_id);
        $this->db->where ('registerdate',date("Y-m-d"));
        $this->db->order_by('next_time','ACS');
        $query = $this->db->get();  
        //echo $this->db->last_query();exit;
        if($query && $query->num_rows()>0){
            return TRUE;
        }  
        else{
            return false;
        }
    }
    
    //get teeth records
    function getAllrecordsxxx($per_page = 0, $ofset = 0)
    {
        $this->db->select('*');
        $this->db->from('register');
        $this->db->where ('next_visit',date("Y-m-d"));
        $this->db->order_by('next_time','ACS'); 
        if($per_page != 0){
            $this->db->limit($per_page,$ofset);
        }
        $query = $this->db->get();  
        if($query && $query->num_rows()>0){
            return $query->result();
        }  
        else{
            return false;
        }
    }
    
    //get all records
    function getAllrecords($offset=0,$limit=0,$isTotal=FALSE)
    {
        $allSql             = "1";
        if(!$isTotal)
        {
            $this->db->select
                            ('
                              t1.*
                            ');
        }
        $this->db->from('
          register       AS t1
        ');
        $this->db->where ('next_visit',date("Y-m-d"));
        if(!$isTotal)
        {
            $this->db->limit($limit, $offset);

            //order the records
            $this->db->order_by('next_time','ACS');
            $this->db->group_by("t1.urn");

            $query=$this->db->get();
            //echo "<pre/>".$this->db->last_query(); exit;

            if($query->num_rows() > 0)
            {
                return $query;
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return $this->db->count_all_results();
        }
    }
    
    /**
    * @desc This function's usage is to search emergency call report
    */
    function search_records($offset=0,$limit=0,$isTotal=FALSE)
    {
        $allSql             = "1";
        //check for register date
        $allSql = searchdate
        (
            $this->input->post('fday'),
            $this->input->post('fmonth'),
            $this->input->post('fyear'),
            '00',
            '00',
            '0000',
            'date_format(t1.next_visit,"%Y-%m-%d")',
            'dr'
        );
        if(!$isTotal)
        {
            $this->db->select
                            ('
                              t1.*
                            ');
        }
        
        $this->db->from('
          register       AS t1
        ');
        
        if($allSql == '1'){
            $this->db->where ('next_visit',date("Y-m-d")); 
        }else{
            $this->db->where($allSql,null,false); 
        }
        
        if(!$isTotal)
        {
            $this->db->limit($limit, $offset);

            //order the records
            $this->db->order_by("t1.registerdate","ASC");
            $this->db->group_by("t1.urn");

            $query=$this->db->get();
            //echo "<pre/>".$this->db->last_query(); exit;

            if($query->num_rows() > 0)
            {
                return $query;
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return $this->db->count_all_results();
        }
    }
}
<?php
/**
*@author: Aziz Matin
*@created Date: 20-Feb-2019
**/
defined('BASEPATH') OR exit('No direct script access allowed');
class Xray_model extends CI_Model {
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
          xray       AS t1
        ');
        if(!$isTotal)
        {
            $this->db->limit($limit, $offset);

            //order the records
            $this->db->order_by("t1.registerdate","DESC");
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
    
    //get view records
    function getViewRecords($urn = 0)
    {
        $this->db->select('*');
        $this->db->from('xray');
        $this->db->where ('urn', $urn);
        $query = $this->db->get();  
        if($query && $query->num_rows()>0){
            return $query->result();
        }  
        else{
            return false;
        }
    }
    
    //get xray records
    function getXraySub($xray_urn = 0)
    {
        $this->db->select('*');
        $this->db->from('xray_sub');
        $this->db->where ('xray_urn', $xray_urn);
        $this->db->order_by('urn', "asc");
        $query = $this->db->get();  
        if($query && $query->num_rows()>0){
            return $query->result();
        }  
        else{
            return false;
        }
    }
    
    /**
    * @desc This function's usage is to search emergency call report
    */
    function search_records($offset=0,$limit=0,$isTotal=FALSE)
    {
        $allSql             = "1";
        //check for register date
        $allSql .= " AND ".searchdate
        (
            $this->input->post('fday'),
            $this->input->post('fmonth'),
            $this->input->post('fyear'),
            $this->input->post('tday'),
            $this->input->post('tmonth'),
            $this->input->post('tyear'),
            'date_format(t1.registerdate,"%Y-%m-%d")',
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
          xray   AS  t1
        ');
        $this->db->where($allSql,null,false);
        
        // name
        if($this->input->post('patient_id') != '')
        {
            $this->db->where('t1.patient_id',$this->input->post('patient_id'));
        }
        //echo $this->input->post('drug_type');exit;
        // name
        if($this->input->post('name') != '')
        {
            $this->db->where('t1.name',$this->input->post('name'));
        }
        if(!$isTotal)
        {
            $this->db->limit($limit, $offset);

            //order the records
            $this->db->order_by("t1.registerdate","DESC");
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
    
    //get avialab drugs static
    function getStaticData($table,$code)
    {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where("code",$code);  
        $query = $this->db->get();  
        if($query && $query->num_rows()>0){
            return $query->result();
        }  
        else{
            return false;
        }    
    }
    
    //get register data
    function getRegData($p_id)
    {
        $this->db->select('*');
        $this->db->from('register');
        if($p_id){
            $this->db->where ('patient_id', $p_id); 
        }
        $this->db->order_by('id','DESC');
        $query = $this->db->group_by("patient_id");  
        $query = $this->db->get();  
        if($query && $query->num_rows()>0){
            return $query->result();
        }  
        else{
            return false;
        }
    }
    
    //get register data
    function getRegDataNew($p_id)
    {
        $this->db->select('*');
        $this->db->from('xray');
        if($p_id){
            $this->db->where ('patient_id', $p_id); 
        }
        $this->db->order_by('id','DESC');
        $query = $this->db->group_by("patient_id");  
        $query = $this->db->get();  
        if($query && $query->num_rows()>0){
            return $query->result();
        }  
        else{
            return false;
        }
    }
    
    //get static name by urn
    function getStaticName($urn,$code)
    {
        $this->db->select('*');
        $this->db->from("stable");
        $this->db->where("urn",$urn);  
        $this->db->where("code",$code);  
        $query = $this->db->get();  
        if($query && $query->num_rows()>0){
            return $query->result();
        }  
        else{
            return false;
        }    
    }
    
    //check if exist
    function usedExist($parent_urn=0,$urn = 0,$table = "",$field = 'parent_urn')
    {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where ($field, $parent_urn);
        $this->db->where ('urn', $urn);
        $query = $this->db->get();  
        if($query && $query->num_rows()>0){
            return TRUE;
        }  
        else{
            return FALSE;
        }
    }
    
    //get all sub records
    function getAllSubRecordsxxx($per_page = 0, $ofset = 0)
    {
        $this->db->select('*');
        $this->db->from('xray_sub');
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
    
    function getAllSubRecords($offset=0,$limit=0,$isTotal=FALSE)
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
          xray_sub       AS t1
        ');
        if(!$isTotal)
        {
            $this->db->limit($limit, $offset);

            //order the records
            $this->db->order_by("t1.registerdate","DESC");
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
    
    //get sub view record
    function getSubViewRecords($urn = 0)
    {
        $this->db->select('*');
        $this->db->from('xray_sub');
        $this->db->where ('urn', $urn);
        $query = $this->db->get();  
        if($query && $query->num_rows()>0){
            return $query->result();
        }  
        else{
            return false;
        }
    }
    
    /**
    * @desc This function's usage is to search emergency call report
    */
    function search_material_records($offset=0,$limit=0,$isTotal=FALSE)
    {
        $allSql             = "1";
        //check for register date
        $allSql .= " AND ".searchdate
        (
            $this->input->post('fday'),
            $this->input->post('fmonth'),
            $this->input->post('fyear'),
            $this->input->post('tday'),
            $this->input->post('tmonth'),
            $this->input->post('tyear'),
            'date_format(t1.registerdate,"%Y-%m-%d")',
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
          xray_sub   AS  t1
        ');
        $this->db->where($allSql,null,false);
        
        // name
        if($this->input->post('xray_type') != '' AND $this->input->post('xray_type') != '0')
        {
            $this->db->where('t1.xray_type',$this->input->post('xray_type'));
        }
        //echo $this->input->post('drug_type');exit;
        // name
        if($this->input->post('price') != '')
        {
            $this->db->where('t1.price',$this->input->post('price'));
        }
        if(!$isTotal)
        {
            $this->db->limit($limit, $offset);

            //order the records
            $this->db->order_by("t1.id","DESC");
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
    * @desc check if the patient xray done
    */
    function check_xray($p_id)
    {
        $this->db->select('urn,patient_id,fee,remains');
        $this->db->from('xray');
        $this->db->where ('patient_id', $p_id);
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
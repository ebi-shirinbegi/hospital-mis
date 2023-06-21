<?php
/**
*@author: Aziz Matin
*@created Date: 03-July-2019
**/
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    /**
    * @desc construct function
    */
    function __construct()
    {
        parent::__construct();
        //load helper
        $this->load->helper('template');
        $this->load->helper('jdf');
        $this->load->library('pagination');
        $this->load->library('Ajax_pagination');
        $this->load->library('Ajax_pagination_new');
        $this->load->library('Clean_encrypt');
        $this->load->library('Amc_auth');
        $this->amc_auth->is_logged_in();  
        //helper
        $this->load->helper('datecheck');
        //load libraries
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->library('user_agent');
        //load language file
        $this->lang->load("home");
        $this->lang->load("global");
        //load models
        $this->load->model(array('xray/xray_model','urn_model','document/document_model','register/register_model'));
    }

    /**
    * @desc index function
    */
    public function index()
    {
        redirect('xray/home/listRecords');
    }
    
    /**
    * @desc xray list function
    */
    function listRecords($page = 0)
    {
        /*******************************************************
        * @desc ************AJAX PAGINATION*********************
        ********************************************************/
        $str_post_str  = '&ajax=1';  
        $recpage  = $this->config->item('recordperpage');//number of records per page
        $starting = $this->input->post('starting');         //get counter which page record
        //if its the first page than show starting from 0
        if(!$starting)
        {
            $starting =0;
        }
        
        $records   = $this->xray_model->getAllrecords($starting,$recpage,FALSE);
        $rec_total = $this->xray_model->getAllrecords($starting,$recpage,TRUE);
        if($records)
        {
            //echo "<pre/>"; print_r($records->result()); exit;
            $data['records'] = $records;
        }
        else
        {
            $data['records'] = '';
        }
        $this->ajax_pagination_new->make_search(
            $rec_total,
            $starting,
            $recpage,
            $this->lang->line('first'),
            $this->lang->line('last'),
            $this->lang->line('prev'),
            $this->lang->line('next'),
            $this->lang->line('page'),
            $this->lang->line('of'),
            $this->lang->line('total'),
            base_url()."index.php/xray/home/listRecords",
            'list_div1',
            $str_post_str
        );
         
        $data["search"] = FALSE;
        $data['page']   = $starting;
        $data['total']  = $this->ajax_pagination_new->total;
        $data['links']  = $this->ajax_pagination_new->anchors; 
    
        //date and time dropdown data
        $data['days']           = $this->getDateDetails('days');
        $data['months']         = $this->getDateDetails('months');
        $data['years']          = $this->getDateDetails('years');
        $data['title']          = $this->lang->line("xray_list"); 
        $data['filter']         = $this->load->view("filter/xray_filter",$data,true);
        if($this->input->post('ajax')==1)
        {
            $data["search"] = TRUE;
            $this->load->view('filter/xray_filter_list',$data);
        }else{                                                   
            banner();
            sidebar();
            $modal = modal_popup();
            $content = $this->load->view('xray/xray_list',$data,true); 
            content($content);
            footer();
        }
    }
    
    /**
    * @desc add to the xray
    */
    function xray_add($queue_urn = 0)
    {
        if(0){
            echo "You are not loged in";exit;
        }else{
            $this->form_validation->set_rules('p_id', 'p_id', 'trim');
            $this->form_validation->set_rules('name', 'name', 'trim');
            if($this->form_validation->run() == FALSE){
                $data = array(); 
                //patient id dropdown data
                $recortds = $this->xray_model->getRegData(false);
                if($recortds){
                    $data['patientid'] = $recortds;
                }else{
                    $data['patientid'] = "";
                }
                //get the static data
                $xrayStatic = $this->xray_model->getStaticData("stable","xray");
                if($xrayStatic){
                    $data['staticData'] = $xrayStatic;
                }else{
                    $data['staticData'] = "";    
                }
                //echo "<pre>";print_r($xrayStatic);exit;
                //load views and templates
                $data['list_type']      = 0;
                $data['title']          = $this->lang->line('xray_add');
                $data['teeth_add']      = $this->load->view("xray/teeth_add",$data,true);
                banner();
                sidebar();
                $modal = modal_popup();
                $content = $this->load->view("xray/xray_add",$data,true);
                content($content);
                footer();
            }
            else{
                $p_id       = $this->input->post('p_id');    
                $name       = $this->input->post('name');    
                $fee        = $this->input->post('fee');    
                $remains    = $this->input->post('remains');    
                
                $urn = $this->urn_model->getURN('xray','urn');
                $data = array(
                    "patient_id"            => $p_id,
                    "name"                  => $name,
                    "fee"                   => $fee,
                    "remains"               => $remains,
                    "registerdate"          => date("Y-m-d")
                );
                //echo "<pre>";print_r($data);exit;
                $update = $this->xray_model->update('xray',$urn,$data,1000001,"INSERT",$p_id);
                //teeth number
                if($update){
                    $this->teeth_add($urn);        
                }
                //spent drugs
                if($update){
                    redirect("xray/home/view/".$this->clean_encrypt->encode($urn),'refresh');   
                }
            }     
        }
    }
    
    /**
    * @desc add to the xray
    */
    function xray_add_new($queue_urn = 0)
    {
        if(0){
            echo "You are not loged in";exit;
        }else{
            $this->form_validation->set_rules('p_id', 'p_id', 'trim');
            $this->form_validation->set_rules('name', 'name', 'trim');
            if($this->form_validation->run() == FALSE){
                $data = array(); 
                //patient id dropdown data
                $recortds = $this->xray_model->getRegDataNew(false);
                if($recortds){
                    $data['patientid'] = $recortds;
                }else{
                    $data['patientid'] = "";
                }
                //get the static data
                $xrayStatic = $this->xray_model->getStaticData("stable","xray");
                if($xrayStatic){
                    $data['staticData'] = $xrayStatic;
                }else{
                    $data['staticData'] = "";    
                }
                //echo "<pre>";print_r($xrayStatic);exit;
                //load views and templates
                $data['list_type']      = 1;
                $data['title']          = $this->lang->line('xray_add');
                $data['teeth_add']      = $this->load->view("xray/teeth_add",$data,true);
                banner();
                sidebar();
                $modal = modal_popup();
                $content = $this->load->view("xray/xray_add_new",$data,true);
                content($content);
                footer();
            }
            else{
                
                $p_id       = $this->input->post('p_id'); 
                $name       = $this->input->post('name');
                $fee        = $this->input->post('fee');    
                $remains    = $this->input->post('remains'); 
                $ps_id = 0;
                if($p_id != '0' && $p_id != ''){           
                    $ps_id = $p_id;
                }else{
                    $p_urn = $this->urn_model->getURN('xray','urn');
                    $id_date = date("Y-m-d");
                    $gre_date = explode("-",$id_date);
                    $theYear = gregorian_to_jalali($gre_date[0],$gre_date[1],$gre_date[2],"/");
                    $jlali_year = explode("/",$theYear);
                    //$p_id = $this->urn_model->getPatientId('register','patient_id');
                    $ps_id = "X_NDC".$jlali_year[0].$p_urn;
                }    
                //if($ps_id != '' && $ps_id != 0 && $name != ''){
                    $urn = $this->urn_model->getURN('xray','urn');
                    $data = array(
                        "patient_id"            => $ps_id,
                        "name"                  => $name,
                        "fee"                   => $fee,
                        "remains"               => $remains,
                        "registerdate"          => date("Y-m-d")
                    );
                    //echo "<pre>";print_r($data);exit;
                    $update = $this->xray_model->update('xray',$urn,$data,1000001,"INSERT",$ps_id);
                    //teeth number
                    if($update){
                        $this->teeth_add($urn);        
                    }
                    //spent drugs
                    if($update){
                            redirect("xray/home/view/".$this->clean_encrypt->encode($urn),'refresh');
                    }   
                /*}else{
                    echo "name: ".$name.", p_id: $ps_id";exit;
                }*/
            }     
        }
    }
    
    /**
    * @desc xray teeth number function
    */
    function teeth_add($xray_urn = 0)
    {
        if($xray_urn != 0){
            /****************************fill teeth starts*/
            //top right teeth
            $topr1       = $this->input->post('topr1');    
            $topr2       = $this->input->post('topr2');    
            $topr3       = $this->input->post('topr3');    
            $topr4       = $this->input->post('topr4');    
            $topr5       = $this->input->post('topr5');    
            $topr6       = $this->input->post('topr6');    
            $topr7       = $this->input->post('topr7');    
            $topr8       = $this->input->post('topr8');  
            //top left teeth  
            $topl1     = $this->input->post('topl1');
            $topl2     = $this->input->post('topl2');
            $topl3     = $this->input->post('topl3');
            $topl4     = $this->input->post('topl4');
            $topl5     = $this->input->post('topl5');
            $topl6     = $this->input->post('topl6');
            $topl7     = $this->input->post('topl7');
            $topl8     = $this->input->post('topl8');
            //bottom right teeth       
            $bottomr1    = $this->input->post('bottomr1'); 
            $bottomr2    = $this->input->post('bottomr2'); 
            $bottomr3    = $this->input->post('bottomr3'); 
            $bottomr4    = $this->input->post('bottomr4'); 
            $bottomr5    = $this->input->post('bottomr5'); 
            $bottomr6    = $this->input->post('bottomr6'); 
            $bottomr7    = $this->input->post('bottomr7'); 
            $bottomr8    = $this->input->post('bottomr8'); 
            //bottom left teeth      
            $bottoml1      = $this->input->post('bottoml1');     
            $bottoml2      = $this->input->post('bottoml2');     
            $bottoml3      = $this->input->post('bottoml3');     
            $bottoml4      = $this->input->post('bottoml4');     
            $bottoml5      = $this->input->post('bottoml5');     
            $bottoml6      = $this->input->post('bottoml6');     
            $bottoml7      = $this->input->post('bottoml7');     
            $bottoml8      = $this->input->post('bottoml8');
            if($topl1 != '' || $topl2 != '' || $topl3 != '' || $topl4 != '' || $topl5 != '' || $topl6 != '' || $topl7 != '' || $topl8 != '' || $topr1 != '' || $topr2 != '' || $topr3 != '' || $topr4 != '' || $topr5 != '' || $topr6 != '' || $topr7 != '' || $topr8 != '' || $bottomr1 != '' || $bottomr2 != '' || $bottomr3 != '' || $bottomr4 != '' || $bottomr5 != '' || $bottomr6 != '' || $bottomr7 != '' || $bottomr8 != '' || $bottoml1 != '' || $bottoml2 != '' || $bottoml3 != '' || $bottoml4 != '' || $bottoml5 != '' || $bottoml6 != '' || $bottoml7 != '' || $bottoml8 != '')
            {
                $ill_type = 6;
                $xray_data = array(
                    "register_urn"              => $xray_urn,
                    "ill_type"                  => $ill_type,
                    
                    "topright1"                 => $topr1,
                    "topright2"                 => $topr2,
                    "topright3"                 => $topr3,
                    "topright4"                 => $topr4,
                    "topright5"                 => $topr5,
                    "topright6"                 => $topr6,
                    "topright7"                 => $topr7,
                    "topright8"                 => $topr8,
                    
                    "topleft1"                  => $topl1,
                    "topleft2"                  => $topl2,
                    "topleft3"                  => $topl3,
                    "topleft4"                  => $topl4,
                    "topleft5"                  => $topl5,
                    "topleft6"                  => $topl6,
                    "topleft7"                  => $topl7,
                    "topleft8"                  => $topl8,
                    
                    
                    "bottomright1"              => $bottomr1,
                    "bottomright2"              => $bottomr2,
                    "bottomright3"              => $bottomr3,
                    "bottomright4"              => $bottomr4,
                    "bottomright5"              => $bottomr5,
                    "bottomright6"              => $bottomr6,
                    "bottomright7"              => $bottomr7,
                    "bottomright8"              => $bottomr8,
                    
                    "bottomleft1"               => $bottoml1,
                    "bottomleft2"               => $bottoml2,
                    "bottomleft3"               => $bottoml3,
                    "bottomleft4"               => $bottoml4,
                    "bottomleft5"               => $bottoml5,
                    "bottomleft6"               => $bottoml6,
                    "bottomleft7"               => $bottoml7,
                    "bottomleft8"               => $bottoml8
                );
                //echo "<pre>";print_r($xray_data);exit;
                $teeth_urn = $this->urn_model->getURN('teeth','urn');
                $update = $this->register_model->update('teeth',$teeth_urn,$xray_data,1000001,"INSERT");    
            } 
            /****************************fill teeth ends*/
        }
    }
    
    /**
    * @desc edit registered data
    */
    function edit($urn = 0)
    {
        if(0){
            echo "You are not loged in";exit;
        }else{
            $dec_urn = $this->clean_encrypt->decode($urn);
            $this->form_validation->set_rules('p_id', 'p_id', 'trim');
            $this->form_validation->set_rules('name', 'name', 'trim');
            if($this->form_validation->run() == FALSE){
                $data = "";
                $teeth_records = "";
                //get the main xray record
                $records = $this->xray_model->getViewRecords($dec_urn);
                if($records){
                    //get the teeth record
                    $teeth_records  = $this->register_model->getTeethRecords($records[0]->urn,6); 
                    //echo "<pre>";print_r($teeth_records);exit;
                    if($teeth_records){
                        $data['teeth_record'] = $teeth_records;
                        $data['edit'] = true;
                    }else{
                        $data['teeth_record'] = "";
                        $data['edit'] = false;
                    }
                    $data['patientid'] = $records;
                }else{
                    $data['patientid'] = ""; 
                    $data['teeth_record'] = ""; 
                    $data['edit'] = false;
                }
                //get the xray subrecord
                if($records){                                                    
                    $xray_sub  = $this->xray_model->getXraySub($records[0]->urn);
                    $data["record"]             = $records[0];   
                    $data["sub_record"]         = $xray_sub;   
                }else{
                    $data["record"]             = false;   
                    $data["sub_record"]         = false;
                }
                
                //load the teeth sub views
                $data['teeth_edit'] = $this->load->view("xray/teeth_edit",$data,true); 
                $data['teeth_add'] = $this->load->view("xray/teeth_add",$data,true); 
                
                //get the static data
                $xrayStatic = $this->xray_model->getStaticData("stable","xray");
                if($xrayStatic){
                    $data['staticData'] = $xrayStatic;
                }else{
                    $data['staticData'] = "";    
                }
                //top title
                $data['title'] = $this->lang->line("xray_edit");
                $records = false;
                $records = $this->xray_model->getViewRecords($dec_urn);
                $data["record"]             = $records[0];
                $data["enc_urn"]            = $urn;
                
                //load views and templates
                banner();
                sidebar();
                $modal = modal_popup();
                //the views if the record is exist
                $content = $this->load->view("xray/xray_edit",$data,true);
                content($content);
                footer(); 
            }
            else{
                $p_id       = $this->input->post('p_id');    
                $name       = $this->input->post('name'); 
                $fee        = $this->input->post('fee');    
                $remains    = $this->input->post('remains');   
                
                $data = array(
                    "patient_id"            => $p_id,
                    "name"                  => $name, 
                    "fee"                   => $fee,
                    "remains"               => $remains,
                    "registerdate"          => date("Y-m-d")
                );
                //echo "<pre>";print_r($data);exit;
                $update = $this->xray_model->update('xray',$dec_urn,$data,1000001,"UPDATE",$p_id);
                //teeth number
                if($update){
                    $this->teeth_edit($dec_urn);        
                }
                //spent drugs
                if($update){
                    redirect("xray/home/view/".$urn,'refresh');    
                }     
            }
        }
    }
    
    /**
    * @desc fill teeth edit function
    */
    function teeth_edit($xray_urn = 0)
    {
        if($xray_urn != 0){
            /****************************fill teeth starts*/
            $urn         = $this->input->post('teeth_urn');
            //top right teeth               
            $topr1       = $this->input->post('topr1');    
            $topr2       = $this->input->post('topr2');    
            $topr3       = $this->input->post('topr3');    
            $topr4       = $this->input->post('topr4');    
            $topr5       = $this->input->post('topr5');    
            $topr6       = $this->input->post('topr6');    
            $topr7       = $this->input->post('topr7');    
            $topr8       = $this->input->post('topr8');  
            //top left teeth  
            $topl1     = $this->input->post('topl1');
            $topl2     = $this->input->post('topl2');
            $topl3     = $this->input->post('topl3');
            $topl4     = $this->input->post('topl4');
            $topl5     = $this->input->post('topl5');
            $topl6     = $this->input->post('topl6');
            $topl7     = $this->input->post('topl7');
            $topl8     = $this->input->post('topl8');
            //bottom right teeth       
            $bottomr1    = $this->input->post('bottomr1'); 
            $bottomr2    = $this->input->post('bottomr2'); 
            $bottomr3    = $this->input->post('bottomr3'); 
            $bottomr4    = $this->input->post('bottomr4'); 
            $bottomr5    = $this->input->post('bottomr5'); 
            $bottomr6    = $this->input->post('bottomr6'); 
            $bottomr7    = $this->input->post('bottomr7'); 
            $bottomr8    = $this->input->post('bottomr8'); 
            //bottom left teeth      
            $bottoml1      = $this->input->post('bottoml1');     
            $bottoml2      = $this->input->post('bottoml2');     
            $bottoml3      = $this->input->post('bottoml3');     
            $bottoml4      = $this->input->post('bottoml4');     
            $bottoml5      = $this->input->post('bottoml5');     
            $bottoml6      = $this->input->post('bottoml6');     
            $bottoml7      = $this->input->post('bottoml7');     
            $bottoml8      = $this->input->post('bottoml8');
            if($topl1 != '' || $topl2 != '' || $topl3 != '' || $topl4 != '' || $topl5 != '' || $topl6 != '' || $topl7 != '' || $topl8 != '' || $topr1 != '' || $topr2 != '' || $topr3 != '' || $topr4 != '' || $topr5 != '' || $topr6 != '' || $topr7 != '' || $topr8 != '' || $bottomr1 != '' || $bottomr2 != '' || $bottomr3 != '' || $bottomr4 != '' || $bottomr5 != '' || $bottomr6 != '' || $bottomr7 != '' || $bottomr8 != '' || $bottoml1 != '' || $bottoml2 != '' || $bottoml3 != '' || $bottoml4 != '' || $bottoml5 != '' || $bottoml6 != '' || $bottoml7 != '' || $bottoml8 != '')
            {
                $ill_type = 6;
                $ifExist = $this->register_model->teethExist($xray_urn,$urn, $ill_type);
                $xray_data = array(
                    "register_urn"              => $xray_urn,
                    "ill_type"                  => $ill_type,
                    
                    "topright1"                 => $topr1,
                    "topright2"                 => $topr2,
                    "topright3"                 => $topr3,
                    "topright4"                 => $topr4,
                    "topright5"                 => $topr5,
                    "topright6"                 => $topr6,
                    "topright7"                 => $topr7,
                    "topright8"                 => $topr8,
                    
                    "topleft1"                  => $topl1,
                    "topleft2"                  => $topl2,
                    "topleft3"                  => $topl3,
                    "topleft4"                  => $topl4,
                    "topleft5"                  => $topl5,
                    "topleft6"                  => $topl6,
                    "topleft7"                  => $topl7,
                    "topleft8"                  => $topl8,
                    
                    "bottomright1"              => $bottomr1,
                    "bottomright2"              => $bottomr2,
                    "bottomright3"              => $bottomr3,
                    "bottomright4"              => $bottomr4,
                    "bottomright5"              => $bottomr5,
                    "bottomright6"              => $bottomr6,
                    "bottomright7"              => $bottomr7,
                    "bottomright8"              => $bottomr8,
                    
                    "bottomleft1"               => $bottoml1,
                    "bottomleft2"               => $bottoml2,
                    "bottomleft3"               => $bottoml3,
                    "bottomleft4"               => $bottoml4,
                    "bottomleft5"               => $bottoml5,
                    "bottomleft6"               => $bottoml6,
                    "bottomleft7"               => $bottoml7,
                    "bottomleft8"               => $bottoml8
                );
                //echo "<pre>";print_r($xray_data);exit;
                if($ifExist){
                    $update = $this->register_model->updateTeeth('teeth',$urn,$xray_data,1000001,"UPDATE",$ill_type); 
                }else{
                    $fill_urn = $this->urn_model->getURN('teeth','urn');
                    $update = $this->register_model->update('teeth',$fill_urn,$xray_data,1000001,"INSERT"); 
                }   
            } 
            /****************************fill teeth ends*/
        }
    }
    
    /**
    * @desc view function
    */
    function view($urn = 0)
    {
        if(0){
            echo "You are not loged in.";exit;
        }else{
            $dec_urn = $this->clean_encrypt->decode($urn);
            $data = "";
            $records = false;
            //top title
            $data['title'] = $this->lang->line("xray_view");
            $records = $this->xray_model->getViewRecords($dec_urn);
            if($records){
                $teeth_records  = $this->register_model->getTeethRecords($records[0]->urn,6);
                if($teeth_records){
                    $data["teeth_record"]       = $teeth_records;
                    $data['view'] = true;
                }else{
                    $data["teeth_record"]       = "";
                    $data['view'] = false; 
                }
            }else{
                $data["teeth_record"] = "";
                $data['view'] = false; 
            }
            $data['teeth_view'] = $this->load->view("xray/teeth_view",$data,true);
            ///echo "<pre>";print_r($records);exit;
            if($records){                                                    
                $xray_sub  = $this->xray_model->getXraySub($records[0]->urn);
                $data["record"]             = $records[0];   
                $data["sub_record"]         = $xray_sub;   
            }else{
                $data["record"]             = false;   
                $data["sub_record"]         = false;
            }
            //load views and templates
            banner();
            sidebar();
            $content = $this->load->view("xray/xray_view",$data,true);
            content($content);
            footer();   
        }
    }
    
    /**
    * @desc multiple function
    */
    function multiple()
    {
        //echo "<pre>";print_r($_POST);exit;
        $counter        = $this->input->post('no');
        $xray_type      = $this->lang->line('xray_type');
        $amount_lable   = $this->lang->line('amount');
        $drug_type      = $this->lang->line('drug_type');
        $select         = $this->lang->line('select');
        $item_price     = $this->lang->line('item_price');
        $total_amount   = $this->lang->line('total');
        $base_url       = base_url();
        $staticData     = $this->xray_model->getStaticData("stable","xray");  
        $content        = "";
        
        if($counter >0){
            $content .= "<table class=\"table\" id=\"imRemovable$counter\">
                            <tr>
                                <td scope=\"col\" width=\"33%\" class=\"iEntry\" colspan=\"4\">
                                    <span class=\"btn btn-danger ino\">$counter</span><input type=\"button\" id=\"rm\" class=\"btn btn-danger\" value=\"-\" onclick=\"javascript:removeElement('imRemovable$counter','$counter');\" >
                                </td>
                            </tr>
                            <tr id=\"tardivid\"> 
                                <td scope=\"col\" width=\"25%\" class=\"iEntry\">
                                    <div class=\"inputfield\">
                                        <div class=\"rLabel\">
                                            <label class=\"\" for=\"textinput\">$xray_type : </label>                
                                        </div>
                                        <div class=\"textfield btm20padding\">
                                            <select id=\"xray_type$counter\" name=\"xray_type[]\" class=\"form-control nopadding\">
                                                <option value=\"0\">$select</option>";
                                                if($staticData){
                                                    foreach($staticData as $static){
                                                        $content .= "<option value=\"$static->urn\">$static->name</option>";
                                                    }
                                                }
                                            $content .="</select>
                                        </div>
                                    </div>
                                </td>
                                <td scope=\"col\" width=\"25%\" class=\"iEntry\">
                                    <div class=\"inputfield\">
                                        <div class=\"rLabel\">
                                            <label class=\"\" for=\"textinput\">$item_price : </label>                
                                        </div>
                                        <div class=\"textfield btm20padding\">
                                            <input id=\"price$counter\" name=\"price[]\" type=\"text\" placeholder=\"$item_price\" class=\"form-control iInput\" onkeyup=\"totalThePrice('price$counter','amount$counter','total$counter')\">     
                                        </div>
                                    </div>    
                                </td>
                                <td scope=\"col\" width=\"25%\" class=\"iEntry\">
                                    <div class=\"inputfield\">
                                        <div class=\"rLabel\">
                                            <label class=\"\" for=\"textinput\">$amount_lable : </label>                
                                        </div>
                                        <div class=\"textfield btm20padding\">
                                              <input id=\"amount$counter\" name=\"amount[]\" type=\"text\" placeholder=\"$amount_lable\" class=\"form-control iInput\" onkeyup=\"totalThePrice('price$counter','amount$counter','total$counter')\"> 
                                        </div>
                                    </div>    
                                </td>
                                <td scope=\"col\" width=\"25%\" class=\"iEntry\">
                                    <div class=\"inputfield\">
                                        <div class=\"rLabel\">
                                            <label class=\"\" for=\"textinput\">$total_amount : </label>                
                                        </div>
                                        <div class=\"textfield btm20padding\">
                                            <input id=\"total$counter\" name=\"total[]\" type=\"text\" placeholder=\"$total_amount\" class=\"form-control iInput\">     
                                        </div>
                                    </div>    
                                </td>  
                            </tr>
                    </table>";
        }
        echo $content;
    }          
    
    /**
    * @desc get date dropdown data
    */
    function getDateDetails($var = "")
    {
        $range = 0;
        $content = "";
        if($var == "days"){
            $range = 31;
            for($i = 1; $i<=$range; $i++){
                $content .= "<option value='$i'>$i</option>";
            }
        }else if($var == "months"){
            $range = 12;
            for($i = 1; $i<=$range; $i++){
                $content .= "<option value='$i'>".$this->lang->line('month'.$i)."</option>";
            }
        }else if($var == "years"){ 
            $year = date("Y")-621;
            //$year = date("Y")-621;
            $i = $year+1;
            $range = $year-12;
            for($i; $i>=$range; $i--){
                $content .= "<option value='$i'>$i</option>";
            }
        }
        return $content;     
    } 
    
    /**
    * @desc filter copied function
    */
    function filter()
    {
        // Check if user is supervisor, or has view role, or all view role, or dep all view role
        if(1)
        {           
            $search_keys="";
            //integrate ajax pagination
            $str_post_str  = '&ajax=1';
            //integrate ajax pagination
            // name
            if($this->input->post('patient_id') != "")
            {
                $str_post_str .= '&patient_id='.$this->input->post('patient_id');
            }
            // name
            if($this->input->post('name') != "")
            {
                $str_post_str .= '&name='.$this->input->post('name');
            }                   
            
            // start day
            $str_post_str .= '&fday='.$this->input->post('fday');
            //echo $str_post_str; exit;
            // start month
            $str_post_str .= '&fmonth='.$this->input->post('fmonth');
            // start year
            $str_post_str .= '&fyear='.$this->input->post('fyear');
            //------------Register end date --------------------
            $str_post_str .= '&tday='.$this->input->post('tday');
            // start month
            $str_post_str .= '&tmonth='.$this->input->post('tmonth');
            // start year
            $str_post_str .= '&tyear='.$this->input->post('tyear');

            $recpage  = $this->config->item('recordperpage');//number of records per page
            $starting = $this->input->post('starting');         //get counter which page record
            //if its the first page than show starting from 0
            if(!$starting)
            {
                $starting =0;
            }
            
            
            $records   = $this->xray_model->search_records($starting,$recpage,FALSE);
            $rec_total = $this->xray_model->search_records($starting,$recpage,TRUE);
            if($records)
            {
                //echo "<pre/>"; print_r($records->result()); exit;
                $data['records'] = $records;
            }
            else
            {
                $data['records'] = '';
            }
            $this->ajax_pagination_new->make_search(
                $rec_total,
                $starting,
                $recpage,
                $this->lang->line('first'),
                $this->lang->line('last'),
                $this->lang->line('prev'),
                $this->lang->line('next'),
                $this->lang->line('page'),
                $this->lang->line('of'),
                $this->lang->line('total'),
                base_url()."index.php/xray/home/filter",
                'list_div1',
                $str_post_str
            );
             
            $data["search"] = TRUE;
            $data['page']   = $starting;
            $data['total']  = $this->ajax_pagination_new->total;
            //$data['page']       = $page;
            $data['title']      = $this->lang->line("xray_list");
            //$data['page']   = $starting;
            $data['links']  = $this->ajax_pagination_new->anchors;
            //$data['total']  = $this->ajax_pagination->total;
            $this->load->view("filter/xray_filter_list",$data);
        }
        else
        {
            echo $this->load->view('unauthorized');
        }
    }
    
    /**
    * @desc create report
    */
    function genDBexelprint()
    {
        $allsql = $this->input->post('allsql');
        $allsql = $this->clean_encrypt->decode($allsql);
        //echo "<pre>".$allsql;exit;
        //Get data from the database (model)
        $reportObj = $this->xray_model->search_records($allsql);

        //echo "<pre>";print_r($reportObj->result());exit;

        if($reportObj !=false)
        {
            $this->load->helper('phpexcel');
            $excel = new PHPExcel();
            $excel->getProperties()
                ->setCreator("WEOPREG")
                ->setLastModifiedBy("WEOPREG")
                ->setTitle("WEOPREG")
                ->setSubject("WEOPREG")
                ->setDescription("WEOPREG")
                ->setKeywords("WEOPREG")
                ->setCategory("WEOPREG");
            $excel->setActiveSheetIndex(0);
            // we are selecting a worksheet
            $excel->getActiveSheet()->setTitle($this->lang->line('xray_report'));
            $excel->getActiveSheet()->getSheetView()->setZoomScale(100);
            //$lang = $this->mng_auth->get_language();
            $excel->getActiveSheet()->setRightToLeft(true);
            $rotation = -90;
            ini_set('memory_limit','4026M');
            $excel->getActiveSheet()->setShowGridlines(true);
            $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $excel->getActiveSheet()->getPageMargins()->setTop(0.8);
            $excel->getActiveSheet()->getPageMargins()->setRight(1.3);
            $excel->getActiveSheet()->getPageMargins()->setLeft(1.3);
            $excel->getActiveSheet()->getPageMargins()->setBottom('0.3');
            $excel->getActiveSheet()->getPageMargins()->setFooter('0.3');
            $excel->getActiveSheet()->getPageMargins()->setHeader('0.3');
            $styleArrayTitles = array
            (
                'font' => array(
                    'bold' => TRUE,
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
            );
            
            
            $styleTitle = array(
                'font' => array(
                        'bold' => TRUE,
                        'color' => array('rgb' => '215966'),
                        'size'  => 18,
                        'name'  => 'Verdana'
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
            );
            
            $styleInfo = array(
                'font' => array(
                        'bold' => TRUE,
                        'color' => array('rgb' => '215966'),
                        'size'  => 9,
                        'name'  => 'Verdana'
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
            );
            
            $headerStyle = array
            (
                'font' => array(
                        'bold' => TRUE,
                        'color' => array('rgb' => '953635'),
                        'size'  => 24,
                        'name'  => 'Verdana'
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
            );

            //NO style
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' =>$rotation,
                ),
                'borders' => array(
                                    'allborders' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                            'color' => array('argb' => '#DBDBB7'),
                                        ),
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation' => $rotation,
                        'startcolor' => array(
                            'argb' => 'CCCCCC',
                        ),
                        'endcolor' => array(
                            'argb' => 'CCCCCC',
                    ),
                ),
            );
            //NO style
            $styleTitles = array(
                'font' => array(
                    'bold' => true,
                    'color' => array('rgb' => 'ffffff'), 
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                                    'allborders' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                            'color' => array('argb' => '#FFFFFF'),
                                        ),
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'startcolor' => array(
                            'argb' => '215966',
                        ),
                        'endcolor' => array(
                            'argb' => '215966',
                    ),
                ),
            );


            $border = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('argb' => '00000000'),
                    ),
                ),
                'alignment' => array
                (
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
            
            $noBorder = array(
                'alignment' => array
                (
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
            
            //Start title
            $countrow = 1;
            $excel->getActiveSheet()->getStyle('C'.$countrow.':E'.$countrow)->applyFromArray($headerStyle);
            $excel->getActiveSheet()->getStyle('C1')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C2:E2')->applyFromArray($styleTitle);
            $excel->getActiveSheet()->getStyle('F1:G1')->applyFromArray($styleInfo);
            $excel->getActiveSheet()->getStyle('F1')->getAlignment()->setWrapText(true);
            
            
            //$excel->getActiveSheet()->getStyle('A'.$countrow.':F'.$countrow)->applyFromArray($border);
            $excel->getActiveSheet()->mergeCells('A'.$countrow.':B'.$countrow);
            $excel->getActiveSheet()->mergeCells('C'.$countrow.':E'.$countrow);
            $excel->getActiveSheet()->mergeCells('F'.$countrow.':G'.$countrow);
            $excel->getActiveSheet()->mergeCells('A2:B2');
            $excel->getActiveSheet()->mergeCells('C2:E2');
            $excel->getActiveSheet()->mergeCells('F2:G2');
            
            $excel->getActiveSheet()->getRowDimension($countrow)->setRowHeight(88);
            $excel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension(3)->setRowHeight(30);

            $excel->getActiveSheet()->getStyle('A'.$countrow)->getFont()->setSize(14);
            //No
            $countrow = 3;
            
            //insert logo
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('Logo');
            $objDrawing->setDescription('Logo');
            $logo = 'assets/images/amc.png'; // Provide path to your logo file
            $objDrawing->setPath($logo);  //setOffsetY has no effect
            $objDrawing->setCoordinates('A1');
            $objDrawing->setHeight(110); // logo height
            $objDrawing->setWorksheet($excel->getActiveSheet()); 
            $objDrawing->setOffsetX(25);    // setOffsetX works properly
            $objDrawing->setOffsetY(8);  //setOffsetY has no effect
            $objDrawing->setCoordinates('A1');
        
            //titles
            $title1 = $this->lang->line('ndcdr');
            $title12 = $this->lang->line('ndcen');
            $main_title = $this->lang->line('xray_report');
            $title = $title1.PHP_EOL.$title12;
            
            $phone = $this->lang->line('d_contacts');
            $email = $this->lang->line('email');
            $address = $this->lang->line('address');
            
            $info = $phone.PHP_EOL.$email.PHP_EOL.$address;
        
            ////number
            $excel->getActiveSheet()->setCellValue('C2',$main_title);
            $excel->getActiveSheet()->setCellValue('C1',$title);
            $excel->getActiveSheet()->setCellValue('F1',$info);
            $excel->getActiveSheet()->setCellValue('A'.$countrow,$this->lang->line('id'));
            $excel->getActiveSheet()->setCellValue('B'.$countrow,$this->lang->line('serial_no'));
            $excel->getActiveSheet()->setCellValue('C'.$countrow,$this->lang->line('patient_name'));
            $excel->getActiveSheet()->setCellValue('D'.$countrow,$this->lang->line('fee'));
            $excel->getActiveSheet()->setCellValue('E'.$countrow,$this->lang->line('remains'));
            $excel->getActiveSheet()->setCellValue('F'.$countrow,$this->lang->line('totalFee'));
            $excel->getActiveSheet()->setCellValue('G'.$countrow,$this->lang->line('registerDate')); 

            $excel->getActiveSheet()->getStyle('A'.$countrow.':G'.($countrow))->applyFromArray($styleTitles);
            $countrow++;


            $excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
            $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $excel->getActiveSheet()->getColumnDimension('C')->setWidth(23);
            $excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            //$excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);


            $totalcal=0;
            $counter = 1;
            $countrow = 4;
            $fee = 0;
            $remains = 0;
            $totalfee = 0;
            $totalRecords = $reportObj->num_rows();
            $totalmoneyt =  0;
            foreach($reportObj->result() AS $item)
            {
                $excel->getActiveSheet()->getStyle('G'.$countrow)->getAlignment()->setReadorder(PHPExcel_Style_Alignment::READORDER_RTL);
                $sub_record = $this->xray_model->getXraySub($item->urn);
                $film = 0;
                $fixer = 0;
                $deploper = 0;
                $totalmoney = 0;    
                //echo "<pre>";print_r($deploper);exit;
                /*$total_buy = $item->amout*$item->buy_price;
                $total_sale = $item->amout*$item->sale_price;*/
                $excel->getActiveSheet()->setCellValue('A'.$countrow,$counter);
                $excel->getActiveSheet()->setCellValue('B'.$countrow,$item->patient_id);
                $excel->getActiveSheet()->setCellValue('C'.$countrow,$item->name);
                
                $excel->getActiveSheet()->setCellValue('D'.$countrow,$item->fee);
                $excel->getActiveSheet()->setCellValue('E'.$countrow,$item->remains);
                $excel->getActiveSheet()->setCellValue('F'.$countrow,$item->fee+$item->remains);
                //register date on report
                if($item->registerdate){
                    $reg_date   = explode(" ",$item->registerdate);
                    $date_arr1  = explode("-",$reg_date[0]);
                    $jdate      = gregorian_to_jalali($date_arr1[0],$date_arr1[1],$date_arr1[2],"/");
                    $jdate_arr  = explode("/",$jdate);
                    $jday       = $jdate_arr[2];
                    $jmonth     = $jdate_arr[1];
                    $jyear      = $jdate_arr[0];
                    $reg_date = $jday." - ".$this->lang->line('month'.$jmonth)." - ".$jyear;
                 }
                $excel->getActiveSheet()->setCellValue('G'.$countrow,$reg_date);
                
                $fee +=  $item->fee;
                $remains +=  $item->remains;
                $totalfee +=  $item->fee+$item->remains;
                //$totalmoneyt +=  $totalmoney;
                
                $counter++;
                $countrow++;
            }


            $excel->getActiveSheet()->mergeCells('A'.$countrow.':C'.($countrow));
            $excel->getActiveSheet()->setCellValue('A'.$countrow,$this->lang->line('total'));
            $excel->getActiveSheet()->getStyle('A'.$countrow)->getFont()->setSize(14);
            $excel->getActiveSheet()->getRowDimension($countrow)->setRowHeight(24);
            
            $excel->getActiveSheet()->setCellValue('D'.$countrow,$fee);
            $excel->getActiveSheet()->setCellValue('E'.$countrow,$remains);
            $excel->getActiveSheet()->setCellValue('F'.$countrow,$totalfee);
            //$excel->getActiveSheet()->setCellValue('G'.$countrow,$totalmoneyt);
            
            $excel->getActiveSheet()->getStyle('A3:G'.($countrow))->applyFromArray($border);
            $excel->getActiveSheet()->getStyle('A3:G'.($countrow-1))->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->setShowGridlines(FALSE);
            
            //signature
            $countrow++;
            $countrow++;
            $excel->getActiveSheet()->mergeCells('A'.$countrow.':G'.$countrow);  
            $excel->getActiveSheet()->getStyle('A'.($countrow))->applyFromArray($styleTitle);
            $excel->getActiveSheet()->setCellValue('A'.$countrow,$this->lang->line('signature'));
            ob_end_clean();
            $name = $this->lang->line('xray_report');
            // redirect to cleint browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=$name.xlsx");
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $objWriter->save('php://output');

        }
    }
    
    /**
    * @desc bring the name of patient
    */
    function patientNameById()
    {
        if(0){
            echo "You are not loged in.";exit;
        }else{
            $p_id = $this->input->post('pid');
            $records = $this->xray_model->getRegData($p_id); 
            //echo "<pre>";print_r($records);exit;  
            if($records){
                echo $records[0]->name;
            }else{
                echo "No one";
            }
        }    
    }
    
    /**
    * @desc bring the name of patient
    */
    function patientNameByIdNew()
    {
        if(0){
            echo "You are not loged in.";exit;
        }else{
            $p_id = $this->input->post('pid');
            $records = $this->xray_model->getRegDataNew($p_id); 
            //echo "<pre>";print_r($records);exit;  
            if($records){
                echo $records[0]->name;
            }else{
                echo "No one";
            }
        }    
    }
    
    /**
    * @desc xray material list
    */
    function material_list($page = 0)
    {
        /*******************************************************
        * @desc ************AJAX PAGINATION*********************
        ********************************************************/
        $str_post_str  = '&ajax=1';  
        $recpage  = $this->config->item('recordperpage');//number of records per page
        $starting = $this->input->post('starting');         //get counter which page record
        //if its the first page than show starting from 0
        if(!$starting)
        {
            $starting =0;
        }
        
        $records   = $this->xray_model->getAllSubRecords($starting,$recpage,FALSE);
        $rec_total = $this->xray_model->getAllSubRecords($starting,$recpage,TRUE);
        if($records)
        {
            //echo "<pre/>"; print_r($records->result()); exit;
            $data['records'] = $records;
        }
        else
        {
            $data['records'] = '';
        }
        $this->ajax_pagination_new->make_search(
            $rec_total,
            $starting,
            $recpage,
            $this->lang->line('first'),
            $this->lang->line('last'),
            $this->lang->line('prev'),
            $this->lang->line('next'),
            $this->lang->line('page'),
            $this->lang->line('of'),
            $this->lang->line('total'),
            base_url()."index.php/xray/home/material_list",
            'list_div1',
            $str_post_str
        );
         
        $data["search"] = FALSE;
        $data['page']   = $starting;
        $data['total']  = $this->ajax_pagination_new->total;
        $data['links']  = $this->ajax_pagination_new->anchors; 
    
        //date and time dropdown data
        $data['days']           = $this->getDateDetails('days');
        $data['months']         = $this->getDateDetails('months');
        $data['years']          = $this->getDateDetails('years');
        $data['title']          = $this->lang->line("xray_material_list"); 
        $data['filter']         = $this->load->view("filter/x_material_filter",$data,true);
        if($this->input->post('ajax')==1)
        {
            $data["search"] = TRUE;
            $this->load->view('filter/x_material_filter_list',$data);
        }else{                                                   
            banner();
            sidebar();
            $modal = modal_popup();
            $content = $this->load->view('xray/material/xray_material_list',$data,true); 
            content($content);
            footer();
        }
    }
    
    
    /**
    * @desc xray material add
    */
    function xray_material_add()
    {
        if(0){
            echo "You are not loged in";exit;
        }else{
            $this->form_validation->set_rules('xray_type[]', 'xray_type', 'trim');
            $this->form_validation->set_rules('price[]', 'price', 'trim');
            if($this->form_validation->run() == FALSE){
                $data = array(); 
                //patient id dropdown data
                $recortds = $this->xray_model->getRegData(false);
                if($recortds){
                    $data['patientid'] = $recortds;
                }else{
                    $data['patientid'] = "";
                }
                //get the static data
                $xrayStatic = $this->xray_model->getStaticData("stable","xray");
                if($xrayStatic){
                    $data['staticData'] = $xrayStatic;
                }else{
                    $data['staticData'] = "";    
                }
                //echo "<pre>";print_r($xrayStatic);exit;
                //load views and templates
                $data['list_type']      = 0;
                $data['title']          = $this->lang->line('xray_material_add');
                banner();
                sidebar();
                $modal = modal_popup();
                $content = $this->load->view("xray/material/xray_material_add",$data,true);
                content($content);
                footer();
            }
            else{
                $amount             = $this->input->post('amount');       
                $xray_type          = $this->input->post('xray_type');    
                $price              = $this->input->post('price');       
                $total              = $this->input->post('total');      
                $xray_data = array();
                $updateDrug = false;
                foreach($xray_type AS $key=>$value){
                    if($xray_type[$key] != '' && $xray_type[$key] != 0){
                        $xray_data = array(
                            "xray_type"                 => $xray_type[$key],  
                            "amount"                    => $amount[$key],
                            "price"                     => $price[$key],
                            "total"                     => $total[$key],
                            "registerdate"              => date("Y-m-d")
                        );
                        $xray_urn = $this->urn_model->getURN('xray_sub','urn');
                        $updateDrug = $this->xray_model->update('xray_sub',$xray_urn,$xray_data,1000001,"INSERT");
                    }
                } 
                if($updateDrug){
                    redirect("xray/home/material_list",'refresh');
                }
            }     
        }
    }
    
    /**
    * @desc xray material edit
    */
    function xray_material_edit($urn = 0)
    {
        if(0){
            echo "You are not loged in";exit;
        }else{
            $dec_urn = $this->clean_encrypt->decode($urn);
            $this->form_validation->set_rules('xray_type', 'xray_type', 'trim');
            $this->form_validation->set_rules('price', 'price', 'trim');
            if($this->form_validation->run() == FALSE){
                $data = "";
                $teeth_records = "";
                //get the main xray record
                $records = $this->xray_model->getSubViewRecords($dec_urn);
                //echo "<pre>";print_r($records);exit;
                //get the xray subrecord
                if($records){                                                    
                    $data["record"]             = $records[0];     
                }else{
                    $data["record"]             = false;   
                }
                
                //get the static data
                $xrayStatic = $this->xray_model->getStaticData("stable","xray");
                if($xrayStatic){
                    $data['staticData'] = $xrayStatic;
                }else{
                    $data['staticData'] = "";    
                }
                //top title
                $data['title'] = $this->lang->line("xray_material_edit");
                $data["enc_urn"]            = $urn;
                
                //load views and templates
                banner();
                sidebar();
                $modal = modal_popup();
                //the views if the record is exist
                $content = $this->load->view("xray/material/xray_material_edit",$data,true);
                content($content);
                footer(); 
            }
            else{
                $amount             = $this->input->post('amount');       
                $xray_type          = $this->input->post('xray_type');    
                $price              = $this->input->post('price');       
                $total              = $this->input->post('total');  
                $data = array(
                    "xray_type"                 => $xray_type,  
                    "amount"                    => $amount,
                    "price"                     => $price,
                    "total"                     => $total
                );
                //echo "<pre>";print_r($data);exit;
                $update = $this->xray_model->update('xray_sub',$dec_urn,$data,1000001,"UPDATE");
                //spent drugs
                if($update){
                    redirect("xray/home/material_list/".$urn,'refresh');    
                }     
            }
        }
    }
    
    /**
    * @desc xray material filter
    */
    function filterMaterial()
    {
        // Check if user is supervisor, or has view role, or all view role, or dep all view role
        if(1)
        {           
            $search_keys="";
            //integrate ajax pagination
            $str_post_str  = '&ajax=1';
            //integrate ajax pagination
            // name
            if($this->input->post('xray_type') != "")
            {
                $str_post_str .= '&xray_type='.$this->input->post('xray_type');
            }
            // name
            if($this->input->post('price') != "")
            {
                $str_post_str .= '&price='.$this->input->post('price');
            }                   
            
            // start day
            $str_post_str .= '&fday='.$this->input->post('fday');
            //echo $str_post_str; exit;
            // start month
            $str_post_str .= '&fmonth='.$this->input->post('fmonth');
            // start year
            $str_post_str .= '&fyear='.$this->input->post('fyear');
            //------------Register end date --------------------
            $str_post_str .= '&tday='.$this->input->post('tday');
            // start month
            $str_post_str .= '&tmonth='.$this->input->post('tmonth');
            // start year
            $str_post_str .= '&tyear='.$this->input->post('tyear');

            $recpage  = $this->config->item('recordperpage');//number of records per page
            $starting = $this->input->post('starting');         //get counter which page record
            //if its the first page than show starting from 0
            if(!$starting)
            {
                $starting =0;
            }
            
            
            $records   = $this->xray_model->search_material_records($starting,$recpage,FALSE);
            $rec_total = $this->xray_model->search_material_records($starting,$recpage,TRUE);
            if($records)
            {
                //echo "<pre/>"; print_r($records->result()); exit;
                $data['records'] = $records;
            }
            else
            {
                $data['records'] = '';
            }
            $this->ajax_pagination_new->make_search(
                $rec_total,
                $starting,
                $recpage,
                $this->lang->line('first'),
                $this->lang->line('last'),
                $this->lang->line('prev'),
                $this->lang->line('next'),
                $this->lang->line('page'),
                $this->lang->line('of'),
                $this->lang->line('total'),
                base_url()."index.php/xray/home/filterMaterial",
                'list_div1',
                $str_post_str
            );
             
            $data["search"] = TRUE;
            $data['page']   = $starting;
            $data['total']  = $this->ajax_pagination_new->total;
            //$data['page']       = $page;
            $data['title']      = $this->lang->line("xray_material_list");
            //$data['page']   = $starting;
            $data['links']  = $this->ajax_pagination_new->anchors;
            //$data['total']  = $this->ajax_pagination->total;
            $this->load->view("filter/x_material_filter_list",$data);
        }
        else
        {
            echo $this->load->view('unauthorized');
        }
    }
}
?>
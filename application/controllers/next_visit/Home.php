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
        $this->load->model(array('register/register_model','urn_model','document/document_model','next_visit/next_model'));
    }

    /**
    * @desc index function
    */
    public function index()
    {
        redirect('remains/home/next_visit_list');
    }
    
    /**
    * @desc register list function
    */
    function next_visit_list($page = 0)
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
        
        $records   = $this->next_model->getAllrecords($starting,$recpage,FALSE);
        $rec_total = $this->next_model->getAllrecords($starting,$recpage,TRUE);
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
            base_url()."index.php/next_visit/home/next_visit_list",
            'list_div1',
            $str_post_str
        );
         
        $data["search"] = FALSE;
        $data['page']   = $starting;
        $data['total']  = $this->ajax_pagination_new->total;
        $data['links']  = $this->ajax_pagination_new->anchors; 
        //get next visits
        $next_visit = $this->register_model->getStaticData("stable","qu");  
        $data['next_visit'] = $next_visit;     
        //date and time dropdown data
        $data['days']           = $this->getDateDetails('days');
        $data['months']         = $this->getDateDetails('months');
        $data['years']          = $this->getDateDetails('years');
        $data['title']          = $this->lang->line("today_visits_list");  
        $data['filter']         = $this->load->view("filter/next_filter",$data,true); 
        if($this->input->post('ajax')==1)
        {
            $data["search"] = TRUE;
            $this->load->view('filter/next_filter_list',$data);
        }else{                                                   
            banner();
            sidebar();
            $modal = modal_popup();
            $content = $this->load->view('next_visit/next_list',$data,true);
            content($content);
            footer();
        }
    }
    
    /**
    * @desc view function
    */
    function view($urn = 0,$drug = '')
    {
        if(0){
            echo "You are not loged in.";exit;
        }else{
            $dec_urn = $this->clean_encrypt->decode($urn);
            $data = "";
            $records = false;
            $teeth_records = false;
            $prof_pic = false;
            $used_drugs = false;
            $drug_records = false;
            //top title
            $data['title'] = $this->lang->line("next_visit");
            $records = $this->register_model->getViewRecords($dec_urn);
            //echo "<pre>";print_r($records);exit;
            if($records){
                $teeth_records  = $this->register_model->getTeethRecords($records[0]->urn);
                $drug_records   = $this->register_model->getDrugRecords($records[0]->urn);
                $prof_pic       = $this->document_model->getPicture($records[0]->patient_id);
                $used_drugs     = $this->register_model->get_drugs_by_urn($records[0]->urn,0);
                
                //used drugs during 
                $used_drugs_static = $this->register_model->get_used_drugs(0);
                $data['used_drugs'] = $used_drugs_static;
                //echo "<pre>";print_r($prof_pic);exit;
                
                //get spent drugs
                $spent_drugs_static = $this->register_model->spentDrugsStatic();                
                $data['spent_drugs'] = $spent_drugs_static;
            }
            
            $data["record"]             = $records[0];
            $data["profPic"]            = $prof_pic[0];
            $data["teeth_record"]       = $teeth_records;
            $data["drug_record"]        = $drug_records;
            $data["used_drugs_rec"]     = $used_drugs;
            
            //load views and templates
            banner();
            sidebar();
            $modal = modal_popup();
            $data['cover_view'] = $this->load->view("register/cover_view",$data,true); 
            $data['build_view'] = $this->load->view("register/build_view",$data,true); 
            $data['fill_view'] = $this->load->view("register/fill_view",$data,true); 
            $data['clean_view'] = $this->load->view("register/clean_view",$data,true); 
            $data['ortho_view'] = $this->load->view("register/ortho_view",$data,true);
            $data['exo_view'] = $this->load->view("register/exo_view",$data,true);  
            if($drug == 'drug'){
                $content = $this->load->view("register/drugs/add_partial",$data,true);
            }elseif($drug == 'used_drug'){
                $content = $this->load->view("register/drugs/add_used_drug",$data,true);
            }else{
                $content = $this->load->view("next_visit/next_view",$data,true);
            }
            content($content);
            footer();   
        }
    }
    
    /**
    * @desc edit registered data
    */
    function pay($urn = 0)
    {
        if(0){
            echo "You are not loged in";exit;
        }else{
            $dec_urn = $this->clean_encrypt->decode($urn);
            $records = $this->register_model->getViewRecords($dec_urn); 
            $remain = $records[0]->remains;   
            $fee = $records[0]->fee;  
            $calcfee = $remain+$fee;
            $calcaremain = 0;
            $data = array(
                "fee"                       => $calcfee,
                "remains"                   => $calcaremain
            );
            //echo "<pre>";print_r($data);exit;
            $update = $this->register_model->update('register',$dec_urn,$data,1000001,"UPDATE");
            //used drugs
            if($update){
                redirect("next_visit/home/view/".$urn);
            }
        }
    }
    
    /**
    * @desc edit registered data
    */
    function next_visit($urn = 0)
    {
        if(0){
            echo "You are not loged in";exit;
        }else{
            $dec_urn = $this->clean_encrypt->decode($urn);
            $this->form_validation->set_rules('name', 'name', 'trim|required');
            $this->form_validation->set_rules('f_name', 'f_name', 'trim|required');
            $this->form_validation->set_rules('contact', 'contact', 'trim');
            $this->form_validation->set_rules('address', 'address', 'trim');
            if($this->form_validation->run() == FALSE){
                $data = "";
                $records = false;
                $teeth_records = false;
                $prof_pic = false;
                $used_drugs = false;
                //top title
                $data['title'] = $this->lang->line("next_visit");
                $records = $this->register_model->getViewRecords($dec_urn);
                if($records){
                    $teeth_records  = $this->register_model->getTeethRecords($records[0]->urn);
                    $drug_records   = $this->register_model->getDrugRecords($records[0]->urn);
                    $prof_pic       = $this->document_model->getPicture($records[0]->patient_id);
                    $used_drugs     = $this->register_model->get_drugs_by_urn($records[0]->urn,0);
                    //echo "<pre>";print_r($used_drugs);exit;
                    
                    //used drugs during 
                    $used_drugs_static = $this->register_model->get_used_drugs(0);
                    $data['used_drugs'] = $used_drugs_static;
                    
                    //get next visits
                    $next_visit = $this->register_model->getStaticData("stable","qu");  
                    $data['next_visit'] = $next_visit;
                    
                    //doctors
                    $doctors = $this->register_model->doctors();
                    $data['doctors']        = $doctors;
                    
                    //next visit date
                    $next_visit = $records[0]->next_visit;
                    $next_visit_arr = explode(" ",$next_visit);
                    $next_arr = explode("-", $next_visit_arr[0]);
                    $next_v_year = $next_arr[0];
                    $next_v_month = $next_arr[1];
                    $next_v_day = $next_arr[2];
                    $addate = gregorian_to_jalali($next_v_year,$next_v_month,$next_v_day,"/");
                    $my_date = explode("/",$addate);
                    $addateday = $my_date[2];
                    $addatemonth = $my_date[1];
                    $addateyear = $my_date[0];
                    $data['days']       = $this->getDateDetails('days',$addateday,0,0);
                    $data['months']     = $this->getDateDetails('months',0,$addatemonth,0);
                    $data['years']      = $this->getDateDetails('years',0,0,$addateyear);
                    //next visit time
                    $data['hour']           = $this->getTimeDetails('hour');
                    $data['minute']         = $this->getTimeDetails('minute');
                    
                    //get spent drugs
                    $spent_drugs_static = $this->register_model->spentDrugsStatic();                
                    $data['spent_drugs'] = $spent_drugs_static;
                    
                    //spend drugs record
                    $spent_drugs     = $this->register_model->get_used_drug_by_urn($records[0]->urn,0);
                    $data['spent_drugs_record'] = $spent_drugs;
                }
                
                $data["record"]             = $records[0];
                $data["profPic"]            = $prof_pic[0];
                $data["teeth_record"]       = $teeth_records;
                $data["drug_record"]        = $drug_records;
                $data["used_drugs_rec"]     = $used_drugs;
                $data["enc_urn"]            = $urn;
                
                //load views and templates
                banner();
                sidebar();
                $modal = modal_popup();
                //the views if the record is exist
                $data['fill_add'] = $this->load->view("register/fill_add",$data,true);
                $data['cover_add'] = $this->load->view("register/cover_add",$data,true);
                $data['build_add'] = $this->load->view("register/build_add",$data,true);
                $data['clean_add'] = $this->load->view("register/clean_add",$data,true);
                $data['ortho_add'] = $this->load->view("register/orthodant_add",$data,true);
                $data['exo_add']        = $this->load->view("register/exo_add",$data,true);
 
                $content = $this->load->view("next_visit/next_visit",$data,true);
                content($content);
                footer(); 
            }
            else{
                $p_id       = $this->input->post('p_id');    
                $name       = $this->input->post('name');    
                $f_name     = $this->input->post('f_name');       
                $contact    = $this->input->post('contact');       
                $visit      = $this->input->post('visit');       
                $fee        = $this->input->post('fee');       
                $remains    = $this->input->post('remains');       
                $fill       = $this->input->post('fill');       
                $cover      = $this->input->post('cover');       
                $build      = $this->input->post('build');       
                $clean      = $this->input->post('clean'); 
                $ortho      = $this->input->post('orthodant'); 
                $exo        = $this->input->post('exo');      
                $addrass    = $this->input->post('addrass');
                $doctor     = $this->input->post('doctor');
                
                $day        = $this->input->post('day');
                $month      = $this->input->post('month');
                $year       = $this->input->post('year');
                
                $hour       = $this->input->post('hour');
                $minute     = $this->input->post('minute');
                
                $date = jalali_to_gregorian($year,$month,$day,"-");
                $time = $hour.":".$minute;
                $check = $this->register_model->check_if_exist($name,$f_name,$visit, $contact);
                //echo "<pre>";print_r($check);exit;
                if($check){
                    $this->view($this->clean_encrypt->encode($check[0]->urn));
                }else{       
                    $urn = $this->urn_model->getURN('register','urn');
                    $data = array(
                        "patient_id"            => $p_id,
                        "name"                  => $name,
                        "f_name"                => $f_name,
                        "contact"               => $contact,
                        "address"               => $addrass,
                        "visit"                 => $visit,
                        "fee"                   => $fee,
                        "remains"               => $remains,
                        "fill_teeth"            => $fill,
                        "cover_teeth"           => $cover,
                        "build_teeth"           => $build,
                        "clean"                 => $clean,
                        "ortodancy"             => $ortho,
                        "exodontics"            => $exo,
                        "doctor"                => $doctor,
                        "next_visit"            => $date,
                        "next_time"             => $time,
                        "registerdate"          => date("Y-m-d")
                    );
                    //echo "<pre>";print_r($data);exit;
                    $update = $this->register_model->update('register',$urn,$data,1000001,"INSERT",$p_id);
                    if($update == true && $fill == 1){
                        $this->fill_teeth_add($urn);        
                    }
                    if($update == true && $cover == 1){
                        $this->cover_teeth_add($urn);        
                    }
                    if($update == true && $build == 1){
                        $this->build_teeth_add($urn);        
                    }
                    if($update == true && $clean == 1){
                        $this->clean_teeth_add($urn);        
                    }
                    if($update == true && $ortho == 1){
                        $this->ortho_teeth_add($urn);        
                    }
                    if($update == true && $exo == 1){
                        $this->exo_teeth_add($urn);        
                    }
                    //used drugs
                    if($update){
                        $used_drug       = $this->input->post('used_drugs');    
                        $used_drug_data = array();
                        foreach($used_drug AS $keys=>$values){
                            if($used_drug[$keys] != '' && $used_drug[$keys] != 0){
                                $used_drug_data = array(
                                    "parent_urn"                => $urn,
                                    "name"                      => $used_drug[$keys]
                                );
                                $used_drug_urn = $this->urn_model->getURN('used_drug','urn');
                                $updateUsedDrug = $this->register_model->update('used_drug',$used_drug_urn,$used_drug_data,1000001,"INSERT");
                            }
                        }    
                    }
                    //spent drugs
                    if($update){
                        $drug       = $this->input->post('drug');    
                        $price      = $this->input->post('price');       
                        $amount     = $this->input->post('amount');       
                        $total      = $this->input->post('total');
                        $drug_data = array();
                        foreach($drug AS $key=>$value){
                            if($drug[$key] != '' && $drug[$key] != 0){
                                $drug_data = array(
                                    "parent_urn"                => $urn,
                                    "name"                      => $drug[$key],
                                    "amout"                     => $amount[$key],
                                    "price"                     => $price[$key],
                                    "total_price"               => $total[$key]
                                );
                                $drug_urn = $this->urn_model->getURN('spent_drug','urn');
                                $updateDrug = $this->register_model->update('spent_drug',$drug_urn,$drug_data,1000001,"INSERT");
                            }
                        }     
                    }
                    
                    /****************************Attachment*************************/
                    
                    if($update){
                        redirect("next_visit/home/view/".$this->clean_encrypt->encode($urn));
                    }

                }
 
            }
        }
    }
    
    /**
    * @desc fill teeth add function
    */
    function fill_teeth_add($register_urn = 0)
    {
        if($register_urn != 0){
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
                $ill_type = 1;
                $fill_data = array(
                    "register_urn"              => $register_urn,
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
                //echo "<pre>";print_r($fill_data);exit;
                $fill_urn = $this->urn_model->getURN('teeth','urn');
                $update = $this->register_model->update('teeth',$fill_urn,$fill_data,1000001,"INSERT");    
            } 
            /****************************fill teeth ends*/
        }
    }
    
    /**
    * @desc cover teeth add function
    */
    function cover_teeth_add($register_urn = 0)
    {
        if($register_urn != 0){
            /****************************cover teeth starts*/
            $golden       = $this->input->post('golden');
            $silver       = $this->input->post('silver');
            $samecolor    = $this->input->post('samecolor');
            $zarconiam    = $this->input->post('zarconiam');
            $mx           = $this->input->post('mx');
            //top right teeth
            $ctopr1       = $this->input->post('ctopr1');    
            $ctopr2       = $this->input->post('ctopr2');    
            $ctopr3       = $this->input->post('ctopr3');    
            $ctopr4       = $this->input->post('ctopr4');    
            $ctopr5       = $this->input->post('ctopr5');    
            $ctopr6       = $this->input->post('ctopr6');    
            $ctopr7       = $this->input->post('ctopr7');    
            $ctopr8       = $this->input->post('ctopr8');  
            //top left teeth  
            $ctopl1     = $this->input->post('ctopl1');
            $ctopl2     = $this->input->post('ctopl2');
            $ctopl3     = $this->input->post('ctopl3');
            $ctopl4     = $this->input->post('ctopl4');
            $ctopl5     = $this->input->post('ctopl5');
            $ctopl6     = $this->input->post('ctopl6');
            $ctopl7     = $this->input->post('ctopl7');
            $ctopl8     = $this->input->post('ctopl8');
            //bottom right teeth       
            $cbottomr1    = $this->input->post('cbottomr1'); 
            $cbottomr2    = $this->input->post('cbottomr2'); 
            $cbottomr3    = $this->input->post('cbottomr3'); 
            $cbottomr4    = $this->input->post('cbottomr4'); 
            $cbottomr5    = $this->input->post('cbottomr5'); 
            $cbottomr6    = $this->input->post('cbottomr6'); 
            $cbottomr7    = $this->input->post('cbottomr7'); 
            $cbottomr8    = $this->input->post('cbottomr8'); 
            //bottom left teeth      
            $cbottoml1      = $this->input->post('cbottoml1');     
            $cbottoml2      = $this->input->post('cbottoml2');     
            $cbottoml3      = $this->input->post('cbottoml3');     
            $cbottoml4      = $this->input->post('cbottoml4');     
            $cbottoml5      = $this->input->post('cbottoml5');     
            $cbottoml6      = $this->input->post('cbottoml6');     
            $cbottoml7      = $this->input->post('cbottoml7');     
            $cbottoml8      = $this->input->post('cbottoml8');
            if(($golden != '' || $silver != '' || $samecolor != '' || $zarconiam != '' || $mx != '') || ($ctopl1 != '' || $ctopl2 != '' || $ctopl3 != '' || $ctopl4 != '' || $ctopl5 != '' || $ctopl6 != '' || $ctopl7 != '' || $ctopl8 != '' || $ctopr1 != '' || $ctopr2 != '' || $ctopr3 != '' || $ctopr4 != '' || $ctopr5 != '' || $ctopr6 != '' || $ctopr7 != '' || $ctopr8 != '' || $cbottomr1 != '' || $cbottomr2 != '' || $cbottomr3 != '' || $cbottomr4 != '' || $cbottomr5 != '' || $cbottomr6 != '' || $cbottomr7 != '' || $cbottomr8 != '' || $cbottoml1 != '' || $cbottoml2 != '' || $cbottoml3 != '' || $cbottoml4 != '' || $cbottoml5 != '' || $cbottoml6 != '' || $cbottoml7 != '' || $cbottoml8 != ''))
            {
                $ill_type = 2;
                $cover_data = array(
                    "register_urn"              => $register_urn,
                    "ill_type"                  => $ill_type,
                    
                    "golden"                    => $golden,
                    "silver"                    => $silver,
                    "same_color"                => $samecolor,
                    "zarconiam"                 => $zarconiam,
                    "mx"                        => $mx,
                    
                    "topright1"                 => $ctopr1,
                    "topright2"                 => $ctopr2,
                    "topright3"                 => $ctopr3,
                    "topright4"                 => $ctopr4,
                    "topright5"                 => $ctopr5,
                    "topright6"                 => $ctopr6,
                    "topright7"                 => $ctopr7,
                    "topright8"                 => $ctopr8,
                    
                    "topleft1"                  => $ctopl1,
                    "topleft2"                  => $ctopl2,
                    "topleft3"                  => $ctopl3,
                    "topleft4"                  => $ctopl4,
                    "topleft5"                  => $ctopl5,
                    "topleft6"                  => $ctopl6,
                    "topleft7"                  => $ctopl7,
                    "topleft8"                  => $ctopl8,
                    
                    
                    "bottomright1"              => $cbottomr1,
                    "bottomright2"              => $cbottomr2,
                    "bottomright3"              => $cbottomr3,
                    "bottomright4"              => $cbottomr4,
                    "bottomright5"              => $cbottomr5,
                    "bottomright6"              => $cbottomr6,
                    "bottomright7"              => $cbottomr7,
                    "bottomright8"              => $cbottomr8,
                    
                    "bottomleft1"               => $cbottoml1,
                    "bottomleft2"               => $cbottoml2,
                    "bottomleft3"               => $cbottoml3,
                    "bottomleft4"               => $cbottoml4,
                    "bottomleft5"               => $cbottoml5,
                    "bottomleft6"               => $cbottoml6,
                    "bottomleft7"               => $cbottoml7,
                    "bottomleft8"               => $cbottoml8
                );
                //echo "<pre>";print_r($fill_data);exit;
                $fill_urn = $this->urn_model->getURN('teeth','urn');
                $update = $this->register_model->update('teeth',$fill_urn,$cover_data,1000001,"INSERT");    
            } 
            /****************************cover teeth ends*/
        }
    }
    
    /**
    * @desc cover teeth add function
    */
    function build_teeth_add($register_urn = 0)
    {
        if($register_urn != 0){
            /****************************build teeth starts*/
            $partial      = $this->input->post('partial');
            $complete     = $this->input->post('complete');
            $implent      = $this->input->post('implent');
            $ccpalet      = $this->input->post('ccpalet');
            $fulbredge    = $this->input->post('fulbredge');
            //top right teeth
            $btopr1       = $this->input->post('btopr1');    
            $btopr2       = $this->input->post('btopr2');    
            $btopr3       = $this->input->post('btopr3');    
            $btopr4       = $this->input->post('btopr4');    
            $btopr5       = $this->input->post('btopr5');    
            $btopr6       = $this->input->post('btopr6');    
            $btopr7       = $this->input->post('btopr7');    
            $btopr8       = $this->input->post('btopr8');  
            //top left teeth  
            $btopl1     = $this->input->post('btopl1');
            $btopl2     = $this->input->post('btopl2');
            $btopl3     = $this->input->post('btopl3');
            $btopl4     = $this->input->post('btopl4');
            $btopl5     = $this->input->post('btopl5');
            $btopl6     = $this->input->post('btopl6');
            $btopl7     = $this->input->post('btopl7');
            $btopl8     = $this->input->post('btopl8');
            //bottom right teeth       
            $bbottomr1    = $this->input->post('bbottomr1'); 
            $bbottomr2    = $this->input->post('bbottomr2'); 
            $bbottomr3    = $this->input->post('bbottomr3'); 
            $bbottomr4    = $this->input->post('bbottomr4'); 
            $bbottomr5    = $this->input->post('bbottomr5'); 
            $bbottomr6    = $this->input->post('bbottomr6'); 
            $bbottomr7    = $this->input->post('bbottomr7'); 
            $bbottomr8    = $this->input->post('bbottomr8'); 
            //bottom left teeth      
            $bbottoml1      = $this->input->post('bbottoml1');     
            $bbottoml2      = $this->input->post('bbottoml2');     
            $bbottoml3      = $this->input->post('bbottoml3');     
            $bbottoml4      = $this->input->post('bbottoml4');     
            $bbottoml5      = $this->input->post('bbottoml5');     
            $bbottoml6      = $this->input->post('bbottoml6');     
            $bbottoml7      = $this->input->post('bbottoml7');     
            $bbottoml8      = $this->input->post('bbottoml8');
            if(($implent != '' || $complete != '' || $partial != '' || $ccpalet != '' || $fulbredge != '') || ($btopl1 != '' || $btopl2 != '' || $btopl3 != '' || $btopl4 != '' || $btopl5 != '' || $btopl6 != '' || $btopl7 != '' || $btopl8 != '' || $btopr1 != '' || $btopr2 != '' || $btopr3 != '' || $btopr4 != '' || $btopr5 != '' || $btopr6 != '' || $btopr7 != '' || $btopr8 != '' || $bbottomr1 != '' || $bbottomr2 != '' || $bbottomr3 != '' || $bbottomr4 != '' || $bbottomr5 != '' || $bbottomr6 != '' || $bbottomr7 != '' || $bbottomr8 != '' || $bbottoml1 != '' || $bbottoml2 != '' || $bbottoml3 != '' || $bbottoml4 != '' || $bbottoml5 != '' || $bbottoml6 != '' || $bbottoml7 != '' || $bbottoml8 != ''))
            {
                $ill_type = 3;
                $build_data = array(
                    "register_urn"              => $register_urn,
                    "ill_type"                  => $ill_type,
                    
                    "partial"                   => $partial,
                    "complete"                  => $complete,
                    "implent"                   => $implent,
                    "ccpalete"                  => $ccpalet,
                    "full_bredge"               => $fulbredge,
                    
                    "topright1"                 => $btopr1,
                    "topright2"                 => $btopr2,
                    "topright3"                 => $btopr3,
                    "topright4"                 => $btopr4,
                    "topright5"                 => $btopr5,
                    "topright6"                 => $btopr6,
                    "topright7"                 => $btopr7,
                    "topright8"                 => $btopr8,
                    
                    "topleft1"                  => $btopl1,
                    "topleft2"                  => $btopl2,
                    "topleft3"                  => $btopl3,
                    "topleft4"                  => $btopl4,
                    "topleft5"                  => $btopl5,
                    "topleft6"                  => $btopl6,
                    "topleft7"                  => $btopl7,
                    "topleft8"                  => $btopl8,
                    
                    
                    "bottomright1"              => $bbottomr1,
                    "bottomright2"              => $bbottomr2,
                    "bottomright3"              => $bbottomr3,
                    "bottomright4"              => $bbottomr4,
                    "bottomright5"              => $bbottomr5,
                    "bottomright6"              => $bbottomr6,
                    "bottomright7"              => $bbottomr7,
                    "bottomright8"              => $bbottomr8,
                    
                    "bottomleft1"               => $bbottoml1,
                    "bottomleft2"               => $bbottoml2,
                    "bottomleft3"               => $bbottoml3,
                    "bottomleft4"               => $bbottoml4,
                    "bottomleft5"               => $bbottoml5,
                    "bottomleft6"               => $bbottoml6,
                    "bottomleft7"               => $bbottoml7,
                    "bottomleft8"               => $bbottoml8
                );
                //echo "<pre>";print_r($fill_data);exit;
                $build_urn = $this->urn_model->getURN('teeth','urn');
                $update = $this->register_model->update('teeth',$build_urn,$build_data,1000001,"INSERT");    
            } 
            /****************************build teeth ends*/
        }
    }
    
    /**
    * @desc clean teeth add function
    */
    function clean_teeth_add($register_urn)
    {
        if($register_urn != 0){
            /****************************build teeth starts*/
            $jermgery       = $this->input->post('jermgery');
            $bleching       = $this->input->post('bleching');
            if($jermgery != '' || $bleching != ''){
                $ill_type = 4;
                $clean_data = array(
                    "register_urn"                  => $register_urn,
                    "ill_type"                      => $ill_type,
                    
                    "jermgery"                      => $jermgery,
                    "bleching"                      => $bleching
                );
                //echo "<pre>";print_r($fill_data);exit;
                $clean_urn = $this->urn_model->getURN('teeth','urn');
                $update = $this->register_model->update('teeth',$clean_urn,$clean_data,1000001,"INSERT");
            }
        }
    }
    
    /**
    * @desc clean teeth add function
    */
    function ortho_teeth_add($register_urn)
    {
        if($register_urn != 0){
            /****************************build teeth starts*/
            $top_teeth          = $this->input->post('top_teeth');
            $bottom_teeth       = $this->input->post('bottom_teeth');
            $orth_complete      = $this->input->post('orth_complete');
            if($top_teeth != '' || $bottom_teeth != '' || $orth_complete != ''){
                $ill_type = 5;
                $clean_data = array(
                    "register_urn"                  => $register_urn,
                    "ill_type"                      => $ill_type,
                    
                    "top_teeth"                     => $top_teeth,
                    "bottom_teeth"                  => $bottom_teeth,
                    "all_teeth"                     => $orth_complete
                );
                //echo "<pre>";print_r($fill_data);exit;
                $clean_urn = $this->urn_model->getURN('teeth','urn');
                $update = $this->register_model->update('teeth',$clean_urn,$clean_data,1000001,"INSERT");
            }
        }
    } 
    
    /**
    * @desc exo(kashidan dandan) teeth add function
    */
    function exo_teeth_add($register_urn = 0)
    {
        if($register_urn != 0){
            /****************************exo teeth starts*/
            //top right teeth
            $topr1       = $this->input->post('etopr1');    
            $topr2       = $this->input->post('etopr2');    
            $topr3       = $this->input->post('etopr3');    
            $topr4       = $this->input->post('etopr4');    
            $topr5       = $this->input->post('etopr5');    
            $topr6       = $this->input->post('etopr6');    
            $topr7       = $this->input->post('etopr7');    
            $topr8       = $this->input->post('etopr8');  
            //top left teeth  
            $topl1     = $this->input->post('etopl1');
            $topl2     = $this->input->post('etopl2');
            $topl3     = $this->input->post('etopl3');
            $topl4     = $this->input->post('etopl4');
            $topl5     = $this->input->post('etopl5');
            $topl6     = $this->input->post('etopl6');
            $topl7     = $this->input->post('etopl7');
            $topl8     = $this->input->post('etopl8');
            //bottom right teeth       
            $bottomr1    = $this->input->post('ebottomr1'); 
            $bottomr2    = $this->input->post('ebottomr2'); 
            $bottomr3    = $this->input->post('ebottomr3'); 
            $bottomr4    = $this->input->post('ebottomr4'); 
            $bottomr5    = $this->input->post('ebottomr5'); 
            $bottomr6    = $this->input->post('ebottomr6'); 
            $bottomr7    = $this->input->post('ebottomr7'); 
            $bottomr8    = $this->input->post('ebottomr8'); 
            //bottom left teeth      
            $bottoml1      = $this->input->post('ebottoml1');     
            $bottoml2      = $this->input->post('ebottoml2');     
            $bottoml3      = $this->input->post('ebottoml3');     
            $bottoml4      = $this->input->post('ebottoml4');     
            $bottoml5      = $this->input->post('ebottoml5');     
            $bottoml6      = $this->input->post('ebottoml6');     
            $bottoml7      = $this->input->post('ebottoml7');     
            $bottoml8      = $this->input->post('ebottoml8');
            if($topl1 != '' || $topl2 != '' || $topl3 != '' || $topl4 != '' || $topl5 != '' || $topl6 != '' || $topl7 != '' || $topl8 != '' || $topr1 != '' || $topr2 != '' || $topr3 != '' || $topr4 != '' || $topr5 != '' || $topr6 != '' || $topr7 != '' || $topr8 != '' || $bottomr1 != '' || $bottomr2 != '' || $bottomr3 != '' || $bottomr4 != '' || $bottomr5 != '' || $bottomr6 != '' || $bottomr7 != '' || $bottomr8 != '' || $bottoml1 != '' || $bottoml2 != '' || $bottoml3 != '' || $bottoml4 != '' || $bottoml5 != '' || $bottoml6 != '' || $bottoml7 != '' || $bottoml8 != '')
            {
                $ill_type = 7;
                $fill_data = array(
                    "register_urn"              => $register_urn,
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
                //echo "<pre>";print_r($fill_data);exit;
                $fill_urn = $this->urn_model->getURN('teeth','urn');
                $update = $this->register_model->update('teeth',$fill_urn,$fill_data,1000001,"INSERT");    
            } 
            /****************************fill teeth ends*/
        }
    }
    
    /**
    * @desc get date dropdown data
    */
    function getProDateDetails($var = "",$day = 0,$month = 0, $year = 0)
    {
        $range = 0;
        $content = "";
        if($var == "days"){
            $range = 31;
            for($i = 1; $i<=$range; $i++){       
                if(strlen($i)<2){
                    $i = "0".$i;
                }
                if($day != 0 && $day == $i){
                    $content .= "<option value='$i' selected = 'selected'>$i</option>";    
                }else{
                    $content .= "<option value='$i'>$i</option>";
                }
            }
        }else if($var == "months"){
            $range = 12;
            for($i = 1; $i<=$range; $i++){
                if($month != 0 && $month == $i){
                    $content .= "<option value='$i' selected = 'selected'>".$this->lang->line('month'.$i)."</option>";    
                }else{
                    $content .= "<option value='$i'>".$this->lang->line('month'.$i)."</option>"; 
                }
            }
        }else if($var == "years"){ 
            $year = date("Y")-621;
            $i = $year;
            $range = $year-12;
            for($i; $i>=$range; $i--){
                if($year != 0 && $year == $i){
                    $content .= "<option value='$i' selected = 'selected'>$i</option>";     
                }else{
                    $content .= "<option value='$i'>$i</option>"; 
                }
            }
        }
        return $content;     
    }
    
    /**
    * @desc get time dropdown data
    */
    function getProTimeDetails($var = "",$hour = 0,$minute = 0)
    {
        $range = 0;
        $content = "";
        if($var == "hour"){
            $range = 24;
            for($i = 1; $i<=$range; $i++){       
                if(strlen($i)<2){
                    $i = "0".$i;
                }
                if($hour != 0 && $hour == $i){
                    $content .= "<option value='$i' selected = 'selected'>$i</option>";    
                }else{
                    $content .= "<option value='$i'>$i</option>";
                }
            }
        }else if($var == "minute"){
            $range = 60;
            for($i = 0; $i<=$range; $i++){
                if(strlen($i)<2){
                    $i = "0".$i;
                }
                if($minute == $i){
                    $content .= "<option value='$i' selected = 'selected'>".$i."</option>";    
                }else{
                    $content .= "<option value='$i'>".$i."</option>"; 
                }
            }
        }
        return $content;
    }  
    
    /**
    * @desc get time dropdown data
    */
    function getTimeDetails($var = "")
    {
        $range = 0;
        $content = "";
        if($var == "minute"){
            $range = 59;
            for($i = 1; $i<=$range; $i++){
                if($i<10){
                    $i = '0'.$i;
                }
                $content .= "<option value='$i'>$i</option>";
            }
        }else if($var == "hour"){
            $range = 24;
            for($i = 1; $i<=$range; $i++){
                if($i<10){
                    $i = '0'.$i;
                }
                $content .= "<option value='$i'>".$i."</option>";
            }
        }
        return $content; 
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
            $i = $year;
            $range = $year-12;
            for($i; $i>=$range; $i--){
                $content .= "<option value='$i'>$i</option>";
            }
        }
        return $content;     
    }  
    
    /**
    * @desc multiple function
    */
    function multiple()
    {
        //echo "<pre>";print_r($_POST);exit;
        $counter = $this->input->post('no');
        $pirce_label = $this->lang->line('price');
        $amount_lable = $this->lang->line('amount');
        $total_amount = $this->lang->line('total');
        //get spent drugs
        $spent_drugs = $this->register_model->spentDrugsStatic();
        $base_url = base_url();
        $content = "";
        if($counter >0){
            $content .= "<table class=\"table\" id=\"imRemovable$counter\">
                <tr>
                        <td scope=\"col\" width=\"33%\" class=\"iEntry\" colspan=\"3\">
                            <span class=\"btn btn-danger ino\">$counter</span><input type=\"button\" id=\"rm\" class=\"btn btn-danger\" value=\"-\" onclick=\"javascript:removeElement('imRemovable$counter','$counter');\" >
                        </td>
                    </tr>
                    <tr id=\"tardivid$counter\">
                        <div>
                        <td scope=\"col\" width=\"33%\" class=\"iEntry\">
                            <div class=\"inputfield\">
                                <div class=\"rLabel\">
                                    <label class=\"\" for=\"textinput\">".$this->lang->line('drugs')." : </label>                
                                </div>
                                <div class=\"textfield btm20padding\">
                                      <select id=\"drug[]\" name=\"drug[]\" class=\"chosen-select-rtl$counter form-control nopadding\"  tabindex=\"4\">
                                        <option value=\"0\">".$this->lang->line('select')."</option>
                                        ";  
                                        if($spent_drugs){
                                            foreach($spent_drugs as $sd){
                                                $content .= "<option value=\"$sd->urn\">$sd->name</option>"; 
                                            }
                                        }
                        $content .="</select>
                                </div>
                            </div> 
                        </td> 
                        <td scope=\"col\" width=\"67%\" class=\"iEntry\" colspan=\"2\">
                            <table class=\"table\"> 
                                <tr>
                                    <td scope=\"col\" width=\"20%\" style=\"border:none;padding:0;\">
                                        <div class=\"inputfield\">
                                            <div class=\"rLabel\">
                                                <label class=\"\" for=\"textinput\">".$this->lang->line('price')." : </label>                
                                            </div>
                                            <div class=\"textfield btm20padding\">
                                                <input id=\"price$counter\" name=\"price[]\" type=\"text\" placeholder=\"$pirce_label\" class=\"form-control iInput\" style=\"min-width:140px; width:140px; display:inline;\" onkeyup=\"totalThePrice('price$counter','amount$counter','total$counter')\">
                                            </div>
                                        </div>
                                    </td>
                                    <td scope=\"col\" width=\"20%\" style=\"border:none;padding:0;\">
                                        <div class=\"inputfield\">
                                            <div class=\"rLabel\">
                                                &nbsp;&nbsp;&nbsp;<label class=\"\" for=\"textinput\">".$this->lang->line('amount')." : </label>                
                                            </div>
                                            <div class=\"textfield btm20padding\">
                                                &nbsp;&nbsp;<input id=\"amount$counter\" name=\"amount[]\" type=\"text\" placeholder=\"$amount_lable\" class=\"form-control iInput\" style=\"width:120px; min-width:120px; display:inline;\" onkeyup=\"totalThePrice('price$counter','amount$counter','total$counter')\"> 
                                            </div>
                                        </div>
                                    </td>
                                    <td scope=\"col\" width=\"60%\" style=\"border:none;padding:0;\">
                                        <div class=\"inputfield\">
                                            <div class=\"rLabel\">
                                                &nbsp;&nbsp;&nbsp;<label class=\"\" for=\"textinput\">".$this->lang->line('total')." : </label>                
                                            </div>
                                            <div class=\"textfield btm20padding\">
                                                &nbsp;&nbsp;<input id=\"total$counter\" name=\"total[]\" type=\"text\" placeholder=\"$total_amount\" class=\"form-control iInput\" style=\"width:155px; min-width:157px; display:inline;\" onkeyup=\"totalThePrice('price$counter','amount$counter','total$counter')\"> 
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td> 
                        </div>  
                    </tr>";
        }
        echo $content;
    }
    
    /**
    * @desc multiple used drug function
    */
    function multiple_used_drug()
    {
        //echo "<pre>";print_r($_POST);exit;
        $counter = $this->input->post('no');
        $pirce_label = $this->lang->line('price');
        $amount_lable = $this->lang->line('amount');
        $select = $this->lang->line('select');
        $base_url = base_url();
        $used_drugs = $this->register_model->get_used_drugs(0);
        $content = "";
        if($counter >0){
            $content .= "<div id=\"imDeletable$counter\" style=\"margin-top:5px;\">
                        <select id=\"used_drugs$counter\" name=\"used_drugs[]\" class=\"form-control nopadding\" style=\"width:255px;display:inline\">
                            <option value=\"0\">$select</option>
                            ";
                            if($used_drugs){
                                foreach($used_drugs as $ud){
                                    $content .= "<option value=\"$ud->urn\">$ud->name</option>";
                                }
                            }
                        $content .= "</select>
                        <input type=\"button\" id=\"rm\" class=\"btn btn-danger\" value=\"-\" onclick=\"javascript:removeElement('imDeletable$counter','$counter');\" style=\"min-width:40px;margin-right:5px\">
                        </div>";
        }
        echo $content;
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
            
            // start day
            $str_post_str .= '&fday='.$this->input->post('fday');
            //echo $str_post_str; exit;
            // start month
            $str_post_str .= '&fmonth='.$this->input->post('fmonth');
            // start year
            $str_post_str .= '&fyear='.$this->input->post('fyear');
            
            $recpage  = $this->config->item('recordperpage');//number of records per page
            $starting = $this->input->post('starting');         //get counter which page record
            //if its the first page than show starting from 0
            if(!$starting)
            {
                $starting =0;
            }
            
            
            $records   = $this->next_model->search_records($starting,$recpage,FALSE);
            $rec_total = $this->next_model->search_records($starting,$recpage,TRUE);
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
                base_url()."index.php/next_visit/home/filter",
                'list_div1',
                $str_post_str
            );
             
            $data["search"] = TRUE;
            $data['page']   = $starting;
            $data['total']  = $this->ajax_pagination_new->total;
            //$data['page']       = $page;
            $data['title']      = $this->lang->line("register_list");
            //$data['page']   = $starting;
            $data['links']  = $this->ajax_pagination_new->anchors;
            //$data['total']  = $this->ajax_pagination->total;
            $this->load->view("filter/next_filter_list",$data);
        }
        else
        {
            echo $this->load->view('unauthorized');
        }
    }
        
}

?>
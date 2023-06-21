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
        //load libraries
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->library('user_agent');
        //helper
        $this->load->helper('datecheck');
        //load language file
        $this->lang->load("home");
        $this->lang->load("global");
        //load models
        $this->load->model(array('register/register_model','urn_model','document/document_model','xray/xray_model'));
        
        $this->amc_auth->is_logged_in();
    }

    /**
    * @desc index function
    */
    public function index()
    {
        redirect('register/home/register_list');
    }
   
    /**
    * @desc register list function
    */
    function register_list($page = 0)
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
        
        $records   = $this->register_model->getAllrecords($starting,$recpage,FALSE);
        $rec_total = $this->register_model->getAllrecords($starting,$recpage,TRUE);
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
            base_url()."index.php/register/home/register_list",
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
        $data['title']      = $this->lang->line("register_list"); 
        $data['filter']  = $this->load->view("filter/register_filter",$data,true); 
        if($this->input->post('ajax')==1)
        {
            $data["search"] = TRUE;
            $this->load->view('filter/reg_filter_list',$data);
        }else{                                                   
            banner();
            sidebar();
            $modal = modal_popup();
            $content = $this->load->view('register/register_list',$data,true);
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
            $data['title'] = $this->lang->line("register_view");
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
                $content = $this->load->view("register/register_view",$data,true);
            }
            content($content);
            footer();   
        }
    }
    
    /**
    * @desc add to the register
    */
    function register_add($queue_urn = 0)
    {
        if(0){
            echo "You are not loged in";exit;
        }else{
            $this->form_validation->set_rules('name', 'name', 'trim|required');
            $this->form_validation->set_rules('f_name', 'f_name', 'trim|required');
            $this->form_validation->set_rules('contact', 'contact', 'trim');
            $this->form_validation->set_rules('address', 'address', 'trim');
            if($this->form_validation->run() == FALSE){
                $dect_q_urn = $this->clean_encrypt->decode($queue_urn);
                //top title
                $data['title'] = $this->lang->line("register");
                //if it come form queue list
                if($dect_q_urn){
                    $sub_rec = $this->register_model->getQueueByURN($dect_q_urn);
                    $sub_details = $sub_rec[0];
                }
                else{
                    $sub_details = false;   
                }
                //get used drugs
                $used_drugs = $this->register_model->get_used_drugs(0);
                $data['used_drugs'] = $used_drugs;
                
                //get next visits
                $next_visit = $this->register_model->getStaticData("stable","qu");  
                $data['next_visit'] = $next_visit;
                
                //doctors
                $doctors = $this->register_model->doctors();
                $data['doctors']        = $doctors;
                $data['queue_details']  = $sub_details;
                $data['queue_urn']      = $dect_q_urn;
                //date and time dropdown data
                $data['days']           = $this->getDateDetails('days');
                $data['months']         = $this->getDateDetails('months');
                $data['years']          = $this->getDateDetails('years');
                //times
                $data['hour']           = $this->getTimeDetails('hour');
                $data['minute']         = $this->getTimeDetails('minute');
                
                //get spent drugs
                $spent_drugs = $this->register_model->spentDrugsStatic();                
                $data['spent_drugs'] = $spent_drugs;
                /*echo "<pre>";print_r($spent_drugs);exit;*/ 
                
                //load views and templates
                banner();
                sidebar();
                $modal = modal_popup();
                $data['fill_add']       = $this->load->view("register/fill_add",$data,true);
                $data['cover_add']      = $this->load->view("register/cover_add",$data,true);
                $data['build_add']      = $this->load->view("register/build_add",$data,true);
                $data['clean_add']      = $this->load->view("register/clean_add",$data,true);
                $data['orthodant_add']  = $this->load->view("register/orthodant_add",$data,true);
                $data['exo_add']        = $this->load->view("register/exo_add",$data,true);
                $content = $this->load->view("register/register_add",$data,true);
                content($content);
                footer();
            }
            else{
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
                
                $queue_urn  = $this->input->post('queue_urn');
                $check = $this->register_model->check_if_exist($name,$f_name,$visit, $contact);
                //echo "<pre>";print_r($check);exit;
                if($check){
                    $this->view($this->clean_encrypt->encode($check[0]->urn));
                }else{       
                    $urn = $this->urn_model->getURN('register','urn');
                    $id_date = date("Y-m-d");
                    $gre_date = explode("-",$id_date);
                    $theYear = gregorian_to_jalali($gre_date[0],$gre_date[1],$gre_date[2],"/");
                    $jlali_year = explode("/",$theYear);
                    //echo "<pre>";print_r($jlali_year);exit;
                    //$p_id = $this->urn_model->getPatientId('register','patient_id');
                    $p_id = "NDC".$jlali_year[0].$urn;
                    $data = array(
                        "queue_urn"             => $queue_urn,
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
                        //echo "<pre>";print_r($_FILES);exit;
                        if($_FILES['attachment']['size']>0){
                            //echo "<pre>";print_r($_FILES);exit;
                            if($this->upload($urn.'_'.rand(99,1000).date('Ymd His'),'attachment')){
                                $formdata = array(
                                    'att_id'            => $urn,
                                    'ent_id'            => $p_id,
                                    'att_act_name'      => $_FILES['attachment']['name'],
                                    'att_path_name'     => $this->file_info['file_name'],
                                    'att_extension'     => $this->file_info['file_ext'],
                                    'att_size'          => $this->file_info['file_size'],
                                    'att_type'          => 5,
                                    'user_id'            => 1000001,
                                    'reg_date'          => date('Y-m-d H:i:s'),
                                    'att_entity_urn'    => $urn,
                                    'att_entity_type'   => 'patient_reg'
                                );
                                $att_urn =  $this->urn_model->getURN('attachment','urn');
                                //echo $att_urn;exit;
                                $this->document_model->uploads('attachment',$att_urn,$formdata,'1000001','INSERT');
                            }
                        }
                    }
                    /****************************Attachment*************************/
                    
                    if($update){
                        redirect("register/home/view/".$this->clean_encrypt->encode($urn));
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
    * @desc edit registered data
    */
    function register_edit($urn = 0)
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
                $data['title'] = $this->lang->line("register_edit");
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
                    $data['days']       = $this->getProDateDetails('days',$addateday,0,0);
                    $data['months']     = $this->getProDateDetails('months',0,$addatemonth,0);
                    $data['years']      = $this->getProDateDetails('years',0,0,$addateyear);
                    //next visit time
                    $next_visit_time =  $records[0]->next_time; 
                    $next_time = explode(":",$next_visit_time);
                    $next_hour =  $next_time[0];
                    $next_minute =  $next_time[1];
                    $data['nhour']       = $this->getProTimeDetails('hour',$next_hour,0,0);
                    $data['nminute']     = $this->getProTimeDetails('minute',0,$next_minute,0);
                    
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
                $data['cover_edit'] = $this->load->view("register/drugs/cover_edit",$data,true); 
                $data['build_edit'] = $this->load->view("register/drugs/build_edit",$data,true); 
                $data['fill_edit'] = $this->load->view("register/drugs/fill_edit",$data,true); 
                $data['clean_edit'] = $this->load->view("register/drugs/clean_edit",$data,true);
                $data['ortho_edit'] = $this->load->view("register/drugs/ortho_edit",$data,true);
                $data['exo_edit'] = $this->load->view("register/drugs/exo_edit",$data,true);
                //the views if the record is not exist
                $data['fill_add'] = $this->load->view("register/fill_add",$data,true);
                $data['cover_add'] = $this->load->view("register/cover_add",$data,true);
                $data['build_add'] = $this->load->view("register/build_add",$data,true);
                $data['clean_add'] = $this->load->view("register/clean_add",$data,true);
                $data['ortho_add'] = $this->load->view("register/orthodant_add",$data,true);
                $data['exo_add'] = $this->load->view("register/exo_add",$data,true);
 
                $content = $this->load->view("register/register_edit",$data,true);
                content($content);
                footer(); 
            }
            else{
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
                
                $queue_urn  = $this->input->post('queue_urn');
                $check = $this->register_model->check_if_exist($name,$f_name,$visit, $contact);
                $id_date = date("Y-m-d");
                $gre_date = explode("-",$id_date);
                $theYear = gregorian_to_jalali($gre_date[0],$gre_date[1],$gre_date[2],"/");
                $jlali_year = explode("/",$theYear);
                //echo "<pre>";print_r($jlali_year);exit;
                //$p_id = $this->urn_model->getPatientId('register','patient_id');
                $p_id = "NDC".$jlali_year[0].$urn;
                $data = array(
                    //"patient_id"            => $p_id,
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
                    "next_time"             => $time
                );
                //echo "<pre>";print_r($data);exit;
                $update = $this->register_model->update('register',$dec_urn,$data,1000001,"UPDATE");
                if($update == true && $fill == 1){
                    $this->fill_teeth_edit($dec_urn);        
                }
                if($update == true && $cover == 1){
                    $this->cover_teeth_edit($dec_urn);        
                }
                if($update == true && $build == 1){
                    $this->build_teeth_edit($dec_urn);        
                }
                if($update == true && $clean == 1){
                    $this->clean_teeth_edit($dec_urn);        
                }
                if($update == true && $ortho == 1){
                    $this->ortho_teeth_edit($dec_urn);        
                }
                if($update == true && $exo == 1){
                    $this->exo_teeth_edit($dec_urn);        
                }
                //used drugs
                if($update){
                    $used_drug       = $this->input->post('used_drugs');  
                    $used_urn = $this->input->post('used_drug_urn');  
                    $used_drug_data = array();
                    foreach($used_drug AS $keys=>$values){
                        if($used_drug[$keys] != '' && $used_drug[$keys] != 0){
                            $used_drug_data = array(
                                "parent_urn"                => $dec_urn,
                                "name"                      => $used_drug[$keys]
                            );
                            //echo $used_urn[$keys];exit;
                            if($this->register_model->usedExist($dec_urn,$used_urn[$keys],'used_drug')){
                                $updateUsedDrug = $this->register_model->update('used_drug',$used_urn[$keys],$used_drug_data,1000001,"UPDATE");
                            }else{
                                $used_drug_urn = $this->urn_model->getURN('used_drug','urn');
                                $updateUsedDrug = $this->register_model->update('used_drug',$used_drug_urn,$used_drug_data,1000001,"INSERT");
                            }
                        }
                    }    
                }
                //spent drugs
                if($update){
                    $drug       = $this->input->post('drug');    
                    $price      = $this->input->post('price');       
                    $amount     = $this->input->post('amount');       
                    $total      = $this->input->post('total');
                    $spent_urn  = $this->input->post('spent_drug_urn');
                    $drug_data = array();
                    foreach($drug AS $key=>$value){
                        if($drug[$key] != '' && $drug[$key] != 0){
                            $drug_data = array(
                                "parent_urn"                => $dec_urn,
                                "name"                      => $drug[$key],
                                "amout"                     => $amount[$key],
                                "price"                     => $price[$key],
                                "total_price"               => $total[$key]
                            );
                            if($this->register_model->usedExist($dec_urn,$spent_urn[$key],'spent_drug')){
                                $updateUsedDrug = $this->register_model->update('spent_drug',$spent_urn[$key],$drug_data,1000001,"UPDATE");
                            }else{
                                
                                $drug_urn = $this->urn_model->getURN('spent_drug','urn');
                                $updateDrug = $this->register_model->update('spent_drug',$drug_urn,$drug_data,1000001,"INSERT");
                            }
                        }
                    }     
                }
                
                if($update){
                    redirect("register/home/view/".$urn);
                }

                //}
            }
        }
    }
    
    /**
    * @desc fill teeth edit function
    */
    function fill_teeth_edit($register_urn = 0)
    {
        if($register_urn != 0){
            /****************************fill teeth starts*/
            $urn         = $this->input->post('fill_urn');
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
                $ifExist = $this->register_model->teethExist($register_urn,$urn, $ill_type);
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
                if($ifExist){
                    $update = $this->register_model->updateTeeth('teeth',$urn,$fill_data,1000001,"UPDATE",$ill_type); 
                }else{
                    $fill_urn = $this->urn_model->getURN('teeth','urn');
                    $update = $this->register_model->update('teeth',$fill_urn,$fill_data,1000001,"INSERT"); 
                }   
            } 
            /****************************fill teeth ends*/
        }
    }
    
    /**
    * @desc cover teeth edit function
    */
    function cover_teeth_edit($register_urn = 0)
    {
        if($register_urn != 0){
            $urn         = $this->input->post('cover_urn');
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
            if(($golden != '' || $silver != '' || $samecolor != ''  || $zarconiam != '' || $mx != '') || ($ctopl1 != '' || $ctopl2 != '' || $ctopl3 != '' || $ctopl4 != '' || $ctopl5 != '' || $ctopl6 != '' || $ctopl7 != '' || $ctopl8 != '' || $ctopr1 != '' || $ctopr2 != '' || $ctopr3 != '' || $ctopr4 != '' || $ctopr5 != '' || $ctopr6 != '' || $ctopr7 != '' || $ctopr8 != '' || $cbottomr1 != '' || $cbottomr2 != '' || $cbottomr3 != '' || $cbottomr4 != '' || $cbottomr5 != '' || $cbottomr6 != '' || $cbottomr7 != '' || $cbottomr8 != '' || $cbottoml1 != '' || $cbottoml2 != '' || $cbottoml3 != '' || $cbottoml4 != '' || $cbottoml5 != '' || $cbottoml6 != '' || $cbottoml7 != '' || $cbottoml8 != ''))
            {
                $ill_type = 2;
                $ifExist = $this->register_model->teethExist($register_urn,$urn, $ill_type); 
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
                //echo "<pre>";print_r($cover_data);exit;
                if($ifExist){
                    $update = $this->register_model->updateTeeth('teeth',$urn,$cover_data,1000001,"UPDATE",$ill_type); 
                }else{
                    $fill_urn = $this->urn_model->getURN('teeth','urn');
                    $update = $this->register_model->update('teeth',$fill_urn,$cover_data,1000001,"INSERT");  
                }   
            } 
            /****************************cover teeth ends*/
        }
    }
    
    /**
    * @desc cover teeth edit function
    */
    function build_teeth_edit($register_urn = 0)
    {
        if($register_urn != 0){
            $urn         = $this->input->post('build_urn'); 
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
            if(($implent != '' || $complete != '' || $partial != ''  || $ccpalet != '' || $fulbredge != '') || ($btopl1 != '' || $btopl2 != '' || $btopl3 != '' || $btopl4 != '' || $btopl5 != '' || $btopl6 != '' || $btopl7 != '' || $btopl8 != '' || $btopr1 != '' || $btopr2 != '' || $btopr3 != '' || $btopr4 != '' || $btopr5 != '' || $btopr6 != '' || $btopr7 != '' || $btopr8 != '' || $bbottomr1 != '' || $bbottomr2 != '' || $bbottomr3 != '' || $bbottomr4 != '' || $bbottomr5 != '' || $bbottomr6 != '' || $bbottomr7 != '' || $bbottomr8 != '' || $bbottoml1 != '' || $bbottoml2 != '' || $bbottoml3 != '' || $bbottoml4 != '' || $bbottoml5 != '' || $bbottoml6 != '' || $bbottoml7 != '' || $bbottoml8 != ''))
            {
                $ill_type = 3;
                $ifExist = $this->register_model->teethExist($register_urn, $urn, $ill_type);
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
                //echo "<pre>";print_r($build_data);exit;
                if($ifExist){
                    $update = $this->register_model->updateTeeth('teeth',$urn,$build_data,1000001,"UPDATE",$ill_type); 
                }else{
                    $fill_urn = $this->urn_model->getURN('teeth','urn');
                    $update = $this->register_model->update('teeth',$fill_urn,$build_data,1000001,"INSERT");  
                } 
            } 
            /****************************build teeth ends*/
        }
    }
    
    /**
    * @desc clean teeth edit function
    */
    function clean_teeth_edit($register_urn)
    {
        if($register_urn != 0){
            $urn         = $this->input->post('clean_urn'); 
            /****************************build teeth starts*/
            $jermgery       = $this->input->post('jermgery');
            $bleching       = $this->input->post('bleching');
            if($jermgery != '' || $bleching != ''){
                $ill_type = 4;
                $ifExist = $this->register_model->teethExist($register_urn,$urn, $ill_type);
                $clean_data = array(
                    "register_urn"                  => $register_urn,
                    "ill_type"                      => $ill_type,
                    
                    "jermgery"                      => $jermgery,
                    "bleching"                      => $bleching
                );
                //echo "<pre>";print_r($fill_data);exit;
                if($ifExist){
                    $update = $this->register_model->updateTeeth('teeth',$urn,$clean_data,1000001,"UPDATE",$ill_type); 
                }else{
                    $clean_urn = $this->urn_model->getURN('teeth','urn');
                    $update = $this->register_model->update('teeth',$clean_urn,$clean_data,1000001,"INSERT");  
                }
            }
        }
    }
    
    /**
    * @desc clean teeth edit function
    */
    function ortho_teeth_edit($register_urn)
    {
        if($register_urn != 0){
            $urn         = $this->input->post('orthodan_urn'); 
            /****************************build teeth starts*/
            $top_teeth          = $this->input->post('top_teeth');
            $bottom_teeth       = $this->input->post('bottom_teeth');
            $orth_complete      = $this->input->post('orth_complete');
            if($top_teeth != '' || $bottom_teeth != '' || $orth_complete != ''){
                $ill_type = 5;
                $ifExist = $this->register_model->teethExist($register_urn,$urn, $ill_type);
                $ortho_data = array(
                    "register_urn"                  => $register_urn,
                    "ill_type"                      => $ill_type,
                    
                    "top_teeth"                     => $top_teeth,
                    "bottom_teeth"                  => $bottom_teeth,
                    "all_teeth"                     => $orth_complete
                );
                //echo "<pre>";print_r($fill_data);exit;
                if($ifExist){
                    $update = $this->register_model->updateTeeth('teeth',$urn,$ortho_data,1000001,"UPDATE",$ill_type); 
                }else{
                    $ortho_urn = $this->urn_model->getURN('teeth','urn');
                    $update = $this->register_model->update('teeth',$ortho_urn,$ortho_data,1000001,"INSERT");  
                }
            }
        }
    }
    
    /**
    * @desc fill teeth edit function
    */
    function exo_teeth_edit($register_urn = 0)
    {
        if($register_urn != 0){
            /****************************fill teeth starts*/
            $urn         = $this->input->post('exo_urn');
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
                $ifExist = $this->register_model->teethExist($register_urn,$urn, $ill_type);
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
                if($ifExist){
                    $update = $this->register_model->updateTeeth('teeth',$urn,$fill_data,1000001,"UPDATE",$ill_type); 
                }else{
                    $fill_urn = $this->urn_model->getURN('teeth','urn');
                    $update = $this->register_model->update('teeth',$fill_urn,$fill_data,1000001,"INSERT"); 
                }   
            } 
            /****************************fill teeth ends*/
        }
    }
    
    /**
    * @desc add partial function
    */
    function add_partial($urn = 0)
    {
        $dec_urn = $this->clean_encrypt->decode($urn);
        if($dec_urn != 0){
            $updateDrug = "";
            $drug       = $this->input->post('drug');    
            $price      = $this->input->post('price');       
            $amount     = $this->input->post('amount');       
            $total      = $this->input->post('total');
            $drug_data = array();
            foreach($drug AS $key=>$value){
                if($drug[$key] != '' && $drug[$key] != 0){
                    $drug_data = array(
                        "parent_urn"                => $dec_urn,
                        "name"                      => $drug[$key],
                        "amout"                     => $amount[$key],
                        "price"                     => $price[$key],
                        "total_price"               => $total[$key]
                    );
                    $drug_urn = $this->urn_model->getURN('spent_drug','urn');
                    $updateDrug = $this->register_model->update('spent_drug',$drug_urn,$drug_data,1000001,"INSERT");
                }else{
                    redirect("register/home/view/".$this->clean_encrypt->encode($dec_urn));
                }
            }     
            if($updateDrug){
                redirect("register/home/view/".$this->clean_encrypt->encode($dec_urn));
            }
        }
    }
    
    /**
    * @desc add_used_drug function
    */
    function add_used_drug($urn = 0)
    {
        $dec_urn = $this->clean_encrypt->decode($urn);
        if($dec_urn != 0){
            $updateDrug = "";
            $used_drugs       = $this->input->post('used_drugs');    
            $used_drug_data = array();
            foreach($used_drugs AS $keys=>$values){
                if($used_drugs[$keys] != '' && $used_drugs[$keys] != 0){
                    $used_drug_data = array(
                        "parent_urn"                => $dec_urn,
                        "name"                      => $used_drugs[$keys]
                    );
                    $used_drug_urn = $this->urn_model->getURN('used_drug','urn');
                    $updateDrug = $this->register_model->update('used_drug',$used_drug_urn,$used_drug_data,1000001,"INSERT");
                }else{
                    redirect("register/home/view/".$urn,'refresh');
                }
            }     
            if($updateDrug){
                redirect("register/home/view/".$urn,'refresh');
            }
        }
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
            //echo $var;exit; 
            $year1 = date("Y")-621;
            $i = $year1+1;
            $range = $year1-12;
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
    * upload attachment function
    */
    function upload($file_name=0, $inputName = "")
    {
        if($_FILES){
            $config['upload_path']      =  "./uploads";
            $config['allowed_types']    =  "*";
            $config['file_name']        =  $file_name;
            $config['max_size']         =  '99999999999';
            $this->upload->initialize($config);
            if(!$this->upload->do_upload($inputName)){
                return FALSE;
            }else{
                $this->file_info = $this->upload->data();
                return TRUE;
            }
        }
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
            // father name
            if($this->input->post('f_name') != "")
            {
                $str_post_str .= '&f_name='.$this->input->post('f_name');
            }
            // contact
            if($this->input->post('contact') != "")
            {
                $str_post_str .= '&contact='.$this->input->post('contact');
            }
            // fee
            if($this->input->post('fee') != "0")
            {
                $str_post_str .= '&fee='.$this->input->post('fee');
            }
            // visit
            if($this->input->post('visit') != "")
            {
                $str_post_str .= '&visit='.$this->input->post('visit');
            }
            // remains
            if($this->input->post('remains') != "")
            {
                $str_post_str .= '&remains='.$this->input->post('remains');
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
            
            
            $records   = $this->register_model->search_records($starting,$recpage,FALSE);
            $rec_total = $this->register_model->search_records($starting,$recpage,TRUE);
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
                base_url()."index.php/register/home/filter",
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
            $this->load->view("filter/reg_filter_list",$data);
        }
        else
        {
            echo $this->load->view('unauthorized');
        }
    }
    
    /**
    * @desc print report
    */
    function genDBexelprint()
    {
        $allsql = $this->input->post('allsql');
        $allsql = $this->clean_encrypt->decode($allsql);
        //echo "<pre>".$allsql;exit;
        //Get data from the database (model)
        $reportObj = $this->register_model->search_records($allsql);
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
            $excel->getActiveSheet()->setTitle($this->lang->line('reg_reports'));
            $excel->getActiveSheet()->getSheetView()->setZoomScale(100);
            //$lang = $this->mng_auth->get_language();
            $excel->getActiveSheet()->setRightToLeft(true);
            $rotation = -90;
            ini_set('memory_limit','4026M');
            $excel->getActiveSheet()->setShowGridlines(FALSE);
            $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $excel->getActiveSheet()->getPageSetup()->setFitToPage(true);
            $excel->getActiveSheet()->getPageSetup()->setFitToHeight(0);
            $excel->getActiveSheet()->getPageMargins()->setTop(0.8);
            $excel->getActiveSheet()->getPageMargins()->setRight(0.9);
            $excel->getActiveSheet()->getPageMargins()->setLeft(0.9);
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
                        'size'  => 16,
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
                        'size'  => 10,
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
            $excel->getActiveSheet()->setShowGridlines(FALSE);
            //$excel->getActiveSheet()->getStyle('A'.$countrow.':O'.$countrow)->applyFromArray($border);
            $excel->getActiveSheet()->getStyle('C'.$countrow.':M'.$countrow)->applyFromArray($headerStyle);
            $excel->getActiveSheet()->getStyle('C1')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C2:M2')->applyFromArray($styleTitle);
            $excel->getActiveSheet()->getStyle('N1:Q1')->applyFromArray($styleInfo);
            $excel->getActiveSheet()->getStyle('N1')->getAlignment()->setWrapText(true);
            
            $excel->getActiveSheet()->mergeCells('A'.$countrow.':B'.$countrow);
            $excel->getActiveSheet()->mergeCells('C'.$countrow.':M'.$countrow);
            $excel->getActiveSheet()->mergeCells('N'.$countrow.':Q'.$countrow);
            $excel->getActiveSheet()->mergeCells('A2:B2');
            $excel->getActiveSheet()->mergeCells('C2:M2');
            $excel->getActiveSheet()->mergeCells('N2:Q2');
            
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
            $objDrawing->setOffsetX(8);    // setOffsetX works properly
            $objDrawing->setOffsetY(8);  //setOffsetY has no effect
            $objDrawing->setCoordinates('A1'); 
        
            ////number
            $title1 = $this->lang->line('ndcdr');
            $title12 = $this->lang->line('ndcen');
            $main_title = $this->lang->line('reg_report');
            $title = $title1.PHP_EOL.$title12;
            
            $phone = $this->lang->line('d_contacts');
            $email = $this->lang->line('email');
            $address = $this->lang->line('address');
            
            $info = $phone.PHP_EOL.$email.PHP_EOL.$address;
            
            $excel->getActiveSheet()->setCellValue('C2',$main_title);
            $excel->getActiveSheet()->setCellValue('C1',$title);
            $excel->getActiveSheet()->setCellValue('N1',$info);
            $excel->getActiveSheet()->setCellValue('A3',$this->lang->line('id'));
            $excel->getActiveSheet()->setCellValue('B'.$countrow,$this->lang->line('serial_no'));
            $excel->getActiveSheet()->setCellValue('C'.$countrow,$this->lang->line('name'));
            $excel->getActiveSheet()->setCellValue('D'.$countrow,$this->lang->line('f_name'));
            $excel->getActiveSheet()->setCellValue('E'.$countrow,$this->lang->line('contact'));
            //ill type
            $excel->getActiveSheet()->setCellValue('F'.$countrow,$this->lang->line('fills'));
            $excel->getActiveSheet()->setCellValue('G'.$countrow,$this->lang->line('covers'));
            $excel->getActiveSheet()->setCellValue('H'.$countrow,$this->lang->line('builds'));
            $excel->getActiveSheet()->setCellValue('I'.$countrow,$this->lang->line('cleaned'));
            $excel->getActiveSheet()->setCellValue('J'.$countrow,$this->lang->line('ortodant'));
            $excel->getActiveSheet()->setCellValue('K'.$countrow,$this->lang->line('exodontic'));
            
            $excel->getActiveSheet()->setCellValue('L'.$countrow,$this->lang->line('visit'));
            $excel->getActiveSheet()->setCellValue('M'.$countrow,$this->lang->line('fee'));
            $excel->getActiveSheet()->setCellValue('N'.$countrow,$this->lang->line('remains'));
            $excel->getActiveSheet()->setCellValue('O'.$countrow,$this->lang->line('totalFee'));
            $excel->getActiveSheet()->setCellValue('P'.$countrow,$this->lang->line('total_drug'));
            $excel->getActiveSheet()->setCellValue('Q'.$countrow,$this->lang->line('registerDate'));

            $excel->getActiveSheet()->getStyle('A'.$countrow.':Q'.($countrow))->applyFromArray($styleTitles);
            $countrow++;


            $excel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
            $excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
            $excel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
            $excel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
            $excel->getActiveSheet()->getColumnDimension('F')->setWidth(7);
            $excel->getActiveSheet()->getColumnDimension('G')->setWidth(7);
            $excel->getActiveSheet()->getColumnDimension('H')->setWidth(7);
            $excel->getActiveSheet()->getColumnDimension('I')->setWidth(7);
            $excel->getActiveSheet()->getColumnDimension('J')->setWidth(7);
            $excel->getActiveSheet()->getColumnDimension('K')->setWidth(7);
            $excel->getActiveSheet()->getColumnDimension('L')->setWidth(9);
            $excel->getActiveSheet()->getColumnDimension('M')->setWidth(8);
            $excel->getActiveSheet()->getColumnDimension('N')->setWidth(8);
            $excel->getActiveSheet()->getColumnDimension('O')->setWidth(8);
            $excel->getActiveSheet()->getColumnDimension('P')->setWidth(14);
            $excel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);


            $totalcal=0;
            $counter = 1;
            $countrow = 4;
            $fee = 0;
            $remains = 0;
            $totalall = 0;
            $total_drug_price = 0;
            $totalRecords = $reportObj->num_rows();
            foreach($reportObj->result() AS $item)
            {
                $drugs = $this->register_model->getDrugPrice($item->urn);
                if($drugs){
                    $drug_price = $drugs[0]->total_price;
                }else{
                    $drug_price = "";
                }
                //sest text direction right to left
                $excel->getActiveSheet()->getStyle('Q'.$countrow)->getAlignment()->setReadorder(PHPExcel_Style_Alignment::READORDER_RTL);
                $excel->getActiveSheet()->setCellValue('A'.$countrow,$counter);
                $excel->getActiveSheet()->setCellValue('B'.$countrow,$item->patient_id);
                $excel->getActiveSheet()->setCellValue('C'.$countrow,$item->name);
                $excel->getActiveSheet()->setCellValue('D'.$countrow,$item->f_name);
                $excel->getActiveSheet()->setCellValue('E'.$countrow,$item->contact);
                
                //make tick mark for ill type
                $mark = "✓";
                if($item->fill_teeth == 1){
                    $excel->getActiveSheet()->setCellValue('F'.$countrow,$mark);
                }
                if($item->cover_teeth == 1){
                    $excel->getActiveSheet()->setCellValue('G'.$countrow,$mark);
                }
                if($item->build_teeth == 1){
                    $excel->getActiveSheet()->setCellValue('H'.$countrow,$mark);
                }
                if($item->clean == 1){
                    $excel->getActiveSheet()->setCellValue('I'.$countrow,$mark);
                }
                if($item->ortodancy == 1){
                    $excel->getActiveSheet()->setCellValue('J'.$countrow,$mark);
                }
                if($item->exodontics == 1){
                    $excel->getActiveSheet()->setCellValue('K'.$countrow,$mark);
                }
                
                //make visit static data
                $visit = "";
                $visits = $this->register_model->getStaticName($item->visit,"qu");
                if($visits){
                    $visit = $visits[0]->name;
                }
                $excel->getActiveSheet()->setCellValue('L'.$countrow,$visit);
                $excel->getActiveSheet()->setCellValue('M'.$countrow,$item->fee);
                $excel->getActiveSheet()->setCellValue('N'.$countrow,$item->remains);
                $excel->getActiveSheet()->setCellValue('O'.$countrow,$item->remains+$item->fee);
                $excel->getActiveSheet()->setCellValue('P'.$countrow,$drug_price);
                
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
                $excel->getActiveSheet()->setCellValue('Q'.$countrow,$reg_date);
                
                $fee +=  $item->fee;
                $remains += $item->remains;
                $totalall += $item->remains+$item->fee;
                $total_drug_price += $drug_price;
                
                $counter++;
                $countrow++;
            }


            $excel->getActiveSheet()->mergeCells('A'.$countrow.':L'.($countrow));
            $excel->getActiveSheet()->setCellValue('A'.$countrow,$this->lang->line('total'));
            $excel->getActiveSheet()->getStyle('A'.$countrow)->getFont()->setSize(14);
            $excel->getActiveSheet()->getRowDimension($countrow)->setRowHeight(24);
            
            $excel->getActiveSheet()->setCellValue('M'.$countrow,$fee);
            $excel->getActiveSheet()->setCellValue('N'.$countrow,$remains);
            $excel->getActiveSheet()->setCellValue('O'.$countrow,$totalall);
            $excel->getActiveSheet()->setCellValue('P'.$countrow,$total_drug_price);
            $excel->getActiveSheet()->getStyle('A3:Q'.($countrow))->applyFromArray($border);
            //$excel->getActiveSheet()->getStyle('A3:O'.($countrow))->applyFromArray($noBorder);
            $excel->getActiveSheet()->getStyle('A3:Q'.($countrow-1))->getAlignment()->setWrapText(true);
            $countrow++;
            $countrow++;
            $excel->getActiveSheet()->mergeCells('A'.$countrow.':Q'.$countrow); 
            $excel->getActiveSheet()->getStyle('A'.($countrow))->applyFromArray($styleTitle);
            $excel->getActiveSheet()->setCellValue('A'.$countrow,$this->lang->line('signature'));
            
            ob_end_clean();
            $name = $this->lang->line('reg_reports');
            // redirect to cleint browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=$name.xlsx");
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $objWriter->save('php://output');

        }
    }
    
    /**
    * @desc print report
    */
    function generalReport($gen_rep = 0)
    {
        if($gen_rep == 0){
            //get next visits
            $this->session->set_flashdata('msg','<div class="alert alert-success">'.$this->lang->line('please_pid').' !</div>');
            //patient id dropdown data
            $recortds = $this->xray_model->getRegData(false);
            if($recortds){
                $data['patientid'] = $recortds;
            }else{
                $data['patientid'] = "";
            }
            
            $next_visit = $this->register_model->getStaticData("stable","qu");  
            $data['next_visit'] = $next_visit;     
            //date and time dropdown data
            $data['days']           = $this->getDateDetails('days');
            $data['months']         = $this->getDateDetails('months');
            $data['years']          = $this->getDateDetails('years');
            $data['title']          = $this->lang->line("general_report"); 
                                                       
            banner();
            sidebar();
            $modal = modal_popup();
            $content = $this->load->view('general_report/report',$data,true);
            content($content);
            footer();
        }else{   
            $allsql = $this->input->post('allsql');
            $allsql = $this->clean_encrypt->decode($allsql);
            //echo "<pre>";print_r($_POST);exit;  
            if($this->input->post('patient_id') != '0'){  
                //Get data from the database (model)  
                //echo "<pre>";print_r($_POST);exit;
                $reportObj = $this->register_model->search_records($allsql);
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
                    $excel->getActiveSheet()->setTitle($this->lang->line('reg_reports'));
                    $excel->getActiveSheet()->getSheetView()->setZoomScale(100);
                    //$lang = $this->mng_auth->get_language();
                    $excel->getActiveSheet()->setRightToLeft(true);
                    $rotation = -90;
                    ini_set('memory_limit','4026M');
                    $excel->getActiveSheet()->setShowGridlines(FALSE);
                    $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                    $excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                    $excel->getActiveSheet()->getPageSetup()->setFitToPage(true);
                    $excel->getActiveSheet()->getPageSetup()->setFitToHeight(0);
                    $excel->getActiveSheet()->getPageMargins()->setTop(0.8);
                    $excel->getActiveSheet()->getPageMargins()->setRight(0.9);
                    $excel->getActiveSheet()->getPageMargins()->setLeft(0.9);
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
                                'size'  => 16,
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
                                'size'  => 10,
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
                    $excel->getActiveSheet()->setShowGridlines(FALSE);
                    //$excel->getActiveSheet()->getStyle('A'.$countrow.':O'.$countrow)->applyFromArray($border);
                    $excel->getActiveSheet()->getStyle('C'.$countrow.':M'.$countrow)->applyFromArray($headerStyle);
                    $excel->getActiveSheet()->getStyle('C1')->getAlignment()->setWrapText(true);
                    $excel->getActiveSheet()->getStyle('C2:M2')->applyFromArray($styleTitle);
                    $excel->getActiveSheet()->getStyle('N1:Q1')->applyFromArray($styleInfo);
                    $excel->getActiveSheet()->getStyle('N1')->getAlignment()->setWrapText(true);
                    
                    $excel->getActiveSheet()->mergeCells('A'.$countrow.':B'.$countrow);
                    $excel->getActiveSheet()->mergeCells('C'.$countrow.':M'.$countrow);
                    $excel->getActiveSheet()->mergeCells('N'.$countrow.':Q'.$countrow);
                    $excel->getActiveSheet()->mergeCells('A2:B2');
                    $excel->getActiveSheet()->mergeCells('C2:M2');
                    $excel->getActiveSheet()->mergeCells('N2:Q2');
                    
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
                    $objDrawing->setOffsetX(8);    // setOffsetX works properly
                    $objDrawing->setOffsetY(8);  //setOffsetY has no effect
                    $objDrawing->setCoordinates('A1'); 
                
                    ////number
                    $title1 = $this->lang->line('ndcdr');
                    $title12 = $this->lang->line('ndcen');
                    $main_title = $this->lang->line('reg_report');
                    $title = $title1.PHP_EOL.$title12;
                    
                    $phone = $this->lang->line('d_contacts');
                    $email = $this->lang->line('email');
                    $address = $this->lang->line('address');
                    
                    $info = $phone.PHP_EOL.$email.PHP_EOL.$address;
                    
                    $excel->getActiveSheet()->setCellValue('C2',$main_title);
                    $excel->getActiveSheet()->setCellValue('C1',$title);
                    $excel->getActiveSheet()->setCellValue('N1',$info);
                    $excel->getActiveSheet()->setCellValue('A3',$this->lang->line('id'));
                    $excel->getActiveSheet()->setCellValue('B'.$countrow,$this->lang->line('serial_no'));
                    $excel->getActiveSheet()->setCellValue('C'.$countrow,$this->lang->line('name'));
                    $excel->getActiveSheet()->setCellValue('D'.$countrow,$this->lang->line('f_name'));
                    $excel->getActiveSheet()->setCellValue('E'.$countrow,$this->lang->line('contact'));
                    //ill type
                    $excel->getActiveSheet()->setCellValue('F'.$countrow,$this->lang->line('fills'));
                    $excel->getActiveSheet()->setCellValue('G'.$countrow,$this->lang->line('covers'));
                    $excel->getActiveSheet()->setCellValue('H'.$countrow,$this->lang->line('builds'));
                    $excel->getActiveSheet()->setCellValue('I'.$countrow,$this->lang->line('cleaned'));
                    $excel->getActiveSheet()->setCellValue('J'.$countrow,$this->lang->line('ortodant'));
                    $excel->getActiveSheet()->setCellValue('K'.$countrow,$this->lang->line('exodontic'));
                    
                    $excel->getActiveSheet()->setCellValue('L'.$countrow,$this->lang->line('xray'));
                    $excel->getActiveSheet()->setCellValue('M'.$countrow,$this->lang->line('visit'));
                    $excel->getActiveSheet()->setCellValue('N'.$countrow,$this->lang->line('reg_fee'));
                    $excel->getActiveSheet()->setCellValue('O'.$countrow,$this->lang->line('xray_fee'));
                    $excel->getActiveSheet()->setCellValue('P'.$countrow,$this->lang->line('price'));
                    $excel->getActiveSheet()->setCellValue('Q'.$countrow,$this->lang->line('registerDate'));

                    $excel->getActiveSheet()->getStyle('A'.$countrow.':Q'.($countrow))->applyFromArray($styleTitles);
                    $countrow++;


                    $excel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
                    $excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                    $excel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
                    $excel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
                    $excel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
                    $excel->getActiveSheet()->getColumnDimension('F')->setWidth(7);
                    $excel->getActiveSheet()->getColumnDimension('G')->setWidth(7);
                    $excel->getActiveSheet()->getColumnDimension('H')->setWidth(7);
                    $excel->getActiveSheet()->getColumnDimension('I')->setWidth(7);
                    $excel->getActiveSheet()->getColumnDimension('J')->setWidth(7);
                    $excel->getActiveSheet()->getColumnDimension('K')->setWidth(7);
                    $excel->getActiveSheet()->getColumnDimension('L')->setWidth(7);
                    $excel->getActiveSheet()->getColumnDimension('M')->setWidth(7);
                    $excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
                    $excel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
                    $excel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
                    $excel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);


                    $totalcal=0;
                    $counter = 1;
                    $countrow = 4;
                    $fee = 0;
                    $remains = 0;
                    $totalall = 0;
                    $total_drug_price = 0;
                    $totalRecords = $reportObj->num_rows();
                    foreach($reportObj->result() AS $item)
                    {
                        $drugs = $this->register_model->getDrugPrice($item->urn);
                        if($drugs){
                            $drug_price = $drugs[0]->total_price;
                        }else{
                            $drug_price = "";
                        }
                        //sest text direction right to left
                        $excel->getActiveSheet()->getStyle('Q'.$countrow)->getAlignment()->setReadorder(PHPExcel_Style_Alignment::READORDER_RTL);
                        $excel->getActiveSheet()->setCellValue('A'.$countrow,$counter);
                        $excel->getActiveSheet()->setCellValue('B'.$countrow,$item->patient_id);
                        $excel->getActiveSheet()->setCellValue('C'.$countrow,$item->name);
                        $excel->getActiveSheet()->setCellValue('D'.$countrow,$item->f_name);
                        $excel->getActiveSheet()->setCellValue('E'.$countrow,$item->contact);
                        
                        //make tick mark for ill type
                        $mark = "✓";
                        if($item->fill_teeth == 1){
                            $excel->getActiveSheet()->setCellValue('F'.$countrow,$mark);
                        }
                        if($item->cover_teeth == 1){
                            $excel->getActiveSheet()->setCellValue('G'.$countrow,$mark);
                        }
                        if($item->build_teeth == 1){
                            $excel->getActiveSheet()->setCellValue('H'.$countrow,$mark);
                        }
                        if($item->clean == 1){
                            $excel->getActiveSheet()->setCellValue('I'.$countrow,$mark);
                        }
                        if($item->ortodancy == 1){
                            $excel->getActiveSheet()->setCellValue('J'.$countrow,$mark);
                        }
                        if($item->exodontics == 1){
                            $excel->getActiveSheet()->setCellValue('K'.$countrow,$mark);
                        }
                        
                        //make visit static data
                        $visit = "";
                        $visits = $this->register_model->getStaticName($item->visit,"qu");
                        if($visits){
                            $visit = $visits[0]->name;
                        }
                        $check_xray = $this->xray_model->check_xray($this->input->post('patient_id'));
                        print_r($check_xray);exit;
                        $excel->getActiveSheet()->setCellValue('L'.$countrow,"xray");
                        $excel->getActiveSheet()->setCellValue('M'.$countrow,$visit);
                        $excel->getActiveSheet()->setCellValue('N'.$countrow,$item->remains+$item->fee);
                        $excel->getActiveSheet()->setCellValue('O'.$countrow,"xrayFee");
                        $excel->getActiveSheet()->setCellValue('P'.$countrow,$drug_price);
                        
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
                        $excel->getActiveSheet()->setCellValue('Q'.$countrow,$reg_date);
                        
                        $fee +=  $item->fee;
                        $remains += $item->remains;
                        $totalall += $item->remains+$item->fee;
                        $total_drug_price += $drug_price;
                        
                        $counter++;
                        $countrow++;
                    }


                    $excel->getActiveSheet()->mergeCells('A'.$countrow.':L'.($countrow));
                    $excel->getActiveSheet()->setCellValue('A'.$countrow,$this->lang->line('total'));
                    $excel->getActiveSheet()->getStyle('A'.$countrow)->getFont()->setSize(14);
                    $excel->getActiveSheet()->getRowDimension($countrow)->setRowHeight(24);
                    
                    $excel->getActiveSheet()->setCellValue('M'.$countrow,$fee);
                    $excel->getActiveSheet()->setCellValue('N'.$countrow,$remains);
                    $excel->getActiveSheet()->setCellValue('O'.$countrow,$totalall);
                    $excel->getActiveSheet()->setCellValue('P'.$countrow,$total_drug_price);
                    $excel->getActiveSheet()->getStyle('A3:Q'.($countrow))->applyFromArray($border);
                    //$excel->getActiveSheet()->getStyle('A3:O'.($countrow))->applyFromArray($noBorder);
                    $excel->getActiveSheet()->getStyle('A3:Q'.($countrow-1))->getAlignment()->setWrapText(true);
                    
                    $countrow++;
                    $countrow++;
                    $excel->getActiveSheet()->mergeCells('C'.$countrow.':M'.$countrow); 
                    $excel->getActiveSheet()->getStyle('C'.($countrow))->applyFromArray($styleTitle);
                    $excel->getActiveSheet()->setCellValue('C'.$countrow,$this->lang->line('signature'));
                    
                    ob_end_clean();
                    $name = $this->lang->line('reg_reports');
                    // redirect to cleint browser
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header("Content-Disposition: attachment;filename=$name.xlsx");
                    header('Cache-Control: max-age=0');
                    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
                    $objWriter->save('php://output');

                }
            }else{
                $this->session->set_flashdata('msg','<div class="alert alert-danger">'.$this->lang->line('please_pid').' !</div>');   
                //patient id dropdown data
                $recortds = $this->xray_model->getRegData(false);
                if($recortds){
                    $data['patientid'] = $recortds;
                }else{
                    $data['patientid'] = "";
                }
                
                 //get next visits
                $next_visit = $this->register_model->getStaticData("stable","qu");  
                $data['next_visit'] = $next_visit;     
                //date and time dropdown data
                $data['days']           = $this->getDateDetails('days');
                $data['months']         = $this->getDateDetails('months');
                $data['years']          = $this->getDateDetails('years');
                $data['title']          = $this->lang->line("general_report"); 
                                                           
                banner();
                sidebar();
                $modal = modal_popup();
                $content = $this->load->view('general_report/report',$data,true);
                content($content);
                footer();
            }
        }
    }
}

?>
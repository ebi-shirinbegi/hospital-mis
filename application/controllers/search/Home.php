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
        $this->load->library('Clean_encrypt');  
        $this->load->library('Amc_auth');  
        //load libraries
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->library('user_agent');
        //load language file
        $this->lang->load("home");
        $this->lang->load("global");
        //load models
        $this->load->model(array('register/register_model','urn_model','document/document_model'));
        
        $this->amc_auth->is_logged_in();
    }

    /**
    * @desc index function
    */
    public function index()
    {
        redirect('search/home/search');
    }
    
    /**
    * @desc search function
    */
    function search()
    {
        $data = array();
        $data['title'] = $this->lang->line("search_page");
        banner();
        sidebar();
        $modal = modal_popup();
        $content = $this->load->view('search/search',$data,true);
        content($content);
        footer();
    }
    
    /**
    * @desc bring form function
    */
    function bringForm($form_code = "")
    {
        if($form_code == "reg"){
            $this->load->view("search/register_search");
        }elseif($form_code == "drug"){
            $this->load->view("search/drugs_search");
        }elseif($form_code == "remns"){
            $this->load->view("search/remains_search");
        }elseif($form_code == "expns"){
            $this->load->view("search/expense_search");
        }   
    }
    
    /**
    * @desc register list function
    */
    function register_list($page = 0)
    {
        /*******************************************************
        * @desc ************AJAX PAGINATION*********************
        ********************************************************/
         $config['base_url'] = base_url() . "index.php/register/home/register_list";
         $count = $this->register_model->getAllrecords(); 
         $total_row = count($count);
         $config["total_rows"] = $total_row;

         $config['per_page'] = 5;
         $perPage = $config['per_page'];

         $config['use_page_numbers'] = TRUE;
         $config['num_links'] = $total_row;
         $config['cur_tag_open'] = '&nbsp;<a class="current">';
         $config['cur_tag_close'] = '</a>';
         $config['next_link'] = $this->lang->line('next');
         $config['prev_link'] = $this->lang->line('prev');

         $this->pagination->initialize($config);
         if($this->uri->segment(3)){
             if($page != 0){
                $page = ($page*5)-5;
             }else{
                 $page = $page;
             }
         }
         else{
            $page = 1;
         }
         $str_links = $this->pagination->create_links();
         $data["links"] = explode('&nbsp;',$str_links );
         $register_list = $this->register_model->getAllrecords($perPage, $page);
         //echo "<pre>";print_r($register_list);exit; 
        /*******************************************************
        * @desc ************AJAX PAGINATION*********************
        ********************************************************/
        $data['page']       = $page;
        $data['title']      = $this->lang->line("register_list"); 
        $data['records']    = $register_list; 
        
        banner();
        sidebar();
        $modal = modal_popup();
        $content = $this->load->view('register/register_list',$data,true);
        content($content);
        footer();
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
   
}

?>
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
        $this->amc_auth->is_logged_in();
		//load libraries
        $this->load->library('form_validation');
		$this->load->library('upload');
		$this->load->library('user_agent');
		//load language file
        $this->lang->load("home");
		$this->lang->load("global");
		//load models
		$this->load->model(array('queue/queue_model','urn_model','document/document_model'));
	}

    /**
    * @desc index function
    */
	public function index()
	{
		redirect('queue/home/listRecords');
	}
    
	/**
    * @desc add function 
    */
	function add()
    {
		if(0){
			echo "You are not loged in";exit;
		}else{
			$this->form_validation->set_rules('name', 'name', 'trim|required');
			$this->form_validation->set_rules('f_name', 'f_name', 'trim|required');
			if($this->form_validation->run() == FALSE){
                $queue_date = $this->queue_model->queue_date();
                if($queue_date){
                    $parent_urn = $queue_date[0]->urn;
                }else{
                    $parent_urn = "";
                }
                $data['parent_urn'] = $parent_urn;
				$data['title'] = $this->lang->line("add_to_queue");
                $data['queue_no'] = $this->generateNo();
				banner();
				sidebar();
				$modal = modal_popup();
				$content = $this->load->view("queue/queue_add",$data,true);
				content($content);
				footer();
			}else{
                $parent_urn = $this->input->post('parent_urn');    
				$name = $this->input->post('name');	
				$f_name = $this->input->post('f_name');	
				$queue_no = $this->input->post('queue_no');
                $check = $this->queue_model->check_queue($name,$f_name);
                if($check){
                    $this->listRecords();
                }else{
				    $data = array(
                        "parent_urn"        => $parent_urn,
					    "name"				=> $name,
					    "f_name"			=> $f_name,
                        "no"                => $queue_no,
					    "registerdate"	    => date("Y/m/d")
				    );
				    $urn = $this->urn_model->getURN('queue','urn');
				    $update = $this->queue_model->update('queue',$urn,$data,1000001,"INSERT");
				    if($update){
                        $this->listRecords();
                    }else{
                        echo "<h1 class='alert alert-danger'>Faild Adding To Queue!</h1>";exit;
                    }
                }
			}
		}
	}
    
    /**
    * upload attachment function
    */
    function upload($file_name=0, $inputName = "")
    {
        if($_FILES){
            $config['upload_path']  =  "./uploads";
            $config['allowed_types']  =  "*";
            $config['file_name']  =  $file_name;
            $config['max_size']  =  '99999';
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
    * @desc generate queue number
    */
    function generateNo()
    {
        $range = 500;
        $content = "";
        for($i = 1; $i<=$range; $i++){
            $content .= "<option value='$i'>$i</option>";
        }
        return $content;  
    } 
    
    /**
    * @desc get date dropdown data
    */
    function getDateDetails($var = "",$day = 0,$month = 0, $year = 0)
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
    * @desc adding today date record
    */
    function add_date()
    {
        $day = $this->input->post('day');    
        $month = $this->input->post('month');    
        $year = $this->input->post('year');
        $date = jalali_to_gregorian($year,$month,$day,"/");   
        $queue_date = $this->queue_model->checkdate($date);
        if($queue_date){
            $this->listRecords();
        }else{
            $data = array(
                "registerdate"                => $date
            );
            $urn = $this->urn_model->getURN('queue_date','urn');
            $update = $this->queue_model->update('queue_date',$urn,$data,1000001,"INSERT");
            if($update){
                $this->listRecords();
            }else{
                echo "<h1 class='alert alert-danger'>Faild Adding To Queue!</h1>";exit;
            }
        }    
    }
    
    /**
    * @desc visit function
    */
    function visit()
    {
        $urn = $this->input->post("urn");
        $update = $this->queue_model->visit($urn);
        if($update){
        
        }else{
            exit("Can't Update Now");
        }
    }
    
    /**
    * @desc check if number existe
    */
    function checkNumber()
    {
        //echo "<pre>";print_r($_POST);exit;
        $no = $this->input->post("no");
        $check = $this->queue_model->checkIfTaken($no);
        if($check){
            echo "<div class='alert alert-danger' style='margin-bottom:-28px;padding:6px'>".$this->lang->line("taken")."</div>";
        }else{
            echo "<div class='alert alert-success' style='margin-bottom:-28px;padding:6px;'>".$this->lang->line("available")."</div>";
        }
    }
    
    /**
    * @desc list of not refered to doctor
    */
    public function listRecords($page = 0)
    {         
        //echo $page;exit;     
        $list_type = $this->input->post("no");
        if(($list_type == 0 || $list_type == 1) && $list_type != ""){
            $list_type = 0;
        }else{
            $list_type = "nothing";     
        } 
        
        /*******************************************************
        * @desc ************AJAX PAGINATION*********************
        ********************************************************/
        
         $config['base_url'] = base_url() . "index.php/queue/home/listRecords";
         $count = $this->queue_model->getTodayQueue(0); 
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
          //echo $page;exit;
         $str_links = $this->pagination->create_links();
         $data["links"] = explode('&nbsp;',$str_links );
         $queue_list = $this->queue_model->getTodayQueue(0,$perPage, $page); 
         //$data['showNotice']=$this->Nen_Model->showNotice($perPage, $page);
         
        /*******************************************************
        * @desc ************AJAX PAGINATION*********************
        ********************************************************/
        
        $queue_date = $this->queue_model->queue_date();
        /*echo date("Y-m-d");exit; */
        if($queue_date){
            $date_arr = explode(" ",$queue_date[0]->registerdate);
            $date_arr1 = explode("-",$date_arr[0]);               
            $jdate = gregorian_to_jalali($date_arr1[0],$date_arr1[1],$date_arr1[2],"/");
            $jdate_arr = explode("/",$jdate);
            $data['jday'] = $jdate_arr[2];
            $data['jmonth'] = $jdate_arr[1];
            $data['jyear'] = $jdate_arr[0];
        }else{
            $data['jday'] = false;
        }
        //adras datedetails
        $date_day = date("d");
        $date_month = date("m");
        $date_year = date("Y");
        $addate = gregorian_to_jalali($date_year,$date_month,$date_day,"/");
        $my_date = explode("/",$addate);
        $addateday = $my_date[2];
        $addatemonth = $my_date[1];
        $addateyear = $my_date[0];
        
        $data['days']       = $this->getDateDetails('days',$addateday,0,0);
        $data['months']     = $this->getDateDetails('months',0,$addatemonth,0);
        $data['years']      = $this->getDateDetails('years',0,0,$addateyear);
        
        //$date['mp_day'] = $this->datedetails(1,0,0,'day','dari',1,$addateday,1);
        //echo "<pre>";print_r($date['mp_day']);exit; 
        $data['page']       = $page;
        $data['title']      = $this->lang->line("queue_list"); 
        $data['records']    = $queue_list; 
        $data['list_type']  = 0;
        
        banner();
        sidebar();
        $modal = modal_popup();
        $content = $this->load->view('queue/queue_list',$data,true);
        content($content);
        footer();
    }
    
    /**
    * @desc list of refered to doctor
    */
    public function listRecord($page = 0)
    {         
        //echo $page;exit;     
        $list_type = $this->input->post("no");
        if(($list_type == 0 || $list_type == 1) && $list_type != ""){
            $list_type = 1;
        }else{
            $list_type = "nothing";     
        } 
        
        /*******************************************************
        * @desc ************AJAX PAGINATION*********************
        ********************************************************/
        
         $config['base_url'] = base_url() . "index.php/queue/home/listRecord";
         $count = $this->queue_model->getTodayQueue(1); 
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
          //echo $page;exit;
         $str_links = $this->pagination->create_links();
         $data["links"] = explode('&nbsp;',$str_links );
         $queue_list = $this->queue_model->getTodayQueue(1,$perPage, $page); 
         //$data['showNotice']=$this->Nen_Model->showNotice($perPage, $page);
         
        /*******************************************************
        * @desc ************AJAX PAGINATION*********************
        ********************************************************/
        
        $queue_date = $this->queue_model->queue_date();
        /*echo date("Y-m-d");exit; */
        if($queue_date){
            $date_arr = explode(" ",$queue_date[0]->registerdate);
            //echo "<pre>";print_r($date_arr);exit;
            $date_arr1 = explode("-",$date_arr[0]);
            $jdate = gregorian_to_jalali($date_arr1[0],$date_arr1[1],$date_arr1[2],"/");
            $jdate_arr = explode("/",$jdate);
            $data['jday'] = $jdate_arr[2];
            $data['jmonth'] = $jdate_arr[1];
            $data['jyear'] = $jdate_arr[0];
        }else{
            $data['jday'] = false;
        }
        $data['page']       = $page;
        $data['days']       = $this->getDateDetails('days');
        $data['months']     = $this->getDateDetails('months');
        $data['years']      = $this->getDateDetails('years');
        $data['title']      = $this->lang->line("queue_list"); 
        $data['records']    = $queue_list; 
        $data['list_type']  = 1; 
        //if($list_type == "nothing"){
            banner();
            sidebar();
            $modal = modal_popup();
            $content = $this->load->view('queue/queue_list',$data,true);
            content($content);
            footer();
        /*}else{
            $this->load->view('queue/filter_list',$data);
        }*/                                                  
    }
    
    /**
    * @desc datedetails function
    */
    function datedetails($day = 0, $month = 0, $year = 0, $field, $typeofdate = "", $foredit = 0, $section = 0, $stop =0)
    {
        if($day != 0){
            $StartDateDay = '';
            if($foredit == 0 || $section == "00"){
                if($stop == 0){
                    $StartDateDay .= "<option value='00' selected=\"selected\" ".set_select($field,'',TRUE) .">".$this->lang->line("day")."</option>";
                }else{
                    $StartDateDay .= "<option value='' selected=\"selected\" ".set_select($field,'',TRUE) .">".$this->lang->line("day")."</option>";    
                }
            }
            for($i = 1;$i<=31; $i++){
                if(strlen($i<2)){
                    $i = "0".$i;
                }
                if($section != 0 && $section == $i){
                    $StartDateDay .= "<option value='$i' selected=\"selected\">".$i."</option>";    
                }else{
                    $StartDateDay .= "<option value='".$i."' ".set_select($field,$i) .">".$i."</option>"; 
                }
            }
            return $StartDateDay;
        }
    }
}

?>
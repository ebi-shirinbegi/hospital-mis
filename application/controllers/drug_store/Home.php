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
        $this->load->model(array('drug_store/drug_model','urn_model','document/document_model'));
    }

    /**
    * @desc index function
    */
    public function index()
    {
        redirect('drug_store/home/listRecords');
    }
    
    /**
    * @desc register list function
    */
    function listRecordsxxx($page = 0)
    {
        /*******************************************************
        * @desc ************AJAX PAGINATION*********************
        ********************************************************/
         $config['base_url'] = base_url() . "index.php/drug_store/home/listRecords";
         $count = $this->drug_model->getAllrecords(); 
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
         $register_list = $this->drug_model->getAllrecords($perPage, $page);
         
        /*******************************************************
        * @desc ************AJAX PAGINATION*********************
        ********************************************************/
        $spent_drugs = $this->drug_model->spentDrugsStatic();                
        $data['spent_drugs'] = $spent_drugs;
        
        //date and time dropdown data
        $data['days']           = $this->getDateDetails('days');
        $data['months']         = $this->getDateDetails('months');
        $data['years']          = $this->getDateDetails('years');
        
        $data['total_rec']  = $total_row;
        $data['all_drug']   = $count;
        $data['page']       = $page;
        $data['title']      = $this->lang->line("drug_list"); 
        $data['records']    = $register_list; 
        $data['filter']  = $this->load->view("filter/drugStor_filter",$data,true); 
        
        banner();
        sidebar();
        $modal = modal_popup();
        $content = $this->load->view('drug_store/drug_list',$data,true);
        content($content);
        footer();
    }
    
    
    
    
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
        
        $records   = $this->drug_model->getAllrecords($starting,$recpage,FALSE);
        $rec_total = $this->drug_model->getAllrecords($starting,$recpage,TRUE);
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
            base_url()."index.php/drug_store/home/listRecords",
            'list_div1',
            $str_post_str
        );
         
        $data["search"] = FALSE;
        $data['page']   = $starting;
        $data['total']  = $this->ajax_pagination_new->total;
        $data['links']  = $this->ajax_pagination_new->anchors;  
        
        $spent_drugs = $this->drug_model->spentDrugsStatic();               
        $data['spent_drugs'] = $spent_drugs;
        
        //static drug names
        $count = $this->drug_model->getAllrecords();
        $data['all_drug']   = $count;    
        //date and time dropdown data
        $data['days']           = $this->getDateDetails('days');
        $data['months']         = $this->getDateDetails('months');
        $data['years']          = $this->getDateDetails('years');
        $data['title']          = $this->lang->line("drug_list"); 
        $data['filter']         = $this->load->view("filter/drugStor_filter",$data,true); 
        if($this->input->post('ajax')==1)
        {
            $data["search"] = TRUE;
            $this->load->view('filter/drug_filter_list',$data);
        }else{                                                   
            banner();
            sidebar();
            $modal = modal_popup();
            $content = $this->load->view('drug_store/drug_list',$data,true);
            content($content);
            footer();
        }
    }
    
    
    
    
    /**
    * @desc add to the register
    */
    function drug_add($queue_urn = 0)
    {
        if(0){
            echo "You are not loged in";exit;
        }else{
            $this->form_validation->set_rules('name[]', 'name[]', 'trim');
            $this->form_validation->set_rules('amount[]', 'amount[]', 'trim');
            $this->form_validation->set_rules('buy_price[]', 'buy_price[]', 'trim');
            $this->form_validation->set_rules('remark[]', 'remark[]', 'trim');
            if($this->form_validation->run() == FALSE){
                //load views and templates
                $data = array();
                $data['title'] = $this->lang->line('drug_add');
                banner();
                sidebar();
                $modal = modal_popup();
                $content = $this->load->view("drug_store/drug_add",$data,true);
                content($content);
                footer();
            }
            else{
                $updateDrug = "";
                $drug           = $this->input->post('name');  
                //print_r($_POST);exit; 
                $drug_type      = $this->input->post('drug_type');       
                $amount         = $this->input->post('amount');       
                $buy_price      = $this->input->post('buy_price');
                $sale_price     = $this->input->post('sale_price');
                $remark         = $this->input->post('remark');
                $drug_data = array();
                foreach($drug AS $key=>$value){
                    if($drug[$key] != ''){
                        $drug_data = array(
                            "name"                      => $drug[$key],
                            "type"                      => $drug_type[$key],
                            "amout"                     => $amount[$key],
                            "buy_price"                 => $buy_price[$key],
                            "sale_price"                => $sale_price[$key],
                            "remark"                    => $remark[$key],
                            "registerdate"              => date("Y-m-d")
                        );
                        $drug_urn = $this->urn_model->getURN('available_drugs','urn');
                        $updateDrug = $this->drug_model->update('available_drugs',$drug_urn,$drug_data,1000001,"INSERT");
                    }
                }
                if($updateDrug){
                    redirect("drug_store/home/listRecords",'refresh');
                }     
            }
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
            $this->form_validation->set_rules('name[]', 'name[]', 'trim');
            $this->form_validation->set_rules('amount[]', 'amount[]', 'trim');
            $this->form_validation->set_rules('buy_price[]', 'buy_price[]', 'trim');
            $this->form_validation->set_rules('remark[]', 'remark[]', 'trim');
            if($this->form_validation->run() == FALSE){
                $data = "";
                $records = false;
                //top title
                $data['title'] = $this->lang->line("register_edit");
                $records = $this->drug_model->getViewRecords($dec_urn);
                
                $data["record"]             = $records[0];
                $data["enc_urn"]            = $urn;
                
                //load views and templates
                banner();
                sidebar();
                $modal = modal_popup();
                //the views if the record is exist
                $content = $this->load->view("drug_store/drug_edit",$data,true);
                content($content);
                footer(); 
            }
            else{
                $updateDrug = "";
                $drug           = $this->input->post('name');  
                //print_r($_POST);exit; 
                $drug_type      = $this->input->post('drug_type');       
                $amount         = $this->input->post('amount');       
                $buy_price      = $this->input->post('buy_price');
                $sale_price     = $this->input->post('sale_price');
                $remark         = $this->input->post('remark');
                $drug_data = array();
                foreach($drug AS $key=>$value){
                    if($drug[$key] != ''){
                        $drug_data = array(
                            "name"                      => $drug[$key],
                            "type"                      => $drug_type[$key],
                            "amout"                     => $amount[$key],
                            "buy_price"                 => $buy_price[$key],
                            "sale_price"                => $sale_price[$key],
                            "remark"                    => $remark[$key]
                        );
                        $updateDrug = $this->drug_model->update('available_drugs',$dec_urn,$drug_data,1000001,"UPDATE");
                    }
                }
                if($updateDrug){
                    redirect("drug_store/home/view/$urn",'refresh');
                }     
            }
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
            //top title
            $data['title'] = $this->lang->line("drug_view");
            $records = $this->drug_model->getViewRecords($dec_urn);
            //echo "<pre>";print_r($records);exit;
            $data["record"]             = $records[0];
            //load views and templates
            banner();
            sidebar();
            $content = $this->load->view("drug_store/drug_view",$data,true);
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
        $counter = $this->input->post('no');
        $drug = $this->lang->line('drugs');
        $amount_lable = $this->lang->line('amount');
        $drug_type = $this->lang->line('drug_type');
        $sale_pice = $this->lang->line('sale_price');
        $buy_pice = $this->lang->line('buy_price');
        $remark = $this->lang->line('remark');
        $total_amount = $this->lang->line('total');
        $base_url = base_url();
        $content = "";
        if($counter >0){
            $content .= "<table class=\"table\" id=\"imRemovable$counter\">
                            <tr>
                                <td scope=\"col\" width=\"33%\" class=\"iEntry\" colspan=\"3\">
                                    <span class=\"btn btn-danger ino\">$counter</span><input type=\"button\" id=\"rm\" class=\"btn btn-danger\" value=\"-\" onclick=\"javascript:removeElement('imRemovable$counter','$counter');\" >
                                </td>
                                <!--<td scope=\"col\" width=\"33%\" class=\"iEntry\">
                                    <label class=\"\" for=\"textinput\">$total_amount : </label>                
                                    <input id=\"total$counter\" name=\"total$counter\" type=\"text\" placeholder=\"$total_amount\" class=\"form-control iInput\" disabled style=\"display:inline;max-width:200px;\"> 
                                    <input type=\"hidden\" id=\"total$counter\" name=\"total$counter\" type=\"text\" placeholder=\"$total_amount\" class=\"form-control iInput\" style=\"display:inline;max-width:200px;\">-->      
                                </td>
                            </tr>
                            <tr> 
                                <td scope=\"col\" width=\"33%\" class=\"iEntry\">
                                    <div class=\"inputfield\">
                                        <div class=\"rLabel\">
                                            <label class=\"\" for=\"textinput\">$drug : </label>                
                                        </div>
                                        <div class=\"textfield btm20padding\">
                                            <input id=\"name[]\" name=\"name[]\" type=\"text\" placeholder=\"$drug\" class=\"form-control iInput\" >     
                                        </div>
                                    </div>
                                </td>
                                <td scope=\"col\" width=\"34%\" class=\"iEntry\">
                                    <div class=\"inputfield\">
                                        <div class=\"rLabel\">
                                            <label class=\"\" for=\"textinput\">$drug_type : </label>                
                                        </div>
                                        <div class=\"textfield btm20padding\">
                                            <input id=\"drug_type[]\" name=\"drug_type[]\" type=\"text\" placeholder=\"$drug_type\" class=\"form-control iInput\">     
                                        </div>
                                    </div>    
                                </td>
                                <td scope=\"col\" width=\"33%\" class=\"iEntry\">
                                    <div class=\"inputfield\">
                                        <div class=\"rLabel\">
                                            <label class=\"\" for=\"textinput\">$amount_lable : </label>                
                                        </div>
                                        <div class=\"textfield btm20padding\">
                                              <input id=\"amount$counter\" name=\"amount[]\" type=\"text\" placeholder=\"$amount_lable\" class=\"form-control iInput\" onkeyup=\"totalThePrice('buy_price$counter','amount$counter','total$counter')\"> 
                                        </div>
                                    </div>    
                                </td>
                            </tr>
                            <tr>
                                <td scope=\"col\" width=\"33%\" class=\"iEntry\">
                                    <div class=\"inputfield\">
                                        <div class=\"rLabel\">
                                            <label class=\"\" for=\"textinput\">$buy_pice : </label>                
                                        </div>
                                        <div class=\"textfield btm20padding\">
                                            <input id=\"buy_price$counter\" name=\"buy_price[]\" type=\"text\" placeholder=\"$buy_pice\" class=\"form-control iInput\" onkeyup=\"totalThePrice('buy_price$counter','amount$counter','total$counter')\">     
                                        </div>
                                    </div>    
                                </td>
                                <td scope=\"col\" width=\"33%\" class=\"iEntry\">
                                    <div class=\"inputfield\">
                                        <div class=\"rLabel\">
                                            <label class=\"\" for=\"textinput\">$sale_pice : </label> 
                                        </div>
                                        <div class=\"textfield btm20padding\">
                                            <input id=\"sale_price[]\" name=\"sale_price[]\" type=\"text\" placeholder=\"$sale_pice\" class=\"form-control iInput\">     
                                        </div>
                                    </div>    
                                </td>
                                <td scope=\"col\" width=\"34%\" class=\"iEntry\">
                                    <div class=\"inputfield\">
                                        <div class=\"rLabel\">
                                            <label class=\"\" for=\"textinput\">$remark : </label>     
                                        </div>
                                        <div class=\"textfield btm20padding\">
                                            <input id=\"remark[]\" name=\"remark[]\" type=\"text\" placeholder=\"$remark\" class=\"form-control iInput\">     
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
            if($this->input->post('drugs') != "")
            {
                $str_post_str .= '&drugs='.$this->input->post('drugs');
            }
            // name
            if($this->input->post('drug_type') != "")
            {
                $str_post_str .= '&drug_type='.$this->input->post('drug_type');
            }                   
            // father name
            if($this->input->post('buy_price') != "")
            {
                $str_post_str .= '&buy_price='.$this->input->post('buy_price');
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
            
            
            $records   = $this->drug_model->search_records($starting,$recpage,FALSE);
            $rec_total = $this->drug_model->search_records($starting,$recpage,TRUE);
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
                base_url()."index.php/drug_store/home/filter",
                'list_div1',
                $str_post_str
            );
             
            $data["search"] = TRUE;
            $data['page']   = $starting;
            $data['total']  = $this->ajax_pagination_new->total;
            //$data['page']       = $page;
            $data['title']      = $this->lang->line("drug_list");
            //$data['page']   = $starting;
            $data['links']  = $this->ajax_pagination_new->anchors;
            //$data['total']  = $this->ajax_pagination->total;
            $this->load->view("filter/drug_filter_list",$data);
        }
        else
        {
            echo $this->load->view('unauthorized');
        }
    }
    
    function genDBexelprint()
    {
        $allsql = $this->input->post('allsql');
        $allsql = $this->clean_encrypt->decode($allsql);
        //echo "<pre>".$allsql;exit;
        //Get data from the database (model)
        $reportObj = $this->drug_model->search_records($allsql);

        //echo "<pre>";print_r($reportObj);exit;

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
            $excel->getActiveSheet()->setTitle($this->lang->line('drug_reports'));
            $excel->getActiveSheet()->getSheetView()->setZoomScale(100);
            //$lang = $this->mng_auth->get_language();
            $excel->getActiveSheet()->setRightToLeft(true);
            $rotation = -90;
            ini_set('memory_limit','4026M');
            $excel->getActiveSheet()->setShowGridlines(true);
            $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $excel->getActiveSheet()->getPageMargins()->setTop(0.8);
            $excel->getActiveSheet()->getPageMargins()->setRight(0.3);
            $excel->getActiveSheet()->getPageMargins()->setLeft(0.3);
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
                            'argb' => '00bcd4',
                        ),
                        'endcolor' => array(
                            'argb' => '00bcd4',
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
            //Start title
            $countrow = 1;
            $excel->getActiveSheet()->getStyle('A'.$countrow.':H'.$countrow)->applyFromArray($border);
            $excel->getActiveSheet()->mergeCells('A'.$countrow.':H'.$countrow);
            $excel->getActiveSheet()->getRowDimension($countrow)->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);

            $excel->getActiveSheet()->getStyle('A'.$countrow)->getFont()->setSize(14);
            //No
            $countrow++;
        
            ////number
            $excel->getActiveSheet()->setCellValue('A1',$this->lang->line('drug_reports'));
            $excel->getActiveSheet()->setCellValue('A'.$countrow,$this->lang->line('id'));
            $excel->getActiveSheet()->setCellValue('B'.$countrow,$this->lang->line('drugs'));
            $excel->getActiveSheet()->setCellValue('C'.$countrow,$this->lang->line('drug_type'));
            $excel->getActiveSheet()->setCellValue('D'.$countrow,$this->lang->line('amount'));
            $excel->getActiveSheet()->setCellValue('E'.$countrow,$this->lang->line('buy_price'));
            $excel->getActiveSheet()->setCellValue('F'.$countrow,$this->lang->line('sale_price'));
            $excel->getActiveSheet()->setCellValue('G'.$countrow,$this->lang->line('total_buy'));
            $excel->getActiveSheet()->setCellValue('H'.$countrow,$this->lang->line('total_sale'));

            $excel->getActiveSheet()->getStyle('A'.$countrow.':H'.($countrow))->applyFromArray($styleTitles);
            $countrow++;


            $excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
            $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $excel->getActiveSheet()->getColumnDimension('C')->setWidth(18);
            $excel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
            $excel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
            $excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);


            $totalcal=0;
            $counter = 1;
            $countrow = 3;
            $fee = 0;
            $remains = 0;
            $totalRecords = $reportObj->num_rows();
            foreach($reportObj->result() AS $item)
            {
                $total_buy = $item->amout*$item->buy_price;
                $total_sale = $item->amout*$item->sale_price;
                $excel->getActiveSheet()->setCellValue('A'.$countrow,$counter);
                $excel->getActiveSheet()->setCellValue('B'.$countrow,$item->name);
                $excel->getActiveSheet()->setCellValue('C'.$countrow,$item->type);
                $excel->getActiveSheet()->setCellValue('D'.$countrow,$item->amout);
                $excel->getActiveSheet()->setCellValue('E'.$countrow,$item->buy_price);
                $excel->getActiveSheet()->setCellValue('F'.$countrow,$item->sale_price);
                $excel->getActiveSheet()->setCellValue('G'.$countrow,$total_buy);
                $excel->getActiveSheet()->setCellValue('H'.$countrow,$total_sale);
                
                $amount +=  $item->amout;
                $buy +=  $item->buy_price;
                $buy_total +=  $total_buy;
                $sale +=  $item->sale_price;
                $sale_total +=  $total_sale;
                
                $counter++;
                $countrow++;
            }


            $excel->getActiveSheet()->mergeCells('A'.$countrow.':C'.($countrow));
            $excel->getActiveSheet()->setCellValue('A'.$countrow,$this->lang->line('total'));
            $excel->getActiveSheet()->getStyle('A'.$countrow)->getFont()->setSize(14);
            $excel->getActiveSheet()->getRowDimension($countrow)->setRowHeight(24);
            
            $excel->getActiveSheet()->setCellValue('D'.$countrow,$amount);
            $excel->getActiveSheet()->setCellValue('E'.$countrow,$buy);
            $excel->getActiveSheet()->setCellValue('F'.$countrow,$sale);
            $excel->getActiveSheet()->setCellValue('G'.$countrow,$buy_total);
            $excel->getActiveSheet()->setCellValue('H'.$countrow,$sale_total);
            $excel->getActiveSheet()->getStyle('A3:H'.($countrow))->applyFromArray($border);
            $excel->getActiveSheet()->getStyle('A3:H'.($countrow-1))->getAlignment()->setWrapText(true);
            ob_end_clean();
            $name = $this->lang->line('drug_reports');
            // redirect to cleint browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=$name.xlsx");
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $objWriter->save('php://output');

        }
    }
}
?>
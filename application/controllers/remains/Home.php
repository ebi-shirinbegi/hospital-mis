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
        $this->load->model(array('register/register_model','urn_model','document/document_model','remains/remain_model'));
    }

    /**
    * @desc index function
    */
    public function index()
    {
        redirect('remains/home/remain_list');
    }
    
    /**
    * @desc register list function
    */
    function remain_list($page = 0)
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
        
        $records   = $this->remain_model->getAllrecords($starting,$recpage,FALSE);
        $rec_total = $this->remain_model->getAllrecords($starting,$recpage,TRUE);
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
            base_url()."index.php/remains/home/remain_list",
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
        $data['title']          = $this->lang->line("remain_list"); 
        $data['filter']         = $this->load->view("filter/remain_filter",$data,true); 
        if($this->input->post('ajax')==1)
        {
            $data["search"] = TRUE;
            $this->load->view('filter/remain_filter_list',$data);
        }else{                                                   
            banner();
            sidebar();
            $modal = modal_popup();
            $content = $this->load->view('remains/remain_list',$data,true);
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
            $data['title'] = $this->lang->line("remain_view");
            $records = $this->register_model->getViewRecords($dec_urn);
            //echo "<pre>";print_r($records);exit;
            if($records){
                $teeth_records  = $this->register_model->getTeethRecords($records[0]->urn);
                $drug_records   = $this->register_model->getDrugRecords($records[0]->urn);
                $prof_pic       = $this->document_model->getPicture($records[0]->urn);
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
            $content = $this->load->view("remains/remain_view",$data,true);
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
                redirect("remains/home/view/".$urn);
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
            
            
            $records   = $this->remain_model->search_records($starting,$recpage,FALSE);
            $rec_total = $this->remain_model->search_records($starting,$recpage,TRUE);
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
                base_url()."index.php/remains/home/filter",
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
            $this->load->view("filter/remain_filter_list",$data);
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
        $reportObj = $this->remain_model->search_records($allsql);
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
}

?>
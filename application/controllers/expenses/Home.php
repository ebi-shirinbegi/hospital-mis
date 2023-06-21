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
        $this->load->model(array('expenses/expense_model','urn_model','document/document_model'));
    }

    /**
    * @desc index function
    */
    public function index()
    {
        redirect('expenses/home/listRecords');
    }
    
    /**
    * @desc register list function
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
        
        $records   = $this->expense_model->getAllrecords($starting,$recpage,FALSE);
        $rec_total = $this->expense_model->getAllrecords($starting,$recpage,TRUE);
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
            base_url()."index.php/expenses/home/listRecords",
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
        $data['title']      = $this->lang->line("expense_list");  
        $data['filter']     = $this->load->view("filter/expense_filter",$data,true); 
        if($this->input->post('ajax')==1)
        {
            $data["search"] = TRUE;
            $this->load->view('filter/expense_filter_list',$data);
        }else{                                                   
            banner();
            sidebar();
            $modal = modal_popup();
            $content = $this->load->view('expenses/expense_list',$data,true);
            content($content);
            footer();
        }
    }
    
    /**
    * @desc add to the register
    */
    function add($queue_urn = 0)
    {
        if(0){
            echo "You are not loged in";exit;
        }else{
            $this->form_validation->set_rules('name[]', 'name[]', 'trim');
            $this->form_validation->set_rules('amount[]', 'amount[]', 'trim');
            $this->form_validation->set_rules('price[]', 'price[]', 'trim');
            if($this->form_validation->run() == FALSE){
                //load views and templates
                $data = array();
                $data['title'] = $this->lang->line('expenses_add');
                banner();
                sidebar();
                $modal = modal_popup();
                $content = $this->load->view("expenses/expense_add",$data,true);
                content($content);
                footer();
            }
            else{
                $updateExpense = "";
                $name           = $this->input->post('name');         
                $amount         = $this->input->post('amount');       
                $price          = $this->input->post('price');
                $drug_data = array();
                foreach($name AS $key=>$value){
                    if($name[$key] != ''){
                        $updateExpense = array(
                            "name"                      => $name[$key],
                            "amount"                    => $amount[$key],
                            "price"                     => $price[$key],
                            "registerdate"              => date("Y-m-d") 
                        );
                        $expense_urn = $this->urn_model->getURN('expenses','urn');
                        $updateDrug = $this->expense_model->update('expenses',$expense_urn,$updateExpense,1000001,"INSERT");
                    }
                }
                if($updateDrug){
                    redirect("expenses/home/listRecords",'refresh');
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
            $this->form_validation->set_rules('price[]', 'price[]', 'trim');
            if($this->form_validation->run() == FALSE){
                $data = "";
                $records = false;
                //top title
                $data['title'] = $this->lang->line("register_edit");
                $records = $this->expense_model->getViewRecords($dec_urn);
                //echo "<pre>";print_r($records[0]);exit;
                $data["record"]             = $records[0];
                $data["enc_urn"]            = $urn;
                
                //load views and templates
                banner();
                sidebar();
                $modal = modal_popup();
                //the views if the record is exist
                $content = $this->load->view("expenses/expense_edit",$data,true);
                content($content);
                footer(); 
            }
            else{
                $updateExpense = "";
                $name           = $this->input->post('name');         
                $amount         = $this->input->post('amount');       
                $price          = $this->input->post('price');
                $drug_data = array();
                foreach($name AS $key=>$value){
                    if($name[$key] != ''){
                        $updateExpense = array(
                            "name"                      => $name[$key],
                            "amount"                    => $amount[$key],
                            "price"                     => $price[$key],
                            "is_updated"                => 1
                        );
                        //echo "<pre>";print_r($dec_urn);exit;
                        $updateDrug = $this->expense_model->update('expenses',$dec_urn,$updateExpense,1000001,"UPDATE");
                    }
                }
                if($updateDrug){
                    redirect("expenses/home/listRecords",'refresh');
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
        $name = $this->lang->line('name');
        $amount_lable = $this->lang->line('the_amount');
        $price = $this->lang->line('item_price');
        $base_url = base_url();
        $content = "";
        if($counter >0){
            $content .= "<table class=\"table\" id=\"imRemovable$counter\">
                            <tr>
                                <td scope=\"col\" width=\"33%\" class=\"iEntry\" colspan=\"3\">
                                    <span class=\"btn btn-danger ino\">$counter</span><input type=\"button\" id=\"rm\" class=\"btn btn-danger\" value=\"-\" onclick=\"javascript:removeElement('imRemovable$counter','$counter');\" >
                                </td>
                            </tr>
                            <tr> 
                                <td scope=\"col\" width=\"33%\" class=\"iEntry\">
                                    <div class=\"inputfield\">
                                        <div class=\"rLabel\">
                                            <label class=\"\" for=\"textinput\">$name : </label>                
                                        </div>
                                        <div class=\"textfield btm20padding\">
                                            <input id=\"name[]\" name=\"name[]\" type=\"text\" placeholder=\"$name\" class=\"form-control iInput\" >     
                                        </div>
                                    </div>
                                </td>
                                <td scope=\"col\" width=\"33%\" class=\"iEntry\">
                                    <div class=\"inputfield\">
                                        <div class=\"rLabel\">
                                            <label class=\"\" for=\"textinput\">$amount_lable : </label>                
                                        </div>
                                        <div class=\"textfield btm20padding\">
                                              <input id=\"amount$counter\" name=\"amount[]\" type=\"text\" placeholder=\"$amount_lable\" class=\"form-control iInput\"> 
                                        </div>
                                    </div>    
                                </td>
                                <td scope=\"col\" width=\"34%\" class=\"iEntry\">
                                    <div class=\"inputfield\">
                                        <div class=\"rLabel\">
                                            <label class=\"\" for=\"textinput\">$price : </label>                
                                        </div>
                                        <div class=\"textfield btm20padding\">
                                            <input id=\"price$counter\" name=\"price[]\" type=\"text\" placeholder=\"$price\" class=\"form-control iInput\">     
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
            if($this->input->post('name') != "")
            {
                $str_post_str .= '&name='.$this->input->post('name');
            }                   
            // father name
            if($this->input->post('the_amount') != "")
            {
                $str_post_str .= '&the_amount='.$this->input->post('the_amount');
            }
            // contact
            if($this->input->post('item_price') != "")
            {
                $str_post_str .= '&item_price='.$this->input->post('item_price');
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
            
            
            $records   = $this->expense_model->search_records($starting,$recpage,FALSE);
            $rec_total = $this->expense_model->search_records($starting,$recpage,TRUE);
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
                base_url()."index.php/expenses/home/filter",
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
            $this->load->view("filter/expense_filter_list",$data);
        }
        else
        {
            echo $this->load->view('unauthorized');
        }
    }
    
    function genDBexelprintXXX()
    {
        $allsql = $this->input->post('allsql');
        $allsql = $this->clean_encrypt->decode($allsql);
        //echo "<pre>".$allsql;exit;
        //Get data from the database (model)
        $reportObj = $this->expense_model->search_records($allsql);

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
            $excel->getActiveSheet()->setTitle($this->lang->line('expense_reports'));
            $excel->getActiveSheet()->getSheetView()->setZoomScale(100);
            //$lang = $this->mng_auth->get_language();
            $excel->getActiveSheet()->setRightToLeft(true);
            $rotation = -90;
            ini_set('memory_limit','4026M');
            $excel->getActiveSheet()->setShowGridlines(true);
            $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
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
            $excel->getActiveSheet()->getStyle('A'.$countrow.':D'.$countrow)->applyFromArray($border);
            $excel->getActiveSheet()->mergeCells('A'.$countrow.':D'.$countrow);
            $excel->getActiveSheet()->getRowDimension($countrow)->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);

            $excel->getActiveSheet()->getStyle('A'.$countrow)->getFont()->setSize(14);
            //No
            $countrow++;
        
            ////number
            $excel->getActiveSheet()->setCellValue('A1',$this->lang->line('expense_reports'));
            $excel->getActiveSheet()->setCellValue('A'.$countrow,$this->lang->line('id'));
            $excel->getActiveSheet()->setCellValue('B'.$countrow,$this->lang->line('name'));
            $excel->getActiveSheet()->setCellValue('C'.$countrow,$this->lang->line('the_amount'));
            $excel->getActiveSheet()->setCellValue('D'.$countrow,$this->lang->line('item_price'));

            $excel->getActiveSheet()->getStyle('A'.$countrow.':D'.($countrow))->applyFromArray($styleTitles);
            $countrow++;


            $excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
            $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);


            $totalcal=0;
            $counter = 1;
            $countrow = 3;
            $fee = 0;
            $remains = 0;
            $totalRecords = $reportObj->num_rows();
            foreach($reportObj->result() AS $item)
            {
                $excel->getActiveSheet()->setCellValue('A'.$countrow,$counter);
                $excel->getActiveSheet()->setCellValue('B'.$countrow,$item->name);
                $excel->getActiveSheet()->setCellValue('C'.$countrow,$item->amount);
                $excel->getActiveSheet()->setCellValue('D'.$countrow,$item->price);
                
                $price +=  $item->price;
                
                $counter++;
                $countrow++;
            }


            $excel->getActiveSheet()->mergeCells('A'.$countrow.':C'.($countrow));
            $excel->getActiveSheet()->setCellValue('A'.$countrow,$this->lang->line('total'));
            $excel->getActiveSheet()->getStyle('A'.$countrow)->getFont()->setSize(14);
            $excel->getActiveSheet()->getRowDimension($countrow)->setRowHeight(24);
            
            $excel->getActiveSheet()->setCellValue('D'.$countrow,$price);
            $excel->getActiveSheet()->getStyle('A3:D'.($countrow))->applyFromArray($border);
            $excel->getActiveSheet()->getStyle('A3:D'.($countrow-1))->getAlignment()->setWrapText(true);
            ob_end_clean();
            $name = $this->lang->line('expense_reports');
            // redirect to cleint browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=$name.xlsx");
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $objWriter->save('php://output');

        }
    }
    
    /**
    * @desc make report of expenses
    */
    function genDBexelprint()
    {
        $allsql = $this->input->post('allsql');
        $allsql = $this->clean_encrypt->decode($allsql);
        //echo "<pre>".$allsql;exit;
        //Get data from the database (model)
        $reportObj = $this->expense_model->search_records($allsql);

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
            $excel->getActiveSheet()->setTitle($this->lang->line('expense_reports'));
            $excel->getActiveSheet()->getSheetView()->setZoomScale(100);
            //$lang = $this->mng_auth->get_language();
            $excel->getActiveSheet()->setRightToLeft(true);
            $rotation = -90;
            ini_set('memory_limit','4026M');
            $excel->getActiveSheet()->setShowGridlines(true);
            $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $excel->getActiveSheet()->getPageMargins()->setTop(0.8);
            $excel->getActiveSheet()->getPageMargins()->setRight(1.8);
            $excel->getActiveSheet()->getPageMargins()->setLeft(1.8);
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
                        'size'  => 18,
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
            $excel->getActiveSheet()->mergeCells('A'.$countrow.':B'.$countrow);
            $excel->getActiveSheet()->mergeCells('D'.$countrow.':E'.$countrow);
            $excel->getActiveSheet()->mergeCells('D2:E2');
            $excel->getActiveSheet()->getRowDimension($countrow)->setRowHeight(88);
            $excel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension(3)->setRowHeight(30);

            $excel->getActiveSheet()->getStyle('A'.$countrow)->getFont()->setSize(14);
            //No
            $countrow=3;
            
            //insert logo
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('Logo');
            $objDrawing->setDescription('Logo');
            $logo = 'assets/images/amc.png'; // Provide path to your logo file
            $objDrawing->setPath($logo);  //setOffsetY has no effect
            $objDrawing->setCoordinates('A1');
            $objDrawing->setHeight(110); // logo height
            $objDrawing->setWorksheet($excel->getActiveSheet()); 
            $objDrawing->setOffsetX(30);    // setOffsetX works properly
            $objDrawing->setOffsetY(8);  //setOffsetY has no effect
            $objDrawing->setCoordinates('A1');
            
            $title1 = $this->lang->line('ndcdr');
            $title12 = $this->lang->line('ndcen');
            $main_title = $this->lang->line('expense_reports');
            $title = $title1.PHP_EOL.$title12;
            
            $phone = $this->lang->line('d_contacts');
            $email = $this->lang->line('email');
            $address = $this->lang->line('address');
            
            $info = $phone.PHP_EOL.$email.PHP_EOL.$address;
        
            ////number
            $excel->getActiveSheet()->setCellValue('C2',$main_title);
            $excel->getActiveSheet()->setCellValue('C1',$title);
            $excel->getActiveSheet()->setCellValue('D1',$info);
            
            //$excel->getActiveSheet()->setCellValue('C2',$this->lang->line('expense_reports'));
            $excel->getActiveSheet()->setCellValue('A'.$countrow,$this->lang->line('id'));
            $excel->getActiveSheet()->setCellValue('B'.$countrow,$this->lang->line('name'));
            $excel->getActiveSheet()->setCellValue('C'.$countrow,$this->lang->line('the_amount'));
            $excel->getActiveSheet()->setCellValue('D'.$countrow,$this->lang->line('item_price'));
            $excel->getActiveSheet()->setCellValue('E'.$countrow,$this->lang->line('registerDate'));

            //set style 
            $excel->getActiveSheet()->getStyle('C1')->applyFromArray($headerStyle);
            $excel->getActiveSheet()->getStyle('C1')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C2')->applyFromArray($styleTitle);
            
            $excel->getActiveSheet()->getStyle('D1:E1')->applyFromArray($styleInfo);
            $excel->getActiveSheet()->getStyle('D1')->getAlignment()->setWrapText(true);
            
            $excel->getActiveSheet()->getStyle('A'.$countrow.':E'.($countrow))->applyFromArray($styleTitles);
            $countrow++;


            $excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
            $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $excel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
            $excel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
            $excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);


            $totalcal=0;
            $counter = 1;
            $countrow = 4;
            $fee = 0;
            $remains = 0;
            $totalRecords = $reportObj->num_rows();
            foreach($reportObj->result() AS $item)
            {
                //sest text direction right to left
                $excel->getActiveSheet()->getStyle('E'.$countrow)->getAlignment()->setReadorder(PHPExcel_Style_Alignment::READORDER_RTL);
                $excel->getActiveSheet()->setCellValue('A'.$countrow,$counter);
                $excel->getActiveSheet()->setCellValue('B'.$countrow,$item->name);
                $excel->getActiveSheet()->setCellValue('C'.$countrow,$item->amount);
                $excel->getActiveSheet()->setCellValue('D'.$countrow,$item->price);
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
                $excel->getActiveSheet()->setCellValue('E'.$countrow,$reg_date);
                
                $price +=  $item->price;
                
                $counter++;
                $countrow++;
            }


            $excel->getActiveSheet()->mergeCells('A'.$countrow.':C'.($countrow));
            $excel->getActiveSheet()->setCellValue('A'.$countrow,$this->lang->line('total'));
            $excel->getActiveSheet()->getStyle('A'.$countrow)->getFont()->setSize(14);
            $excel->getActiveSheet()->getRowDimension($countrow)->setRowHeight(24);
            
            $excel->getActiveSheet()->setCellValue('D'.$countrow,$price);
            $excel->getActiveSheet()->getStyle('A3:E'.($countrow))->applyFromArray($border);
            //$excel->getActiveSheet()->getStyle('A3:D'.($countrow))->applyFromArray($noBorder);
            $excel->getActiveSheet()->getStyle('A3:E'.($countrow-1))->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->setShowGridlines(FALSE); 
            
            //signature
            $countrow++;
            $countrow++;
            $excel->getActiveSheet()->mergeCells('A'.$countrow.':E'.$countrow);  
            $excel->getActiveSheet()->getStyle('A'.($countrow))->applyFromArray($styleTitle);
            $excel->getActiveSheet()->setCellValue('A'.$countrow,$this->lang->line('signature'));
            
            ob_end_clean();
            $name = $this->lang->line('expense_reports');
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
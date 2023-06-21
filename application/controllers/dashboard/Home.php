<?php
/**
*@author: Aziz Matin
*@created Date: 20-Feb-2019
**/
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	//construct function
	function __construct()
	{
		parent::__construct();
		$this->load->helper('template');
        $this->load->helper('jdf');
        $this->load->library('Clean_encrypt'); 
        $this->lang->load("home");
		$this->lang->load("global");
        $this->load->library('Amc_auth');
        $this->amc_auth->is_logged_in();
	}

	public function index()
	{
		banner();
		sidebar();
		$modal = modal_popup();
		$dashboard = $this->load->view('dashbord/dashboard','',true);
		content($dashboard);
		footer();
	}
}

?>
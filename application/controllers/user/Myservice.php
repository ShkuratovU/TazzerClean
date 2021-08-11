<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Myservice extends MY_Controller {     # upgrade maksimU for whitelabel using MY_Controller
	 function __construct() { 
        parent::__construct(); 
        error_reporting(0);
        
        $this->data['base_url'] = base_url();
        $this->session->keep_flashdata('error_message');
        $this->session->keep_flashdata('success_message');
        $this->load->helper('user_timezone_helper');
        $this->load->helper('push_notifications');
        $this->load->model('api_model','api');

        $this->load->model('service_model','service');
        $this->load->model('home_model','home');
        // Load pagination library 
        $this->load->library('ajax_pagination'); 
         
        // Load post model 
        $this->load->model('post'); 
         
        // Per page limit 
        $this->perPage = 12; 
        $this->data['theme']     = 'user';
        $this->data['module']    = 'service';
        
    } 
     
    public function index(){ 
    	  if(empty($this->session->userdata('id'))){
          redirect(base_url());
          }
        $data = array(); 
          
        // Get record count 
        $conditions['returnType'] = 'count'; 

        $totalRec = $this->post->getRows($conditions); 
        // Pagination configuration 
        $config['target']      = '#dataList'; 
        $config['base_url']    = base_url('user/myservice/ajaxPaginationData'); 
        $config['total_rows']  = $totalRec; 
        $config['per_page']    = $this->perPage; 
         
        // Initialize pagination library 
        $this->ajax_pagination->initialize($config); 
         
        // Get records 
        $conditions = array( 
            'limit' => $this->perPage 
        ); 
        $this->data['services'] = $this->post->getRows($conditions); 
       	
        $this->data['page'] = 'my_service_new';
        // Load the list page view 
        $this->load->vars($this->data);
        $this->load->view($this->data['theme'].'/template');
      
    } 
     
    function ajaxPaginationData(){ 
        // Define offset 
        $page = $this->input->post('page'); 
        if(!$page){ 
            $offset = 0; 
        }else{ 
            $offset = $page; 
        } 
         
        // Get record count 
        $conditions['returnType'] = 'count'; 
        $totalRec = $this->post->getRows($conditions); 
         
        // Pagination configuration 
        $config['target']      = '#dataList'; 
        $config['base_url']    =  base_url('user/myservice/ajaxPaginationData'); 
        $config['total_rows']  = $totalRec; 
        $config['per_page']    = $this->perPage; 
        $config['cur_page'] = $page;
        // Initialize pagination library 
        $this->ajax_pagination->initialize($config); 
         
        // Get records 
        $conditions = array( 
            'start' => $offset, 
            'limit' => $this->perPage 
        ); 
        $this->data['services'] = $this->post->getRows($conditions); 
        
        // Load the data list view 
        $this->load->view('user/service/ajax-data', $this->data, false); 
    }
    public function inactive_services()
    { 
         if(empty($this->session->userdata('id'))){
          redirect(base_url());
          }
        $data = array(); 
          
        // Get record count 
        $conditions['returnType'] = 'count'; 

        $totalRec = $this->post->getInactiveRows($conditions); 
        // Pagination configuration 
        $config['target']      = '#dataList'; 
        $config['base_url']    = base_url('user/myservice/inactiveajaxPaginationData'); 
        $config['total_rows']  = $totalRec; 
        $config['per_page']    = $this->perPage; 
         
        // Initialize pagination library 
        $this->ajax_pagination->initialize($config); 
         
        // Get records 
        $conditions = array( 
            'limit' => $this->perPage 
        ); 
        $this->data['services'] = $this->post->getInactiveRows($conditions); 
        
        $this->data['page'] = 'my_service_inactive';
        // Load the list page view 
       $this->load->vars($this->data);
     $this->load->view($this->data['theme'].'/template');
    } 
      function inactiveajaxPaginationData(){ 
        // Define offset 
        $page = $this->input->post('page'); 
        if(!$page){ 
            $offset = 0; 
        }else{ 
            $offset = $page; 
        } 
         
        // Get record count 
        $conditions['returnType'] = 'count'; 
        $totalRec = $this->post->getInactiveRows($conditions); 
         
        // Pagination configuration 
        $config['target']      = '#dataList'; 
        $config['base_url']    =  base_url('user/myservice/inactiveajaxPaginationData'); 
        $config['total_rows']  = $totalRec; 
        $config['per_page']    = $this->perPage; 
         
        // Initialize pagination library 
        $this->ajax_pagination->initialize($config); 
         
        // Get records 
        $conditions = array( 
            'start' => $offset, 
            'limit' => $this->perPage 
        ); 
        $this->data['services'] = $this->post->getInactiveRows($conditions); 
        
        // Load the data list view 
        $this->load->view('user/service/service-inactive-ajax-data', $this->data, false); 
    }
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calendar extends CI_Controller {

	public $data=array();
	public $loggedin_method_arr = array('calendar');
	public $controller_arr = array('user','frontend','fbcontroller','gpluscontroller','routemanager','ajax');
	function __construct()
	{
		parent::__construct();
		$this->load->model('userdata');
		$this->data=$this->defaultdata->getFrontendDefaultData();
		if(array_search($this->data['tot_segments'][2],$this->loggedin_method_arr) !== false)
		{
			if($this->defaultdata->is_session_active() == 0)
			{
				redirect(base_url('login'));
			}
		}
		if($this->defaultdata->is_session_active() == 1)
		{
			$user_cond = array();
			$user_cond['id'] = $this->session->userdata('usrid'); 
			$this->data['user_details'] = $this->userdata->grabUserData($user_cond);
		}
		if($this->session->userdata('usrtype')!=5)
		{
			redirect(base_url('login'));
		}
	}

	

	public function index()
	{
		$user_id = $this->session->userdata('usrid');

		$crmType = 'consultant';
		$search_data = $this->input->get();
		$task = $this->userdata->getAllcalTasks($crmType, $search_data);
		
		$cal_event = $this->userdata->getAllCalendarEvent($user_id);
		
		$calendarData = array_merge($task, $cal_event);

		$this->data['calendarData'] = json_encode($calendarData);
		$this->data['search_data'] = $search_data;
		//echo '<pre>'; print_r($search_data);die;
		$this->load->view('consultant/calendar/index',$this->data);
	}


	public function addCalendarEvent()
	{
		$return_data['has_error'] = 1;
		//echo '<pre>'; print_r($this->input->post());
		$post_data = $this->input->post();

		$input_data['title'] = $post_data['title'];
		$input_data['starttime'] = $post_data['starttime'];
		$input_data['endtime'] = $post_data['endtime'];
		$input_data['user_id'] = $this->session->userdata('usrid');
		$input_data['userType'] = $this->session->userdata('usrtype');
		$input_data['className'] = $post_data['className'];

		$lastId = $this->userdata->setTable(TABLE_CALENDAR_EVENT)->insert($input_data);
		$this->userdata->unsetTable();	
		if($lastId)
		{
			$return_data['lastId'] = $lastId;
			$return_data['has_error'] = 0;
		}
		echo json_encode($return_data);
	}


	public function editCalendarEvent()
	{
		$return_data['has_error'] = 1;
		//echo '<pre>'; print_r($this->input->post());
		$post_data = $this->input->post();

		if($post_data['id']>0)
		{
			if(isset($post_data['title']))
			{
				$input_data['title'] = $post_data['title'];
			}
			if(isset($post_data['starttime']))
			{
				$input_data['starttime'] = $post_data['starttime'];
			}
			if(isset($post_data['endtime']))
			{
				$input_data['endtime'] = $post_data['endtime'];
			}
			$lastId = $this->userdata->setTable(TABLE_CALENDAR_EVENT)->update($input_data, array('id'=>$post_data['id']));
			$this->userdata->unsetTable();	
			
			$return_data['has_error'] = 0;
		}
			
		echo json_encode($return_data);
	}
	

	public function deleteCalendarEvent()
	{
		$return_data['has_error'] = 1;
		//echo '<pre>'; print_r($this->input->post());
		$post_data = $this->input->post();

		$input_data['id'] = $post_data['id'];
		$input_data['user_id'] = $this->session->userdata('usrid');

		$count_data = $this->userdata->setTable(TABLE_CALENDAR_EVENT)->countRows($input_data);
		$this->userdata->unsetTable();	
		if($count_data>0)
		{
			$this->userdata->setTable(TABLE_CALENDAR_EVENT)->delete(array('id'=>$post_data['id']));
			$this->userdata->unsetTable();
			$return_data['has_error'] = 0;
		}
		echo json_encode($return_data);
	}
}


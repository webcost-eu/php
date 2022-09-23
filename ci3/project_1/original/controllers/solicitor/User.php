<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public $data=array();
	public $loggedin_method_arr = array('user');
	
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
		if($this->session->userdata('usrtype')!=3)
		{
			redirect(base_url('login'));
		}
	}

	


	public function communicator()
	{
		$user_id = $this->session->userdata('usrid');
		$user_data = $this->userdata->fetchOne(array('id'=>$user_id, 'userType'=>3));
		$id = 1;
		$this->chatdata->messageRead($id, $user_id);
		$chat_message = $this->chatdata->userMessages($id);
		foreach ($chat_message as $chat) {
			$chat->form_user_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$chat->from_user_id), 'name');
		}
		//echo '<pre>'; print_r($chat_message); die;

		$this->data['user_data'] = $user_data;
		$this->data['chat_message'] = $chat_message;

		$this->data['singleMessage'] = $this->load->view('solicitor/single_message',$this->data,TRUE);
		$this->load->view('solicitor/communicator',$this->data);
	}

	public function postChat()
	{
		$return_data = array();
		$post_data = $this->input->post();
		$input_data['from_user_id'] = $this->session->userdata('usrid');
		$input_data['to_user_id'] = $post_data['to_user_id'];
		$input_data['message'] = $post_data['message'];
		$input_data['type'] = 'T';
		$input_data['postedTime'] = time();
		$lastId = $this->chatdata->insert($input_data);
		if($lastId)
		{
			$this->data['chat_message'] = $this->chatdata->fetchAll(array('id'=>$lastId));
			foreach ($this->data['chat_message'] as $chat) {
				$chat->form_user_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$chat->from_user_id), 'name');
			}

			$return_data['singleMessage'] = $this->load->view('solicitor/single_message',$this->data,TRUE);
		}
		echo json_encode($return_data);
	}


	public function loadUnreadMessages()
	{
		$return_data = array();
		$post_data = $this->input->post();
		$to_user_id = $this->session->userdata('usrid');
		$from_user_id = $post_data['from_user_id'];
		
		$this->data['chat_message'] = $this->chatdata->loadUnreadMessages($to_user_id, $from_user_id);
		foreach ($this->data['chat_message'] as $chat) {
			$chat->form_user_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$chat->from_user_id), 'name');
		}

		$return_data['singleMessage'] = $this->load->view('solicitor/single_message',$this->data,TRUE);
		$this->chatdata->messageRead($from_user_id, $to_user_id);
		echo json_encode($return_data);
	}



	












	
}


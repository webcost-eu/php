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
		if($this->session->userdata('usrtype')!=2)
		{
			redirect(base_url('login'));
		}
	}

	


	public function communicator()
	{
		$user_id = $this->session->userdata('usrid');
		$user_data = $this->userdata->fetchOne(array('id'=>$user_id, 'userType'=>2));
		$id = 1;
		$this->chatdata->messageRead($id, $user_id);
		$chat_message = $this->chatdata->userMessages($id);
		foreach ($chat_message as $chat) {
			$chat->form_user_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$chat->from_user_id), 'name');
		}
		//echo '<pre>'; print_r($chat_message); die;

		$this->data['user_data'] = $user_data;
		$this->data['chat_message'] = $chat_message;

		$this->data['singleMessage'] = $this->load->view('agent/single_message',$this->data,TRUE);
		$this->load->view('agent/communicator',$this->data);
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

			$return_data['singleMessage'] = $this->load->view('agent/single_message',$this->data,TRUE);
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

		$return_data['singleMessage'] = $this->load->view('agent/single_message',$this->data,TRUE);
		$this->chatdata->messageRead($from_user_id, $to_user_id);
		echo json_encode($return_data);
	}



	public function mlm()
	{
		$user_id = $this->session->userdata('usrid');

		$agent_data = $this->userdata->fetchAll(array('id'=>$user_id, 'userType'=>2));
		foreach ($agent_data as $agent) {
			$agent->mlm_agent_data = $this->userdata->getAgentInMLMStructure($user_id);
		}
			
		$this->data['mlm_structure_view'] = $this->nestedArrayToList($agent_data);
		//echo '<pre>'; print_r($agent_data); die;

		
		$this->load->view('agent/mlm',$this->data);
	}


	public function nestedArrayToList($dataArray)
	{
		$html = '';
		$html .= '<ol class="dd-list">';
		foreach ($dataArray as $key => $value) {
			
			$profile_url = 'javascript:void(0)';
			if($this->session->userdata('usrid') != $value->id)
			{
				$profile_url = base_url('agent/subagent/information/'.$value->id);
			}
			
			$html .= '<li class="dd-item dd3-item">
						  <div class=" dd3-handle"></div>
		                  <div class="dd3-content">
		                      <a href="'.$profile_url.'" target="_blank">'.$value->name.'</a>
		                  </div>';
			        
			        if(count($value->mlm_agent_data)>0)
			        {
			        	$html .= $this->nestedArrayToList($value->mlm_agent_data);
			        }

	        $html .= '</li>';          
		}
		$html .= '</ol>';

		return $html;
	}










	
}


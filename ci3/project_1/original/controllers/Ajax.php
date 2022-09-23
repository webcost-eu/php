<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {
	public $data=array();
	public $controller_arr = array('user','frontend','fbcontroller','gpluscontroller','routemanager','ajax','admin');
	function __construct()
	{
		parent::__construct();
		$this->load->model('userdata');
	}
	

	public function getSolicitorEmployee()
	{
		$id = $this->input->post('id');		
		$solicitor_employee = $this->userdata->fetchAll(array('parent_id'=>$id, 'userType'=>3));
		
		$fields = "";
		$fields .= '<option value="">'.$this->defaultdata->gradLanguageText(335).'</option>';
		if(count($solicitor_employee)>0)
		{	
			foreach ($solicitor_employee as $key => $val) 
			{
				$fields .= '<option value="'.$val->id.'">'.$val->name.'</option>';
			}
		}
		echo $fields;
	}

	public function getSubStatucCase()
	{
		$id = $this->input->post('id');		
		$substatus = $this->defaultdata->get_results(TABLE_CLIENT_SELECT_OPTION, array('parent_id'=>$id, 'type'=>3));
		
		$fields = "";		
		$fields .= '<option value="">'.$this->defaultdata->gradLanguageText(335).'</option>';
		if(count($substatus)>0)
		{	
			foreach ($substatus as $key => $val) 
			{
				$fields .= '<option value="'.$val->id.'">'.$val->name.'</option>';
			}
		}
		echo $fields;

	}
	public function getSubStatucCase2()
	{
		$id = $this->input->post('id');		
		$substatus = $this->defaultdata->get_results(TABLE_CLIENT_SELECT_OPTION, array('parent_id'=>$id, 'type'=>3));
		
		$fields = '<select class="form-control select2" name="sub_status_of_case" id="sub_status_of_case" required>';		
		$fields .= '<option value="">'.$this->defaultdata->gradLanguageText(335).'</option>';
		if(count($substatus)>0)
		{	
			foreach ($substatus as $key => $val) 
			{
				$fields .= '<option value="'.$val->id.'">'.$val->name.'</option>';
			}
		}
		$fields .= '</select>';
		echo $fields;

	}

	public function getCardNumberForAdmin()
	{
		$return_data = array();
		$post_data = $this->input->post();	

		$card_number = $post_data['card_number'];

		$card_data = $this->userdata->getCardProgramByCardNumber($card_number);
		
		$match = "";
		if(count($card_data)>0)
		{	
			foreach ($card_data as $key => $val) 
			{
				$match .= '<li data-agent-id="'.$val->agent_id.'">'.$val->card_number.'</li>';
			}
		}

		$return_data['match'] = $match;
		echo json_encode($return_data);
	}

	public function getCardNumberForAgent()
	{
		$return_data = array();
		$post_data = $this->input->post();	

		$card_number = $post_data['card_number'];
		$agent_id = $post_data['agent_id'];

		$card_data = $this->userdata->getCardProgramByCardNumber($card_number, $agent_id);
		
		$match = "";
		if(count($card_data)>0)
		{	
			foreach ($card_data as $key => $val) 
			{
				$match .= '<li data-agent-id="'.$val->agent_id.'">'.$val->card_number.'</li>';
			}
		}

		$return_data['match'] = $match;
		echo json_encode($return_data);
	}

	public function keepMeLogin()
	{
		$log_record = $this->defaultdata->get_results(TABLE_USERLOGIN_RECORD, array('user_id'=>$this->session->userdata('usrid')), array('id'=>'DESC'), 1, 0);		
		if(count($log_record)>0)
		{
			$this->defaultdata->update(TABLE_USERLOGIN_RECORD, array('logouttime'=>0), array('id'=>$log_record[0]->id));
		}

		$this->defaultdata->update(TABLE_USER, array('last_activity_time'=>time()), array('id'=>$this->session->userdata('usrid')));
	}

	public function wpClientRegister($value='')
	{
		$responce_data = array();
		$post_data = $this->input->post();
		//print_r($post_data); die;

		$user_data['name'] = $post_data['name'];
		$user_data['phone'] = $post_data['phone'];
		$user_data['emailAddress'] = $post_data['email'];
		
		$name_arr = explode(' ', $user_data['name']);
		$user_data['firstName'] = $name_arr[0];
		$user_data['lastName'] = str_replace($user_data['firstName'].' ', '', $user_data['name']);
		$user_data['added_from'] = 2;
		$user_data['userType'] = 4;
		$user_data['status'] = 'Y';
		$user_data['postedtime'] = time();
		$user_data['ipaddress'] = $_SERVER["REMOTE_ADDR"];
		$user_data['CA_status'] = $post_data['contact_acceptance'];
		$user_data['MA_status'] = $post_data['marketing_acceptance'];
		
		$user_id = $this->defaultdata->insert(TABLE_USER, $user_data);

		if($user_id)
		{
			$client_data['user_id'] = $user_id;
			$client_data['agent'] = $post_data['agent_id'];
			$client_data['status_of_case'] = 20;
			$client_data['info_client'] = 'Y';
			$client_data['info_client_txt'] = $post_data['message'];			

			$client_id = $this->defaultdata->insert(TABLE_CLIENTS, $client_data);


			//==== ACCEPTANCE TABLE ENTRY ====//
			$acceptance_data['source_table'] = 1;
			$acceptance_data['source_id'] = $user_id;
			$acceptance_data['ip_address'] = $_SERVER["REMOTE_ADDR"]; 
			$acceptance_data['postedtime'] = time();
			if($post_data['contact_acceptance']=='Y')
			{
				$acceptance_data['type'] = 1;
				$acceptance_data['is_checked'] = 'Y';
				$acceptance_data['clause'] = $post_data['clause_CA'];
				$this->defaultdata->insert(TABLE_ACCEPTANCE, $acceptance_data);

				if($post_data['marketing_acceptance']=='Y')
				{
					$acceptance_data['type'] = 2;
					$acceptance_data['is_checked'] = 'Y';
					$acceptance_data['clause'] = $post_data['clause_MA'];
					$this->defaultdata->insert(TABLE_ACCEPTANCE, $acceptance_data);
				}
				else
				{
					$acceptance_data['type'] = 2;
					$acceptance_data['is_checked'] = 'N';
					$acceptance_data['clause'] = $post_data['clause_MA'];
					$this->defaultdata->insert(TABLE_ACCEPTANCE, $acceptance_data);
				}
			}
			//==== ACCEPTANCE TABLE ENTRY ====//

			$responce_data['has_error'] = 0;

		}

		echo json_encode($responce_data);
	}


	public function headerNotification()
	{
		
		$user_id = $this->session->userdata('usrid');
		$return_data = array();
		$html = '';
		$count = 0;
		$today = strtotime(date('Y-m-d'));

		$condition = array('user_id'=>$user_id, 'status'=>1);

		$count = $this->defaultdata->count_record(TABLE_NOTIFICATION, $condition);

		$this->data['notificationData'] = $this->defaultdata->get_results(TABLE_NOTIFICATION, $condition, array('id'=>'DESC'), 100, 0);
		
		$userTypeName = 'admin';
		
		foreach ($this->data['notificationData'] as $notification) 
		{
			if($notification->userType==1){ $userTypeName = 'admin'; }
			if($notification->userType==2){ $userTypeName = 'agent'; }
			if($notification->userType==3){ $userTypeName = 'solicitor'; }
			if($notification->userType==5){ $userTypeName = 'consultant'; }

			$notification->background = 'bg-danger';
			$notification->icon = 'mdi mdi-comment';

			switch($notification->type)
			{
				case 1:
				$notification->title = $this->defaultdata->getField(TABLE_CLIENT_COMMENT, array('id'=>$notification->source_id), 'title');
				$notification->client_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$notification->client_id), 'name');
				$notification->redirect_url = base_url($userTypeName.'/client/comment/'.$notification->client_id);
				$notification->background = 'bg-danger';
				$notification->icon = 'mdi mdi-comment';
				break;

				case 2:
				$notification->title = $this->defaultdata->getField(TABLE_CLIENT_TASKS, array('id'=>$notification->source_id), 'subject');
				$notification->client_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$notification->client_id), 'name');
				$notification->redirect_url = base_url($userTypeName.'/client/task/'.$notification->client_id);
				$notification->background = 'bg-info';
				$notification->icon = 'mdi mdi-alarm';
				break;

			}
			
		}
		//echo "<pre>"; print_r($this->data['reminderData']);die;
		$html .= $this->load->view('user/header_notification',$this->data,TRUE);
		$return_data['count'] = $count;
		$return_data['html'] = $html;
		echo json_encode($return_data);

	}

	public function readNotification($value='')
	{
		$id = $this->input->post('id');
		$this->defaultdata->update(TABLE_NOTIFICATION, array('status'=>2), array('id'=>$id));

		// Read all other comments form this client
		$notification = $this->defaultdata->get_single_row(TABLE_NOTIFICATION, array('id'=>$id));
		$update_cond['client_id'] = $notification->client_id;
		$update_cond['user_id'] = $this->session->userdata('usrid');
		$update_cond['type'] = 1;
		$this->defaultdata->update(TABLE_NOTIFICATION, array('status'=>2), $update_cond);

		echo 1;
	}

	public function openCloseMenu()
	{
		if($this->session->userdata('closemenu'))
		{
			$this->session->unset_userdata('closemenu');
			echo 'open';
		}
		else
		{
			$this->session->set_userdata('closemenu', 1);
			echo 'close';
		}
	}

	public function acceptanceRecord()
	{
		$return_data =  array();
		$post_data = $this->input->post();
		$this->data['acceptance'] = $this->defaultdata->get_results(TABLE_ACCEPTANCE, array('source_table'=>$post_data['source_table'], 'source_id'=>$post_data['source_id']));

		$return_data['acceptance_table'] = $this->load->view('user/acceptance_table', $this->data, TRUE);
		echo json_encode($return_data);
	}

	public function acceptWithdrawn()
	{
		$return_data =  array();
		$post_data = $this->input->post();
		$acceptance_data = $this->defaultdata->get_single_row(TABLE_ACCEPTANCE, array('id'=>$post_data['id']));
		if(!empty($acceptance_data))
		{
			$time = time();
			$this->defaultdata->update(TABLE_ACCEPTANCE, array('withdrawn_date'=>$time), array('id'=>$post_data['id']));

			if($acceptance_data->source_table==1)
			{
				if($acceptance_data->type==1)
				{
					$update_user['CA_status'] = 'N';
				}
				else
				{
					$update_user['MA_status'] = 'N';
				}
				$this->defaultdata->update(TABLE_USER, $update_user, array('id'=>$acceptance_data->source_id));
			}
			
			$return_data['withdrawn_date'] = date('d/m/Y', $time);
			$return_data['has_error'] = 0;
		}
		else
		{
			$return_data['has_error'] = 1;
		}
		echo json_encode($return_data);
	}

	public function saveAcceptanceField()
	{
		$return_data['has_error'] = 0;
		$id = $this->input->post('id');
		$field_name = $this->input->post('field_name');
		$new_value = $this->input->post('new_value');
		
		$cond['id'] = $id;		
		$return_data['new_value'] = nl2br($new_value);
				
		if($return_data['has_error'] == 0)
		{
			$update_data[$field_name] = $new_value;
			$this->defaultdata->update(TABLE_ACCEPTANCE, $update_data, $cond);
			$this->userdata->unsetTable();
		}
		echo json_encode($return_data);
	}

	public function changeUserStatus()
	{
		$post_data = $this->input->post();
		$update_user = $this->defaultdata->update(TABLE_USER, array('status'=>$post_data['status']), array('id'=>$post_data['user_id']));
		//echo $this->db->last_query();
		echo 1;
	}

	public function changeUserShareFields()
	{
		$post_data = $this->input->post();
		$update_user = $this->defaultdata->update(TABLE_USER, array($post_data['field_name']=>$post_data['value']), array('id'=>$post_data['user_id']));
		echo $post_data['value'];
	}

	public function checkMyActivityTime()
	{
		$return_data = array(); 
		$user = $this->defaultdata->get_single_row(TABLE_USER, array('id'=>$this->session->userdata('usrid')));
		$time_deff = time() - $user->last_activity_time;
		$return_data['inactive_time'] = $time_deff;
		$return_data['status'] = '';
		if($time_deff >= 50*60)
		{
			$return_data['status'] = 'logout';
		}
		elseif($time_deff >= 40*60) 
		{
			$return_data['status'] = 'warning';
		}
		echo json_encode($return_data);
	}

	

}
/* End of file ajax.php */
/* Location: ./application/controllers/ajax.php */
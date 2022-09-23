<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public $data=array();
	public $loggedin_method_arr = array('dashboard');
	//public $controller_arr = array('user','frontend','fbcontroller','gpluscontroller','routemanager','ajax');
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

	public function index()
	{
		$user_id = $this->session->userdata('usrid');
		//automatic force to change password once a 3 months time

		$user_data = $this->defaultdata->get_single_row(TABLE_USER, array('id'=>$user_id));
		if($user_data->password_update_time) {
			$last_password_change = $user_data->password_update_time;
		} else {
			$last_password_change = $user_data->postedtime;
		}

		$systemSettings = $this->defaultdata->get_single_row(TABLE_GENERAL_SETTINGS, array('id'=>1));

		$limitOfDays = 90;
		if(!empty($systemSettings->number_of_days_change_auto_password)){
			$limitOfDays = $systemSettings->number_of_days_change_auto_password;
		}

		$tomorrow = date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s", $last_password_change) . "+".$limitOfDays." days"));

		if(!empty($systemSettings->number_of_days_change_auto_password)){
			$numberOfDays = $systemSettings->number_of_days;
		}

		$date_diff = strtotime($tomorrow) - time();
		$date_diff = floor($date_diff/(60*60*24));
		$date_diff = ($date_diff > 0) ? $date_diff : 0;

		$this->data['password_change_worning'] = '';
		if($date_diff <= $numberOfDays)
		{
			$change_password_link = base_url('agent/dashboard/change_password');

			$this->data['password_change_worning'] = '<p>Prosimy o zmianę hasła do Systemu. Jeżeli nie zmienisz hasła, dostęp do Systemu zostanie zablokowany. Możesz wówczas odzyskać hasło ze strony logowania hasła (opcja Zapomniałeś hasła?).</p><p>Dostęp zostanie zablokowany za następującą liczbę dni: '.$date_diff;

			$this->data['password_change_worning'] .= '<p><a href="'.$change_password_link.'">Zmień hasło teraz</a></p>';
		}

		//=========== CLIENT TABLE ===========//
		/*$this->data['allClients'] = $this->userdata->getUserClientData();
		foreach ($this->data['allClients'] as $user) {			
			$user->type_of_accident = $user->type_of_accident?$this->defaultdata->getField(TABLE_CLIENT_SELECT_OPTION, array('id'=>$user->type_of_accident), 'name'):'';
			$user->agent_name = $user->agent?$this->defaultdata->getField(TABLE_USER, array('id'=>$user->agent), 'name'):'';
		}*/
		//echo "<pre>"; print_r($this->data['allClients']);die;
		//=========== CLIENT TABLE ===========//

		//=========== TASK TABLE ===========//
		$user_data = $this->userdata->fetchOne(array('id'=>$user_id));
		//$mlm_user_id = $this->userdata->getAllUsersId($user_id);
		$mlm_user_id = array($user_id); //default checked Show only my tasks

		$this->data['taskData'] = $this->userdata->getTaskDataByUserIds($mlm_user_id, array('is_reminder'=>'N', 'status'=>'P'), array('deadline'=>'ASC'), array('count'=>100, 'start'=>0));
		foreach ($this->data['taskData'] as $task) {			
			$task->client_name = $task->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->client_id), 'name'):'';
			$task->assigned_user_name = $task->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->assigned_user), 'name'):'';
			$task->created_user_name = $task->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->created_user), 'name'):'';
		}
		$this->data['taskTable'] = $this->load->view('agent/search_task',$this->data,TRUE);
		//=========== TASK TABLE ===========//
		
		//=========== REMINDER TABLE ===========//
		/*$this->data['reminderData'] = $this->userdata->getTaskDataByUserIds($mlm_user_id, array('is_reminder'=>'Y'), array('id'=>'DESC'));
		foreach ($this->data['reminderData'] as $reminder) {			
			$reminder->client_name = $reminder->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->client_id), 'name'):'';
			$reminder->assigned_user_name = $reminder->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->assigned_user), 'name'):'';
			$reminder->created_user_name = $reminder->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->created_user), 'name'):'';
		}*/
		//=========== REMINDER TABLE ===========//
		
		
		//=========== COMMENT TABLE ===========//
		//$mlm_user_id = $this->userdata->getAllUsersId($user_id);
		$mlm_user_id = array($user_id);
		$comment_ids = array();
		$clientData = $this->userdata->getClientDetailsByAgentMLMids($mlm_user_id);
		
		if(!empty($clientData))
		{	
			foreach ($clientData as $key => $value) 
			{
				if(in_array($value->agent, $mlm_user_id) || in_array($value->central_provision_agent, $mlm_user_id))
				{
					if($value->agent==$user_id)
					{
						$commentCond = array('to_user_id'=>$value->user_id, 'share_agent'=>'Y');
					}
					else
					{
						$commentCond = array('to_user_id'=>$value->user_id, 'share_agent_manager'=>'Y');
					}
					$commentData = $this->userdata->setTable(TABLE_CLIENT_COMMENT)->fetchAll($commentCond);
					$this->userdata->unsetTable();
					foreach ($commentData as $comm) {
						$comment_ids[] = $comm->id;
					}

				}
			}
		}

		$this->data['commentData'] = array();
		if(!empty($comment_ids))
		{
			$this->data['commentData'] = $this->userdata->getCommentDataByCommentIds($comment_ids, array(), array('id'=>'DESC'), array('count'=>100, 'start'=>0));
			if(!empty($this->data['commentData']))
			{
				foreach ($this->data['commentData'] as $comment) {
					if($comment->reply_from_id =='0' && $comment->parent_id=='0'){
						$comment->from_user_name = $comment->from_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->from_user_id), 'name'):'';
					}else{
						$comment->from_user_name = $comment->reply_from_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->reply_from_id), 'name'):'';
					}
						$comment->to_user_name = $comment->to_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->to_user_id), 'name'):'';
				}
			}
		}
		$this->data['commentTable'] = $this->load->view('agent/search_comment',$this->data,TRUE);
		//=========== COMMENT TABLE ===========//

		//=========== CALENDAR EVENT TABLE ===========//
		/*$this->data['calendarEvent'] = $this->userdata->setTable(TABLE_CALENDAR_EVENT)->fetchAll(array(), array('id'=>'DESC'));
		$this->userdata->unsetTable();*/

		$mlm_user_id = $this->userdata->getAllUsersId($user_id);
		$this->data['calendarEvent'] = $this->userdata->getCalendarDataByUserIds($mlm_user_id, array(), array('id'=>'DESC'));
		foreach ($this->data['calendarEvent'] as $event) {
			$event->user_name = $event->user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$event->user_id), 'name'):'';
		}
		//=========== CALENDAR EVENT TABLE ===========//

		/*$this->data['allUser'] = $this->userdata->fetchAll(array('userType !='=>4), array('id'=>'DESC'));*/

		/*$this->data['allUser'] = $this->userdata->getUserDataByUserIds($mlm_user_id, array('userType'=>3), array('id'=>'DESC'));
		foreach ($this->data['allUser'] as $user) {
			$user->userLoginData = $this->defaultdata->get_single_row(TABLE_USERLOGIN, array('uid'=>$user->id), 'lastlogintime, logouttime');
			$user->profile_url = 'javascript:void(0)';
			
		}*/

		$this->load->view('agent/dashboard',$this->data);
	}

	public function searchTask()
	{
		$return_data = array();
		$user_id = $this->session->userdata('usrid');
		$user_data = $this->userdata->fetchOne(array('id'=>$user_id));
		$mlm_user_id = $this->userdata->getAllUsersId($user_id);
		$post_data = $this->input->post();
		//echo "<pre>"; print_r($this->input->post());
		$cond = array();
		$cond['is_reminder'] = 'N';
		$cond['status'] = 'P';
		if(isset($post_data['my_task']))
		{
			$mlm_user_id = array($user_id);
		}
		if(isset($post_data['priority_task']))
		{
			$cond['priority'] = 'Y';
		}
		if(isset($post_data['today_delayed']))
		{
			$cond['deadline <='] = strtotime(date('Y-m-d'));
		}
		$this->data['taskData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond, array('deadline'=>'ASC'), array('count'=>100, 'start'=>0));

		$this->data['taskData'] = $this->userdata->getTaskDataByUserIds($mlm_user_id, $cond, array('id'=>'DESC'));
		//echo $this->db->last_query();
		$this->userdata->unsetTable();
		foreach ($this->data['taskData'] as $task) {			
			$task->client_name = $task->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->client_id), 'name'):'';
			$task->assigned_user_name = $task->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->assigned_user), 'name'):'';
			$task->created_user_name = $task->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->created_user), 'name'):'';
		}

		$return_data['taskTable'] = $this->load->view('agent/search_task',$this->data,TRUE);
		echo json_encode($return_data);
	}



	public function searchComment()
	{
		$post_data = $this->input->post();
		$return_data = array();
		$user_id = $this->session->userdata('usrid');
		$user_data = $this->userdata->fetchOne(array('id'=>$user_id));		
		$mlm_user_id = $this->userdata->getAllUsersId($user_id);

		if(in_array('me', $post_data['userType']))
		{
			$mlm_user_id = array($user_id);
			$post_data = array();
		}
		
		$search_user = $this->userdata->getAllUserByUserType($post_data);
		$user_ids = array();
		foreach ($search_user as $key => $value) {
			$user_ids[] = $value->id;
		}
			
		$comment_ids = array();
		$clientData = $this->userdata->getClientDetailsByAgentMLMids($mlm_user_id);
		if(!empty($clientData))
		{
			foreach ($clientData as $key => $value) 
			{
				if(in_array($value->agent, $mlm_user_id)|| in_array($value->central_provision_agent, $mlm_user_id))
				{
					if($value->agent==$user_id)
					{
						$commentCond = array('to_user_id'=>$value->user_id, 'share_agent'=>'Y');
					}
					else
					{
						$commentCond = array('to_user_id'=>$value->user_id, 'share_agent_manager'=>'Y');
					}
					$commentData = $this->userdata->setTable(TABLE_CLIENT_COMMENT)->fetchAll($commentCond);
					$this->userdata->unsetTable();
					foreach ($commentData as $comm) {
						$comment_ids[] = $comm->id;
					}

				}
			}
		}

		$this->data['commentData'] = array();
		if(!empty($comment_ids))
		{	
			$this->data['commentData'] = $this->userdata->getCommentDataByCommentIds($comment_ids, array(), array('id'=>'DESC'), array('count'=>100, 'start'=>0));

			if(!empty($this->data['commentData']))
			{	
				foreach ($this->data['commentData'] as $key => $comment) 
				{
					
					if(!empty($user_ids) && !in_array($comment->from_user_id, $user_ids))
					{
						unset($this->data['commentData'][$key]);
					}
					$comment->from_user_name = $comment->from_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->from_user_id), 'name'):'';
					$comment->to_user_name = $comment->to_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->to_user_id), 'name'):'';
				}
			}
		}
		
		$return_data['commentTable'] = $this->load->view('agent/search_comment',$this->data,TRUE);
		echo json_encode($return_data);
	}






	public function change_password()
	{
		$this->load->view('agent/change_password',$this->data);
	}

	public function changePasswordProcess()
	{
		$user_id = $this->session->userdata('usrid');
		$post_data = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('oldPassword', 'Old Password', 'trim|required');
		$this->form_validation->set_rules('newPassword', 'New Password', 'trim|required');
		$this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'trim|required|matches[newPassword]');
		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
		}
		else
		{
			$user_data['id'] = $user_id;
			$user_data['userPassword'] = md5($post_data['oldPassword']);
			$checkPassword = $this->userdata->countRows($user_data);
			if($checkPassword==0)
			{
				$this->session->set_flashdata('error', $this->defaultdata->gradLanguageText(439));
			}
			else
			{
				$update_data['userPassword'] = md5($post_data['newPassword']);
				$update_data['password_update_time'] = time();
				
				$this->userdata->update($update_data, array('id'=>$user_id));
				$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(440));
			}
		}
		redirect(base_url('agent/dashboard/change_password'));
	}

	public function headerNotification()
	{
		$user_id = $this->session->userdata('usrid');
		$user_data = $this->userdata->fetchOne(array('id'=>$user_id));
		$mlm_user_id = $this->userdata->getAllUsersId($user_id);
		$return_data = array();
		$html = '';
		$count = 0;
		$today = strtotime(date('Y-m-d'));
		$tomorrow = $today + 24*60*60;

		$comment_ids = array();
		$clientData = $this->userdata->getClientDetailsByAgentMLMids($mlm_user_id);
		if(!empty($clientData))
		{	
			foreach ($clientData as $key => $value) 
			{
				if(in_array($value->agent, $mlm_user_id))
				{
					if($value->agent==$user_id)
					{
						$commentCond = array('to_user_id'=>$value->user_id, 'share_agent'=>'Y');
					}
					else
					{
						$commentCond = array('to_user_id'=>$value->user_id, 'share_agent_manager'=>'Y');
					}
					$commentData = $this->userdata->setTable(TABLE_CLIENT_COMMENT)->fetchAll($commentCond);
					$this->userdata->unsetTable();
					foreach ($commentData as $comm) {
						$comment_ids[] = $comm->id;
					}

				}
			}
		}

		$this->data['commentData'] = array();
		if(!empty($comment_ids))
		{	
			$this->data['commentData'] = $this->userdata->getCommentDataByCommentIds($comment_ids, array('from_user_id !='=>$user_id, 'postedtime >='=>$today, 'postedtime <='=>$tomorrow), array('id'=>'DESC'));

			if(!empty($this->data['commentData']))
			{
				foreach ($this->data['commentData'] as $comment) 
				{
					$comment->to_user_name = $comment->to_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->to_user_id), 'name'):'';
					$count += 1;
				}
			}
		}


		$html .= $this->load->view('agent/notification_html',$this->data,TRUE);

		$return_data['count'] = $count;
		$return_data['html'] = $html;
		echo json_encode($return_data);

	}






















	
}


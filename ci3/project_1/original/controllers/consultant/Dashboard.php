<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public $data=array();
	public $loggedin_method_arr = array('dashboard');
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

		$type = $this->input->get('type');

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
			$change_password_link = base_url('consultant/dashboard/change_password');

			$this->data['password_change_worning'] = '<p>Prosimy o zmianę hasła do Systemu. Jeżeli nie zmienisz hasła, dostęp do Systemu zostanie zablokowany. Możesz wówczas odzyskać hasło ze strony logowania hasła (opcja Zapomniałeś hasła?).</p><p>Dostęp zostanie zablokowany za następującą liczbę dni: '.$date_diff;

			$this->data['password_change_worning'] .= '<p><a href="'.$change_password_link.'">Zmień hasło teraz</a></p>';
		}

		if (!$type) {
			$type = 1;
		}

		//automatic force to change password once a 3 months time

		switch ($type) {
			case 1:
				//=========== CLIENT TABLE ===========//
				//only Status of Case = ZGŁOSZENIE
				$cond = array('C.status_of_case'=>20);
				$this->data['allClients'] = $this->userdata->getUserClientData(array(), $cond);
				foreach ($this->data['allClients'] as $user) {
					$user->type_of_accident = $user->type_of_accident?$this->defaultdata->getField(TABLE_CLIENT_SELECT_OPTION, array('id'=>$user->type_of_accident), 'name'):'';
					$user->agent_name = $user->agent?$this->defaultdata->getField(TABLE_USER, array('id'=>$user->agent), 'name'):'';
				}
				//echo "<pre>"; print_r($this->data['allClients']);die;
				//=========== CLIENT TABLE ===========//
				break;
			case 2:
				//=========== TASK TABLE ===========//
				$cond_task = array();
				$cond_task['is_reminder'] = 'N';
				$cond_task['status'] = 'P';
				$cond_task['assigned_user'] = $user_id; //default checked Show only my tasks
				//$this->data['taskData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond_task, array('deadline'=>'ASC'), array('count'=>100, 'start'=>0));
				$this->data['taskData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond_task, array('deadline'=>'ASC'));
				$this->userdata->unsetTable();


				foreach ($this->data['taskData'] as $task) {
					$consultant_id = $task->client_id?$this->defaultdata->getField(TABLE_CLIENTS, array('user_id'=>$task->client_id), 'consultant'):'';

					$task->consultant_name = $consultant_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$consultant_id), 'name'):'';
					$task->client_name = $task->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->client_id), 'name'):'';
					$task->assigned_user_name = $task->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->assigned_user), 'name'):'';
					$task->created_user_name = $task->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->created_user), 'name'):'';
				}
				$this->data['taskTable'] = $this->load->view('consultant/search_task',$this->data,TRUE);
				//=========== TASK TABLE ===========//
				break;
			case 3:
				//=========== REMINDER TABLE ===========//
				//$cond_reminder = array();
				//$cond_reminder['is_reminder'] = 'Y';
				//$cond_reminder['status'] = 'P';
				//$cond_reminder['assigned_user'] = $user_id; //default checked Show only my reminders
				$noActionReminder = (!empty($this->defaultdata->gradLanguageText(537))) ? $this->defaultdata->gradLanguageText(537) : 'REMINDER: No action';
				$cond_reminder = 'status="P" AND is_reminder="Y" AND assigned_user ='.$user_id.' AND (subject = "Przedawnienie sprawy" OR subject = "EXPIRATION REMINDER" OR subject = "Uwaga: Brak czynności w sprawie" OR subject = "'.$noActionReminder.'")';
				$this->data['reminderData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond_reminder, array('deadline'=>'ASC'));
				$this->userdata->unsetTable();
				foreach ($this->data['reminderData'] as $reminder) {
					$reminder->client_name = $reminder->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->client_id), 'name'):'';
					$reminder->assigned_user_name = $reminder->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->assigned_user), 'name'):'';
					$reminder->created_user_name = $reminder->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->created_user), 'name'):'';
				}
				$this->data['reminderTable'] = $this->load->view('consultant/search_reminder',$this->data,TRUE);
				//=========== REMINDER TABLE ===========//
				break;
			case 4:
				//=========== COMMENT TABLE ===========//
				$user_id = $this->session->userdata('usrid');
				$client_data = $this->defaultdata->get_results(TABLE_CLIENTS, array('consultant'=>$user_id));
				$client_ids = array();
				foreach ($client_data as $key => $value) {
					$client_ids[] = $value->user_id;
				}

				$this->data['commentData'] = $this->userdata->getCommentDataByUserIds($client_ids, array(), array('id'=>'DESC'));

				//$this->data['commentData'] = $this->defaultdata->get_results(TABLE_CLIENT_COMMENT, array(), array('id'=>'DESC'), 100, 0));

				foreach ($this->data['commentData'] as $comment) {
					if($comment->reply_from_id =='0' && $comment->parent_id=='0'){
						$comment->from_user_name = $comment->from_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->from_user_id), 'name'):'';
					}else{
						$comment->from_user_name = $comment->reply_from_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->reply_from_id), 'name'):'';
					}
					$comment->to_user_name = $comment->to_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->to_user_id), 'name'):'';
				}
				$this->data['commentTable'] = $this->load->view('consultant/search_comment',$this->data,TRUE);
				//=========== COMMENT TABLE ===========//
				break;
			case 5:
				//=========== CALENDAR EVENT TABLE ===========//
				$this->data['calendarEvent'] = $this->userdata->setTable(TABLE_CALENDAR_EVENT)->fetchAll(array(), array('id'=>'DESC'));
				$this->userdata->unsetTable();
				foreach ($this->data['calendarEvent'] as $event) {
					$event->user_name = $event->user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$event->user_id), 'name'):'';
				}
				//=========== CALENDAR EVENT TABLE ===========//
				break;
			case 6:
				//=========== LOGIN LOGOUT TABLE ===========//
				$this->data['allLogRecord'] = $this->userdata->getSeanchUserByUserType();
				foreach ($this->data['allLogRecord'] as $log_data) {
					$log_data->userData = $this->defaultdata->get_single_row(TABLE_USER, array('id'=>$log_data->user_id));
					$log_data->profile_url = 'javascript:void(0)';
					if($log_data->userType==2){ $log_data->profile_url = base_url('consultant/agent/information/'.$log_data->user_id); }
					if($log_data->userType==3){ $log_data->profile_url = base_url('consultant/solicitor/information/'.$log_data->user_id); }
				}

				$this->data['loginTable'] = $this->load->view('consultant/search_login',$this->data,TRUE);
				//=========== LOGIN LOGOUT TABLE ===========//
				break;
		}

		$this->data['type'] = $type;

		$this->load->view('consultant/dashboard',$this->data);
	}

	
	public function searchTask()
	{
		$return_data = array();
		$user_id = $this->session->userdata('usrid');
		$post_data = $this->input->post();
		//echo "<pre>"; print_r($this->input->post());
		$cond = array();
		$cond['is_reminder'] = 'N';
		$cond['status'] = 'P';
		if(isset($post_data['my_task']))
		{
			$cond['assigned_user'] = $user_id;
		}
		if(isset($post_data['priority_task']))
		{
			$cond['priority'] = 'Y';
		}
		if(isset($post_data['today_delayed']))
		{
			$cond['deadline <='] = strtotime(date('Y-m-d'));
		}

		if(isset($post_data['assigned_user_type'])){
			$cond['assigned_user_type ='] = $post_data['assigned_user_type'];
		}

		if(!empty($post_data['deadline_start'])){
			$this->db->where('deadline >=', strtotime(str_replace('/','-',$post_data['deadline_start'])));
		}

		if(!empty($post_data['deadline_end'])){
			$this->db->where('deadline <=', strtotime(str_replace('/','-',$post_data['deadline_end'])));
		}

		if(!empty($post_data['deadline_start']) && !empty($post_data['deadline_end'])){
			$this->db->where('deadline >=', strtotime(str_replace('/','-',$post_data['deadline_start'])));
			$this->db->where('deadline <=', strtotime(str_replace('/','-',$post_data['deadline_end'])));
		}


		$this->data['taskData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond, array('deadline'=>'ASC'), array('count'=>100, 'start'=>0));
		//echo $this->db->last_query();
		$this->userdata->unsetTable();
		foreach ($this->data['taskData'] as $task) {			
			$task->client_name = $task->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->client_id), 'name'):'';
			$task->assigned_user_name = $task->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->assigned_user), 'name'):'';
			$task->created_user_name = $task->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->created_user), 'name'):'';
		}

		$return_data['taskTable'] = $this->load->view('consultant/search_task',$this->data,TRUE);
		echo json_encode($return_data);
	}

	public function searchReminder()
	{
		$return_data = array();
		$user_id = $this->session->userdata('usrid');
		$post_data = $this->input->post();
		//echo "<pre>"; print_r($this->input->post());
		$cond = array();
		$cond['is_reminder'] = 'Y';
		$cond['status'] = 'P';
		if(isset($post_data['my_reminder']))
		{
			$cond['assigned_user'] = $user_id;
		}

		if(!empty($post_data['deadline_start'])){
			$this->db->where('deadline >=', strtotime(str_replace('/','-',$post_data['deadline_start'])));
		}

		if(!empty($post_data['deadline_end'])){
			$this->db->where('deadline <=', strtotime(str_replace('/','-',$post_data['deadline_end'])));
		}

		if(!empty($post_data['deadline_start']) && !empty($post_data['deadline_end'])){
			$this->db->where('deadline >=', strtotime(str_replace('/','-',$post_data['deadline_start'])));
			$this->db->where('deadline <=', strtotime(str_replace('/','-',$post_data['deadline_end'])));
		}

		$expirationReminder = (!empty($this->defaultdata->gradLanguageText(536))) ? $this->defaultdata->gradLanguageText(536) : 'EXPIRATION REMINDER';

		if(isset($post_data['show_aging']) && empty($post_data['no_action'])){
			$this->db->where('subject',"Przedawnienie sprawy");
			$this->db->or_where('subject',$expirationReminder);
		}

		$noActionReminder = (!empty($this->defaultdata->gradLanguageText(537))) ? $this->defaultdata->gradLanguageText(537) : 'REMINDER: No action';

		if(isset($post_data['no_action']) && empty($post_data['show_aging'])){
			$this->db->where('subject',"Uwaga: Brak czynności w sprawie");
			$this->db->or_where('subject',$noActionReminder);
		}

		$this->data['reminderData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond, array('deadline'=>'DESC'));
		$this->userdata->unsetTable();
		foreach ($this->data['reminderData'] as $reminder) {			
			$reminder->client_name = $reminder->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->client_id), 'name'):'';
			$reminder->assigned_user_name = $reminder->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->assigned_user), 'name'):'';
			$reminder->created_user_name = $reminder->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->created_user), 'name'):'';
		}
		$return_data['reminderTable'] = $this->load->view('consultant/search_reminder',$this->data,TRUE);
		echo json_encode($return_data);
	}

	public function searchComment()
	{
		$return_data = array();
		//echo "<pre>"; print_r($this->input->post());
		$post_data = $this->input->post();

		if(!in_array('me', $post_data['userType']))
		{
			$user_data = $this->userdata->getAllUserByUserType($post_data);
			$user_ids = array();
			foreach ($user_data as $key => $value) {
				$user_ids[] = $value->id;
			}

			if(!empty($post_data['postedtime_start'])){
				$cond['postedtime >='] = strtotime(str_replace('/','-',$post_data['postedtime_start']));
			}

			if(!empty($post_data['postedtime_end'])){
				$cond['postedtime <='] = strtotime(str_replace('/','-',$post_data['postedtime_end']));
			}

			if(!empty($post_data['deadline_start']) && !empty($post_data['deadline_end'])){

				$post_data['deadline_start'] = strtotime(str_replace('/','-',$post_data['deadline_start']));
				$post_data['deadline_end'] = strtotime(str_replace('/','-',$post_data['deadline_end']));

				$cond = "postedtime BETWEEN ".$post_data['deadline_start']."  AND ".$post_data['deadline_end'];
			}

			$this->data['commentData'] = $this->userdata->getCommentDataByFromUserIds($user_ids, $cond, array('id'=>'DESC'));
		}
		else
		{
			$user_id = $this->session->userdata('usrid');
			$client_data = $this->defaultdata->get_results(TABLE_CLIENTS, array('consultant'=>$user_id));
			$client_ids = array();
			foreach ($client_data as $key => $value) {
				$client_ids[] = $value->user_id;
			}

			$this->data['commentData'] = $this->userdata->getCommentDataByUserIds($client_ids, array(), array('id'=>'DESC'));
		}


		
		foreach ($this->data['commentData'] as $comment) {
			$comment->from_user_name = $comment->from_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->from_user_id), 'name'):'';
			$comment->to_user_name = $comment->to_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->to_user_id), 'name'):'';
		}
		$return_data['commentTable'] = $this->load->view('consultant/search_comment',$this->data,TRUE);
		echo json_encode($return_data);
	}


	public function searchLogin($value='')
	{
		$return_data = array();
		//echo "<pre>"; print_r($this->input->post());
		$post_data = $this->input->post();

		$this->data['allLogRecord'] = $this->userdata->getSeanchUserByUserType($post_data);
		
		foreach ($this->data['allLogRecord'] as $log_data) {
			$log_data->userData = $this->defaultdata->get_single_row(TABLE_USER, array('id'=>$log_data->user_id));
			$log_data->profile_url = 'javascript:void(0)';
			if($log_data->userType==2){ $log_data->profile_url = base_url('consultant/agent/information/'.$log_data->user_id); }
			if($log_data->userType==3){ $log_data->profile_url = base_url('consultant/solicitor/information/'.$log_data->user_id); }
			if($log_data->userType==5){ $log_data->profile_url = base_url('admin/consultant'); }
		}
		$return_data['loginTable'] = $this->load->view('consultant/search_login',$this->data,TRUE);



		
		
		echo json_encode($return_data);
	}

	public function headerNotification()
	{
		$user_id = $this->session->userdata('usrid');
		$return_data = array();
		$html = '';
		$count = 0;
		$today = strtotime(date('Y-m-d'));
		$tomorrow = $today + 24*60*60;

		$this->data['commentData'] = $this->userdata->setTable(TABLE_CLIENT_COMMENT)->fetchAll(array('from_user_id !='=>$user_id, 'postedtime >='=>$today, 'postedtime <='=>$tomorrow), array('id'=>'DESC'));
		$this->userdata->unsetTable();
		foreach ($this->data['commentData'] as $comment) {
			$comment->to_user_name = $comment->to_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->to_user_id), 'name'):'';
			$count += 1;
		}

		$this->data['taskData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll(array('is_reminder'=>'Y', 'deadline'=>$today), array('id'=>'DESC'));
		$this->userdata->unsetTable();
		foreach ($this->data['taskData'] as $task) {			
			$task->client_name = $task->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->client_id), 'name'):'';
			$count += 1;
		}

		$html .= $this->load->view('consultant/notification_html',$this->data,TRUE);

		$return_data['count'] = $count;
		$return_data['html'] = $html;
		echo json_encode($return_data);

	}


	public function change_password()
	{
		$this->load->view('consultant/change_password',$this->data);
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
				//$this->session->set_flashdata('error','Old Password not matche');
				$this->session->set_flashdata('error', $this->defaultdata->gradLanguageText(439));
			}
			else
			{
				$update_data['userPassword'] = md5($post_data['newPassword']);
				$update_data['password_update_time'] = time();
				
				$this->userdata->update($update_data, array('id'=>$user_id));
				//$this->session->set_flashdata('success','Password Updated Successfully!!');
				$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(440));
			}
		}
		redirect(base_url('consultant/dashboard/change_password'));
	}















	
}


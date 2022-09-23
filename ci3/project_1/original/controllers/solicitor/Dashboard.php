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
		if($this->session->userdata('usrtype')!=3)
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
			$change_password_link = base_url('solicitor/dashboard/change_password');

			$this->data['password_change_worning'] = '<p>Prosimy o zmianę hasła do Systemu. Jeżeli nie zmienisz hasła, dostęp do Systemu zostanie zablokowany. Możesz wówczas odzyskać hasło ze strony logowania hasła (opcja Zapomniałeś hasła?).</p><p>Dostęp zostanie zablokowany za następującą liczbę dni: '.$date_diff;

			$this->data['password_change_worning'] .= '<p><a href="'.$change_password_link.'">Zmień hasło teraz</a></p>';
		}

		/*$this->data['allClients'] = $this->userdata->getUserClientData();
		foreach ($this->data['allClients'] as $user) {			
			$user->type_of_accident = $user->type_of_accident?$this->defaultdata->getField(TABLE_CLIENT_SELECT_OPTION, array('id'=>$user->type_of_accident), 'name'):'';
			$user->agent_name = $user->agent?$this->defaultdata->getField(TABLE_USER, array('id'=>$user->agent), 'name'):'';
		}*/
		//echo "<pre>"; print_r($this->data['allClients']);die;

		
		//=========== TASK TABLE ===========//
		$user_data = $this->userdata->fetchOne(array('id'=>$user_id));
		//$mlm_user_id = $this->userdata->getAllUsersId($user_id);
		$mlm_user_id = array($user_id);	//default checked Show only my tasks	

		$this->data['taskData'] = $this->userdata->getTaskDataByUserIds($mlm_user_id, array('is_reminder'=>'N', 'status'=>'P'), array('deadline'=>'ASC'), array('count'=>100, 'start'=>0));
		foreach ($this->data['taskData'] as $task) {			
			$task->client_name = $task->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->client_id), 'name'):'';
			$task->assigned_user_name = $task->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->assigned_user), 'name'):'';
			$task->created_user_name = $task->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->created_user), 'name'):'';
		}
		$this->data['taskTable'] = $this->load->view('solicitor/search_task',$this->data,TRUE);
		//=========== TASK TABLE ===========//

		//=========== REMINDER TABLE ===========//
		//$mlm_user_id = $this->userdata->getAllUsersId($user_id);
		$mlm_user_id = array($user_id);	//default checked Show only my reminders

		$this->data['reminderData'] = $this->userdata->getTaskDataByUserIds($mlm_user_id, array('is_reminder'=>'Y', 'status'=>'P'), array('deadline'=>'ASC'), array('count'=>100, 'start'=>0));
		foreach ($this->data['reminderData'] as $reminder) {			
			$reminder->client_name = $reminder->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->client_id), 'name'):'';
			$reminder->assigned_user_name = $reminder->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->assigned_user), 'name'):'';
			$reminder->created_user_name = $reminder->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->created_user), 'name'):'';
		}
		$this->data['reminderTable'] = $this->load->view('solicitor/search_reminder',$this->data,TRUE);
		//=========== REMINDER TABLE ===========//
		
		
		//=========== COMMENT EVENT TABLE ===========//
		$cond = array();
		if($user_data->parent_id==0) //Solicitor
		{
			$cond = array('solicitor'=>$user_id);
		}
		else //Solicitor Employee
		{
			$cond = array('solicitor_employee'=>$user_id);
		}
		$clientData = $this->userdata->setTable(TABLE_CLIENTS)->fetchAll($cond);
		$this->userdata->unsetTable();
		$client_ids = array(0);
		foreach ($clientData as $key => $value) {
			$client_ids[] = $value->user_id;
		}

		$this->data['commentData'] = $this->userdata->getCommentDataByUserIds($client_ids, array('share_solcitor'=>'Y'), array('id'=>'DESC'), array('count'=>100, 'start'=>0));

		foreach ($this->data['commentData'] as $comment) {
			if($comment->reply_from_id =='0' && $comment->parent_id=='0'){
				$comment->from_user_name = $comment->from_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->from_user_id), 'name'):'';
			}else{
				$comment->from_user_name = $comment->reply_from_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->reply_from_id), 'name'):'';
			}
				$comment->to_user_name = $comment->to_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->to_user_id), 'name'):'';
		}
		$this->data['commentTable'] = $this->load->view('solicitor/search_comment',$this->data,TRUE);
		//=========== COMMENT EVENT TABLE ===========//

		//=========== CALENDAR EVENT TABLE ===========//		
		$mlm_user_id = $this->userdata->getAllUsersId($user_id);

		$this->data['calendarEvent'] = $this->userdata->getCalendarDataByUserIds($mlm_user_id, array(), array('id'=>'DESC'));
		foreach ($this->data['calendarEvent'] as $event) {
			$event->user_name = $event->user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$event->user_id), 'name'):'';
		}
		//=========== CALENDAR EVENT TABLE ===========//
		

		
		//=========== LOGIN LOGOUT TABLE ===========//
		$mlm_user_id = $this->userdata->getAllUsersId($user_id);
		$this->data['allLogRecord'] = $this->userdata->getUserLoginRecords($mlm_user_id, array('ULR.userType'=>3), array('ULR.id'=>'DESC'), array('count'=>100, 'start'=>0));
		//echo $this->db->last_query();
		foreach ($this->data['allLogRecord'] as $log_data) {
			$log_data->profile_url = 'javascript:void(0)';
		}
		//=========== LOGIN LOGOUT TABLE ===========//

		$this->load->view('solicitor/dashboard',$this->data);
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

		$this->data['taskData'] = $this->userdata->getTaskDataByUserIds($mlm_user_id, $cond, array('deadline'=>'ASC'), array('count'=>100, 'start'=>0));
		//echo $this->db->last_query();
		$this->userdata->unsetTable();
		foreach ($this->data['taskData'] as $task) {			
			$task->client_name = $task->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->client_id), 'name'):'';
			$task->assigned_user_name = $task->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->assigned_user), 'name'):'';
			$task->created_user_name = $task->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->created_user), 'name'):'';
		}

		$return_data['taskTable'] = $this->load->view('solicitor/search_task',$this->data,TRUE);
		echo json_encode($return_data);
	}

	public function searchReminder()
	{
		$return_data = array();
		$user_id = $this->session->userdata('usrid');
		$user_data = $this->userdata->fetchOne(array('id'=>$user_id));
		$mlm_user_id = $this->userdata->getAllUsersId($user_id);
		$post_data = $this->input->post();
		//echo "<pre>"; print_r($this->input->post());
		$cond = array();
		$cond['is_reminder'] = 'Y';
		$cond['status'] = 'P';
		if(isset($post_data['my_reminder']))
		{
			$mlm_user_id = array($user_id);
		}
		
		$this->data['reminderData'] = $this->userdata->getTaskDataByUserIds($mlm_user_id, $cond, array('deadline'=>'DESC'), array('count'=>100, 'start'=>0));
		
		foreach ($this->data['reminderData'] as $reminder) {			
			$reminder->client_name = $reminder->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->client_id), 'name'):'';
			$reminder->assigned_user_name = $reminder->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->assigned_user), 'name'):'';
			$reminder->created_user_name = $reminder->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->created_user), 'name'):'';
		}
		$return_data['reminderTable'] = $this->load->view('solicitor/search_reminder',$this->data,TRUE);

		echo json_encode($return_data);
	}

	public function searchComment()
	{
		$return_data = array();
		//echo "<pre>"; print_r($this->input->post());
		$user_id = $this->session->userdata('usrid');
		$user_data = $this->userdata->fetchOne(array('id'=>$user_id));
		$mlm_user_id = $this->userdata->getAllUsersId($user_id);
		$post_data = $this->input->post();

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

		
		$cond = array();
		if($user_data->parent_id==0) //Solicitor
		{
			$cond = array('solicitor'=>$user_id);
		}
		else //Solicitor Employee
		{
			$cond = array('solicitor_employee'=>$user_id);
		}
		$clientData = $this->userdata->setTable(TABLE_CLIENTS)->fetchAll($cond);
		$this->userdata->unsetTable();
		$client_ids = array(0);
		foreach ($clientData as $key => $value) {
			$client_ids[] = $value->user_id;
		}

		$this->data['commentData'] = $this->userdata->getCommentDataByUserIds($client_ids, array('share_solcitor'=>'Y'), array('id'=>'DESC'), array('count'=>100, 'start'=>0));

		foreach ($this->data['commentData'] as $key => $comment) {
			if(!empty($user_ids) && !in_array($comment->from_user_id, $user_ids))
			{
				unset($this->data['commentData'][$key]);
			}
			$comment->from_user_name = $comment->from_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->from_user_id), 'name'):'';
			$comment->to_user_name = $comment->to_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->to_user_id), 'name'):'';
		}
		
		$return_data['commentTable'] = $this->load->view('solicitor/search_comment',$this->data,TRUE);
		echo json_encode($return_data);
	}




	public function change_password()
	{
		$this->load->view('solicitor/change_password',$this->data);
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
		redirect(base_url('solicitor/dashboard/change_password'));
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

		$cond = array();
		if($user_data->parent_id==0) //Solicitor
		{
			$cond = array('solicitor'=>$user_id);
		}
		else //Solicitor Employee
		{
			$cond = array('solicitor_employee'=>$user_id);
		}
		$clientData = $this->userdata->setTable(TABLE_CLIENTS)->fetchAll($cond);
		$this->userdata->unsetTable();
		$client_ids = array(0);
		foreach ($clientData as $key => $value) {
			$client_ids[] = $value->user_id;
		}

		$this->data['commentData'] = $this->userdata->getCommentDataByUserIds($client_ids, array('from_user_id !='=>$user_id, 'share_solcitor'=>'Y', 'postedtime >='=>$today, 'postedtime <='=>$tomorrow), array('id'=>'DESC'));

		foreach ($this->data['commentData'] as $comment) {
			$comment->to_user_name = $comment->to_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->to_user_id), 'name'):'';
			$count += 1;
		}


		$this->data['taskData'] = $this->userdata->getTaskDataByUserIds($mlm_user_id, array('is_reminder'=>'Y', 'deadline'=>$today), array('id'=>'DESC'));
		$this->userdata->unsetTable();
		foreach ($this->data['taskData'] as $task) {			
			$task->client_name = $task->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->client_id), 'name'):'';
			$count += 1;
		}

		$html .= $this->load->view('solicitor/notification_html',$this->data,TRUE);

		$return_data['count'] = $count;
		$return_data['html'] = $html;
		echo json_encode($return_data);

	}



















	
}


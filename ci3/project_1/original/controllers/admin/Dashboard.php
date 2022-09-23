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
		if($this->session->userdata('usrtype')!=1)
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
			$change_password_link = base_url('admin/dashboard/change_password');

			$this->data['password_change_worning'] = '<p>Prosimy o zmianę hasła do Systemu. Jeżeli nie zmienisz hasła, dostęp do Systemu zostanie zablokowany. Możesz wówczas odzyskać hasło ze strony logowania hasła (opcja Zapomniałeś hasła?).</p><p>Dostęp zostanie zablokowany za następującą liczbę dni: '.$date_diff;
			
			$this->data['password_change_worning'] .= '<p><a href="'.$change_password_link.'">Zmień hasło teraz</a></p>';
		}


		//only Status of Case = ZGŁOSZENIE
		$cond = array('C.status_of_case'=>20);
		$this->data['allClients'] = $this->userdata->getUserClientData(array(), $cond);	
		foreach ($this->data['allClients'] as $user) {			
			$user->type_of_accident = $user->type_of_accident?$this->defaultdata->getField(TABLE_CLIENT_SELECT_OPTION, array('id'=>$user->type_of_accident), 'name'):'';
			$user->agent_name = $user->agent?$this->defaultdata->getField(TABLE_USER, array('id'=>$user->agent), 'name'):'';
		}
		//echo "<pre>"; print_r($this->data['allClients']);die;

		
		$cond_task['is_reminder'] = 'N';
		$cond_task['status'] = 'P';
		$cond_task['assigned_user'] = $user_id; //default checked Show only my tasks
		$this->data['taskData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond_task, array('deadline'=>'ASC'), array('count'=>100, 'start'=>0));
		$this->userdata->unsetTable();
		//echo $this->db->last_query();die;
		foreach ($this->data['taskData'] as $task) {			
			$task->client_name = $task->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->client_id), 'name'):'';
			$task->assigned_user_name = $task->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->assigned_user), 'name'):'';
			$task->created_user_name = $task->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->created_user), 'name'):'';
		}
		$this->data['taskTable'] = $this->load->view('admin/search_task',$this->data,TRUE);



		$cond_reminder['is_reminder'] = 'Y';
		$cond_reminder['status'] = 'P';
		$cond_reminder['assigned_user'] = $user_id; //default checked Show only my reminders
		$this->data['reminderData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond_reminder, array('deadline'=>'ASC'), array('count'=>100, 'start'=>0));
		$this->userdata->unsetTable();
		foreach ($this->data['reminderData'] as $reminder) {			
			$reminder->client_name = $reminder->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->client_id), 'name'):'';
			$reminder->assigned_user_name = $reminder->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->assigned_user), 'name'):'';
			$reminder->created_user_name = $reminder->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->created_user), 'name'):'';
		}
		$this->data['reminderTable'] = $this->load->view('admin/search_reminder',$this->data,TRUE);



		$this->data['commentData'] = $this->userdata->setTable(TABLE_CLIENT_COMMENT)->fetchAll(array(), array('id'=>'DESC'), array('count'=>100, 'start'=>0));
		$this->userdata->unsetTable();
		foreach ($this->data['commentData'] as $comment) {
			if($comment->reply_from_id =='0' && $comment->parent_id=='0'){
				$comment->from_user_name = $comment->from_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->from_user_id), 'name'):'';
			}else{
				$comment->from_user_name = $comment->reply_from_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->reply_from_id), 'name'):'';
			}
			
			$comment->to_user_name = $comment->to_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->to_user_id), 'name'):'';
		}
		$this->data['commentTable'] = $this->load->view('admin/search_comment',$this->data,TRUE);

		$this->data['calendarEvent'] = $this->userdata->setTable(TABLE_CALENDAR_EVENT)->fetchAll(array(), array('id'=>'DESC'));
		$this->userdata->unsetTable();
		foreach ($this->data['calendarEvent'] as $event) {
			$event->user_name = $event->user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$event->user_id), 'name'):'';
		}

		
		$this->data['allLogRecord'] = $this->userdata->getSeanchUserByUserType();
		
		foreach ($this->data['allLogRecord'] as $log_data) {
			$log_data->userData = $this->defaultdata->get_single_row(TABLE_USER, array('id'=>$log_data->user_id));
			$log_data->profile_url = 'javascript:void(0)';
			if($log_data->userType==2){ $log_data->profile_url = base_url('admin/agent/information/'.$log_data->user_id); }
			if($log_data->userType==3){ $log_data->profile_url = base_url('admin/solicitor/information/'.$log_data->user_id); }
			if($log_data->userType==5){ $log_data->profile_url = base_url('admin/consultant'); }
		}
		$this->data['loginTable'] = $this->load->view('admin/search_login',$this->data,TRUE);


		$this->load->view('admin/dashboard',$this->data);
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
		//$this->data['taskData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond, array('deadline'=>'ASC'), array('count'=>100, 'start'=>0));
		$this->data['taskData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond, array('deadline'=>'ASC'));
		//echo $this->db->last_query();
		$this->userdata->unsetTable();
		foreach ($this->data['taskData'] as $task) {			
			$task->client_name = $task->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->client_id), 'name'):'';
			$task->assigned_user_name = $task->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->assigned_user), 'name'):'';
			$task->created_user_name = $task->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->created_user), 'name'):'';
		}

		$return_data['taskTable'] = $this->load->view('admin/search_task',$this->data,TRUE);
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

		$this->data['reminderData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond, array('deadline'=>'DESC'), array('count'=>100, 'start'=>0));
		$this->userdata->unsetTable();
		foreach ($this->data['reminderData'] as $reminder) {			
			$reminder->client_name = $reminder->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->client_id), 'name'):'';
			$reminder->assigned_user_name = $reminder->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->assigned_user), 'name'):'';
			$reminder->created_user_name = $reminder->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->created_user), 'name'):'';
		}
		$return_data['reminderTable'] = $this->load->view('admin/search_reminder',$this->data,TRUE);
		echo json_encode($return_data);
	}

	public function searchComment()
	{
		$return_data = array();
		//echo "<pre>"; print_r($this->input->post());
		$post_data = $this->input->post();

		$user_data = $this->userdata->getAllUserByUserType($post_data);
		$user_ids = array();
		foreach ($user_data as $key => $value) {
			$user_ids[] = $value->id;
		}

		
		$this->data['commentData'] = $this->userdata->getCommentDataByFromUserIds($user_ids, array(), array('id'=>'DESC'), array('count'=>100, 'start'=>0));
		
		foreach ($this->data['commentData'] as $comment) {
			$comment->from_user_name = $comment->from_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->from_user_id), 'name'):'';
			$comment->to_user_name = $comment->to_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->to_user_id), 'name'):'';
		}
		$return_data['commentTable'] = $this->load->view('admin/search_comment',$this->data,TRUE);
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
			if($log_data->userType==2){ $log_data->profile_url = base_url('admin/agent/information/'.$log_data->user_id); }
			if($log_data->userType==3){ $log_data->profile_url = base_url('admin/solicitor/information/'.$log_data->user_id); }
			if($log_data->userType==5){ $log_data->profile_url = base_url('admin/consultant'); }
		}
		
		$return_data['loginTable'] = $this->load->view('admin/search_login',$this->data,TRUE);
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

		$html .= $this->load->view('admin/notification_html',$this->data,TRUE);

		$return_data['count'] = $count;
		$return_data['html'] = $html;
		echo json_encode($return_data);

	}


	public function change_password()
	{
		$this->load->view('admin/change_password',$this->data);
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
		redirect(base_url('admin/dashboard/change_password'));
	}


	public function invoice_details()
    {
        $this->load->model('invoicedetails');

        $this->data['create_new'] = false;
        $user_cond['id'] = 1;

        $this->data['invoice_details'] = $this->invoicedetails->grabInvoiceData($user_cond);
          if(empty($this->data['invoice_details'])){
              $this->data['create_new'] = true;
          }
        $this->load->view('admin/invoice_details',$this->data);
    }
	
	public function admin_settings()
	{
		$user_id = $this->session->userdata('usrid');
		$this->data['userData'] = $this->defaultdata->get_single_row(TABLE_USER, array('id'=>$user_id));
		
		$this->load->view('admin/admin_settings',$this->data);
	}


	public function saveAdminSettings(){
		
		$user_id = $this->session->userdata('usrid');
		$post_data = $this->input->post();
		
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('firstName', 'First Name', 'trim|required');
		$this->form_validation->set_rules('lastName', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('emailAddress', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
		} else
		{			
			$update_data['firstName'] = $post_data['firstName'];
			$update_data['lastName'] = $post_data['lastName'];
			$update_data['emailAddress'] = $post_data['emailAddress'];
			$update_data['phone'] = $post_data['phone'];
			
			$this->userdata->update($update_data, array('id'=>$user_id));
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(384));
		}
		
		redirect(base_url('admin/dashboard/admin_settings'));
		
	}

	public function default_email_footer_settings(){

		$this->data['userData'] = $this->defaultdata->get_single_row(TABLE_GENERAL_SETTINGS, array('id'=>1));

		$this->load->view('admin/default_email_footer_settings',$this->data);
		
	}
	
	public function saveDefaultEmailFooterSettings(){

		$post_data = $this->input->post();
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('default_email_footer_text', 'Default Email Footer Text', 'trim|required');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
		} else
		{			
			$update_data['default_email_footer_text'] = $post_data['default_email_footer_text'];
			$this->defaultdata->update(TABLE_GENERAL_SETTINGS, $update_data, array('id'=>1));
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(384));
		}
	
		redirect(base_url('admin/dashboard/default_email_footer_settings'));
		
	}

	public function change_password_email(){


		$this->data['userData'] = $this->defaultdata->get_single_row(TABLE_GENERAL_SETTINGS, array('id'=>1));

		$this->load->view('admin/change_password_email',$this->data);

	}

	public function saveDefaultRegisterEmailSettings(){

		$post_data = $this->input->post();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('emailAddressSender', 'Define the senders address to reset the password', 'trim|required|valid_email');
		$this->form_validation->set_rules('emailTitle', 'Email title', 'trim|required');
		$this->form_validation->set_rules('default_register_email_text', 'Define the password reset email', 'trim|required');


		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
		} else
		{
			$update_data['emailAddressSender'] = $post_data['emailAddressSender'];
			$update_data['emailTitle'] = $post_data['emailTitle'];
			$update_data['default_register_email_text'] = $post_data['default_register_email_text'];

			$this->defaultdata->update(TABLE_GENERAL_SETTINGS, $update_data, array('id'=>1));
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(384));
		}

		redirect(base_url('admin/dashboard/change_password_email'));

	}

	public function change_system_images(){
		$this->data['userData'] = $this->defaultdata->get_single_row(TABLE_GENERAL_SETTINGS, array('id'=>1));
		$this->data['imagickNotInstall'] = false;
		if (!extension_loaded('imagick'))
			$this->data['imagickNotInstall'] = true;
		$this->load->view('admin/change_system_images',$this->data);
	}

	public function saveSystemImages(){

		$this->load->library('upload');
		$this->load->library('favicon');

		$settingsData = $this->defaultdata->get_single_row(TABLE_GENERAL_SETTINGS, array('id'=>1));

		//delete if checkbox
		$deleteLogo = $this->input->post('delete_logo');
		$deleteFavicon =  $this->input->post('delete_favicon');

		if($deleteLogo){
			if(!file_exists('/uploads/logos/'.$settingsData->logo)) {
				unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/logos/'.$settingsData->logo);
			}
		}

		if($deleteFavicon){
			if(!file_exists('/uploads/favicons/'.$settingsData->favicon)) {
				unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/favicons/'.$settingsData->favicon);
			}
		}

		if(!empty($_FILES['logo']['name'])) {

			if(!file_exists('/uploads/logos/'.$settingsData->logo)) {
				unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/logos/'.$settingsData->logo);
			}

			//upload logo
			$config['upload_path'] = 'uploads/logos';
			$config['allowed_types'] = 'ico|jpg|png';
			$config['max_size'] = 100;
			$config['max_width'] = 232;
			$config['max_height'] = 768;
			$config['remove_spaces'] = TRUE;
			$config['encrypt_name'] = TRUE;
			$config['overwrite'] = TRUE;

			$this->upload->initialize($config);

			if (!$this->upload->do_upload('logo')) {
				$this->session->set_flashdata('error', $this->upload->display_errors());
			} else {
				$fileData = $this->upload->data();

				$update_data['logo'] = $fileData['file_name'];
				$this->defaultdata->update(TABLE_GENERAL_SETTINGS, $update_data, array('id' => 1));
				$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(384));
			}
		}

		if(!empty($_FILES['favicon']['name'])) {

			if(!file_exists('/uploads/favicons/'.$settingsData->favicon)) {
				unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/favicons/'.$settingsData->favicon);
			}

			// upload favicon
			$config['upload_path'] = 'uploads/favicons';
			$config['allowed_types'] = 'ico|jpg|png';
			$config['max_size'] = 100;
			$config['max_width'] = 310;
			$config['max_height'] = 310;
			$config['remove_spaces'] = TRUE;
			$config['encrypt_name'] = TRUE;
			$config['overwrite'] = TRUE;

			$this->upload->initialize($config);

			if (!$this->upload->do_upload('favicon')) {
				$this->session->set_flashdata('error', $this->upload->display_errors());
			} else {
				$fileData = $this->upload->data();

				$update_data['favicon'] = $fileData['file_name'];
				$this->defaultdata->update(TABLE_GENERAL_SETTINGS, $update_data, array('id' => 1));

				array_map('unlink', glob($_SERVER['DOCUMENT_ROOT'].'/favicon/*'));

				//generate favicons
				$fav = new FaviconGenerator( $_SERVER['DOCUMENT_ROOT'].'/uploads/favicons/'. $fileData['file_name']);
				$fav->setCompression(FaviconGenerator::COMPRESSION_VERYHIGH);

				$fav->setConfig(array(
					'apple-background'    => FaviconGenerator::COLOR_BLUE,
					'apple-margin'        => 15,
					'android-background'  => FaviconGenerator::COLOR_GREEN,
					'android-margin'      => 15,
					'android-name'        => 'Demo',
					'android-url'         => ROOT_URL,
					'android-orientation' => FaviconGenerator::ANDROID_PORTRAIT,
					'ms-background'       => FaviconGenerator::COLOR_GREEN,
				));

				$fav->createAllAndGetHtml();

				$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(384));
			}
		}

		redirect(base_url('admin/dashboard/change_system_images'));

	}

	public function change_automatic_email(){
		$this->data['userData'] = $this->defaultdata->get_single_row(TABLE_GENERAL_SETTINGS, array('id'=>1));
		$this->load->view('admin/change_automatic_email',$this->data);
	}

	public function saveAutomaticRegisterEmailSettings(){
		$post_data = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('default_automatic_email_text', 'Default Email Footer Text', 'trim|required');
		$this->form_validation->set_rules('number_of_days', 'Number of days', 'trim|required');
		$this->form_validation->set_rules('number_of_days_change_auto_password', 'Number of auto password change', 'trim|required');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
		} else
		{
			$update_data['default_automatic_email_title'] = $post_data['default_automatic_email_title'];
			$update_data['default_automatic_email_text'] = $post_data['default_automatic_email_text'];
			$update_data['number_of_days'] = $post_data['number_of_days'];
			$update_data['number_of_days_change_auto_password'] = $post_data['number_of_days_change_auto_password'];

			$this->defaultdata->update(TABLE_GENERAL_SETTINGS, $update_data, array('id'=>1));
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(384));
		}

		redirect(base_url('admin/dashboard/change_automatic_email'));
	}

	public function change_reminder_settings(){
		$this->data['userData'] = $this->defaultdata->get_single_row(TABLE_GENERAL_SETTINGS, array('id'=>1));

		$this->data['client_field'] = $this->defaultdata->get_single_row(TABLE_CLIENT_SELECT_OPTION, array('id'=>3));
		if(count($this->data['client_field'])==0)
		{
			redirect(base_url('admin/client/setting'));
		}
		$this->data['status_of_case'] = $this->defaultdata->get_results(TABLE_CLIENT_SELECT_OPTION, array('type'=>2));

		$this->load->view('admin/change_reminder_settings',$this->data);
	}

	public function saveReminderSettings(){
		$post_data = $this->input->post();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('limitation_period', 'Limitation period', 'trim|is_natural');
		$this->form_validation->set_rules('deadline_task', 'Deadline task', 'trim|is_natural');
		$this->form_validation->set_rules('name[]', 'Options', 'trim');
		//$this->form_validation->set_rules('days_to_complete', 'Days to complete', 'trim|is_natural');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
		} else
		{

			$option_data['type'] = $post_data['id'];

			if($post_data['parent_id']) {
				$option_data['parent_id'] = $post_data['parent_id'];
			} else {
				$option_data['parent_id'] = 0;
			}

			for($i=0;$i<count($post_data['name']);$i++)
			{
				$option_data['name'] = $post_data['name'][$i];

				if(isset($post_data['reminder_time']))
				{
					$option_data['reminder_time'] = $post_data['reminder_time'][$i];
				}

				if($post_data['option_id'][$i] > 0) {
					$this->userdata->setTable(TABLE_CLIENT_SELECT_OPTION)->update($option_data, array('id'=>$post_data['option_id'][$i]));
					$this->userdata->unsetTable();
				} else {
					$this->userdata->setTable(TABLE_CLIENT_SELECT_OPTION)->insert($option_data);
					$this->userdata->unsetTable();
				}
			}

			$update_data['limitation_period'] = $post_data['limitation_period'];
			$update_data['deadline_task'] = $post_data['deadline_task'];
			//$update_data['days_to_complete'] = $post_data['days_to_complete'];

			$this->defaultdata->update(TABLE_GENERAL_SETTINGS, $update_data, array('id'=>1));
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(384));
		}

		redirect(base_url('admin/dashboard/change_reminder_settings'));
	}

	public function change_file_upload_settings(){
		$this->data['userData'] = $this->defaultdata->get_single_row(TABLE_GENERAL_SETTINGS, array('id'=>1));
		$this->load->view('admin/change_file_upload_settings',$this->data);
	}

	public function saveFileUploadsSettings(){
		$post_data = $this->input->post();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('max_email_file_size', 'Maximum email file size', 'trim|is_natural');
		$this->form_validation->set_rules('max_comment_attachment_file_size', 'Maximum comment attachment file size', 'trim|is_natural');
		$this->form_validation->set_rules('max_form_attachment_file_size', 'Maximum form attachment file size', 'trim|is_natural');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
		} else
		{

			//change htaccess file if exist
			$path = $_SERVER['DOCUMENT_ROOT'].'/.htaccess';

			if(file_exists($path)){
				$htaccess = file_get_contents($path);
				$pattern = "@^php_value post_max_size (.*)$@m";
				$htaccess = preg_replace($pattern, "php_value post_max_size ".$post_data['max_form_attachment_file_size']."M", $htaccess);
				$pattern = "@^php_value upload_max_filesize (.*)$@m";
				$htaccess = preg_replace($pattern, "php_value upload_max_filesize ".$post_data['max_form_attachment_file_size']."M", $htaccess);
				$pattern = "@^php_value memory_limit (.*)$@m";
				$htaccess = preg_replace($pattern, "php_value memory_limit ".$post_data['max_form_attachment_file_size']."M", $htaccess);
				file_put_contents($path,$htaccess);
			}

			$update_data['max_email_file_size'] = $post_data['max_email_file_size'];
			$update_data['max_comment_attachment_file_size'] = $post_data['max_comment_attachment_file_size'];
			$update_data['max_form_attachment_file_size'] = $post_data['max_form_attachment_file_size'];

			$this->defaultdata->update(TABLE_GENERAL_SETTINGS, $update_data, array('id'=>1));
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(384));


		}

		redirect(base_url('admin/dashboard/change_file_upload_settings'));
	}

	public function change_comments_title_email(){
		$this->data['userData'] = $this->defaultdata->get_single_row(TABLE_GENERAL_SETTINGS, array('id'=>1));
		$this->load->view('admin/change_comments_title_email',$this->data);
	}

	public function saveCommentsTitleSettings(){

		$post_data = $this->input->post();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('client_comment_title', 'Enter the email title', 'trim|required');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
		} else
		{
			$update_data['client_comment_title'] = $post_data['client_comment_title'];

			$this->defaultdata->update(TABLE_GENERAL_SETTINGS, $update_data, array('id'=>1));
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(384));
		}

		redirect(base_url('admin/dashboard/change_comments_title_email'));

	}

	public function mailgun_settings(){
		$this->data['userData'] = $this->defaultdata->get_single_row(TABLE_GENERAL_SETTINGS, array('id'=>1));
		$this->load->view('admin/mailgun_settings',$this->data);
	}

	public function saveMailgunSettings(){

		$post_data = $this->input->post();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('mailgun_domain', 'Email domain', 'trim|required');
		$this->form_validation->set_rules('mailgun_api_key', 'MailGun API Key', 'trim|required');
		$this->form_validation->set_rules('mailgun_admin_mail', 'Administrator\'s address', 'trim|required|valid_email');


		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
		} else
		{
			$update_data['mailgun_domain'] = $post_data['mailgun_domain'];
			$update_data['mailgun_api_key'] = $post_data['mailgun_api_key'];
			$update_data['mailgun_admin_mail'] = $post_data['mailgun_admin_mail'];

			$this->defaultdata->update(TABLE_GENERAL_SETTINGS, $update_data, array('id'=>1));
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(384));
		}

		redirect(base_url('admin/dashboard/mailgun_settings'));

	}

	public function searchAllTask()
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
		// TODO: add filter date start and end deadline
		if(isset($post_data['date_of_task_start']) && $post_data['date_of_task_start'])
		{
			$cond['deadline >='] = strtotime(str_replace('/','-', $post_data['date_of_task_start']));
		}

		if(isset($post_data['date_of_task_end']) && $post_data['date_of_task_end'])
		{
			$cond['deadline <='] = strtotime(str_replace('/','-', $post_data['date_of_task_end']));
		}
		//$this->data['taskData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond, array('deadline'=>'ASC'), array('count'=>100, 'start'=>0));
		$this->data['taskData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond, array('deadline'=>'ASC'));
		//echo $this->db->last_query();
		$this->userdata->unsetTable();
		foreach ($this->data['taskData'] as $task) {
			$task->client_name = $task->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->client_id), 'name'):'';
			$task->assigned_user_name = $task->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->assigned_user), 'name'):'';
			$task->created_user_name = $task->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$task->created_user), 'name'):'';
		}

		$return_data['taskTable'] = $this->load->view('admin/history/search_task', $this->data, TRUE);

		echo json_encode($return_data);
	}

	public function searchAllReminder()
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

		if (isset($post_data['date_of_reminder_start']) && $post_data['date_of_reminder_start']) {
			$cond['deadline >='] = strtotime(str_replace('/','-', $post_data['date_of_reminder_start']));
		}

		if (isset($post_data['date_of_reminder_end']) && $post_data['date_of_reminder_end']) {
			$cond['deadline <='] = strtotime(str_replace('/','-', $post_data['date_of_reminder_end']));
		}

		$this->data['reminderData'] = $this->userdata->setTable(TABLE_CLIENT_TASKS)->fetchAll($cond, array('deadline'=>'DESC'));
		$this->userdata->unsetTable();
		foreach ($this->data['reminderData'] as $reminder) {
			$reminder->client_name = $reminder->client_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->client_id), 'name'):'';
			$reminder->assigned_user_name = $reminder->assigned_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->assigned_user), 'name'):'';
			$reminder->created_user_name = $reminder->created_user?$this->defaultdata->getField(TABLE_USER, array('id'=>$reminder->created_user), 'name'):'';
		}

		$return_data['reminderTable'] = $this->load->view('admin/history/search_reminder', $this->data, TRUE);

		echo json_encode($return_data);
	}

	public function searchAllComment()
	{
		$return_data = array();
		//echo "<pre>"; print_r($this->input->post());
		$post_data = $this->input->post();
		$cond = array();

		$user_data = $this->userdata->getAllUserByUserType($post_data);
		$user_ids = array();
		foreach ($user_data as $key => $value) {
			$user_ids[] = $value->id;
		}

		if (isset($post_data['date_of_comment_start']) && $post_data['date_of_comment_start']) {
			$cond['postedtime >='] = strtotime(str_replace('/','-', $post_data['date_of_comment_start']));
		}

		if (isset($post_data['date_of_comment_end']) && $post_data['date_of_comment_end']) {
			$cond['postedtime <='] = strtotime(str_replace('/','-', $post_data['date_of_comment_end']));
		}

		if (isset($post_data['comment_type']) && $post_data['comment_type']) {
			$cond['comment_type_id'] = $post_data['comment_type'];
		}

		$this->data['commentData'] = $this->userdata->getCommentDataByFromUserIds($user_ids, $cond, array('id'=>'DESC'));

		foreach ($this->data['commentData'] as $comment) {
			$comment->from_user_name = $comment->from_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->from_user_id), 'name'):'';
			$comment->to_user_name = $comment->to_user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$comment->to_user_id), 'name'):'';
		}

		$return_data['commentTable'] = $this->load->view('admin/history/search_comment', $this->data, TRUE);

		echo json_encode($return_data);
	}


	public function searchAllLogin($value='')
	{
		$return_data = array();
		//echo "<pre>"; print_r($this->input->post());
		$post_data = $this->input->post();
		$cond = null;

		//TODO: filter date postedTime
		if (isset($post_data['date_of_login_start']) && $post_data['date_of_login_start']) {
			$data = strtotime(str_replace('/','-', $post_data['date_of_login_start']));

			$cond = '(logintime >= '.$data.' OR logouttime >= '.$data.')';
		}

		if (isset($post_data['date_of_login_end']) && $post_data['date_of_login_end']) {
			$data = strtotime(str_replace('/','-', $post_data['date_of_login_end']));

			$cond .= ' AND (logintime <= '.$data.' OR logouttime <= '.$data.')';
		}

		$this->data['allLogRecord'] = $this->userdata->getSeanchUserByUserType($post_data, $cond);

		foreach ($this->data['allLogRecord'] as $log_data) {
			$log_data->userData = $this->defaultdata->get_single_row(TABLE_USER, array('id'=>$log_data->user_id));
			$log_data->profile_url = 'javascript:void(0)';
			if($log_data->userType==2){ $log_data->profile_url = base_url('admin/agent/information/'.$log_data->user_id); }
			if($log_data->userType==3){ $log_data->profile_url = base_url('admin/solicitor/information/'.$log_data->user_id); }
			if($log_data->userType==5){ $log_data->profile_url = base_url('admin/consultant'); }
		}

		$return_data['loginTable'] = $this->load->view('admin/history/search_login', $this->data, TRUE);

		echo json_encode($return_data);
	}




}


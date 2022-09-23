<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Croncontroller extends CI_Controller {

	public $data=array();
	public $loggedout_method_arr = array('index');
	function __construct()
	{
		parent::__construct();
		$this->data=$this->defaultdata->getFrontendDefaultData();
		$this->load->model('userdata');
		
	}
	

	//cron for 1 day
	public function sendReminderTask()
	{
		$this->data['general_settings'] = $this->defaultdata->grabSettingData();

		$limitationPeriod = (!empty($this->data['general_settings']->limitation_period)) ? $this->data['general_settings']->limitation_period : 30;
		$deadlineTask = (!empty($this->data['general_settings']->deadline_task)) ? $this->data['general_settings']->deadline_task : 20;
		//$daysToComplete = (!empty($this->data['general_settings']->days_to_complete)) ? $this->data['general_settings']->days_to_complete : 10;
		$expirationReminder = (!empty($this->defaultdata->gradLanguageText(536))) ? $this->defaultdata->gradLanguageText(536) : 'EXPIRATION REMINDER';
		$noActionReminder = (!empty($this->defaultdata->gradLanguageText(537))) ? $this->defaultdata->gradLanguageText(537) : 'REMINDER: No action';

		$i = $j = 0;
		$days30 = $limitationPeriod*24*60*60;
		$client_data = $this->userdata->setTable(TABLE_CLIENTS)->fetchAll();
		$this->userdata->unsetTable();
		foreach ($client_data as $client) 
		{
			if(in_array($client->status_of_case, array(17, 18, 19, 20))){
				if($client->date_of_perdiction > time())
				{
					$userData = $this->userdata->fetchOne(array('id'=>$client->user_id));
					
					/********* EXPIRATION REMINDER *******/
					if($client->date_of_perdiction > 0 && $client->consultant > 0)
					{
						$beforePerdiction =  $client->date_of_perdiction - strtotime(date('Y-m-d'));
						if($beforePerdiction == $days30)
						{
							$task_data['client_id'] = $client->user_id;
							$task_data['subject'] = $expirationReminder;
							$task_data['deadline'] = strtotime(date('Y-m-d'))+($deadlineTask*24*60*60);
							$task_data['assigned_user'] = $client->consultant;
							$task_data['assigned_user_type'] = 'C';
							$task_data['created_user'] = 1;
							$task_data['priority'] = 'N';
							$task_data['status'] = 'P';
							$task_data['completedTime'] = 0;
							$task_data['is_reminder'] = 'Y';

							$reminder1Data = $this->userdata->setTable(TABLE_CLIENT_TASKS)->countRows($task_data);
							$this->userdata->unsetTable();
							
							if($reminder1Data == 0)
							{
								$this->userdata->setTable(TABLE_CLIENT_TASKS)->insert($task_data);
								$this->userdata->unsetTable();
								$i++;
							}
						}
					}

				}

				/********* REMINDER: No action *******/
				if($client->sub_status_of_case > 0 && $client->consultant > 0)
				{
					$reminder_time =  $deadlineTask; //$this->defaultdata->getField(TABLE_CLIENT_SELECT_OPTION, array('id'=>$client->sub_status_of_case), 'reminder_time');
					$reminder_sec = '';
					if($reminder_time > 0)
					{
						$reminder_sec = $reminder_time*24*60*60;
					}

					$commentData = $this->userdata->setTable(TABLE_CLIENT_COMMENT)->fetchAll(array('to_user_id'=>$client->user_id, 'from_user_id'=>$client->consultant), array('postedtime'=>'DESC'), array('count'=>1, 'start'=>0));
					$this->userdata->unsetTable();

					if(count($commentData) > 0)
					{
						$lastCommentTime = time()-$commentData[0]->postedtime;
					}
					else
					{
						$lastCommentTime = time()-$userData->postedtime;
					}	


					if(!empty($reminder_sec) && $lastCommentTime > $reminder_sec)
					{
						$task_data['client_id'] = $client->user_id;
						$task_data['subject'] = $noActionReminder;
						$task_data['deadline'] = strtotime(date('Y-m-d'))+($deadlineTask*24*60*60);
						$task_data['assigned_user'] = $client->consultant;
						$task_data['assigned_user_type'] = 'C';
						$task_data['created_user'] = 1;
						$task_data['priority'] = 'N';
						$task_data['status'] = 'P';
						$task_data['completedTime'] = 0;
						$task_data['is_reminder'] = 'Y';

						$reminder2Data = $this->userdata->setTable(TABLE_CLIENT_TASKS)->countRows(array('client_id'=>$client->user_id, 'subject'=>$noActionReminder, 'assigned_user'=>$client->consultant, 'status'=>'P', 'is_reminder'=>'Y'));
						$this->userdata->unsetTable();
						
						if($reminder2Data == 0)
						{
							$this->userdata->setTable(TABLE_CLIENT_TASKS)->insert($task_data);
							$this->userdata->unsetTable();
							$j++;
						}
					}

					

				}
				
			}
		}

		echo $i.' EXPIRATION REMINDER added </br> '.$j.' REMINDER: No action added';
	}
	
	//cron for 30 min
	public function autoLogoutAllUser()
	{
			
		$this->defaultdata->update(TABLE_USERLOGIN_RECORD, array('logouttime'=>time()), array('logouttime'=>0));
	}


	//cron for 1 day
	public function autoPasswordChange()
	{
		$this->data['general_settings'] = $this->defaultdata->grabSettingData();
		$systemSettings = $this->defaultdata->get_single_row(TABLE_GENERAL_SETTINGS, array('id'=>1));
		//automatic force to change password once a 3 months time


		$user_data = $this->defaultdata->get_results(TABLE_USER, array('userType !='=>4));

		$limitOfDays = 90;
		if(!empty($systemSettings->number_of_days_change_auto_password)){
			$limitOfDays = $systemSettings->number_of_days_change_auto_password;
		}

		if(count($user_data)>0)
		{
			foreach ($user_data as $key => $user) 
			{				
				if($user->password_update_time) {
					$last_password_change = $user->password_update_time;
				} else {
					$last_password_change = $user->postedtime;
				}

				$time_diff = time();

				$tomorrow = date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s", $last_password_change) . "+".$limitOfDays." days"));
				$date_diff = strtotime($tomorrow); //- time();
				
				if($time_diff > $date_diff)
				{
					$unique_id = encrypt($user->id,'porgotPassword');
					$reset_pass_link =  base_url('reset-password/'.$unique_id);

					$to = $user->emailAddress;
					//$subject = "Password Recovery";
					$subject = ($this->data['general_settings']->default_automatic_email_title) ? $this->data['general_settings']->default_automatic_email_title : "Odzyskiwanie hasła do Systemu Partnera Kancelarii";

					//$mailcontent = "<p>Dear ".$user_data->name.",</p>";
					$mailcontent = '';

					if(!empty($this->data['general_settings']->default_automatic_email_text)) {
						$mailcontent = nl2br($this->data['general_settings']->default_automatic_email_text);
						$mailcontent = str_replace('{RESET_PASS_LINK}', $reset_pass_link, $mailcontent);
					}

					$from_name = $this->data['general_settings']->contactEmailName;
					$from = $this->data['general_settings']->Contact_Email;
					
					//$send_mail = $this->defaultdata->sendMail($to, $subject, $mailcontent, $from, $from_name);

					//if($send_mail)
					//{
						$newRandomPassword = getRandomUserPassword();
						$update_data['userPassword'] = md5($newRandomPassword);
						$update_data['password_update_time'] = time();
						$update = $this->userdata->update($update_data, array('id'=>$user->id));
					//}
				}

			}
		}
	}


	//cron for 1 day
	public function sendNotificationForTask()
	{
		$i = 0; 
		$today = strtotime(date('Y-m-d'));
		$deadline_tasks = $this->defaultdata->get_results(TABLE_CLIENT_TASKS, array('deadline'=>$today, 'is_reminder'=>'Y', 'status'=>'P'));
		
		if(count($deadline_tasks) > 0)
		{
			foreach ($deadline_tasks as $task) 
			{
				$noti_data['client_id'] = $task->client_id;
				$noti_data['type'] = 2;
				$noti_data['source_table'] = TABLE_CLIENT_TASKS;
				$noti_data['source_id'] = $task->id;
				$noti_data['show_time'] = strtotime(date('Y-m-d'));
				$noti_data['status'] = 1;
				$noti_data['postedtime'] = time();
				
				$user_ids = $this->userdata->getTaskNotiUserIds($task->assigned_user, $task->assigned_user_type);

				foreach ($user_ids as $key => $val) 
				{
					$noti_data['user_id'] = $val;
					$noti_data['userType'] = $this->defaultdata->getField(TABLE_USER, array('id'=>$val), 'userType');
					$this->defaultdata->insert(TABLE_NOTIFICATION, $noti_data);
				}
				$i++;
			}
		}

		echo $i.' task deadline found';
	}
	
}

/* End of file Croncontroller.php */
/* Location: ./application/controllers/Croncontroller.php */
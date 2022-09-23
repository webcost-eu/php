<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public $data=array();
	public $loggedin_method_arr = array('my-account', 'update-profile', 'update-shipping-address', 'update-social-channel', 'messages', 'resources', 'update-reviewer-bio', 'consulting-price', 'reports');
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
	}

	public function login()
	{

		// $this->defaultdata->sendDefaultMailGun();
		// echo "done"; die;
		if($this->session->userdata('usrid') != '')
		{
			if($this->session->userdata('usrtype')==1)
			{
				redirect(base_url('admin/dashboard'));
			}
			elseif($this->session->userdata('usrtype')==2)
			{
				redirect(base_url('agent/dashboard'));
			}
			elseif($this->session->userdata('usrtype')==3)
			{
				redirect(base_url('solicitor/dashboard'));
			}
			elseif($this->session->userdata('usrtype')==5)
			{
				redirect(base_url('consultant/dashboard'));
			}
			else
			{
				redirect(base_url('user/logout'));
			}
		}
		else
		{
			$this->load->view('user/login',$this->data);
		}
	}

	public function loginProcess()
	{
		$login_data=array();
		$input_data = $this->input->post();
		//print_r($input_data);die;
		$this->load->library('form_validation');
		$this->form_validation->set_rules('userName', 'Username', 'trim|required');
		$this->form_validation->set_rules('userPassword', 'Password', 'trim|required');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('login_error',validation_errors());
			$this->session->set_userdata($input_data);
			redirect(base_url('login'));
		}
		else
		{
			$where_str = "(userName='".$input_data['userName']."' OR emailAddress='".$input_data['userName']."') AND userPassword='".md5($input_data['userPassword'])."'";
			$user_data = $this->userdata->grabLoginUserData($where_str);
			//echo '<pre>'; print_r($user_data);die;
			if(count($user_data) > 0)
			{
				if($user_data->status == 'Y')
				{
					
					if($user_data->userType==1)
					{
						$this->userdata->saveLoginLog($user_data->id);
						$this->defaultdata->setLoginSession($user_data);
						redirect(base_url('admin/dashboard'));
					}
					if($user_data->userType==2)
					{
						$this->userdata->saveLoginLog($user_data->id);
						$this->defaultdata->setLoginSession($user_data);
						redirect(base_url('agent/dashboard'));
					}
					if($user_data->userType==3)
					{
						$this->userdata->saveLoginLog($user_data->id);
						$this->defaultdata->setLoginSession($user_data);
						redirect(base_url('solicitor/dashboard'));
					}
					if($user_data->userType==5)
					{
						$this->userdata->saveLoginLog($user_data->id);
						$this->defaultdata->setLoginSession($user_data);
						redirect(base_url('consultant/dashboard'));
					}
					else
					{
						//$this->session->set_flashdata('login_error', '<p>User type is not define</p>');
						$this->session->set_flashdata('login_error', '<p>'.$this->defaultdata->gradLanguageText(446).'</p>');
						$this->session->set_userdata($input_data);
						redirect(base_url('login'));
					}
									
				}
				else
				{
					//$this->session->set_flashdata('login_error','<p>Your account is not activated or blocked by admin.</p>');
					$this->session->set_flashdata('login_error','<p>'.$this->defaultdata->gradLanguageText(447).'</p>');
					$this->session->set_userdata($input_data);
					redirect(base_url('login'));					
				}
			}
			else
			{
				//$this->session->set_flashdata('login_error','<p>Wrong username or password.</p>');
				$this->session->set_flashdata('login_error','<p>'.$this->defaultdata->gradLanguageText(448).'</p>');
				$this->session->set_userdata($input_data);
				redirect(base_url('login'));				
			}
		}
	}

	public function logout()
	{
		$condarr['login_status']=0;
		$condarr['logouttime']=time();
		$this->userdata->updateLoginUser($condarr);
		
		$log_record = $this->defaultdata->get_results(TABLE_USERLOGIN_RECORD, array('user_id'=>$this->session->userdata('usrid')), array('id'=>'DESC'), 1, 0);		
		if(count($log_record)>0)
		{
			$this->defaultdata->update(TABLE_USERLOGIN_RECORD, array('logouttime'=>time()), array('id'=>$log_record[0]->id));
		}
		
		$this->defaultdata->unsetLoginSession();
		redirect(base_url('login'));
	}

	public function notification()
	{
		
		$user_id = $this->session->userdata('usrid');
		$limit =  $this->uri->segment(3);
		$this->load->library('pagination');
		$config['base_url'] = base_url('user/notification');
		$config['total_rows'] = $this->defaultdata->count_record(TABLE_NOTIFICATION, array('user_id'=>$user_id));
		
		$config['per_page'] = 20; 
		$config['uri_segment'] = 3;
		$config['num_links'] = 2;
		$config['use_page_numbers'] = TRUE;
		//$config['page_query_string'] = TRUE;
		$config['reuse_query_string'] = TRUE;
		if($limit==""){$start = 0;}
		else{$start = ($limit-1)*$config['per_page'];}

		$config['full_tag_open'] = '<ul class="pagination pagination-split">';
		$config['full_tag_close'] = '</ul>';
		$config['first_link'] = 'First';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_link'] = 'Last';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['next_link'] = '&raquo;';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_link'] = '&laquo;';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a>';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';

		$this->pagination->initialize($config);
		$this->data['link'] = $this->pagination->create_links();

		$this->data['notificationData'] = $this->defaultdata->get_results(TABLE_NOTIFICATION, array('user_id'=>$user_id), array('id'=>'DESC'), $config['per_page'], $start);
		
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
		$this->load->view('user/notification', $this->data);

		

	}
	public function notificationallread()
	{
		$update_cond['user_id'] = $this->session->userdata('usrid');
		$update_cond['type'] = 1;
		$this->defaultdata->update(TABLE_NOTIFICATION, array('status'=>2), $update_cond);

		redirect(base_url('user/notification'));
	}










	
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */
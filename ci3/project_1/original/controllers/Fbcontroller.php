<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fbcontroller extends CI_Controller {

	public $data=array();
	function __construct()
	{
		parent::__construct();
		$this->load->model('userdata');
	}

	public function index()
	{
		
	}
	/* public function fblogin($userType = 3)
	{
		//echo $userType;exit;
		$config['appId'] = $this->config->item('appId');
		$config['secret'] = $this->config->item('secret');
		
		$this->load->library('facebook/facebook', $config);
		$fb_user_id = $this->facebook->getUser();
		if($fb_user_id)
		{
			$user_profile = $this->facebook->api('/me');
			print_r($user_profile);exit;
			$name = $user_profile['name'];
			$email = $user_profile['email'];
			$user_cond = array();
			$user_cond['emailAddress'] = $email;
			$user_det = $this->userdata->grabUserData($user_cond);
			if(count($user_det) > 0)
			{
				$usr_data = array();
				$usr_data['name'] = $name;
				$usr_data['status'] = 'Y';
				$this->userdata->updateUser($usr_data,$user_cond);
				if($user_det->userName == '')
				{
					$email_arr = explode('@',$email);
					$userName = url_title($email_arr[0],'-',true);
					$usr_data = array();
					$usr_data['userName'] = getRandomUserName($userName);
					$this->userdata->updateUser($usr_data,$user_cond);
				}
			}
			else
			{
				$usr_data = array();
				$email_arr = explode('@',$email);
				$userName = url_title($email_arr[0],'-',true);
				$usr_data['userName'] = getRandomUserName($userName);
				$usr_data['name'] = $name;
				$usr_data['emailAddress'] = $email;
				$usr_data['userType'] = $userType;
				$usr_data['status'] = 'Y';
				$usr_data['postedtime'] = time();
				$usr_data['ipaddress'] = $_SERVER["REMOTE_ADDR"];
				$user_id = $this->userdata->insertUser($usr_data);
				$user_cond['id'] =$user_id;
			}
			$user_det = $this->userdata->grabUserData($user_cond);
			$this->userdata->saveLoginLog($user_det->id);
			$this->defaultdata->setLoginSession($user_det);
			if($userType == 2)
			{
				redirect(base_url('sign-up/step/3'));
			}
			else
			{
				$re_url = base_url('user/profile/'.$user_det->userName);
				redirect($re_url);
			}
		}
		else
		{
			redirect(base_url());
		}
	} */
	public function fblogin($userType = 1)
	{
		$user_profile = $this->input->post();
		//print_r($user_profile);exit;
		$return_data = array();
		$return_data['has_error'] = 1;
		if(isset($user_profile['email']))
		{
			$name = $user_profile['name'];
			$email = $user_profile['email'];
			$user_cond = array();
			$user_cond['emailAddress'] = $email;
			$user_det = $this->userdata->grabUserData($user_cond);
			if(count($user_det) > 0)
			{
				$usr_data = array();
				$usr_data['status'] = 'Y';
				$this->userdata->update($usr_data,$user_cond);
				/*if($user_det->userName == '')
				{
					$email_arr = explode('@',$email);
					$userName = url_title($email_arr[0],'-',true);
					$usr_data = array();
					$usr_data['userName'] = getRandomUserName($userName);
					$this->userdata->updateUser($usr_data,$user_cond);
				}*/
			}
			else
			{
				$usr_data = array();
				/*$email_arr = explode('@',$email);
				$userName = url_title($email_arr[0],'-',true);
				$usr_data['userName'] = getRandomUserName($userName);*/
				$usr_data['name'] = $name;
				$usr_data['emailAddress'] = $email;
				$usr_data['userType'] = $userType;
				$usr_data['status'] = 'Y';
				$usr_data['postedtime'] = time();
				$usr_data['ipaddress'] = $_SERVER["REMOTE_ADDR"];
				$user_id = $this->userdata->insert($usr_data);
				$user_cond['id'] =$user_id;
			}
			$user_det = $this->userdata->grabUserData($user_cond);
			$this->userdata->saveLoginLog($user_det->id);
			$this->defaultdata->setLoginSession($user_det);
			$return_data['has_error'] = 0;

			$return_data['redirect_url'] = base_url('user/update-profile');
		}
		else
		{
			$return_data['has_error'] = 1;
		}
		echo json_encode($return_data);
	}
}

/* End of file fbcontroller.php */
/* Location: ./application/controllers/fbcontroller.php */
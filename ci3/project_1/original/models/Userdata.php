<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Userdata extends MainModel {

	private $data=array();
    public $share_solcitor;
    public $share_agent_manager;
    public $share_agent;                       
	function __construct()
	{
		parent::__construct();
		$this->table = TABLE_USER;
	}
	public function grabUserData($user_cond = array())
	{
		$return_data = array();
		if(count($user_cond) > 0)
		{
			$query = $this->db->get_where($this->table,$user_cond);
			$return_data = $query->row();
		}
		return $return_data;
	}
	public function grabLoginUserData($where_str = '')
	{
		$return_data = array();
		if($where_str != '')
		{
			$this->db->where($where_str);
			$query = $this->db->get($this->table);
			$return_data = $query->row();//echo $this->db->last_query();exit;
		}
		return $return_data;
	}
	public function getActivationEmailTemplate()
	{
		$query = $this->db->get(TABLE_EMAIL_ACTIVATION);
		$mail_data = $query->row();
		return $mail_data;
	}
	
	public function saveLoginLog($id)
	{
		$cond = array('uid' => $id);
		$usr_data = $this->db->get_where(TABLE_USERLOGIN,$cond)->row();
		if(count($usr_data) > 0)
		{
			$up_data = array('lastlogintime' => time(),'ipaddress' => $_SERVER["REMOTE_ADDR"],'login_status'=>1, 'logouttime'=>0);
			$cond = array('uid' => $id);
			$this->db->where($cond);
			$this->db->update(TABLE_USERLOGIN, $up_data);
		}
		else
		{
			$in_data = array('uid' => $id,'lastlogintime' => time(),'ipaddress' => $_SERVER["REMOTE_ADDR"],'login_status'=>1, 'logouttime'=>0);
			$this->db->insert(TABLE_USERLOGIN,$in_data);
		}


		$user = $this->db->get_where(TABLE_USER, array('id'=>$id))->row();
		$insert_data['user_id'] = $id;
		$insert_data['userType'] = $user->userType;
		$insert_data['logintime'] = time();
		$insert_data['logouttime'] = 0;
		$insert_data['ipaddress'] = $_SERVER["REMOTE_ADDR"];
		$this->db->insert(TABLE_USERLOGIN_RECORD,$insert_data);

	}
	public function updateLoginUser($up_data=array())
	{
		$this->db->where('uid', $this->session->userdata('usrid'));
		$this->db->update(TABLE_USERLOGIN, $up_data);
	}


	public function getAllUsers($cond = array())
	{
		$return_data = array();
		if(count($cond) > 0)
		{
			$query = $this->db->get_where($this->table,$cond);
			$return_data = $query->result();
		}
		return $return_data;
	}


	public function getUserClientData($search=array(), $cond=array())
	{
		
		if(isset($search['agent']) && $search['agent']){
			if(isset($search['agent_mlm']) && $search['agent_mlm']){
				$user_ids = $this->getAllUsersId($search['agent']);
			}
		}	


		$this->db->from(TABLE_USER." AS U");
		$this->db->join(TABLE_CLIENTS." AS C","U.id=C.user_id");
		
		if(count($search)>0)
		{
			if(isset($search['status_of_case']) && count($search['status_of_case'])>0){
				$this->db->where_in('C.status_of_case', $search['status_of_case']);
			}

			if(isset($search['sub_status_of_case']) && $search['sub_status_of_case']){
				$this->db->where('C.sub_status_of_case', $search['sub_status_of_case']);
			}

			if(isset($search['consultant']) && $search['consultant']){
				$this->db->where('C.consultant', $search['consultant']);
			}
			if(isset($search['solicitor']) && $search['solicitor']){
				$this->db->where('C.solicitor', $search['solicitor']);
			}
			if(isset($search['solicitor_employee']) && $search['solicitor_employee']){
				$this->db->where('C.solicitor_employee', $search['solicitor_employee']);
			}

			if(!isset($search['all_agents'])){
				if(isset($search['agent']) && $search['agent']){
					
					if(isset($search['agent_mlm']) && $search['agent_mlm']){
						$this->db->where_in('C.agent', $user_ids);
					}else{
						$this->db->where('C.agent', $search['agent']);
					}
				}
			}

			if(isset($search['central_provision_agent']) && $search['central_provision_agent']){
				$this->db->where('C.central_provision_agent', $search['central_provision_agent']);
			}
			
			if(isset($search['type_of_accident']) && $search['type_of_accident']){
				$this->db->where('C.type_of_accident', $search['type_of_accident']);
			}

			if(isset($search['insurer']) && $search['insurer']){
				$this->db->where('C.insurer', $search['insurer']);
			}
			if(isset($search['claiming_amount']) && $search['claiming_amount']){
				$min_amount = $max_amount = '';
				if($search['claiming_amount']==1){
					$min_amount = '';
					$max_amount = 5000;
				} elseif($search['claiming_amount']==2){
					$min_amount = 5000;
					$max_amount = 10000;
				} elseif($search['claiming_amount']==3){
					$min_amount = 10000;
					$max_amount = 50000;
				} elseif($search['claiming_amount']==4){
					$min_amount = 50000;
					$max_amount = 100000;
				} elseif($search['claiming_amount']==5){
					$min_amount = 100000;
					$max_amount = '';
				}

				if($min_amount) {
					$this->db->where('C.sum_of_claiming >=', $min_amount);
				}
				if($max_amount) {
					$this->db->where('C.sum_of_claiming <=', $max_amount);
				}
			}
			if(isset($search['country_id']) && $search['country_id']){
				$this->db->where('C.country_id', $search['country_id']);
			}

			if(isset($search['date_of_accident_start']) && $search['date_of_accident_start']){
				$this->db->where('C.date_of_accident >=', strtotime(str_replace('/','-',$search['date_of_accident_start'])));
			}
			if(isset($search['date_of_accident_end']) && $search['date_of_accident_end']){
				$this->db->where('C.date_of_accident <=', strtotime(str_replace('/','-',$search['date_of_accident_end'])));
			}

			if(isset($search['date_of_agreement_start']) && $search['date_of_agreement_start']){
				$this->db->where('C.date_of_agreement >=', strtotime(str_replace('/','-',$search['date_of_agreement_start'])));
			}
			if(isset($search['date_of_agreement_end']) && $search['date_of_agreement_end']){
				$this->db->where('C.date_of_agreement <=', strtotime(str_replace('/','-',$search['date_of_agreement_end'])));
			}

			if(isset($search['date_of_getting_agreement_start']) && $search['date_of_getting_agreement_start']){
				$this->db->where('C.date_of_getting_agreement >=', strtotime(str_replace('/','-',$search['date_of_getting_agreement_start'])));
			}
			if(isset($search['date_of_getting_agreement_end']) && $search['date_of_getting_agreement_end']){
				$this->db->where('C.date_of_getting_agreement <=', strtotime(str_replace('/','-',$search['date_of_getting_agreement_end'])));
			}

			if(isset($search['postedtime_start']) && $search['postedtime_start']){
				$this->db->where('U.postedtime >=', strtotime(str_replace('/','-',$search['postedtime_start'])));
			}
			if(isset($search['postedtime_end']) && $search['postedtime_end']){
				$this->db->where('U.postedtime <=', strtotime(str_replace('/','-',$search['postedtime_end'])));
			}			
			
			if(isset($search['good_cases']) && $search['good_cases']){
				$this->db->where('C.good_cases', $search['good_cases']);
			}

			if(isset($search['MA_status']) && $search['MA_status']){
				$this->db->where('U.MA_status', $search['MA_status']);
			} 
		}
		
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
		
		$this->db->where(array('U.userType'=>4, 'U.status'=>'Y', 'U.CA_status'=>'Y'));
		$this->db->order_by('U.id','DESC');
		$query = $this->db->get();
		//echo $this->db->last_query();exit;
		$return_data = $query->result();
		return $return_data;
	}

	public function getUserClientDatabyid($cilent_id)
	{
		if(!empty($search)) {
			if (isset($search['agent']) && $search['agent']) {
				if (isset($search['agent_mlm']) && $search['agent_mlm']) {
					$user_ids = $this->getAllUsersId($search['agent']);
				}
			}
		}


		$this->db->from(TABLE_USER." AS U");
		$this->db->join(TABLE_CLIENTS." AS C","U.id=C.user_id");
		$this->db->where('C.user_id', $cilent_id);	
		$this->db->where(array('U.userType'=>4, 'U.status'=>'Y', 'U.CA_status'=>'Y'));
		$this->db->order_by('U.id','DESC');
		$query = $this->db->get();
		//echo $this->db->last_query();exit;
		$return_data = $query->row_array();
		return $return_data;
	}

	public function getAdminAllClientData($search=array(), $cond=array())
	{
		
		if(isset($search['agent']) && $search['agent']){
			if(isset($search['agent_mlm']) && $search['agent_mlm']){
				$user_ids = $this->getAllUsersId($search['agent']);
			}
		}	


		$this->db->from(TABLE_USER." AS U");
		$this->db->join(TABLE_CLIENTS." AS C","U.id=C.user_id");
		
		if(count($search)>0)
		{
			if(isset($search['status_of_case']) && count($search['status_of_case'])>0){
				$this->db->where_in('C.status_of_case', $search['status_of_case']);
			}

			if(isset($search['sub_status_of_case']) && $search['sub_status_of_case']){
				$this->db->where('C.sub_status_of_case', $search['sub_status_of_case']);
			}

			if(isset($search['consultant']) && $search['consultant']){
				$this->db->where('C.consultant', $search['consultant']);
			}
			if(isset($search['solicitor']) && $search['solicitor']){
				$this->db->where('C.solicitor', $search['solicitor']);
			}
			if(isset($search['solicitor_employee']) && $search['solicitor_employee']){
				$this->db->where('C.solicitor_employee', $search['solicitor_employee']);
			}

			if(!isset($search['all_agents'])){
				if(isset($search['agent']) && $search['agent']){
					
					if(isset($search['agent_mlm']) && $search['agent_mlm']){
						$this->db->where_in('C.agent', $user_ids);
					}else{
						$this->db->where('C.agent', $search['agent']);
					}
				}
			}

			if(isset($search['central_provision_agent']) && $search['central_provision_agent']){
				$this->db->where('C.central_provision_agent', $search['central_provision_agent']);
			}
			
			if(isset($search['type_of_accident']) && $search['type_of_accident']){
				$this->db->where('C.type_of_accident', $search['type_of_accident']);
			}

			if(isset($search['insurer']) && $search['insurer']){
				$this->db->where('C.insurer', $search['insurer']);
			}
			if(isset($search['claiming_amount']) && $search['claiming_amount']){
				$min_amount = $max_amount = '';
				if($search['claiming_amount']==1){
					$min_amount = '';
					$max_amount = 5000;
				} elseif($search['claiming_amount']==2){
					$min_amount = 5000;
					$max_amount = 10000;
				} elseif($search['claiming_amount']==3){
					$min_amount = 10000;
					$max_amount = 50000;
				} elseif($search['claiming_amount']==4){
					$min_amount = 50000;
					$max_amount = 100000;
				} elseif($search['claiming_amount']==5){
					$min_amount = 100000;
					$max_amount = '';
				}

				if($min_amount) {
					$this->db->where('C.sum_of_claiming >=', $min_amount);
				}
				if($max_amount) {
					$this->db->where('C.sum_of_claiming <=', $max_amount);
				}
			}
			if(isset($search['country_id']) && $search['country_id']){
				$this->db->where('C.country_id', $search['country_id']);
			}

			if(isset($search['date_of_accident_start']) && $search['date_of_accident_start']){
				$this->db->where('C.date_of_accident >=', strtotime(str_replace('/','-',$search['date_of_accident_start'])));
			}
			if(isset($search['date_of_accident_end']) && $search['date_of_accident_end']){
				$this->db->where('C.date_of_accident <=', strtotime(str_replace('/','-',$search['date_of_accident_end'])));
			}

			if(isset($search['date_of_agreement_start']) && $search['date_of_agreement_start']){
				$this->db->where('C.date_of_agreement >=', strtotime(str_replace('/','-',$search['date_of_agreement_start'])));
			}
			if(isset($search['date_of_agreement_end']) && $search['date_of_agreement_end']){
				$this->db->where('C.date_of_agreement <=', strtotime(str_replace('/','-',$search['date_of_agreement_end'])));
			}

			if(isset($search['date_of_getting_agreement_start']) && $search['date_of_getting_agreement_start']){
				$this->db->where('C.date_of_getting_agreement >=', strtotime(str_replace('/','-',$search['date_of_getting_agreement_start'])));
			}
			if(isset($search['date_of_getting_agreement_end']) && $search['date_of_getting_agreement_end']){
				$this->db->where('C.date_of_getting_agreement <=', strtotime(str_replace('/','-',$search['date_of_getting_agreement_end'])));
			}

			if(isset($search['postedtime_start']) && $search['postedtime_start']){
				$this->db->where('U.postedtime >=', strtotime(str_replace('/','-',$search['postedtime_start'])));
			}
			if(isset($search['postedtime_end']) && $search['postedtime_end']){
				$this->db->where('U.postedtime <=', strtotime(str_replace('/','-',$search['postedtime_end'])));
			}			
			
			if(isset($search['good_cases']) && $search['good_cases']){
				$this->db->where('C.good_cases', $search['good_cases']);
			}

			if(isset($search['MA_status']) && $search['MA_status']){
				$this->db->where('U.MA_status', $search['MA_status']);
			}

			if(isset($search['CA_status']) && $search['CA_status']){
				$this->db->where('U.CA_status', $search['CA_status']);
			}
		}
		
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
		
		$this->db->where(array('U.userType'=>4));
		$this->db->order_by('U.id','DESC');
		$query = $this->db->get();
		$return_data = $query->result();
		return $return_data;
	}

	public function getAgentClientData($search, $mlm_agent_id)
	{
		
		if(isset($search['agent']) && $search['agent']){
			if(isset($search['agent_mlm']) && $search['agent_mlm']){
				$user_ids = $this->getAllUsersId($search['agent']);
			}
		}	


		$this->db->from(TABLE_USER." AS U");
		$this->db->join(TABLE_CLIENTS." AS C","U.id=C.user_id");
		
		if(count($search)>0)
		{
			if(isset($search['status_of_case']) && count($search['status_of_case'])>0){
				$this->db->where_in('C.status_of_case', $search['status_of_case']);
			}

			if(isset($search['sub_status_of_case']) && $search['sub_status_of_case']){
				$this->db->where('C.sub_status_of_case', $search['sub_status_of_case']);
			}

			if(isset($search['consultant']) && $search['consultant']){
				$this->db->where('C.consultant', $search['consultant']);
			}
			if(isset($search['solicitor']) && $search['solicitor']){
				$this->db->where('C.solicitor', $search['solicitor']);
			}

			if(!isset($search['all_agents'])){
				if(isset($search['agent']) && $search['agent']){
					
					if(isset($search['agent_mlm']) && $search['agent_mlm']){
						$this->db->where_in('C.agent', $user_ids);
					}else{
						$this->db->where('C.agent', $search['agent']);
					}
				}
			}

			if(isset($search['central_provision_agent']) && $search['central_provision_agent']){
				$this->db->where('C.central_provision_agent', $search['central_provision_agent']);
			}
			if(isset($search['insurer']) && $search['insurer']){
				$this->db->where('C.insurer', $search['insurer']);
			}
			if(isset($search['claiming_amount']) && $search['claiming_amount']){
				$min_amount = $max_amount = '';
				if($search['claiming_amount']==1){
					$min_amount = '';
					$max_amount = 5000;
				} elseif($search['claiming_amount']==2){
					$min_amount = 5000;
					$max_amount = 10000;
				} elseif($search['claiming_amount']==3){
					$min_amount = 10000;
					$max_amount = 50000;
				} elseif($search['claiming_amount']==4){
					$min_amount = 50000;
					$max_amount = 100000;
				} elseif($search['claiming_amount']==5){
					$min_amount = 100000;
					$max_amount = '';
				}

				if($min_amount) {
					$this->db->where('C.sum_of_claiming >=', $min_amount);
				}
				if($max_amount) {
					$this->db->where('C.sum_of_claiming <=', $max_amount);
				}
			}
			if(isset($search['country_id']) && $search['country_id']){
				$this->db->where('C.country_id', $search['country_id']);
			}

			if(isset($search['date_of_accident_start']) && $search['date_of_accident_start']){
				$this->db->where('C.date_of_accident >=', strtotime(str_replace('/','-',$search['date_of_accident_start'])));
			}
			if(isset($search['date_of_accident_end']) && $search['date_of_accident_end']){
				$this->db->where('C.date_of_accident <=', strtotime(str_replace('/','-',$search['date_of_accident_end'])));
			}

			if(isset($search['date_of_agreement_start']) && $search['date_of_agreement_start']){
				$this->db->where('C.date_of_agreement >=', strtotime(str_replace('/','-',$search['date_of_agreement_start'])));
			}
			if(isset($search['date_of_agreement_end']) && $search['date_of_agreement_end']){
				$this->db->where('C.date_of_agreement <=', strtotime(str_replace('/','-',$search['date_of_agreement_end'])));
			}

			if(isset($search['date_of_getting_agreement_start']) && $search['date_of_getting_agreement_start']){
				$this->db->where('C.date_of_getting_agreement >=', strtotime(str_replace('/','-',$search['date_of_getting_agreement_start'])));
			}
			if(isset($search['date_of_getting_agreement_end']) && $search['date_of_getting_agreement_end']){
				$this->db->where('C.date_of_getting_agreement <=', strtotime(str_replace('/','-',$search['date_of_getting_agreement_end'])));
			}

			if(isset($search['postedtime_start']) && $search['postedtime_start']){
				$this->db->where('U.postedtime >=', strtotime(str_replace('/','-',$search['postedtime_start'])));
			}
			if(isset($search['postedtime_end']) && $search['postedtime_end']){
				$this->db->where('U.postedtime <=', strtotime(str_replace('/','-',$search['postedtime_end'])));
			}			
			
			if(isset($search['good_cases']) && $search['good_cases']){
				$this->db->where('C.good_cases', $search['good_cases']);
			}

			if(isset($search['MA_status']) && $search['MA_status']){
				$this->db->where('U.MA_status', $search['MA_status']);
			}
		}

		if(count($mlm_agent_id)>0)
		{
			$this->db->where_in('C.agent', $mlm_agent_id);
			$this->db->or_where_in('C.central_provision_agent', $mlm_agent_id);
		}
		
		$this->db->where(array('U.userType'=>4, 'U.status'=>'Y', 'U.CA_status'=>'Y'));
		$this->db->order_by('U.id','DESC');
		$query = $this->db->get();
		$return_data = $query->result();
		return $return_data;
	}


	public function getAllUsersId($user_id, $id_arr=array())
	{
		$id_arr[] = $user_id;
		$sub_user = $this->fetchAll(array('parent_id'=>$user_id));
		if(count($sub_user)>0)
		{
			foreach ($sub_user as $user) {
			 	$id_arr = $this->getAllUsersId($user->id, $id_arr);
			}
		}
		return $id_arr; 
	}

	public function getAgentInMLMStructure($user_id)
	{
		$sub_user = $this->fetchAll(array('parent_id'=>$user_id), array('name'=>'ASC'));

		foreach ($sub_user as $user) {
		 	$user->mlm_agent_data = $this->getAgentInMLMStructure($user->id);
		}
		return $sub_user; 
	}

	public function getClientDetailsByAgentMLMids($agent_id_arr)
	{
		$this->db->select("U.name, C.*");
		$this->db->from(TABLE_USER." AS U");
		$this->db->join(TABLE_CLIENTS." AS C","U.id=C.user_id");
					
		if(count($agent_id_arr)>0){
			$this->db->where_in('C.agent', $agent_id_arr);
			$this->db->or_where_in('C.central_provision_agent', $agent_id_arr);
		}
		$this->db->where(array('U.userType'=>4, 'U.status'=>'Y', 'U.CA_status'=>'Y'));
		$this->db->order_by('U.id','DESC');
		$query = $this->db->get();
		$return_data = $query->result();
		return $return_data;
	}


	public function insertAgentlProvision($user_id=0, $time=0, $provition_type=0, $insert_data=array(), $total_provition=0)
	{		
	
	
		$insert_data['agent_id'] = $user_id;
		$provision = $this->setTable(TABLE_AGENT_PROVISION)->fetchOne(array('user_id'=>$user_id, 'provision_start <='=>$time, 'provision_end >='=>$time, 'type'=>$provition_type));
		$this->unsetTable();

		$agentDetails = $this->userdata->setTable(TABLE_AGENTS)->fetchOne(array('user_id'=>$user_id));
		$this->userdata->unsetTable();

		if($provision->provision_percent)
		{
			$porper_provision_percent = $provision->provision_percent - $total_provition;
			
			if($porper_provision_percent > 0)
			{
				
				
				$insert_data['agent_provision_percent'] = $provision->provision_percent;
				$insert_data['porper_provision_percent'] = $porper_provision_percent;
				
				$porper_provision_value = ($insert_data['provision_gross'] * $porper_provision_percent)/100;

				if($agentDetails->company=='Y')
				{
					if($agentDetails->vat_payer=='Y'){
						$porper_provision_value = $porper_provision_value;
					} else{
						$porper_provision_value = $porper_provision_value/1.23;
					}
				} 
				else
				{
					$porper_provision_value = $porper_provision_value/1.23;
				}
				
				$insert_data['porper_provision_value'] = $porper_provision_value;
				
				if($total_provition > 0)
				{
					$insert_data['formula'] = $provision->provision_percent.'% - '.$total_provition.'%';
					$insert_data['provision_type'] = $provision->type;
				}
				else
				{
					if($provition_type==1) {
						$insert_data['formula'] = 'General Provision';
						$insert_data['provision_type'] = 1;
					} elseif($provition_type==2) {
						$insert_data['formula'] = 'Court Provision';
						$insert_data['provision_type'] = 2;
					}
				}

			}
			else
			{
				$upper_provision = $this->setTable(TABLE_AGENT_PROVISION)->fetchOne(array('user_id'=>$user_id, 'provision_start <='=>$time, 'provision_end >='=>$time, 'type'=>4));
				$this->unsetTable();
				
				

	

				$insert_data['porper_provision_percent'] = $upper_provision->provision_percent;
				$insert_data['porper_provision_value'] = ($insert_data['provision_gross'] * $upper_provision->provision_percent)/100;
				$insert_data['formula'] = 'Upper Provision';
				$insert_data['provision_type'] = 4;

			}
			$total_provition =  $total_provition + $insert_data['porper_provision_percent'];

			$this->setTable(TABLE_CLIENT_COMMENT_PROVISION)->insert($insert_data);
			$this->unsetTable();

			//$user = $this->fetchOne(array('id'=>$user_id));
			$user = $this->setTable(TABLE_USER)->fetchOne(array('id'=>$user_id));
			
		
			if($user->parent_id > 0)
			{
				$this->insertAgentlProvision($user->parent_id, $time, $provition_type, $insert_data, $total_provition);
			}
			
				
		}

	}

	public function getAgentProvitionSum($comment_id=0)
	{
		if($comment_id > 0) {
			$commentProvition = $this->setTable(TABLE_CLIENT_COMMENT_PROVISION)->countRows(array('comment_id'=>$comment_id, 'is_active'=>'Y'));
			$this->unsetTable();
			if($commentProvition > 0)
			{
				$this->db->select_sum('porper_provision_value'); 
		        $this->db->where(array('comment_id'=>$comment_id, 'is_active'=>'Y'));
		        $query=$this->db->get(TABLE_CLIENT_COMMENT_PROVISION)->row();
		        return $query->porper_provision_value;
			}
			else
			{
				return 0; 
			}
		} else {
			return 0; 
		}
	}


	public function getTotalSettleValue($agent_bonus_id=0)
	{
		if($agent_bonus_id > 0) {
			$settleValue = $this->setTable(TABLE_CLIENT_AGENT_BONUS_SETTLE)->countRows(array('agent_bonus_id'=>$agent_bonus_id));
			$this->unsetTable();
			if($settleValue > 0)
			{
				$this->db->select_sum('settle_value'); 
		        $this->db->where(array('agent_bonus_id'=>$agent_bonus_id));
		        $query=$this->db->get(TABLE_CLIENT_AGENT_BONUS_SETTLE)->row();
		        return $query->settle_value;
			}
			else
			{
				return 0; 
			}
		} else {
			return 0; 
		}
	}

	public function getCommentDocumentIdByClientId($client_id)
	{
		$comment_ids = array(0);
		$this->db->select('id');
		$this->db->where(array('to_user_id'=>$client_id));
		$this->db->or_where(array('from_user_id'=>$client_id));
		$result_comment = $this->db->get(TABLE_CLIENT_COMMENT)->result();
		if(count($result_comment))
		{
			foreach ($result_comment as $comment) {
				$comment_ids[] = $comment->id;
			}
		}

		$document_ids = array(0);
		$this->db->select('id');
		$this->db->where(array('source_table'=>1));
		if(count($comment_ids)>0)
		{
			$this->db->where_in('source_id', $comment_ids);
		}
		$result_document = $this->db->get(TABLE_COMMENT_DOCUMENT)->result();
		if(count($result_document))
		{
			foreach ($result_document as $document) {
				$document_ids[] = $document->id;
			}
		}
		return $document_ids;
	}

	public function getClientDocumentIdByClientId($user_id)
	{
		$document_ids = array(0);
		$this->db->select('id');
		$this->db->where(array('source_table'=>2, 'source_id'=>$user_id));
		$result_document = $this->db->get(TABLE_COMMENT_DOCUMENT)->result();
		if(count($result_document))
		{
			foreach ($result_document as $document) {
				$document_ids[] = $document->id;
			}
		}
		return $document_ids;
	}

	public function getClientDocByFolderId($id, $folder_id)
	{
		$doc_1 = $this->getCommentDocumentIdByClientId($id);
		$doc_2 = $this->getClientDocumentIdByClientId($id);
		$document_ids = array_merge($doc_1,$doc_2);
		
		$this->db->where(array('folder_id'=>$folder_id, 'status'=>'Y'));
		if(count($document_ids)>0)
		{
			$this->db->where_in('id', $document_ids);
		}
		$this->db->order_by('id', 'DESC');
		return $result = $this->db->get(TABLE_COMMENT_DOCUMENT)->result();
	}

	public function getClientDocByFolderIdInAgentCRM($id, $folder_id, $shareCond=array())
	{
		$doc_1 = $this->getCommentDocumentIdByClientId($id);
		$doc_2 = $this->getClientDocumentIdByClientId($id);
		$document_ids = array_merge($doc_1,$doc_2);

		if(count($shareCond)>0)
		{
			$this->db->where($shareCond);
		}
		
		$this->db->where(array('folder_id'=>$folder_id, 'status'=>'Y'));
		if(count($document_ids)>0)
		{
			$this->db->where_in('id', $document_ids);
		}
		$this->db->order_by('id', 'DESC');
		return $result = $this->db->get(TABLE_COMMENT_DOCUMENT)->result();
	}

	public function getMLMAgentList($mlm_agent_id)
	{
		if(count($mlm_agent_id)>0)
		{
			$this->db->where_in('id', $mlm_agent_id);
		}
		$this->db->where(array('userType'=>2, 'status'=>'Y'));
		$this->db->order_by('name', 'ASC');
		$record = $this->db->get(TABLE_USER)->result();
		return $record;
	}

	public function countClientByStatusAndMLM($mlm_agent_id, $status_of_case)
	{
		$this->db->where(array('status_of_case'=>$status_of_case));
		$this->db->where_in('agent', $mlm_agent_id);
		$record = $this->db->count_all_results(TABLE_CLIENTS);
		return $record;
	}


	public function getTargetProcess($cond=array(), $type_of_accident=array(), $agentMlmIds=array())
	{
		$data = array();

		/*====1ST QUERY====*/ 
		if(count($cond)>0){
			$this->db->where($cond);
		}
		if(count($type_of_accident)>0){
			$this->db->where_in('type_of_accident', $type_of_accident);
		}
		if(count($agentMlmIds)>0){
			$this->db->where_in('agent', $agentMlmIds);
		}
		$query1 = $this->db->get(TABLE_CLIENTS);
		$data['no_of_case'] = $query1->num_rows();

		/*====2ND QUERY====*/ 
		$this->db->select_sum('expect_compensation');
		if(count($cond)>0){
			$this->db->where($cond);
		}
		if(count($type_of_accident)>0){
			$this->db->where_in('type_of_accident', $type_of_accident);
		}
		if(count($agentMlmIds)>0){
			$this->db->where_in('agent', $agentMlmIds);
		}
		$query2 = $this->db->get(TABLE_CLIENTS);
		$data['sum'] = $query2->row()->expect_compensation;
		
		return $data;
	}


	public function getTrainingDocByDocIds($document_ids)
	{
		$this->db->where_in('id', $document_ids);
		$query = $this->db->get(TABLE_TRAINING_DOCUMENTS);
		return $query->result();
	}

	public function getTrainingFolderByFolderIds($folder_ids)
	{
		$this->db->where(array('status'=>'Y'));
		$this->db->where_in('id', $folder_ids);
		$query = $this->db->get(TABLE_TRAINING_FOLDER);
		return $query->result();
	}

	public function getFormulaDocByDocIds($document_ids)
	{
		$this->db->where_in('id', $document_ids);
		$query = $this->db->get(TABLE_FORMULAS_DOCUMENTS);
		return $query->result();
	}
	
	public function getFormulaFolderByFolderIds($folder_ids)
	{
		$this->db->where(array('status'=>'Y'));
		$this->db->where_in('id', $folder_ids);
		$query = $this->db->get(TABLE_FORMULAS_FOLDER);
		return $query->result();
	}

	public function getAllTasks($crmType="", $search=array(), $cond=array())
	{
		if(count($search)>0)
		{
			if(isset($search['user_type']) && count($search['user_type'])>0)
			{
				if(in_array('me', $search['user_type']))
				{
					$user_id = $this->session->userdata('usrid');
					$this->db->where(array('assigned_user'=>$user_id));
				}
				else
				{
					$this->db->where_in('assigned_user_type', $search['user_type']);
				}
			}

		}

		if(count($cond)>0)
		{
			$this->db->where($cond);
		}

		$query = $this->db->get(TABLE_CLIENT_TASKS);
		$result = $query->result();		

		$data = array();
		foreach ($result as $key => $value) {
			$data[$key]['title'] = $value->subject;
			$data[$key]['start'] = $value->deadline*1000+(24*60*60*1000);
			$data[$key]['className'] = 'bg-success ';
			$data[$key]['client'] = $this->defaultdata->getField(TABLE_USER, array('id'=>$value->client_id), 'name');
			if($crmType)
			{
				$data[$key]['url'] = base_url($crmType.'/client/task/'.$value->client_id);
			}
		}


		return $data;
	}

	public function getAllcalTasks($crmType="", $search=array(), $cond=array())
	{
		if(count($search)>0)
		{
			if(isset($search['user_type']) && count($search['user_type'])>0)
			{
				if(in_array('me', $search['user_type']))
				{
					$user_id = $this->session->userdata('usrid');
					$this->db->where(array('assigned_user'=>$user_id));
				}
				else
				{
					$this->db->where_in('assigned_user_type', $search['user_type']);
				}
			}

		}

		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
        $this->db->where('status','P');
		$query = $this->db->get(TABLE_CLIENT_TASKS);
		$result = $query->result();		

		$data = array();
		foreach ($result as $key => $value) {
			$data[$key]['title'] = $value->subject;
			$data[$key]['start'] = $value->deadline*1000+(24*60*60*1000);
			$data[$key]['className'] = 'bg-success ';
			$data[$key]['client'] = $this->defaultdata->getField(TABLE_USER, array('id'=>$value->client_id), 'name');
			if($crmType)
			{
				$data[$key]['url'] = base_url($crmType.'/client/task/'.$value->client_id);
			}
		}


		return $data;
	}

	

	public function getAllCalendarEvent($user_id='')
	{
		$auth_user = $this->session->userdata('usrid');

		if($user_id)
		{	
			$this->db->where(array('user_id'=>$user_id));
		}	
		$query = $this->db->get(TABLE_CALENDAR_EVENT);
		$result = $query->result();
		$data = array();
		foreach ($result as $key => $value) {
			$data[$key]['title'] = $value->title;
			$data[$key]['start'] = $value->starttime*1000;
			$data[$key]['end'] = $value->endtime*1000;
			if($auth_user == $value->user_id)
			{
				$data[$key]['className'] = $value->className.' EditDeleteable';
			}
			else
			{
				$data[$key]['className'] = $value->className;
			}

			$data[$key]['id'] = $value->id;
		}
		return $data;
	}

	public function getToDoList($cond=array())
	{
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}	
		$query = $this->db->get(TABLE_TO_DO_LISTS);
		$result = $query->result();
		$data = array();
		foreach ($result as $key => $value) {
			$data[$key]['id'] = $value->id;
			$data[$key]['text'] = $value->task;
			$data[$key]['done'] = $value->status=='Y'?true:false;
		}
		return $data;
	}

	public function getTaskDataByUserIds($user_ids=array(), $cond=array(), $order=array())
	{
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
		if(count($user_ids)>0)
		{
			$this->db->where_in('assigned_user', $user_ids);
		}
		if(count($order)>0)
		{
			foreach ($order as $key => $value) 
			{
				$this->db->order_by($key, $value);
			}
		}
		$query = $this->db->get(TABLE_CLIENT_TASKS);
		return $query->result();
	}

	public function getCalendarDataByUserIds($user_ids=array(), $cond=array(), $order=array())
	{
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
		if(count($user_ids)>0)
		{
			$this->db->where_in('user_id', $user_ids);
		}
		if(count($order)>0)
		{
			foreach ($order as $key => $value) 
			{
				$this->db->order_by($key, $value);
			}
		}
		$query = $this->db->get(TABLE_CALENDAR_EVENT);
		return $query->result();
	}

	public function getCommentDataByUserIds($user_ids=array(), $cond=array(), $order=array(), $limit=array())
	{
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
		if(count($user_ids)>0)
		{
			$this->db->where_in('to_user_id', $user_ids);
		}
		
		if(count($order)>0)
		{
			foreach ($order as $key => $value) 
			{
				$this->db->order_by($key, $value);
			}
		}
		if(count($limit) > 0)
		{
			$this->db->limit($limit['count'],$limit['start']);
		}
		$query = $this->db->get(TABLE_CLIENT_COMMENT);
		return $query->result();
	}

	public function getUserDataByUserIds($user_ids=array(), $cond=array(), $order=array())
	{
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
		if(count($user_ids)>0)
		{
			$this->db->where_in('id', $user_ids);
		}
		if(count($order)>0)
		{
			foreach ($order as $key => $value) 
			{
				$this->db->order_by($key, $value);
			}
		}
		$query = $this->db->get(TABLE_USER);
		return $query->result();
	}

	public function getCommentDataByCommentIds($comment_ids=array(), $cond=array(), $order=array(), $limit=array())
	{
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
		if(count($comment_ids)>0)
		{
			$this->db->where_in('id', $comment_ids);
		}
		if(count($order)>0)
		{
			foreach ($order as $key => $value) 
			{
				$this->db->order_by($key, $value);
			}
		}
		if(count($limit) > 0)
		{
			$this->db->limit($limit['count'],$limit['start']);
		}
		$query = $this->db->get(TABLE_CLIENT_COMMENT);
		return $query->result();
	}

	public function getSeanchUserByUserType($search=array())
	{
		if(isset($search) && count($search)>0) {
			$this->db->where_in('userType', $search['userType']);

			if(!empty($search['logintime_start'])){
				$cond['logintime >='] = strtotime(str_replace('/','-',$search['logintime_start']));
			}

			if(!empty($search['logintime_end'])){
				$cond['logintime <='] = strtotime(str_replace('/','-',$search['logintime_end']));
			}

			if(!empty($search['logintime_start']) && !empty($search['logintime_end'])){
				$search['logintime_start'] = strtotime(str_replace('/','-',$search['logintime_start']));
				$search['logintime_end'] = strtotime(str_replace('/','-',$search['logintime_end']));
				$cond = "logintime BETWEEN ".$search['logintime_start']."  AND ".$search['logintime_end'];
			}

			$this->db->where($cond);

		} else {
			$this->db->where(array('userType !='=>4));
		}
		$this->db->order_by('id', 'DESC');

		$query = $this->db->get(TABLE_USERLOGIN_RECORD);
		return $query->result();
	}

	public function getAllUserByUserType($search=array())
	{
		if(isset($search) && count($search)>0) {
			$this->db->where_in('userType', $search['userType']);
		} else {
			$this->db->where(array('userType !='=>4));
		}
		$this->db->order_by('id', 'DESC');
		$query = $this->db->get(TABLE_USER);
		return $query->result();
	}

	public function getCommentDataByFromUserIds($user_ids=array(), $cond=array(), $order=array(), $limit=array())
	{
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
		if(count($user_ids)>0)
		{
			$this->db->where_in('from_user_id', $user_ids);
		}
		if(count($order)>0)
		{
			foreach ($order as $key => $value) 
			{
				$this->db->order_by($key, $value);
			}
		}
		if(count($limit) > 0)
		{
			$this->db->limit($limit['count'],$limit['start']);
		}
		$query = $this->db->get(TABLE_CLIENT_COMMENT);
		return $query->result();
	}

	public function getClientHistoryByClientId($client_id, $cond=array())
	{		
		$this->db->group_start();
		$this->db->where(array('client_id'=>$client_id));		
		$this->db->or_where('client_id', 0);
		$this->db->group_end();
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}

		$this->db->order_by('id','DESC');		
		$query = $this->db->get(TABLE_CLIENT_HISTORY);
		return $query->result();
	}
	
	public function getClientWorkingAndBillingTimeByClientId($client_id, $cond=array())
	{		
		$this->db->group_start();
		$this->db->where(array('client_id'=>$client_id));		
		$this->db->or_where('client_id', 0);
		$this->db->group_end();
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}

		$this->db->order_by('action_id','DESC');		
		$query = $this->db->get(TABLE_CLIENT_WORKING_AND_BILLING_TIME);

		return $query->result();
	}

	public function getUserClientInGeneralSearch($search=array(), $cond=array())
	{

		$this->db->from(TABLE_USER." AS U");
		$this->db->join(TABLE_CLIENTS." AS C","U.id=C.user_id");
		
		if(count($search)>0)
		{
			if(isset($search['value']) && $search['value']){
				$this->db->group_start();
				$this->db->like('U.name', $search['value']);
				$this->db->or_like('U.emailAddress', $search['value']);
				$this->db->or_like('U.phone', $search['value']);
				$this->db->or_like('C.insurer_case_no', $search['value']);
				$this->db->group_end();
			}
		}
		
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
		
		$this->db->where(array('U.userType'=>4, 'U.status'=>'Y'));
		$this->db->order_by('U.id','DESC');
		$query = $this->db->get();
		$return_data = $query->result();
		return $return_data;
	}

	public function getAgentClientInGeneralSearch($search=array(), $mlm_agent_id=array())
	{

		$this->db->from(TABLE_USER." AS U");
		$this->db->join(TABLE_CLIENTS." AS C","U.id=C.user_id");
		
		if(count($search)>0)
		{
			if(isset($search['value']) && $search['value']){
				$this->db->group_start();
				$this->db->like('U.name', $search['value']);
				$this->db->or_like('U.emailAddress', $search['value']);
				$this->db->or_like('U.phone', $search['value']);
				$this->db->or_like('C.insurer_case_no', $search['value']);
				$this->db->group_end();
			}
		}
		
		if(count($mlm_agent_id)>0)
		{
			$this->db->where_in('C.agent', $mlm_agent_id);
		}
		
		$this->db->where(array('U.userType'=>4, 'U.status'=>'Y'));
		$this->db->order_by('U.id','DESC');
		$query = $this->db->get();
		$return_data = $query->result();
		return $return_data;
	}

	public function getCardProgramByAgentIds($mlm_agent_id=array())
	{
		if(count($mlm_agent_id)>0)
		{
			$this->db->where_in('agent_id', $mlm_agent_id);
		}
		$this->db->order_by('id', 'DESC');
		$query = $this->db->get(TABLE_CARD_PROGRAM);
		return $query->result();	
	}

	public function getCardProgramByCardNumber($card_number='', $agent_id='')
	{
		if($agent_id)
		{
			$this->db->where('agent_id', $agent_id);
		}

		if($card_number)
		{
			$this->db->like('card_number', $card_number);
		}
		$this->db->limit(20, 0);
		$query = $this->db->get(TABLE_CARD_PROGRAM);
		return $query->result();
	}

	public function getReportSearchByClientIds($table, $where=array(), $order=array(), $client_id=array())
	{
		if(count($where)>0)
		{
			$this->db->where($where);
		}
		if(count($client_id)>0)
		{
			$this->db->where_in('client_id', $client_id);
		}
		if(count($order)>0)
		{
			foreach($order as $key=>$val){
				$this->db->order_by($key,$val);
			}
			
		}
		
		return $this->db->get($table)->result();
	}

	public function getClientPaymentSearchByClientIds($client_id=array())
	{
		$this->db->where(array('comment_type_id'=>4));
		if(count($client_id)>0)
		{
			$this->db->where_in('to_user_id', $client_id);
		}
		$this->db->order_by('id','DESC');
		return $this->db->get(TABLE_CLIENT_COMMENT)->result();
	}
	

	public function getAllParentUsersIds($user_id, $id_arr=array())
	{
		$id_arr[] = $user_id;
		$userDetails = $this->fetchOne(array('id'=>$user_id));
		if(count($userDetails) > 0)
		{	
			$parent_id = $userDetails->parent_id;
			if($parent_id > 0)
			{
				$id_arr = $this->getAllParentUsersIds($parent_id, $id_arr);
			}
		}
		return $id_arr; 
	}

	public function getCommentNotiUserIds($client_id, $share_solcitor, $share_agent, $share_agent_manager,$emailto,$emailcc,$emailbcc,$comment_id)
	{ 
                                      
		$auth_user = $this->session->userdata('usrid');
		$clientDetails = $this->db->where(array('user_id'=>$client_id))->get(TABLE_CLIENTS)->row();
		$user_ids = array();			

		if($clientDetails->consultant)
		{
			$user_ids[] = $clientDetails->consultant;
		}
		else
		{
			$user15 = $this->db->select('id')->where_in('userType', array(1,5))->get(TABLE_USER)->result();
			foreach ($user15 as $key => $val) 
			{
				$user_ids[] = $val->id;
			}
		}
		
		if($share_solcitor=='Y')
		{
			if($clientDetails->solicitor)
			{
				$user_ids[] = $clientDetails->solicitor;
			}
			if($clientDetails->solicitor_employee)
			{
				$user_ids[] = $clientDetails->solicitor_employee;
			}
		}

		
		if($share_agent=='Y')
		{
			if($clientDetails->agent)
			{
				$user_ids[] = $clientDetails->agent;
			}
			if($clientDetails->central_provision_agent)
			{
				$user_ids[] = $clientDetails->central_provision_agent;
			}
		}
		
		if($share_agent_manager=='Y')
		{
			if($clientDetails->agent)
			{	
				$agent_ids = $this->getAllParentUsersIds($clientDetails->agent);
				if(count($agent_ids) > 0)
				{
					foreach ($agent_ids as $key => $value) 
					{
						$user_ids[] = $value;
					}
				}

				if(($key = array_search($clientDetails->agent, $user_ids)) !== false) 
				{
				    unset($user_ids[$key]); // for remove client agent
				}
			}		

		}
       
		if(count($emailto)>0){
			//print_r($emailto);
			foreach($emailto as $list){
				//echo $list;
				$userDetails = $this->setTable(TABLE_USER)->fetchOne(array('emailAddress'=>$list));
				$comment_data=array();
				if($userDetails->userType !='0')
				{
					
					if($userDetails->userType =='4')
					{
						/*if($clientDetails->central_provision_agent){
							
							$comment_data['share_agent'] = 'Y';
							$user_ids[] = $clientDetails->central_provision_agent;
							
						}
						if($clientDetails->agent){
							
							$comment_data['share_agent'] = 'Y';
							$user_ids[] = $clientDetails->agent;
							
						}*/
						
					}
					if($userDetails->userType =='2')
					{ 
						if($clientDetails->central_provision_agent){
							
							$comment_data['share_agent_manager'] = 'Y';
                            $this->share_agent_manager = "Y";                                 
							$user_ids[] = $clientDetails->central_provision_agent;
							
						}
						if($clientDetails->agent){
							$user_ids[] = $clientDetails->agent;
							$comment_data['share_agent'] = 'Y';
							$this->share_agent = "Y";
							
						}
						
					}
					if($userDetails->userType =='3' && $userDetails->parent_id=='0')
					{
						if($clientDetails->solicitor)
						{
							$user_ids[] = $clientDetails->solicitor;
							$comment_data['share_solcitor'] = 'Y';
                            $this->share_solcitor = "Y";      
							
							
						}
					}
					
					if($userDetails->userType =='3' && $userDetails->parent_id!='0' )
					{
						if($clientDetails->solicitor)
						{
							$user_ids[] = $clientDetails->solicitor;
							$comment_data['share_solcitor'] = 'Y';
							$this->share_solcitor = "Y";
							
						}
						if($clientDetails->solicitor_employee)
						{
							$user_ids[] = $clientDetails->solicitor_employee;
							$comment_data['share_solcitor'] = 'Y';
							$this->share_solcitor = "Y";
							
						}
						
					}
					
					if($userDetails->userType =='1')
					{ 
						$user19 = $this->db->select('id')->where('userType', 1)->get(TABLE_USER)->result();
						
						foreach ($user19 as $key => $val) 
						{
							$user_ids[] = $val->id;
						}
						
					}
					//echo $list;
					if(!empty($comment_data)){
					$this->db->where('id',$comment_id);
					$this->db->update('com_client_comment',$comment_data);
					}
					
				}
			}
			
		}
		$user_ids = array_unique($user_ids);
	 // print_r($user_ids);exit;
		if(($key = array_search(0, $user_ids)) !== false) 
		{
		    unset($user_ids[$key]); // for escape inserting junk data
		}
		if(($key = array_search($auth_user, $user_ids)) !== false) 
		{
		    unset($user_ids[$key]); // for not show own comment notification
		}

		return $user_ids;

	}

	public function getCommentNotiUserIds2($client_id, $share_solcitor, $share_agent, $share_agent_manager,$emailto,$emailcc,$emailbcc,$comment_id,$Email_to)
	{ 
		$auth_user = $this->session->userdata('usrid');
		$clientDetails = $this->db->where(array('user_id'=>$client_id))->get(TABLE_CLIENTS)->row();
		$user_ids = array();
		if($clientDetails->consultant)
		{
			$user_ids[] = $clientDetails->consultant;
		}
		else
		{
			$user15 = $this->db->select('id')->where_in('userType', array(1,5))->get(TABLE_USER)->result();
			foreach ($user15 as $key => $val) 
			{
				$user_ids[] = $val->id;
			}
		}

		/*if($share_solcitor=='Y')
		{
			if($clientDetails->solicitor)
			{
				$user_ids[] = $clientDetails->solicitor;
			}
			if($clientDetails->solicitor_employee)
			{
				$user_ids[] = $clientDetails->solicitor_employee;
			}
		}

		
		if($share_agent=='Y')
		{
			if($clientDetails->agent)
			{
				$user_ids[] = $clientDetails->agent;
			}
			if($clientDetails->central_provision_agent)
			{
				$user_ids[] = $clientDetails->central_provision_agent;
			}
			
		}

		if($share_agent_manager=='Y')
		{
			if($clientDetails->agent)
			{	
				$agent_ids = $this->setTable(TABLE_USER)->getAllParentUsersIds($clientDetails->agent);
				if(count($agent_ids) > 0)
				{
					foreach ($agent_ids as $key => $value) 
					{
						$user_ids[] = $value;
					}
				}

				if(($key = array_search($clientDetails->agent, $user_ids)) !== false) 
				{
				    unset($user_ids[$key]); // for remove client agent
				}
			}		

		}*/
		$userDetails1 = $this->setTable(TABLE_USER)->fetchOne(array('emailAddress'=>$Email_to));

		if(count($emailto)>0){
			//print_r($emailto);
			foreach($emailto as $list){
				//echo $list;
				$userDetails = $this->setTable(TABLE_USER)->fetchOne(array('emailAddress'=>$list));
				$comment_data=array();
				if($userDetails->userType !='0')
				{
					
					if($userDetails->userType =='4' && $userDetails1->userType =='2')
					{
						if($clientDetails->central_provision_agent){
							
							$comment_data['share_agent_manager'] = 'Y';
							$user_ids[] = $clientDetails->central_provision_agent;
							
						}
						if($clientDetails->agent){
							
							$comment_data['share_agent'] = 'Y';
							$user_ids[] = $clientDetails->agent;
							
						}
						
					}
					if($userDetails->userType =='4' && $userDetails1->userType =='3')
					{
						
						if($clientDetails->solicitor)
						{
							$comment_data['share_solcitor'] = 'Y';
							$user_ids[] = $clientDetails->solicitor;
							
						}
						
					}
					if($userDetails->userType =='3' && $userDetails->parent_id=='0' && $userDetails1->userType =='2')
					{
						if($clientDetails->central_provision_agent){
							
							$comment_data['share_agent_manager'] = 'Y';
							$user_ids[] = $clientDetails->central_provision_agent;
							
						}
						if($clientDetails->agent){
							$user_ids[] = $clientDetails->agent;
							$comment_data['share_agent'] = 'Y';
							
							
						}
						if($clientDetails->solicitor)
						{
							$user_ids[] = $clientDetails->solicitor;
							$comment_data['share_solcitor'] = 'Y';
							
							
						}
						
					}
					if($userDetails->userType =='3' && $userDetails->parent_id=='0' && $userDetails1->userType =='3')
					{
						if($clientDetails->solicitor)
						{
							$user_ids[] = $clientDetails->solicitor;
							$comment_data['share_solcitor'] = 'Y';
							
							
						}
					}
					if($userDetails->userType =='2' && $userDetails1->userType =='3'  && $userDetails1->parent_id=='0')
					{
						if($clientDetails->central_provision_agent){
							
							$comment_data['share_agent_manager'] = 'Y';
							$user_ids[] = $clientDetails->central_provision_agent;
							
						}
						if($clientDetails->agent){
							$user_ids[] = $clientDetails->agent;
							$comment_data['share_agent'] = 'Y';
							
							
						}
						if($clientDetails->solicitor)
						{
							$user_ids[] = $clientDetails->solicitor;
							$comment_data['share_solcitor'] = 'Y';
							
							
						}
					}
					if($userDetails->userType =='2' && $userDetails1->userType =='3'  && $userDetails1->parent_id!='0')
					{
						if($clientDetails->central_provision_agent){
							
							$comment_data['share_agent_manager'] = 'Y';
							$user_ids[] = $clientDetails->central_provision_agent;
							
						}
						if($clientDetails->agent){
							$user_ids[] = $clientDetails->agent;
							$comment_data['share_agent'] = 'Y';
							
							
						}
						if($clientDetails->solicitor)
						{
							$user_ids[] = $clientDetails->solicitor;
							$comment_data['share_solcitor'] = 'Y';
							
							
						}
						if($clientDetails->solicitor_employee)
						{
							$user_ids[] = $clientDetails->solicitor_employee;
							$comment_data['share_solcitor'] = 'Y';
							
							
						}
					}
					if($userDetails->userType =='2' && $userDetails1->userType =='2')
					{
						if($clientDetails->central_provision_agent){
							
							$comment_data['share_agent_manager'] = 'Y';
							$user_ids[] = $clientDetails->central_provision_agent;
							
						}
						if($clientDetails->agent){
							$user_ids[] = $clientDetails->agent;
							$comment_data['share_agent'] = 'Y';
							
							
						}

					}
					if($userDetails->userType =='3' && $userDetails->parent_id!='0' && $userDetails1->userType =='2')
					{
						if($clientDetails->central_provision_agent){
							
							$comment_data['share_agent_manager'] = 'Y';
							$user_ids[] = $clientDetails->central_provision_agent;
							
						}
						if($clientDetails->agent){
							$user_ids[] = $clientDetails->agent;
							$comment_data['share_agent'] = 'Y';
							
							
						}
						if($clientDetails->solicitor)
						{
							$user_ids[] = $clientDetails->solicitor;
							$comment_data['share_solcitor'] = 'Y';
							
							
						}
						if($clientDetails->solicitor_employee)
						{
							$user_ids[] = $clientDetails->solicitor_employee;
							$comment_data['share_solcitor'] = 'Y';
							
							
						}
						
					}
					if($userDetails->userType =='3' && $userDetails->parent_id!='0' && $userDetails1->userType =='3')
					{
						if($clientDetails->solicitor)
						{
							$user_ids[] = $clientDetails->solicitor;
							$comment_data['share_solcitor'] = 'Y';
							
							
						}
						if($clientDetails->solicitor_employee)
						{
							$user_ids[] = $clientDetails->solicitor_employee;
							$comment_data['share_solcitor'] = 'Y';
							
							
						}
						
						
					}
					if($userDetails->userType =='4' && $userDetails1->userType =='1')
					{
						
						$user20 = $this->db->select('id')->where_in('userType', array(1))->get(TABLE_USER)->result();
						foreach ($user20 as $key => $val) 
						{
							$user_ids[] = $val->id;
						}
						
					}
					if($userDetails->userType =='2' && $userDetails1->userType =='1')
					{
						
						$user21 = $this->db->select('id')->where_in('userType', array(1))->get(TABLE_USER)->result();
						foreach ($user21 as $key => $val) 
						{
							$user_ids[] = $val->id;
						}
						if($clientDetails->central_provision_agent){
							
							$comment_data['share_agent_manager'] = 'Y';
							$user_ids[] = $clientDetails->central_provision_agent;
							
						}
						if($clientDetails->agent){
							$user_ids[] = $clientDetails->agent;
							$comment_data['share_agent'] = 'Y';
							
							
						}
						
					}
					if($userDetails->userType =='3' && $userDetails1->userType =='1')
					{
						
						$user22 = $this->db->select('id')->where_in('userType', array(1))->get(TABLE_USER)->result();
						foreach ($user22 as $key => $val) 
						{
							$user_ids[] = $val->id;
						}
						if($clientDetails->solicitor)
						{
							$user_ids[] = $clientDetails->solicitor;
							$comment_data['share_solcitor'] = 'Y';
							
							
						}
						if($userDetails->parent_id !='0'){
							if($clientDetails->solicitor_employee)
							{
								$user_ids[] = $clientDetails->solicitor_employee;
								$comment_data['share_solcitor'] = 'Y';
								
								
							}
						}
						
					}
					if($userDetails->userType =='5' && $userDetails1->userType =='1')
					{
						
						$user23 = $this->db->select('id')->where_in('userType', array(1))->get(TABLE_USER)->result();
						foreach ($user23 as $key => $val) 
						{
							$user_ids[] = $val->id;
						}
						
					}
					if($userDetails->userType =='5' && $userDetails1->userType =='2' || $userDetails->userType =='5' && $userDetails1->userType =='3' || $userDetails->userType =='3' && $userDetails1->userType =='3')
                    {

//                        $user24 = $this->db->select('id')->where_in('userType', array(1))->get(TABLE_USER)->result();
//                        foreach ($user23 as $key => $val)
//                        {
                            $user_ids[] = $userDetails1->id;
//                        }

                    }																																																   
					//echo $list;
					if(!empty($comment_data)){
					$this->db->where('id',$comment_id);
					$this->db->update('com_client_comment',$comment_data);
					}
					
				}
			}
			
		}
		$user_ids = array_unique($user_ids);
	  
		if(($key = array_search(0, $user_ids)) !== false) 
		{
		    unset($user_ids[$key]); // for escape inserting junk data
		}
		if(($key = array_search($auth_user, $user_ids)) !== false) 
		{
		    unset($user_ids[$key]); // for not show own comment notification
		}
		
		return $user_ids;

	}

	public function getTaskNotiUserIds($assigned_user, $assigned_user_type)
	{
		$user_ids = array();
		$user_ids[] = $assigned_user;
		$user1 = $this->db->select('id')->where_in('userType', array(1))->get(TABLE_USER)->result();
		foreach ($user1 as $key => $val) 
		{
			// 11.06.2018 admin not notified other user task
			//$user_ids[] = $val->id;
		}

		if($assigned_user > 0 && ($assigned_user_type=='S' || $assigned_user_type=='SE'))
		{
			$solicitor_ids = $this->getAllParentUsersIds($assigned_user);
			if(count($solicitor_ids) > 0)
			{
				foreach ($solicitor_ids as $key => $value) 
				{
					$user_ids[] = $value;
				}
			}
		}

		$user_ids = array_unique($user_ids);

		if(($key = array_search(0, $user_ids)) !== false) 
		{
		    unset($user_ids[$key]); // for escape inserting junk data
		}
		return $user_ids;

	}

	public function getUserLoginRecords($user_ids=array(), $cond=array(), $order=array(), $limit=array())
	{
		$this->db->select('*');
		$this->db->from(TABLE_USER." AS U");
		$this->db->join(TABLE_USERLOGIN_RECORD." AS ULR","U.id=ULR.user_id");
		
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
		if(count($user_ids)>0)
		{
			$this->db->where_in('ULR.user_id', $user_ids);
		}
		if(count($order)>0)
		{
			foreach ($order as $key => $value) 
			{
				$this->db->order_by($key, $value);
			}
		}
		if(count($limit) > 0)
		{
			$this->db->limit($limit['count'],$limit['start']);
		}
		
		$query = $this->db->get();
		$return_data = $query->result();
		return $return_data;
	}


	public function getConnectedUserEmails($client_id="")
	{
		$connected_ids = array();	
		$usertype = $this->session->userdata('usrtype');
		$parentID = $this->session->userdata('parentID');	
		if($usertype =='1' || $usertype=='2' || $usertype=='3' || $usertype=='5'){
			$connected_ids[] =  1;
		}
		if($usertype =='1' || $usertype=='2' || $usertype=='5'){
			$user_data = $this->db->where(array('id'=>$client_id))->get(TABLE_USER)->row();
			if(!empty($user_data) && $user_data->emailAddress)
			{
				$connected_ids[] =  $user_data->id; 
			}
		}

		$client_data = $this->db->where(array('user_id'=>$client_id))->get(TABLE_CLIENTS)->row();
		

		if(!empty($client_data))
		{
			if($usertype=='1' || $usertype=='2' || $usertype=='3'){
				if($client_data->consultant) { 
					$connected_ids[] =  $client_data->consultant; 
				}
				
			}
			if($usertype=='1' || $usertype=='5'){			
				if($client_data->solicitor) { 
					$connected_ids[] =  $client_data->solicitor; 
				}
			}
			if($usertype=='3' && $parentID!='0'){			
				if($client_data->solicitor) { 
					$connected_ids[] =  $client_data->solicitor; 
				}
			}
			if($usertype=='1' || $usertype=='3' || $usertype=='5' &&  $parentID=='0'){
				if($client_data->solicitor_employee) { 
					$connected_ids[] =  $client_data->solicitor_employee; 
				}
			}
			if($usertype=='1'|| $usertype=='5' || $usertype=='5'){
				if($client_data->central_provision_agent) { 
					$connected_ids[] =  $client_data->central_provision_agent; 
				}
				
			}
			if($usertype=='1' || $usertype=='5'){
				if($client_data->agent) 
				{ 
					$connected_ids[] =  $client_data->agent;
					$mlm_agent_id = $this->getAllUsersId($client_data->agent);
					if(!empty($mlm_agent_id))
					{
						foreach ($mlm_agent_id as $agent_id) 
						{
							$connected_ids[] =  $agent_id;
						}
					}						 
				}
			} 
		}
		
								
		

		

		
		$connected_ids = array_unique($connected_ids);
		
		$user_result=array();
		$user_result1 = $this->db->select('(email) AS emailAddress,(Client_id) AS id')->where('client_id', $client_id)->group_by('email')->order_by('id','ASC')->get(TABLE_CLIENT_EMAIL)->result();
        
//		$r= $this->db->last_query();

		$user_result2 = $this->db->select('emailAddress, id')->from(TABLE_USER)->where_in('id', $connected_ids)->group_by('emailAddress')->order_by('emailAddress','ASC')->get()->result();
		//print_r($user_result2);
		$result = array_merge( $user_result1, $user_result2 );
		//duplicate objects will be removed
		$user_result = array_map("unserialize", array_unique(array_map("serialize", $result)));
		foreach ($user_result as $user) {
            switch ($user->userType) {
                case 1:
                    $user->typeNameUser = $this->defaultdata->gradLanguageText(289);
                    break;
                case 2:
                    // агент
                    $user->typeNameUser = $this->defaultdata->gradLanguageText(3);
                    break;
                case 3:
                    // правник
                    $user->typeNameUser = $this->defaultdata->gradLanguageText(4);
                    break;
                case 4:
                    // клиент
                    $user->typeNameUser = $this->defaultdata->gradLanguageText(2);
                    break;
                case 5:
                    // консультант
                    $user->typeNameUser = $this->defaultdata->gradLanguageText(5);
                    break;
                default:
                    $user->typeNameUser = '';
            }
        }							 
		return $user_result;
	}


	public function getUseragentInGeneralSearch($search=array(), $cond=array())
	{

		$this->db->select('U.*,(C.name) as manager');
		$this->db->from(TABLE_USER." AS U");
		$this->db->join(TABLE_USER." AS C","U.parent_id=C.id",'left');
		
		if(count($search)>0)
		{
			if(isset($search['value']) && $search['value']){
				$this->db->group_start();
				$this->db->like('U.name', $search['value']);
				$this->db->or_like('U.emailAddress', $search['value']);
				$this->db->or_like('U.phone', $search['value']);
				$this->db->or_like('C.name', $search['value']);
				$this->db->group_end();
			}
		}
		
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
		
		$this->db->where(array('U.userType'=>2, 'U.status'=>'Y'));
		$this->db->order_by('U.id','DESC');
		$query = $this->db->get();
		$return_data = $query->result();
		return $return_data;
	}
	public function getUserconsultantInGeneralSearch($search=array(), $cond=array())
	{
        $this->db->select('U.*,(C.name) as manager');
		$this->db->from(TABLE_USER." AS U");
		$this->db->join(TABLE_USER." AS C","U.parent_id=C.id",'left');
		
		if(count($search)>0)
		{
			if(isset($search['value']) && $search['value']){
				$this->db->group_start();
				$this->db->like('U.name', $search['value']);
				$this->db->or_like('U.emailAddress', $search['value']);
				$this->db->or_like('U.phone', $search['value']);
				$this->db->or_like('C.name', $search['value']);
				$this->db->group_end();
			}
		}
		
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
		
		$this->db->where(array('U.userType'=>5, 'U.status'=>'Y'));
		$this->db->order_by('U.id','DESC');
		$query = $this->db->get();
		$return_data = $query->result();
		return $return_data;
	}
	public function getUsersolicitorInGeneralSearch($search=array(), $cond=array())
	{

		$this->db->select('U.*,(C.name) as manager');
		$this->db->from(TABLE_USER." AS U");
		$this->db->join(TABLE_USER." AS C","U.parent_id=C.id",'left');
		
		if(count($search)>0)
		{
			if(isset($search['value']) && $search['value']){
				$this->db->group_start();
				$this->db->like('U.name', $search['value']);
				$this->db->or_like('U.emailAddress', $search['value']);
				$this->db->or_like('U.phone', $search['value']);
				$this->db->or_like('C.name', $search['value']);
				$this->db->group_end();
			}
		}
		
		if(count($cond)>0)
		{
			$this->db->where($cond);
		}
		
		$this->db->where(array('U.userType'=>3, 'U.status'=>'Y'));
		$this->db->order_by('U.id','DESC');
		$query = $this->db->get();
		$return_data = $query->result();
		return $return_data;
	}

	public function getAllPaymentAction()
	{
		$actions = $this->db->select(array('u_c.id AS c_id', 'u_c.name AS c_name',
			'u_p.name AS p_name', 'c_h.postedTime', 'c_h_r.other_txt'))
			->from(TABLE_CLIENT_HISTORY . ' AS c_h')
			->join(TABLE_CLIENT_HISTORY_RECORDS . ' AS c_h_r', 'c_h_r.client_history_id = c_h.id')
			->join(TABLE_USER . ' AS u_c', 'u_c.id = c_h.client_id')
			->join(TABLE_USER . ' AS u_p', 'u_p.id = c_h.user_id')
			->where_in('c_h.action_type', array(16, 17, 18, 19))
			->order_by('c_h.id', 'DESC')
			->get()
			->result();

		return $actions;
	}

	public function getDownloadFormular()
	{
		$actions = $this->db->select(array('f.display_name',
			'f_d.downloadTime', 'u.id', 'u.name AS user_name', 'u.userType'))
			->from(TABLE_FORMULAS_DOCUMENT_DOWNLOAD . ' AS f_d')
			->join(TABLE_FORMULAS_DOCUMENTS . ' AS f', 'f.id = f_d.document_id')
			->join(TABLE_USER . ' AS u', 'u.id = f_d.user_id')
			->order_by('f_d.id', 'DESC')
			->get()
			->result();

		return $actions;
	}

	public function getDownloadTraining()
	{
		$actions = $this->db->select(array('t.display_name',
			't_d.downloadTime', 'u.id', 'u.name as user_name', 'u.userType'))
			->from(TABLE_TRAINING_DOCUMENT_DOWNLOAD . ' AS t_d')
			->join(TABLE_TRAINING_DOCUMENTS . ' AS t', 't.id = t_d.document_id')
			->join(TABLE_USER . ' AS u', 'u.id = t_d.user_id')
			->order_by('t_d.id', 'DESC')
			->get()
			->result();

		return $actions;
	}

	public function getCommentAction()
	{
		$actions = $this->db->select(array('to_user_id', 'from_user_id', 'message', 'postedtime'))
			->order_by('id', 'DESC')
			->get(TABLE_CLIENT_COMMENT)
			->result();

		return $actions;
	}

	public function getCommentActionEdit()
	{
		$actions = $this->db->select(array(
				'c_h.client_id',
				'c_h.user_id',
				'c_h.postedtime',
				'c_h.action_type',
				'u_e.name as u_name',
				'u_e.userType',
				'u_c.name as c_name',
				'c_h_r.from_txt',
				'c_h_r.to_txt'
			)
		)
			->from(TABLE_CLIENT_HISTORY . ' AS c_h')
			->join(TABLE_CLIENT_HISTORY_RECORDS . ' AS c_h_r', 'c_h.id = c_h_r.client_history_id')
			->join(TABLE_USER . ' AS u_e', 'u_e.id = c_h.user_id')
			->join(TABLE_USER . ' AS u_c', 'u_c.id = c_h.client_id')
			->get()
			->result();

		return $actions;
	}

	public function getLoginActions()
	{
		$actions = $this->db->select(array('u_r.user_id', 'u_r.logintime', 'u_r.logouttime', 'u_r.userType', 'u.name'))
			->from(TABLE_USERLOGIN_RECORD . ' AS u_r')
			->join(TABLE_USER . ' AS u', 'u.id = u_r.user_id')
			->order_by('u_r.id', 'DESC')
			->get()
			->result();

		return $actions;
	}

	public function getCalendarEventActions()
	{
		$actions = $this->db->select(array('c_e.title', 'c_e.starttime', 'c_e.endtime', 'c_e.user_id', 'u.name', 'u.userType'))
			->from(TABLE_CALENDAR_EVENT . ' AS c_e')
			->join(TABLE_USER . ' AS u', 'u.id = c_e.user_id')
			->get()
			->result();

		return $actions;
	}

	public function getTaskActions()
	{
		$actions = $this->db->select(array('c_t.client_id', 'c_t.subject', 'c_t.deadline', 'c_t.created_user', 'u.name', 'u.userType'))
			->from(TABLE_CLIENT_TASKS . ' AS c_t')
			->join(TABLE_USER . ' AS u', 'u.id = c_t.created_user')
			->get()
			->result();

		return $actions;
	}


}
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact extends CI_Controller {

	public $data=array();
	public $loggedin_method_arr = array('contact');
//	public $controller_arr = array('user','frontend','fbcontroller','gpluscontroller','routemanager','ajax');
	function __construct()
	{
		parent::__construct();
		$this->load->model('userdata');
		$this->data = $this->defaultdata->getFrontendDefaultData();
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
		$this->load->model('contactdata');

		$search_data = $this->input->get();
		$this->data['search_data'] = $search_data;

		$conds = [];
		if(!empty($search_data['created_at_from'])) {
			$conds['created_at >='] = strtotime(str_replace('/','-',$search_data['created_at_from']));
		}
		if(!empty($search_data['created_at_to'])) {
			$conds['created_at <='] = strtotime(str_replace('/','-',$search_data['created_at_to']));
		}
		if(!empty($search_data['status_id'])) {
			$conds['status_id'] = $search_data['status_id'];
		}
		if(!empty($search_data['consultant'])) {
			$conds['consultant_id'] = $search_data['consultant'];
		}
		if(!empty($search_data['agent_id'])) {
			$conds['agent_id'] = $search_data['agent_id'];
		}
		$this->data['allConsultant'] = $this->userdata->fetchAll(array('userType' => 5, 'status'=>'Y'));
		$this->data['agents'] = $this->userdata->fetchAll(array('userType' => 2, 'status'=>'Y'));

		$this->data['statuses'] = $this->defaultdata->get_results(TABLE_CLIENT_SELECT_OPTION, array('type'=>107));
		$this->data['contacts'] = $this->contactdata->getList($conds, ['last_name', 'ASC']);
		$this->data['title'] = $this->defaultdata->gradLanguageText(592);
		$this->data['contact_status'] = 'all';

		$this->load->view('admin/contact/list',$this->data);
	}

	public function new_contacts()
	{
		$this->load->model('contactdata');

		$conds = [];
		$conds['status_id'] = 108;
		$search_data = $this->input->get();
		$this->data['search_data'] = $search_data;

		if(!empty($search_data['created_at_from'])) {
			$conds['created_at >='] = strtotime(str_replace('/','-',$search_data['created_at_from']));
		}
		if(!empty($search_data['created_at_to'])) {
			$conds['created_at <='] = strtotime(str_replace('/','-',$search_data['created_at_to']));
		}
		if(!empty($search_data['status_id'])) {
			$conds['status_id'] = $search_data['status_id'];
		}
		if(!empty($search_data['consultant'])) {
			$conds['consultant_id'] = $search_data['consultant'];
		}
		if(!empty($search_data['agent_id'])) {
			$conds['agent_id'] = $search_data['agent_id'];
		}
		$this->data['allConsultant'] = $this->userdata->fetchAll(array('userType' => 5, 'status'=>'Y'));
		$this->data['agents'] = $this->userdata->fetchAll(array('userType' => 2, 'status'=>'Y'));

//		$this->data['statuses'] = $this->defaultdata->get_results(TABLE_CLIENT_SELECT_OPTION, array('type'=>107));
		$this->data['contacts'] = $this->contactdata->getList($conds, ['last_name', 'ASC']);
		$this->data['contact_status'] = 108;
		$this->data['title'] = $this->defaultdata->gradLanguageText(593);

		$this->data['consultants'] = $this->userdata->fetchAll(array('userType'=>5, 'status'=>'Y'), array('id'=>'DESC'));
		$this->data['meeting_types'] = $this->defaultdata->get_results(TABLE_CLIENT_SELECT_OPTION, array('type'=>113));

		$this->load->view('admin/contact/list',$this->data);
	}

	public function contacts_in_progress()
	{
		$this->load->model('contactdata');

		$conds = [];
		$conds['status_id'] = 109;
		$search_data = $this->input->get();
		$this->data['search_data'] = $search_data;

		if(!empty($search_data['created_at_from'])) {
			$conds['created_at >='] = strtotime(str_replace('/','-',$search_data['created_at_from']));
		}
		if(!empty($search_data['created_at_to'])) {
			$conds['created_at <='] = strtotime(str_replace('/','-',$search_data['created_at_to']));
		}
		if(!empty($search_data['status_id'])) {
			$conds['status_id'] = $search_data['status_id'];
		}
		if(!empty($search_data['consultant'])) {
			$conds['consultant_id'] = $search_data['consultant'];
		}
		if(!empty($search_data['agent_id'])) {
			$conds['agent_id'] = $search_data['agent_id'];
		}
		$this->data['allConsultant'] = $this->userdata->fetchAll(array('userType' => 5, 'status'=>'Y'));
		$this->data['agents'] = $this->userdata->fetchAll(array('userType' => 2, 'status'=>'Y'));

//		$this->data['statuses'] = $this->defaultdata->get_results(TABLE_CLIENT_SELECT_OPTION, array('type'=>107));
		$this->data['contacts'] = $this->contactdata->getList($conds, ['last_name', 'ASC']);
		$this->data['title'] = $this->defaultdata->gradLanguageText(593);
		$this->data['contact_status'] = 109; //w toku
		$this->load->view('admin/contact/list',$this->data);
	}

	public function add()
	{

		$this->data['deals'] = $this->defaultdata->get_results(TABLE_DEALS, []);
		$this->data['dealsTypes'] = $this->defaultdata->get_results(TABLE_CLIENT_SELECT_OPTION, array('type'=>1));
		$this->data['consultants'] = $this->userdata->fetchAll(array('userType'=>5, 'status'=>'Y'), array('id'=>'DESC'));
//		if admin all agents else agent //todo
		$this->data['agents'] = $this->userdata->fetchAll(array('userType'=>2, 'status'=>'Y'), array('id'=>'DESC'));

		$this->load->view('admin/contact/new_contacts');
	}

	
	public function addProcess()
	{

		$post_data = $this->input->post();
		$deals = $this->input->post('deals');
		unset($post_data['deals']);
		$post_data['created_at'] = time();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('first_name', $this->defaultdata->gradLanguageText(77), 'trim|required');
		$this->form_validation->set_rules('last_name', $this->defaultdata->gradLanguageText(78), 'trim|required');
		$this->form_validation->set_rules('phone', $this->defaultdata->gradLanguageText(79), 'trim');
		$this->form_validation->set_rules('email', $this->defaultdata->gradLanguageText(79), 'trim|required|valid_email');
		$this->form_validation->set_message('valid_email', $this->defaultdata->gradLanguageText(381));
		$this->form_validation->set_rules('deal_type_id', $this->defaultdata->gradLanguageText(596), 'trim|required|integer');
		$this->form_validation->set_rules('consultant_id', $this->defaultdata->gradLanguageText(87), 'trim|required|integer');
		$this->form_validation->set_rules('agent_id', $this->defaultdata->gradLanguageText(597), 'trim|required|integer');

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
			redirect(base_url('admin/contact/add'));
		} else {

			$lastId = $this->defaultdata->insert(TABLE_CONTACTS, $post_data);
			if($lastId && !empty($deals)) {
				foreach ($deals as $deal_id) {
					$this->defaultdata->insert(TABLE_CONTACT_DEAL, ['contact_id' => $lastId, 'deal_id' => $deal_id]);
				}
			}
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(374));
			
			redirect(base_url('admin/contact/add'));

		}
	}


	public function edit($id)
	{

		$contact = $this->defaultdata->get_single_row(TABLE_CONTACTS, array('id'=>$id));
		if(count($contact)==0)
		{
			redirect(base_url('admin/contact'));
		}

		$this->data['deals'] = $this->defaultdata->get_results(TABLE_DEALS, []);
		$relatedDeals = $this->db->select('deal_id')->from(TABLE_CONTACT_DEAL)->where(['contact_id' => $id])->get()->result_array();
		$this->data['relatedDealsIds'] = array_map (function($o){
			return $o['deal_id'];
		} , $relatedDeals);
		$this->data['dealsTypes'] = $this->defaultdata->get_results(TABLE_CLIENT_SELECT_OPTION, array('type'=>1));
		$this->data['consultants'] = $this->userdata->fetchAll(array('userType'=>5, 'status'=>'Y'), array('id'=>'DESC'));
//		if admin all agents else agent //todo
		$this->data['agents'] = $this->userdata->fetchAll(array('userType'=>2, 'status'=>'Y'), array('id'=>'DESC'));

		$this->data['contact'] = $contact;
		$this->load->view('admin/contact/edit',$this->data);
	}


	public function editProcess()
	{
		$post_data = $this->input->post();
		$deals = $this->input->post('deals');
		unset($post_data['deals']);
		$this->load->library('form_validation');

		$contact = $this->defaultdata->get_single_row(TABLE_CONTACTS, array('id'=>$post_data['id']));

		if(!$contact) {
			$this->form_validation->set_message('id', 'Such contact does not exist');
			$this->session->set_flashdata('error',validation_errors());
			 redirect_back();
		}

		$contact_id = $contact->id;

		$this->form_validation->set_rules('first_name', $this->defaultdata->gradLanguageText(77), 'trim|required');
		$this->form_validation->set_rules('last_name', $this->defaultdata->gradLanguageText(78), 'trim|required');
		$this->form_validation->set_rules('phone', $this->defaultdata->gradLanguageText(79), 'trim');
		$this->form_validation->set_rules('email', $this->defaultdata->gradLanguageText(79), 'trim|required|valid_email');
		$this->form_validation->set_message('valid_email', $this->defaultdata->gradLanguageText(381));
		$this->form_validation->set_rules('deal_type_id', $this->defaultdata->gradLanguageText(596), 'trim|required|integer');
		$this->form_validation->set_rules('consultant_id', $this->defaultdata->gradLanguageText(87), 'trim|required|integer');
		$this->form_validation->set_rules('agent_id', $this->defaultdata->gradLanguageText(597), 'trim|required|integer');



		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
			redirect(base_url('admin/contact/edit/'.$post_data['id']));
		} else {
			unset($post_data['id']);

			$updated = $this->defaultdata->update(TABLE_CONTACTS, $post_data, array('id'=>$contact_id));

			if($updated) {
				if(!empty($deals)) {
					$this->db->where('contact_id', $contact_id)->delete(TABLE_CONTACT_DEAL);
					foreach ($deals as $deal_id) {
						$this->defaultdata->insert(TABLE_CONTACT_DEAL, ['contact_id' => $contact_id, 'deal_id' => $deal_id]);
					}
				}
				$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(611));
			} else {
				$this->session->set_flashdata('error', $this->defaultdata->gradLanguageText(612));
			}
			redirect(base_url('admin/contact'));

		}
	}


	public function remove_contact()
	{
		$contact_id = $this->input->post('contact_id');
		$this->defaultdata->delete(TABLE_CONTACTS, array('id'=>$contact_id));
		echo 1;
	}

	public function remove_note()
	{
		$note_id = $this->input->post('note_id');
		$is_deleted = $this->defaultdata->delete(TABLE_CONTACT_NOTES, array('id'=>$note_id));
		if($is_deleted) {
			echo 1;
		} else {
			echo 0;
		}
	}

	public function remove_meeting()
	{
		$meeting_id = $this->input->post('meeting_id');
		$is_deleted = $this->defaultdata->delete(TABLE_CONTACT_MEETINGS, array('id'=>$meeting_id));
		if($is_deleted) {
			echo 1;
		} else {
			echo 0;
		}
	}

	public function details($id = '')
	{
		$this->load->model('contactdata');
//		$contact_data = $this->userdata->setTable(TABLE_CONTACTS)->fetchOne(array('id'=>$id));
		$contact_data = $this->contactdata->geContDetails($id);

		if(count($contact_data)==0)
		{
			redirect(base_url('admin/contact'));
		}

		$this->data['contact_data'] = $contact_data;
		$this->data['consultants'] = $this->userdata->fetchAll(array('userType'=>5, 'status'=>'Y'), array('id'=>'DESC'));
		$this->data['meeting_types'] = $this->defaultdata->get_results(TABLE_CLIENT_SELECT_OPTION, array('type'=>113));

		$this->data['upper_header'] = $this->load->view('admin/contact/upper_header', $this->data, TRUE);
		$this->data['tab_header'] = $this->load->view('admin/contact/tab_header', $this->data, TRUE);
		$this->data['common_script'] = $this->load->view('admin/contact/common_script', $this->data, TRUE);


		$this->load->view('admin/contact/information',$this->data);
	}

	public function notes($id)
	{
		$this->load->model('contactdata');
//		$contact_data = $this->userdata->setTable(TABLE_CONTACTS)->fetchOne(array('id'=>$id));
		$contact_data = $this->contactdata->geContDetails($id);

		if(count($contact_data)==0)
		{
			redirect(base_url('admin/contact'));
		}
		$this->data['notes'] = $this->db->select('cn.*, u.name as user_name')->from(TABLE_CONTACT_NOTES. ' cn')
			->join(TABLE_USER.' u', 'cn.user_id = u.id')->order_by('id', 'desc')->get()->result();
		$this->data['consultants'] = $this->userdata->fetchAll(array('userType'=>5, 'status'=>'Y'), array('id'=>'DESC'));
		$this->data['meeting_types'] = $this->defaultdata->get_results(TABLE_CLIENT_SELECT_OPTION, array('type'=>113));

		$this->data['contact_data'] = $contact_data;
		$this->data['upper_header'] = $this->load->view('admin/contact/upper_header', $this->data, TRUE);
		$this->data['tab_header'] = $this->load->view('admin/contact/tab_header', $this->data, TRUE);
		$this->data['common_script'] = $this->load->view('admin/contact/common_script', $this->data, TRUE);

		$this->load->view('admin/contact/notes',$this->data);
	}


	public function meetings($id)
	{

		$this->load->model('contactdata');
//		$contact_data = $this->userdata->setTable(TABLE_CONTACTS)->fetchOne(array('id'=>$id));
		$contact_data = $this->contactdata->geContDetails($id);

		if(count($contact_data)==0)
		{
			redirect(base_url('admin/contact'));
		}
		$this->data['meetings'] = $this->db->select('m.*, u.name as consultant_name, o.translation_index as type_trans_index')->from(TABLE_CONTACT_MEETINGS. ' m')
			->join(TABLE_USER.' u', 'm.consultant_id = u.id')
			->join(TABLE_CLIENT_SELECT_OPTION.' o', 'm.meeting_type_id = o.id')
			->order_by('id', 'desc')->get()->result();
		$this->data['consultants'] = $this->userdata->fetchAll(array('userType'=>5, 'status'=>'Y'), array('id'=>'DESC'));
		$this->data['meeting_types'] = $this->defaultdata->get_results(TABLE_CLIENT_SELECT_OPTION, array('type'=>113));

		$this->data['contact_data'] = $contact_data;
		$this->data['upper_header'] = $this->load->view('admin/contact/upper_header', $this->data, TRUE);
		$this->data['tab_header'] = $this->load->view('admin/contact/tab_header', $this->data, TRUE);
		$this->data['common_script'] = $this->load->view('admin/contact/common_script', $this->data, TRUE);

		$this->load->view('admin/contact/meetings',$this->data);
	}


	public function contactCardLiveEdit()
	{
		$return_data['has_error'] = 0;
		$contact_id = $this->input->post('contact_id');
//		$userDetails = $this->userdata->fetchOne(array('id'=>$user_id));
		$field_name = $this->input->post('field_name');
		$new_value = $this->input->post('new_value');
		$field_type = $this->input->post('field_type');
		$table_name = $this->input->post('table_name');

			$cond = array('id'=>$contact_id );
//			$clientData = $this->userdata->fetchOne($cond);
//			$table_constant_name = 'TABLE_USER';


		if($field_type=='date')
		{
			$new_value = strtotime(str_replace('/', '-', $new_value));
			$return_data['new_value'] = date('d/m/Y', $new_value);
		} elseif($field_type=='percentage') {
			$return_data['new_value'] = $new_value>0?$new_value.'%':'';
		} elseif($field_type=='textarea') {
			$return_data['new_value'] = nl2br($new_value);
		} else {
			$return_data['new_value'] = $new_value;
		}


//		if($field_name=='emailAddress' && $userDetails->emailAddress!=$new_value)
//		{
//			$emailCount = $this->defaultdata->count_record(TABLE_USER, array('emailAddress'=>$new_value));
//			if($emailCount>0)
//			{
//				$return_data['has_error'] = 1;
//				$return_data['error_msg'] = $this->defaultdata->gradLanguageText(385);
//			}
//		}
		if($return_data['has_error'] == 0)
		{
			$client_data[$field_name] = $new_value;
			$this->userdata->setTable($table_name)->update($client_data, $cond);
			$this->userdata->unsetTable();
		}

		//=======FOR CLIENT EDIT HISTORY=======//
//		if($clientData->$field_name != $new_value)
//		{
//			$history_data['client_id'] = $user_id;
//			$history_data['user_id'] = $this->session->userdata('usrid');
//			$history_data['source_table'] = $table_constant_name;
//			$history_data['source_id'] = $user_id;
//			$history_data['action_type'] = 1;
//			$history_data['postedtime'] = time();
//			$history_id = $this->userdata->setTable(TABLE_CLIENT_HISTORY)->insert($history_data);
//			$this->userdata->unsetTable();
//
//			$from_txt = $clientData->$field_name;
//			$to_txt = $new_value;
//			if($field_type=='date')
//			{
//				$from_txt = date('d/m/Y', $clientData->$field_name);
//				$to_txt = date('d/m/Y', $new_value);
//			}
//
//			$record_data['client_history_id'] = $history_id;
//			$record_data['field_name'] = $field_name;
//			$record_data['from_txt'] = $from_txt;
//			$record_data['to_txt'] = $to_txt;
//			$this->userdata->setTable(TABLE_CLIENT_HISTORY_RECORDS)->insert($record_data);
//			$this->userdata->unsetTable();
//		}
		//=======FOR CLIENT EDIT HISTORY=======//

		echo json_encode($return_data);
	}

	public function api_add_note()
	{

		$return_data = [];
		$user_id = $this->session->userdata('usrid');
		$input_data = $this->input->post();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', $this->defaultdata->gradLanguageText(233), 'trim|required');
		$this->form_validation->set_rules('description', $this->defaultdata->gradLanguageText(234), 'trim|required');
		$this->form_validation->set_rules('deadline_at', $this->defaultdata->gradLanguageText(146), 'trim|required');
		$this->form_validation->set_rules('id', 'id', 'trim|required|integer');
		$this->form_validation->set_rules('contact_id', 'contact_id', 'trim|required|integer');


		if ($this->form_validation->run() != FALSE) {
			$id = $input_data['id'];

			$note_data['user_id'] = $user_id;
			$note_data['contact_id'] = $input_data['contact_id'];
			$note_data['title'] = $input_data['title'];
			$note_data['deadline_at'] = strtotime(str_replace('/', '-', $input_data['deadline_at']));
			$note_data['created_at'] = time();
			$note_data['description'] = str_replace("\r\n", '',nl2br($input_data['description']));

			if ($id == 0) {

				$lastId = $this->userdata->setTable(TABLE_CONTACT_NOTES)->insert($note_data);
				$this->userdata->unsetTable();
				$return_data['mode'] = 'add';
				$return_data['lastId'] = $lastId;
			} else if($id >0) {
				unset($note_data['id']);
				$this->userdata->setTable(TABLE_CONTACT_NOTES)->update($note_data, ['id' => $id]);
				$this->userdata->unsetTable();
				$return_data['mode'] = 'edit';
			}


			if (isset($lastId)) {
				$note_data['id'] = $lastId;
			} else {
				$note_data['id'] = $id;
			}

			$note_data['user_name'] = $this->session->userdata('usr_name');
			$this->data['noteData'] = (object)$note_data;

			$return_data['single_note'] = $this->load->view('admin/contact/single_note', $this->data, TRUE);

			echo json_encode($return_data);

		}
	}

	public function api_add_meeting()
	{

		$return_data = [];
		$user_id = $this->session->userdata('usrid');
		$input_data = $this->input->post();

		$this->load->library('form_validation');
//		$this->form_validation->set_rules('title', $this->defaultdata->gradLanguageText(233), 'trim|required');
		$this->form_validation->set_rules('description', $this->defaultdata->gradLanguageText(234), 'trim|required');
		$this->form_validation->set_rules('deadline_at', $this->defaultdata->gradLanguageText(146), 'trim|required');
		$this->form_validation->set_rules('id', 'id', 'trim|required|integer');
		$this->form_validation->set_rules('contact_id', 'contact', 'trim|required|integer');
		$this->form_validation->set_rules('consultant_id', 'consultant', 'trim|required|integer');
		$this->form_validation->set_rules('meeting_type', 'Meeting type', 'trim|required|integer');


		if ($this->form_validation->run() != FALSE) {

			$id = $input_data['id'];

			$meeting_data['user_id'] = $user_id;
			$meeting_data['contact_id'] = $input_data['contact_id'];

			$time = explode(':',$input_data['deadline_time']);
			$hours = (int)$time[0]; $minutes = (int)$time[1];
			$meeting_data['deadline_at'] = strtotime(str_replace('/', '-', $input_data['deadline_at'])) + $hours*60*60 + $minutes*60;
			$meeting_data['created_at'] = time();
			$meeting_data['meeting_type_id'] = $input_data['meeting_type'];
			$meeting_data['consultant_id'] = $input_data['consultant_id'];
			$meeting_data['description'] = str_replace("\r\n", '',nl2br($input_data['description']));

			if ($id == 0) {

				$lastId = $this->userdata->setTable(TABLE_CONTACT_MEETINGS)->insert($meeting_data);
				$this->userdata->unsetTable();
				$return_data['mode'] = 'add';
				$return_data['lastId'] = $lastId;

			} else if($id >0) {

				unset($meeting_data['id']);
				$this->userdata->setTable(TABLE_CONTACT_MEETINGS)->update($meeting_data, ['id' => $id]);
				$this->userdata->unsetTable();
				$return_data['mode'] = 'edit';
			}

			$meeting_data['id'] = isset($lastId)? $lastId :$id;

			$this->data['meetingData'] =  $this->db->select('m.*, u.name as consultant_name, o.translation_index as type_trans_index')
				->from(TABLE_CONTACT_MEETINGS. ' m')->where(['m.id'=>$meeting_data['id']])
				->join(TABLE_USER.' u', 'm.consultant_id = u.id')
				->join(TABLE_CLIENT_SELECT_OPTION.' o', 'm.meeting_type_id = o.id')->get()->row();;

			$this->db->set('status_id', 110)->where('id', $input_data['contact_id'])->update(TABLE_CONTACTS);

			$meeting_data['user_name'] = $this->session->userdata('usr_name');

			$return_data['single_meeting'] = $this->load->view('admin/contact/single_meeting', $this->data, TRUE);

			echo json_encode($return_data);

		}
	}

	public function change_status($id = false)
	{
		$status_id = $this->input->post('status_id');
		$res = $this->db->set('status_id', $status_id)->where('id',$id )->update(TABLE_CONTACTS);

		if($res) {
			echo 1;
		} else {
			echo 0;
		}

	}
}


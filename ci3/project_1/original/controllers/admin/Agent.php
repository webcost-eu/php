<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Agent extends CI_Controller {

	public $data=array();
	public $loggedin_method_arr = array('agent');
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
		$this->data['allAgents'] = $this->userdata->fetchAll(array('userType'=>2, 'status'=>'Y'), array('id'=>'DESC'));
		foreach ($this->data['allAgents'] as $user) {
			$user->other_details = $this->userdata->setTable(TABLE_AGENTS)->fetchOne(array('user_id'=>$user->id));
			$this->userdata->unsetTable();
			
			$user->manager = '';
			if($user->parent_id){
				$user->manager = $this->defaultdata->getField(TABLE_USER, array('id'=>$user->parent_id), 'name');
			}

			$user->lastlogintime = $this->defaultdata->getField(TABLE_USERLOGIN, array('uid'=>$user->id), 'lastlogintime');

		}
		
		$this->load->view('admin/agent/list',$this->data);
	}


	public function add()
	{
		$this->data['allAgents'] = $this->userdata->fetchAll(array('userType'=>2, 'status'=>'Y'));
		$this->data['allCountries'] = $this->defaultdata->getAllCountry();
		$this->load->view('admin/agent/add',$this->data);
	}

	public function addProcess()
	{
		//print_r($_FILES);die;
		$input_data = $this->input->post();
		//echo "<pre>"; print_r($input_data);die;
		$this->load->library('form_validation');
		$this->form_validation->set_rules('firstName', 'First Name', 'trim|required');
		$this->form_validation->set_rules('lastName', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('phone', 'Phone number', 'trim|required');
		$this->form_validation->set_rules('userName', 'Username', 'trim|required|is_unique['.TABLE_USER.'.userName]');
		$this->form_validation->set_rules('emailAddress', 'Email', 'trim|required|valid_email|is_unique['.TABLE_USER.'.emailAddress]');
		//$this->form_validation->set_message('valid_email', 'Please enter valid email.');
		$this->form_validation->set_message('valid_email', $this->defaultdata->gradLanguageText(381));
		//$this->form_validation->set_message('is_unique', 'This email is already registered.');
		$this->form_validation->set_message('is_unique', $this->defaultdata->gradLanguageText(382));
		$this->form_validation->set_rules('userPassword', 'Password', 'trim|required');
		$this->form_validation->set_rules('cPassword', 'Confirm Password', 'trim|required|matches[userPassword]');
		$this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
		$this->form_validation->set_rules('NIP', 'NIP', 'trim|required');
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|required');
		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|required');
		$this->form_validation->set_rules('agreement_date', 'Date of Agreement ', 'trim|required');
		$this->form_validation->set_rules('provision_general_percent[]', 'Provision general', 'trim|required');
		$this->form_validation->set_rules('provision_court_percent[]', 'Provision in court', 'trim|required');
		$this->form_validation->set_rules('provision_central_percent[]', 'Central provision', 'trim|required');
		$this->form_validation->set_rules('provision_upper_percent[]', 'Upper provision', 'trim|required');
		$this->form_validation->set_rules('id_of_partner', 'ID of Partner', 'trim|required');

		
		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
			redirect(base_url('admin/agent/add'));
		}
		else
		{
			$this->data['general_settings'] = $this->defaultdata->grabSettingData();

			if(!empty($this->data['general_settings']->default_email_footer_text)) {
				$footer_text = $this->data['general_settings']->default_email_footer_text;
			} else {
				$footer_text = 'Z poważaniem
					{USER_NAME}
					______________________________
					kom. {USER_EMAIL}
					
					
					Niniejsza wiadomość oraz wszystkie załączone do niej pliki przeznaczone są do wyłącznego użytku zamierzonego adresata i mogą zawierać chronione lub poufne informacje. Przeglądanie, wykorzystywanie, ujawnianie lub dystrybuowanie przez osoby do tego nieupoważnione jest zabronione. Jeśli nie jest Pan/Pani wymienionym adresatem niniejszej wiadomości, prosimy o niezwłoczny kontakt z nami i usuniecie wiadomości oraz zniszczenie wszystkich kopii.';
			}

			$user_data['firstName'] = $input_data['firstName'];
			$user_data['lastName'] = $input_data['lastName'];
			$user_data['name'] = $input_data['firstName'].' '.$input_data['lastName'];
			$user_data['phone'] = $input_data['phone'];
			$user_data['userName'] = $input_data['userName'];
			$user_data['emailAddress'] = $input_data['emailAddress'];
			$user_data['userPassword'] = md5($input_data['userPassword']);
			$user_data['parent_id'] = $input_data['parent_id'];
			$user_data['userType'] = 2;
			$user_data['status'] = 'Y';
			$user_data['postedtime'] = time();
			$user_data['ipaddress'] = $_SERVER["REMOTE_ADDR"];
			$user_data['language'] = $input_data['language'];
			$user_data['email_footer_text'] = $footer_text;

			$user_id = $this->userdata->insert($user_data);

			if($user_id > 0)
			{
				$company = isset($input_data['company'])?'Y':'N';
				$vat_payer_txt = $input_data['vat_payer']=='N'?$input_data['vat_payer_txt']:'';
				//$see_instruction = isset($input_data['see_instruction'])?'Y':'N';
				$formulars = isset($input_data['formulars'])?'Y':'N';
				$training_script = isset($input_data['training_script'])?'Y':'N';

				
				$agent_data['user_id'] = $user_id;
				$agent_data['country_id'] = $input_data['country_id'];
				$agent_data['town'] = $input_data['town'];
				$agent_data['street'] = $input_data['street'];
				$agent_data['zip'] = $input_data['zip'];
				$agent_data['company'] = $company;
				$agent_data['company_name'] = $input_data['company_name'];
				$agent_data['NIP'] = $input_data['NIP'];
				$agent_data['KRS'] = $input_data['KRS'];
				$agent_data['account_number'] = $input_data['account_number'];
				$agent_data['bank_name'] = $input_data['bank_name'];
				$agent_data['vat_payer'] = $input_data['vat_payer'];
				$agent_data['vat_payer_txt'] = $vat_payer_txt;
				$agent_data['agreement_date'] = strtotime(str_replace('/','-',$input_data['agreement_date']));
				$agent_data['id_of_partner'] = $input_data['id_of_partner'];
				//$agent_data['see_instruction'] = $see_instruction;
				$agent_data['formulars'] = $formulars;
				$agent_data['training_script'] = $training_script;

				$agent_id = $this->userdata->setTable(TABLE_AGENTS)->insert($agent_data);
				$this->userdata->unsetTable();

				/*======== INSERT MULTIPLE AGENT DOCUMENTS UPLOADS =======*/
				if($_FILES['document']['name'][0])
				{
					$docCount = count($_FILES['document']['name']);
					for($i=0; $i<$docCount; $i++)
					{
						$_FILES['userFile']['name'] = $_FILES['document']['name'][$i];
						$_FILES['userFile']['type'] = $_FILES['document']['type'][$i];
						$_FILES['userFile']['tmp_name'] = $_FILES['document']['tmp_name'][$i];
						$_FILES['userFile']['error'] = $_FILES['document']['error'][$i];
						$_FILES['userFile']['size'] = $_FILES['document']['size'][$i];

						$config['upload_path'] = UPLOAD_PATH_URL.'agents_document/';
						$config['allowed_types'] = '*';
						$config['file_name'] = time().str_replace(' ','-',$_FILES['userFile']['name']);
						$this->load->library('upload');
						$this->upload->initialize($config);
						$this->upload->do_upload('userFile');
						$doc_file = $this->upload->data();
						$displayName = str_replace($doc_file['file_ext'], '', $doc_file['client_name']);

						$document_data['agent_id'] = $user_id;
						$document_data['file_name'] = $doc_file['file_name'];
						$document_data['display_name'] = $displayName;
						$document_data['status'] = 'Y';
						$document_data['postedTime'] = time();

						$this->userdata->setTable(TABLE_AGENT_DOCUMENTS)->insert($document_data);
						$this->userdata->unsetTable();
					}
				}
				/*======== INSERT MULTIPLE AGENT DOCUMENTS UPLOADS =======*/

				$provision_data['user_id'] = $user_id;
				for($i=0;$i<count($input_data['provision_general_percent']);$i++)
				{
					$provision_data['type'] = 1;
					$provision_data['provision_percent'] = $input_data['provision_general_percent'][$i];
					$provision_data['provision_start'] = strtotime(str_replace('/','-',$input_data['provision_general_start'][$i]));
					$provision_data['provision_end'] = strtotime(str_replace('/','-',$input_data['provision_general_end'][$i]));
					$this->userdata->setTable(TABLE_AGENT_PROVISION)->insert($provision_data);
					$this->userdata->unsetTable();
				}

				for($i=0;$i<count($input_data['provision_court_percent']);$i++)
				{
					$provision_data['type'] = 2;
					$provision_data['provision_percent'] = $input_data['provision_court_percent'][$i];
					$provision_data['provision_start'] = strtotime(str_replace('/','-',$input_data['provision_court_start'][$i]));
					$provision_data['provision_end'] = strtotime(str_replace('/','-',$input_data['provision_court_end'][$i]));
					$this->userdata->setTable(TABLE_AGENT_PROVISION)->insert($provision_data);
					$this->userdata->unsetTable();
				}

				for($i=0;$i<count($input_data['provision_central_percent']);$i++)
				{
					$provision_data['type'] = 3;
					$provision_data['provision_percent'] = $input_data['provision_central_percent'][$i];
					$provision_data['provision_start'] = strtotime(str_replace('/','-',$input_data['provision_central_start'][$i]));
					$provision_data['provision_end'] = strtotime(str_replace('/','-',$input_data['provision_central_end'][$i]));
					$this->userdata->setTable(TABLE_AGENT_PROVISION)->insert($provision_data);
					$this->userdata->unsetTable();
				}

				for($i=0;$i<count($input_data['provision_upper_percent']);$i++)
				{
					$provision_data['type'] = 4;
					$provision_data['provision_percent'] = $input_data['provision_upper_percent'][$i];
					$provision_data['provision_start'] = strtotime(str_replace('/','-',$input_data['provision_upper_start'][$i]));
					$provision_data['provision_end'] = strtotime(str_replace('/','-',$input_data['provision_upper_end'][$i]));
					$this->userdata->setTable(TABLE_AGENT_PROVISION)->insert($provision_data);
					$this->userdata->unsetTable();
				}

				if($formulars=='Y')
				{
					$folder_data = array();
					$formulas_folders = $this->defaultdata->get_results(TABLE_FORMULAS_FOLDER, array('status'=>'Y'));
					
					$folder_data['agent_id'] = $user_id;
					foreach ($formulas_folders as $key => $folder)
					{
						$folder_data['folder_id'] = $folder->id;
						$this->defaultdata->insert(TABLE_FORMULAS_FOLDER_SHARE, $folder_data);

						$folder_doc = $this->defaultdata->get_results(TABLE_FORMULAS_DOCUMENTS, array('folder_id'=>$folder->id));
						
						$doc_data = array();
						$doc_data['agent_id'] = $user_id;
						foreach ($folder_doc as $key => $doc)
						{
							$count_doc_share = $this->defaultdata->count_record(TABLE_FORMULAS_DOCUMENT_SHARE, array('document_id'=>$doc->id));
							if($count_doc_share > 0)
							{
								$doc_data['document_id'] = $doc->id;
								$this->defaultdata->insert(TABLE_FORMULAS_DOCUMENT_SHARE, $doc_data);
							}
						}
					}
				}

				if($training_script=='Y')
				{
					$folder_data = array();
					$training_folders = $this->defaultdata->get_results(TABLE_TRAINING_FOLDER, array('status'=>'Y'));
					
					$folder_data['agent_id'] = $user_id;
					foreach ($training_folders as $key => $folder)
					{
						$folder_data['folder_id'] = $folder->id;
						$this->defaultdata->insert(TABLE_TRAINING_FOLDER_SHARE, $folder_data);

						$folder_doc = $this->defaultdata->get_results(TABLE_TRAINING_DOCUMENTS, array('folder_id'=>$folder->id));
						
						$doc_data = array();
						$doc_data['agent_id'] = $user_id;
						foreach ($folder_doc as $key => $doc)
						{
							$count_doc_share = $this->defaultdata->count_record(TABLE_TRAINING_DOCUMENT_SHARE, array('document_id'=>$doc->id));
							if($count_doc_share > 0)
							{
								$doc_data['document_id'] = $doc->id;
								$this->defaultdata->insert(TABLE_TRAINING_DOCUMENT_SHARE, $doc_data);
							}
						}
					}
				}

				//$this->session->set_flashdata('success','Agent Inserted Successfully!!');
				$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(405));
			}
			else
			{
				//$this->session->set_flashdata('error','Something Wrong Please Try Again.');
				$this->session->set_flashdata('error', $this->defaultdata->gradLanguageText(376));
			}
			redirect(base_url('admin/agent/add'));

		}
	}

	public function edit($id)
	{
		$user_data = $this->userdata->fetchOne(array('id'=>$id, 'userType'=>2));

		if(count($user_data)==0)
		{
			redirect(base_url('admin/agent'));
		}
		
		$agent_data = $this->userdata->setTable(TABLE_AGENTS)->fetchOne(array('user_id'=>$id));
		$this->userdata->unsetTable();

		$provision_data = $this->userdata->setTable(TABLE_AGENT_PROVISION)->fetchAll(array('user_id'=>$id));
		$this->userdata->unsetTable();

		$this->data['agent_documents'] = $this->userdata->setTable(TABLE_AGENT_DOCUMENTS)->fetchAll(array('agent_id'=>$id));
		$this->userdata->unsetTable();


		$this->data['user_data'] = $user_data;
		$this->data['agent_data'] = $agent_data;
		$this->data['provision_data'] = $provision_data;

		$this->data['allAgents'] = $this->userdata->fetchAll(array('userType'=>2, 'status'=>'Y', 'id !='=>$id));
		$this->data['allCountries'] = $this->defaultdata->getAllCountry();
		$this->load->view('admin/agent/edit',$this->data);
	}



	public function editProcess()
	{
		//print_r($_FILES);die;
		$input_data = $this->input->post();
		//echo "<pre>"; print_r($input_data);die;
		$userDetails = $this->userdata->fetchOne(array('id'=>$input_data['id']));
		$agentDetails = $this->userdata->setTable(TABLE_AGENTS)->fetchOne(array('user_id'=>$input_data['id']));
		$this->userdata->unsetTable();


		$this->load->library('form_validation');
		$this->form_validation->set_rules('firstName', 'First Name', 'trim|required');
		$this->form_validation->set_rules('lastName', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('phone', 'Phone number', 'trim|required');
		
		if($input_data['userPassword'])
		{
			$this->form_validation->set_rules('userPassword', 'Password', 'trim|required');
			$this->form_validation->set_rules('cPassword', 'Confirm Password', 'trim|required|matches[userPassword]');
		}

		
		$this->form_validation->set_rules('company_name', 'Company Name', 'trim|required');
		$this->form_validation->set_rules('NIP', 'NIP', 'trim|required');
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|required');
		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|required');
		$this->form_validation->set_rules('agreement_date', 'Date of Agreement ', 'trim|required');
		$this->form_validation->set_rules('provision_general_percent[]', 'Provision general', 'trim|required');
		$this->form_validation->set_rules('provision_court_percent[]', 'Provision in court', 'trim|required');
		$this->form_validation->set_rules('provision_central_percent[]', 'Central provision', 'trim|required');
		$this->form_validation->set_rules('provision_upper_percent[]', 'Upper provision', 'trim|required');
		$this->form_validation->set_rules('id_of_partner', 'ID of Partner', 'trim|required');

		
		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
			redirect(base_url('admin/agent/edit/'.$input_data['id']));
		}
		else
		{
			$user_data['firstName'] = $input_data['firstName'];
			$user_data['lastName'] = $input_data['lastName'];
			$user_data['name'] = $input_data['firstName'].' '.$input_data['lastName'];
			$user_data['phone'] = $input_data['phone'];
			
			if($input_data['userPassword'])
			{
				$user_data['userPassword'] = md5($input_data['userPassword']);
			}
			$user_data['parent_id'] = $input_data['parent_id']?$input_data['parent_id']:0;
			$user_data['language'] = $input_data['language'];

			$this->userdata->update($user_data, array('id'=>$input_data['id']));

			if($input_data['id'] > 0)
			{
				$company = isset($input_data['company'])?'Y':'N';
				$vat_payer_txt = $input_data['vat_payer']=='N'?$input_data['vat_payer_txt']:'';
				$see_instruction = isset($input_data['see_instruction'])?'Y':'N';
				$formulars = isset($input_data['formulars'])?'Y':'N';
				$training_script = isset($input_data['training_script'])?'Y':'N';

				$agent_data['country_id'] = $input_data['country_id']?$input_data['country_id']:0;
				$agent_data['town'] = $input_data['town'];
				$agent_data['street'] = $input_data['street'];
				$agent_data['zip'] = $input_data['zip'];
				$agent_data['company'] = $company;
				$agent_data['company_name'] = $input_data['company_name'];
				$agent_data['NIP'] = $input_data['NIP'];
				$agent_data['KRS'] = $input_data['KRS'];
				$agent_data['account_number'] = $input_data['account_number'];
				$agent_data['bank_name'] = $input_data['bank_name'];
				$agent_data['vat_payer'] = $input_data['vat_payer'];
				$agent_data['vat_payer_txt'] = $vat_payer_txt;
				$agent_data['agreement_date'] = $input_data['agreement_date']?strtotime(str_replace('/','-',$input_data['agreement_date'])):0;
				$agent_data['id_of_partner'] = $input_data['id_of_partner'];
				//$agent_data['see_instruction'] = $see_instruction;
				$agent_data['formulars'] = $formulars;
				$agent_data['training_script'] = $training_script;

				$agent_id = $this->userdata->setTable(TABLE_AGENTS)->update($agent_data, array('user_id'=>$input_data['id']));
				$this->userdata->unsetTable();


				//=======FOR AGENT EDIT HISTORY=======//
				$history_data['agent_id'] = $input_data['id'];
				$history_data['user_id'] = $this->session->userdata('usrid');
				$history_data['source_table'] = 'TABLE_AGENTS';
				$history_data['source_id'] = $input_data['id'];
				$history_data['action_type'] = 1;
				$history_data['postedtime'] = time();
				$history_id = $this->userdata->setTable(TABLE_AGENT_HISTORY)->insert($history_data);
				$this->userdata->unsetTable();

				$userfields = $this->db->get(TABLE_USER)->list_fields();
				$notEditUser = array('id', 'name', 'userPassword');
				foreach ($userfields as $key => $value)
				{
					if(!in_array($value, $notEditUser) && isset($user_data[$value]) && $userDetails->$value != $user_data[$value])
					{
						$from_txt = $userDetails->$value;
						$to_txt = $user_data[$value];
						switch($value)
						{
							case 'parent_id':
							$from_txt = $this->defaultdata->getField(TABLE_USER, array('id'=>$userDetails->$value), 'name');
							$to_txt = $this->defaultdata->getField(TABLE_USER, array('id'=>$user_data[$value]), 'name');
							break;

							case 'language':
							$from_txt = $from_txt == 1 ? 'ENG' : 'PL';
							$to_txt = $to_txt == 1 ? 'ENG' : 'PL';

						}

						$record_data['agent_history_id'] = $history_id;
						$record_data['field_name'] = $value;
						$record_data['from_txt'] = $from_txt;
						$record_data['to_txt'] = $to_txt;
						$this->userdata->setTable(TABLE_AGENT_HISTORY_RECORDS)->insert($record_data);
						$this->userdata->unsetTable();
					}
				}

				$agentfields = $this->db->get(TABLE_AGENTS)->list_fields();
				$notEditAgent = array('id', 'user_id', 'document');
				foreach ($agentfields as $key => $value)
				{
					if(!in_array($value, $notEditAgent) && isset($agent_data[$value]) && $agentDetails->$value != $agent_data[$value])
					{
						$from_txt = $agentDetails->$value;
						$to_txt = $agent_data[$value];
						switch($value)
						{
							case 'country_id':
							$from_txt = $agentDetails->$value?$this->defaultdata->getField(TABLE_COUNTRIES, array('idCountry'=>$agentDetails->$value), 'countryName'):'';
							$to_txt = $agent_data[$value]?$this->defaultdata->getField(TABLE_COUNTRIES, array('idCountry'=>$agent_data[$value]), 'countryName'):'';
							break;
 							
 							case 'company':
							$from_txt = $agentDetails->$value=='Y'?'Company Yes':'Company No';
							$to_txt = $agent_data[$value]=='Y'?'Yes':'No';
							break;
							
							case 'vat_payer':
							$from_txt = $agentDetails->$value=='Y'?'Vat Payer Yes':'Vat Payer No';
							$to_txt = $agent_data[$value]=='Y'?'Yes':'No';
							break;

							case 'agreement_date':
							$from_txt = $agentDetails->$value?date('d/m/Y', $agentDetails->$value):'';
							$to_txt = $agent_data[$value]?date('d/m/Y', $agent_data[$value]):'';
							break;

							case 'see_instruction':
							$from_txt = $agentDetails->$value=='Y'?'See Instruction Yes':'See Instruction No';
							$to_txt = $agent_data[$value]=='Y'?'Yes':'No';
							break;

							case 'formulars':
							$from_txt = $agentDetails->$value=='Y'?'Formulars Yes':'Formulars No';
							$to_txt = $agent_data[$value]=='Y'?'Yes':'No';
							break;

							case 'training_script':
							$from_txt = $agentDetails->$value=='Y'?'Training Script Yes':'Training Script No';
							$to_txt = $agent_data[$value]=='Y'?'Yes':'No';
							break;
						}

						$record_data['agent_history_id'] = $history_id;
						$record_data['field_name'] = $value;
						$record_data['from_txt'] = $from_txt;
						$record_data['to_txt'] = $to_txt;
						$this->userdata->setTable(TABLE_AGENT_HISTORY_RECORDS)->insert($record_data);
						$this->userdata->unsetTable();
					}
				}
				//=======FOR AGENT EDIT HISTORY=======//




				/*======== INSERT MULTIPLE AGENT DOCUMENTS UPLOADS =======*/
				if($_FILES['document']['name'][0])
				{
					$docCount = count($_FILES['document']['name']);
					for($i=0; $i<$docCount; $i++)
					{
						$_FILES['userFile']['name'] = $_FILES['document']['name'][$i];
						$_FILES['userFile']['type'] = $_FILES['document']['type'][$i];
						$_FILES['userFile']['tmp_name'] = $_FILES['document']['tmp_name'][$i];
						$_FILES['userFile']['error'] = $_FILES['document']['error'][$i];
						$_FILES['userFile']['size'] = $_FILES['document']['size'][$i];

						$config['upload_path'] = UPLOAD_PATH_URL.'agents_document/';
						$config['allowed_types'] = '*';
						$config['file_name'] = time().str_replace(' ','-',$_FILES['userFile']['name']);
						$this->load->library('upload');
						$this->upload->initialize($config);
						$this->upload->do_upload('userFile');
						$doc_file = $this->upload->data();
						$displayName = str_replace($doc_file['file_ext'], '', $doc_file['client_name']);


						$document_data['agent_id'] = $input_data['id'];
						$document_data['file_name'] = $doc_file['file_name'];
						$document_data['display_name'] = $displayName;
						$document_data['status'] = 'Y';
						$document_data['postedTime'] = time();

						$this->userdata->setTable(TABLE_AGENT_DOCUMENTS)->insert($document_data);
						$this->userdata->unsetTable();
					}
				}
				/*======== INSERT MULTIPLE AGENT DOCUMENTS UPLOADS =======*/



				$provision_data['user_id'] = $input_data['id'];
				for($i=0;$i<count($input_data['provision_general_percent']);$i++)
				{
					$provision_data['type'] = 1;
					$provision_data['provision_percent'] = $input_data['provision_general_percent'][$i];
					$provision_data['provision_start'] = strtotime(str_replace('/','-',$input_data['provision_general_start'][$i]));
					$provision_data['provision_end'] = strtotime(str_replace('/','-',$input_data['provision_general_end'][$i]));
					if($input_data['provision_general_id'][$i] > 0) {
						$this->userdata->setTable(TABLE_AGENT_PROVISION)->update($provision_data, array('id'=>$input_data['provision_general_id'][$i]));
						$this->userdata->unsetTable();

					} else {
						$this->userdata->setTable(TABLE_AGENT_PROVISION)->insert($provision_data);
						$this->userdata->unsetTable();
					}
				}

				for($i=0;$i<count($input_data['provision_court_percent']);$i++)
				{
					$provision_data['type'] = 2;
					$provision_data['provision_percent'] = $input_data['provision_court_percent'][$i];
					$provision_data['provision_start'] = strtotime(str_replace('/','-',$input_data['provision_court_start'][$i]));
					$provision_data['provision_end'] = strtotime(str_replace('/','-',$input_data['provision_court_end'][$i]));
					
					if($input_data['provision_court_id'][$i] > 0) {
						$this->userdata->setTable(TABLE_AGENT_PROVISION)->update($provision_data, array('id'=>$input_data['provision_court_id'][$i]));
						$this->userdata->unsetTable();
					} else {
						$this->userdata->setTable(TABLE_AGENT_PROVISION)->insert($provision_data);
						$this->userdata->unsetTable();
					}
				}

				for($i=0;$i<count($input_data['provision_central_percent']);$i++)
				{
					$provision_data['type'] = 3;
					$provision_data['provision_percent'] = $input_data['provision_central_percent'][$i];
					$provision_data['provision_start'] = strtotime(str_replace('/','-',$input_data['provision_central_start'][$i]));
					$provision_data['provision_end'] = strtotime(str_replace('/','-',$input_data['provision_central_end'][$i]));
					
					if($input_data['provision_central_id'][$i] > 0) {
						$this->userdata->setTable(TABLE_AGENT_PROVISION)->update($provision_data, array('id'=>$input_data['provision_central_id'][$i]));
						$this->userdata->unsetTable();
					} else {
						$this->userdata->setTable(TABLE_AGENT_PROVISION)->insert($provision_data);
						$this->userdata->unsetTable();
					}
				}

				for($i=0;$i<count($input_data['provision_upper_percent']);$i++)
				{
					$provision_data['type'] = 4;
					$provision_data['provision_percent'] = $input_data['provision_upper_percent'][$i];
					$provision_data['provision_start'] = strtotime(str_replace('/','-',$input_data['provision_upper_start'][$i]));
					$provision_data['provision_end'] = strtotime(str_replace('/','-',$input_data['provision_upper_end'][$i]));
					
					if($input_data['provision_upper_id'][$i] > 0) {
						$this->userdata->setTable(TABLE_AGENT_PROVISION)->update($provision_data, array('id'=>$input_data['provision_upper_id'][$i]));
						$this->userdata->unsetTable();
					} else {
						$this->userdata->setTable(TABLE_AGENT_PROVISION)->insert($provision_data);
						$this->userdata->unsetTable();
					}
				}


				//$this->session->set_flashdata('success','Agent Updated Successfully!!');
				$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(406));
			}
			else
			{
				//$this->session->set_flashdata('error', 'Something Wrong Please Try Again.');
				$this->session->set_flashdata('error', $this->defaultdata->gradLanguageText(376));
			}
			
			redirect(base_url('admin/agent/information/'.$input_data['id']));

		}
	}


	public function information($id='')
	{
		
		$user_data = $this->userdata->fetchOne(array('id'=>$id, 'userType'=>2));
		if(count($user_data)==0)
		{
			redirect(base_url('admin/agent'));
		}

		$user_data->manager_name = $user_data->parent_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$user_data->parent_id), 'name'):'';

		/*====== INFORMATION SECTION ======*/
		$agent_data = $this->userdata->setTable(TABLE_AGENTS)->fetchOne(array('user_id'=>$id));
		$this->userdata->unsetTable();

		$agent_data->countryName = $agent_data->country_id?$this->defaultdata->getField(TABLE_COUNTRIES, array('idCountry'=>$agent_data->country_id), 'countryName'):'';
		
		$this->data['provision_general'] = $this->userdata->setTable(TABLE_AGENT_PROVISION)->fetchAll(array('user_id'=>$id, 'type'=>1));
		$this->userdata->unsetTable();

		$this->data['provision_court'] = $this->userdata->setTable(TABLE_AGENT_PROVISION)->fetchAll(array('user_id'=>$id, 'type'=>2));
		$this->userdata->unsetTable();

		$this->data['central_provision'] = $this->userdata->setTable(TABLE_AGENT_PROVISION)->fetchAll(array('user_id'=>$id, 'type'=>3));
		$this->userdata->unsetTable();

		$this->data['upper_provision'] = $this->userdata->setTable(TABLE_AGENT_PROVISION)->fetchAll(array('user_id'=>$id, 'type'=>4));
		$this->userdata->unsetTable();

		/*====== INFORMATION SECTION ======*/
		//echo '<pre>'; print_r($this->data['bonusData']); die;

		$this->data['user_data'] = $user_data;
		$this->data['agent_data'] = $agent_data;
		$this->data['inner_header'] = $this->load->view('admin/agent/header',$this->data,TRUE);
		$this->load->view('admin/agent/information',$this->data);
	}

	public function client($id='')
	{
		
		$user_data = $this->userdata->fetchOne(array('id'=>$id, 'userType'=>2));
		if(count($user_data)==0)
		{
			redirect(base_url('admin/agent'));
		}

		$agent_MLM_ids = $this->userdata->getAllUsersId($id);

		$client_data = $this->userdata->getClientDetailsByAgentMLMids($agent_MLM_ids);
		foreach ($client_data as $client) {
			$client->consultant_name = $client->consultant?$this->defaultdata->getField(TABLE_USER, array('id'=>$client->consultant), 'name'):'';

			$client->agent_name = $client->agent?$this->defaultdata->getField(TABLE_USER, array('id'=>$client->agent), 'name'):'';

			$client->type_of_accident_name = $client->type_of_accident?$this->defaultdata->getField(TABLE_CLIENT_SELECT_OPTION, array('id'=>$client->type_of_accident), 'name'):'';
			$client->status_of_case_name = $client->status_of_case?$this->defaultdata->getField(TABLE_CLIENT_SELECT_OPTION, array('id'=>$client->status_of_case), 'name'):'';
		}
		

		$this->data['user_data'] = $user_data;
		$this->data['client_data'] = $client_data;
		$this->data['inner_header'] = $this->load->view('admin/agent/header',$this->data,TRUE);
		$this->load->view('admin/agent/clients',$this->data);
	}

	public function documents($id='')
	{
		
		$user_data = $this->userdata->fetchOne(array('id'=>$id, 'userType'=>2));
		if(count($user_data)==0)
		{
			redirect(base_url('admin/agent'));
		}

		

		$this->data['document_data'] = $this->userdata->setTable(TABLE_AGENT_DOCUMENTS)->fetchAll(array('agent_id'=>$id));
		$this->userdata->unsetTable();
		
		//echo '<pre>'; print_r($this->data['bonusData']); die;

		$this->data['user_data'] = $user_data;
		$this->data['inner_header'] = $this->load->view('admin/agent/header',$this->data,TRUE);
		$this->load->view('admin/agent/documents',$this->data);
	}


	public function notes($id='')
	{
		
		$user_data = $this->userdata->fetchOne(array('id'=>$id, 'userType'=>2));
		if(count($user_data)==0)
		{
			redirect(base_url('admin/agent'));
		}
		
		$this->data['agent_note'] = $this->userdata->setTable(TABLE_NOTES)->fetchAll(array('user_id'=>$id), array('id'=>'DESC'));
		$this->userdata->unsetTable();
		
		$this->data['single_note'] = $this->load->view('admin/agent/single_note',$this->data,TRUE);
		//echo '<pre>'; print_r($this->data['bonusData']); die;

		$this->data['user_data'] = $user_data;
		$this->data['inner_header'] = $this->load->view('admin/agent/header',$this->data,TRUE);
		$this->load->view('admin/agent/notes',$this->data);
	}

	public function payment($id='')
	{
		
		$user_data = $this->userdata->fetchOne(array('id'=>$id, 'userType'=>2));
		if(count($user_data)==0)
		{
			redirect(base_url('admin/agent'));
		}

		$agent_data = $this->userdata->setTable(TABLE_AGENTS)->fetchOne(array('user_id'=>$id));
		
		$comment_provision = $this->userdata->setTable(TABLE_CLIENT_COMMENT_PROVISION)->fetchAll(array('agent_id'=>$id, 'is_active'=>'Y'), array('id'=>'DESC'));
		$this->userdata->unsetTable();
		foreach ($comment_provision as $provision)
		{
			$provision->client_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$provision->client_id), 'name');
			
			$provision->client_agent_id = $this->defaultdata->getField(TABLE_CLIENTS, array('user_id'=>$provision->client_id), 'agent');

			$provision->client_agent_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$provision->client_agent_id), 'name');

			$provision->invoice_data = '';
			if($provision->agent_invoice_id)
			{
				$provision->invoice_data = $this->userdata->setTable(TABLE_AGENT_INVOICES)->fetchOne(array('id'=>$provision->agent_invoice_id));
				$this->userdata->unsetTable();
			}
		}

		$agent_bonus = $this->userdata->setTable(TABLE_CLIENT_AGENT_BONUS)->fetchAll(array('agent_id'=>$id), array('id'=>'DESC'));
		$this->userdata->unsetTable();

		foreach ($agent_bonus as $bonus)
		{
			$bonus->settle_data = $this->userdata->setTable(TABLE_CLIENT_AGENT_BONUS_SETTLE)->fetchAll(array('agent_bonus_id'=>$bonus->id), array('id'=>'DESC'));
			$this->userdata->unsetTable();

			$bonus->client_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$bonus->client_id), 'name');

			$bonus->invoice_data = '';
			if($bonus->agent_invoice_id)
			{
				$bonus->invoice_data = $this->userdata->setTable(TABLE_AGENT_INVOICES)->fetchOne(array('id'=>$bonus->agent_invoice_id));
				$this->userdata->unsetTable();
			}
		}

		$agent_target = $this->userdata->setTable(TABLE_AGENT_TARGET)->fetchAll(array('agent_id'=>$id, 'status'=>'G'));
		$this->userdata->unsetTable();
		foreach ($agent_target as $target)
		{
			$target->agent_from_structure_name = '';
			if($target->case_source==3)
			{
				$target->agent_from_structure_name = $target->agent_from_structure?$this->defaultdata->getField(TABLE_USER, array('id'=>$target->agent_from_structure), 'name'):'';
			}


			$target->invoice_data = '';
			if($target->agent_invoice_id)
			{
				$target->invoice_data = $this->userdata->setTable(TABLE_AGENT_INVOICES)->fetchOne(array('id'=>$target->agent_invoice_id));
				$this->userdata->unsetTable();
			}
		}
		//echo '<pre>'; print_r($agent_target); die;

		$this->data['user_data'] = $user_data;
		$this->data['agent_data'] = $agent_data;
		$this->data['comment_provision'] = $comment_provision;
		$this->data['agent_bonus'] = $agent_bonus;
		$this->data['agent_target'] = $agent_target;

		$this->data['inner_header'] = $this->load->view('admin/agent/header',$this->data,TRUE);
		$this->load->view('admin/agent/payment',$this->data);
	}

	public function communicator($id='')
	{
		
		$user_data = $this->userdata->fetchOne(array('id'=>$id, 'userType'=>2));
		if(count($user_data)==0)
		{
			redirect(base_url('admin/agent'));
		}
		
		$user_id = $this->session->userdata('usrid');
		$this->chatdata->messageRead($id, $user_id);
		
		$chat_message = $this->chatdata->userMessages($id);
		foreach ($chat_message as $chat) {
			$chat->form_user_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$chat->from_user_id), 'name');
		}
		//echo '<pre>'; print_r($chat_message); die;

		$this->data['user_data'] = $user_data;
		$this->data['chat_message'] = $chat_message;

		$this->data['singleMessage'] = $this->load->view('admin/agent/single_message',$this->data,TRUE);
		$this->data['inner_header'] = $this->load->view('admin/agent/header',$this->data,TRUE);
		$this->load->view('admin/agent/communicator',$this->data);
	}

	public function mlmstructure($id='')
	{
		
		$user_data = $this->userdata->fetchOne(array('id'=>$id, 'userType'=>2));
		if(count($user_data)==0)
		{
			redirect(base_url('admin/agent'));
		}

		$agent_data = $this->userdata->fetchAll(array('id'=>$id, 'userType'=>2));
		foreach ($agent_data as $agent) {
			$agent->mlm_agent_data = $this->userdata->getAgentInMLMStructure($id);
		}
		
		$this->data['mlm_structure_view'] = $this->nestedArrayToList($agent_data);
		//echo '<pre>'; print_r($agent_data); die;

		$this->data['user_data'] = $user_data;
		//$this->data['chat_message'] = $chat_message;

		$this->data['inner_header'] = $this->load->view('admin/agent/header',$this->data,TRUE);
		$this->load->view('admin/agent/mlm_structure',$this->data);
	}

	public function nestedArrayToList($dataArray)
	{
		$html = '';
		$html .= '<ol class="dd-list">';
		foreach ($dataArray as $key => $value) {
			$profile_url = base_url('admin/agent/information/'.$value->id);
			$html .= '<li class="dd-item dd3-item">
						  <div class=" dd3-handle"></div>
		                  <div class="dd3-content">
		                      <a href="'.$profile_url.'" target="_blank">'.$value->name.'</a>
		                  </div>';
			        
			        if(count($value->mlm_agent_data)>0)
			        {
			        	$html .= $this->nestedArrayToList($value->mlm_agent_data);
			        }

	        $html .= '</li>';
		}
		$html .= '</ol>';

		return $html;
	}


	public function history($id='')
	{
		
		$user_data = $this->userdata->fetchOne(array('id'=>$id, 'userType'=>2));
		if(count($user_data)==0)
		{
			redirect(base_url('admin/agent'));
		}


		$this->data['historyData'] = $this->userdata->setTable(TABLE_AGENT_HISTORY)->fetchAll(array('agent_id'=>$id), array('id'=>'DESC'));
		$this->userdata->unsetTable();

		foreach ($this->data['historyData'] as $history) {
			$history->user_name = $history->user_id?$this->defaultdata->getField(TABLE_USER, array('id'=>$history->user_id), 'name'):'';
			
			$recordData = $this->userdata->setTable(TABLE_AGENT_HISTORY_RECORDS)->fetchAll(array('agent_history_id'=>$history->id));
			$this->userdata->unsetTable();
			$history->action_type_name = '';
			$history->changesData = '';
			switch($history->action_type)
			{
				case 1:
				$history->action_type_name = 'Information Edit';
				if(count($recordData)>0)
				{
					foreach ($recordData as $key => $value) {
						$history->changesData .= '<p>From: "'.$value->from_txt.'" To: "'.$value->to_txt.'"</p>';
					}
				}
				break;

				case 2:
				$history->action_type_name = 'Download Document';
				if(count($recordData)>0)
				{
					foreach ($recordData as $key => $value) {
						$history->changesData .= $value->other_txt;
					}
				}
				break;

				case 3:
				$history->action_type_name = 'Delete Document';
				if(count($recordData)>0)
				{
					foreach ($recordData as $key => $value) {
						$history->changesData .= $value->other_txt;
					}
				}
				break;

				case 4:
				$history->action_type_name = 'Edit Note';
				if(count($recordData)>0)
				{
					foreach ($recordData as $key => $value) {
						$history->changesData .= '<p>From: "'.$value->from_txt.'" To: "'.$value->to_txt.'"</p>';
					}
				}
				break;

				case 5:
				$history->action_type_name = 'Delete Note';
				if(count($recordData)>0)
				{
					foreach ($recordData as $key => $value) {
						$history->changesData .= $value->other_txt;
					}
				}
				break;

				
			}
		}

		$this->data['user_data'] = $user_data;
		$this->data['inner_header'] = $this->load->view('admin/agent/header',$this->data,TRUE);
		$this->load->view('admin/agent/history',$this->data);

	}

	public function chengeStatus()
	{
		$post_data = $this->input->post();
		$update_user = $this->userdata->update(array('status'=>$post_data['status']), array('id'=>$post_data['user_id']));
		echo 1;
	}

	public function deleteUser()
	{
		$user_id = $this->input->post('user_id');
		//$this->userdata->update(array('status'=>'N'), array('id'=>$user_id));
		
		$agentData = $this->userdata->setTable(TABLE_AGENTS)->fetchOne(array('user_id'=>$user_id));
		$this->userdata->unsetTable();
		@unlink(UPLOAD_PATH_URL.'agents_document/'.$agentData->document);

		$this->userdata->delete(array('id'=>$user_id));
		$this->defaultdata->delete(TABLE_AGENTS, array('user_id'=>$user_id));
		$this->defaultdata->delete(TABLE_AGENT_PROVISION, array('user_id'=>$user_id));
		$this->defaultdata->delete(TABLE_USERLOGIN_RECORD, array('user_id'=>$user_id));
		
		echo 1;
	}

	public function chengeFormulars()
	{
		$post_data = $this->input->post();
		$update_user = $this->userdata->setTable(TABLE_AGENTS)->update(array('formulars'=>$post_data['formulars']), array('user_id'=>$post_data['user_id']));
		$this->userdata->unsetTable();
		echo 1;
	}

	public function chengeTrainingScript()
	{
		$post_data = $this->input->post();
		$update_user = $this->userdata->setTable(TABLE_AGENTS)->update(array('training_script'=>$post_data['training_script']), array('user_id'=>$post_data['user_id']));
		$this->userdata->unsetTable();
		echo 1;
	}

	public function agentCardLiveEdit()
	{
		
		//echo '<pre>'; print_r($this->input->post()); die;
		$return_data['has_error'] = 0;
		$user_id = $this->input->post('user_id');
		$userDetails = $this->userdata->fetchOne(array('id'=>$user_id));
		$field_name = $this->input->post('field_name');
		$new_value = $this->input->post('new_value');
		$field_type = $this->input->post('field_type');
		$table_name = $this->input->post('table_name');
		if($table_name==TABLE_AGENTS)
		{
			$cond = array('user_id'=>$user_id);

			$agentData = $this->userdata->setTable(TABLE_AGENTS)->fetchOne($cond);
			$this->userdata->unsetTable();
			$table_constant_name = 'TABLE_AGENTS';
		}
		elseif($table_name==TABLE_USER)
		{
			$cond = array('id'=>$user_id);

			$agentData = $this->userdata->fetchOne($cond);
			$this->userdata->unsetTable();
			$table_constant_name = 'TABLE_USER';
		}
		
		if($field_type=='date')
		{
			$new_value = strtotime(str_replace('/','-',$new_value));
			$return_data['new_value'] = date('d/m/Y', $new_value);
		}
		elseif($field_type=='percentage')
		{
			$return_data['new_value'] = $new_value>0?$new_value.'%':'';
		}
		elseif($field_type=='textarea')
		{
			$return_data['new_value'] = nl2br($new_value);
		}
		else
		{
			$return_data['new_value'] = $new_value;
		}

		if($field_name=='userName' && $userDetails->userName!=$new_value)
		{
			$emailCount = $this->defaultdata->count_record(TABLE_USER, array('userName'=>$new_value));
			if($emailCount>0)
			{
				$return_data['has_error'] = 1;
				//$return_data['error_msg'] = 'Username already exists!!';
				$return_data['error_msg'] = $this->defaultdata->gradLanguageText(407);
			}
		}

		if($field_name=='emailAddress' && $userDetails->emailAddress!=$new_value)
		{
			$emailCount = $this->defaultdata->count_record(TABLE_USER, array('emailAddress'=>$new_value));
			if($emailCount>0)
			{
				$return_data['has_error'] = 1;
				//$return_data['error_msg'] = 'Email-id already exists!!';
				$return_data['error_msg'] = $this->defaultdata->gradLanguageText(385);
			}
		}
		if($return_data['has_error'] == 0)
		{
			$agent_data[$field_name] = $new_value;
			$this->userdata->setTable($table_name)->update($agent_data, $cond);
			$this->userdata->unsetTable();
		}

		//=======FOR AGENT EDIT HISTORY=======//
		if($agentData->$field_name != $new_value)
		{
			$history_data['agent_id'] = $user_id;
			$history_data['user_id'] = $this->session->userdata('usrid');
			$history_data['source_table'] = $table_constant_name;
			$history_data['source_id'] = $user_id;
			$history_data['action_type'] = 1;
			$history_data['postedtime'] = time();
			$history_id = $this->userdata->setTable(TABLE_AGENT_HISTORY)->insert($history_data);
			$this->userdata->unsetTable();

			$from_txt = $agentData->$field_name;
			$to_txt = $new_value;

			if($field_type=='date')
			{
				$from_txt = date('d/m/Y', $agentData->$field_name);
				$to_txt = date('d/m/Y', $new_value);
			}
			
			$record_data['agent_history_id'] = $history_id;
			$record_data['field_name'] = $field_name;
			$record_data['from_txt'] = $from_txt;
			$record_data['to_txt'] = $to_txt;
			$this->userdata->setTable(TABLE_AGENT_HISTORY_RECORDS)->insert($record_data);
			$this->userdata->unsetTable();
		}
		//=======FOR AGENT EDIT HISTORY=======//
		
		echo json_encode($return_data);
	}
	

	public function addNewProvision()
	{
		
		//echo '<pre>'; print_r($this->input->post()); die;
		$return_data['has_error'] = 1;
		$post_data = $this->input->post();

		$input_data['type'] = $post_data['type'];
		$input_data['user_id'] = $post_data['user_id'];
		$input_data['provision_percent'] = $post_data['provision_percent'];
		$input_data['provision_start'] = strtotime(str_replace('/','-',$post_data['provision_start']));
		$input_data['provision_end'] = strtotime(str_replace('/','-',$post_data['provision_end']));

		$lastId = $this->userdata->setTable(TABLE_AGENT_PROVISION)->insert($input_data);
		$this->userdata->unsetTable();
		
		if($lastId)
		{
			$return_data['has_error'] = 0;
			$return_data['type'] = $post_data['type'];

			$this->data['provition_data'] = $this->userdata->setTable(TABLE_AGENT_PROVISION)->fetchAll(array('id'=>$lastId));
			$this->userdata->unsetTable();

			$return_data['single_agent_provision'] = $this->load->view('admin/agent/single_agent_provision',$this->data,TRUE);

		}
		
		echo json_encode($return_data);
	}

	public function deleteAgentDocument()
	{
		$return_data['has_error'] = 0;
		$id = $this->input->post('id');
		$documentData = $this->userdata->setTable(TABLE_AGENT_DOCUMENTS)->fetchOne(array('id'=>$id));
		$this->userdata->unsetTable();
		if(count($documentData)>0)
		{
			//=======FOR CLIENT HISTORY=======//
			$history_data['agent_id'] = $documentData->agent_id;
			$history_data['user_id'] = $this->session->userdata('usrid');
			$history_data['source_table'] = 'TABLE_AGENT_DOCUMENTS';
			$history_data['source_id'] = $id;
			$history_data['action_type'] = 3;
			$history_data['postedtime'] = time();
			$history_id = $this->userdata->setTable(TABLE_AGENT_HISTORY)->insert($history_data);
			$this->userdata->unsetTable();

			$record_data['agent_history_id'] = $history_id;
			$record_data['other_txt'] = 'Document: "'.$documentData->display_name.'" is deleted';
			$this->userdata->setTable(TABLE_AGENT_HISTORY_RECORDS)->insert($record_data);
			$this->userdata->unsetTable();
			//=======FOR CLIENT HISTORY=======//


			@unlink(UPLOAD_PATH_URL.'agents_document/'.$documentData->file_name);
			$this->userdata->setTable(TABLE_AGENT_DOCUMENTS)->delete(array('id'=>$id));
			$this->userdata->unsetTable();
		}
		else
		{
			$return_data['has_error'] = 1;
		}
		
		echo json_encode($return_data);
	}

	public function postNote()
	{
		
		//echo '<pre>'; print_r($this->input->post()); die;
		$return_data['has_error'] = 1;
		$post_data = $this->input->post();
		$id = $post_data['id'];

		$noteData = $this->userdata->setTable(TABLE_NOTES)->fetchOne(array('id'=>$id));
		$this->userdata->unsetTable();

		if($id)
		{
			$input_data['title'] = $post_data['title'];
			$input_data['description'] = $post_data['description'];

			$this->userdata->setTable(TABLE_NOTES)->update($input_data, array('id'=>$id));
			$this->userdata->unsetTable();


			//=======FOR AGENT EDIT HISTORY=======//
				$history_data['agent_id'] = $noteData->user_id;
				$history_data['user_id'] = $this->session->userdata('usrid');
				$history_data['source_table'] = 'TABLE_NOTES';
				$history_data['source_id'] = $id;
				$history_data['action_type'] = 4;
				$history_data['postedtime'] = time();
				$history_id = $this->userdata->setTable(TABLE_AGENT_HISTORY)->insert($history_data);
				$this->userdata->unsetTable();

				$notefields = $this->db->get(TABLE_NOTES)->list_fields();
				$notEditNote = array('id', 'user_id', 'created_by', 'postedtime');
				foreach ($notefields as $key => $value)
				{
					if(!in_array($value, $notEditNote) && isset($input_data[$value]) && $noteData->$value != $input_data[$value])
					{
						$record_data['agent_history_id'] = $history_id;
						$record_data['field_name'] = $value;
						$record_data['from_txt'] = $noteData->$value;
						$record_data['to_txt'] = $input_data[$value];
						$this->userdata->setTable(TABLE_AGENT_HISTORY_RECORDS)->insert($record_data);
						$this->userdata->unsetTable();
					}
				}
				//=======FOR AGENT EDIT HISTORY=======//

			$lastId = $id;
			$return_data['mode'] = 'edit';
		}
		else
		{
			$input_data['user_id'] = $post_data['user_id'];
			$input_data['created_by'] = $this->session->userdata('usrid');
			$input_data['title'] = $post_data['title'];
			$input_data['description'] = $post_data['description'];
			$input_data['postedtime'] = time();

			$lastId = $this->userdata->setTable(TABLE_NOTES)->insert($input_data);
			$this->userdata->unsetTable();
			$return_data['mode'] = 'add';
		}
		
		if($lastId)
		{
			$return_data['has_error'] = 0;
			$return_data['lastId'] = $lastId;

			$this->data['agent_note'] = $this->userdata->setTable(TABLE_NOTES)->fetchAll(array('id'=>$lastId));
			$this->userdata->unsetTable();

			$return_data['single_note'] = $this->load->view('admin/agent/single_note',$this->data,TRUE);

		}
		
		echo json_encode($return_data);
	}

	public function editMyNote()
	{
		$id = $this->input->post('id');
		$this->data['agent_note'] = $this->userdata->setTable(TABLE_NOTES)->fetchOne(array('id'=>$id));
		$this->userdata->unsetTable();

		$return_data['editNoteModel'] = $this->load->view('admin/agent/edit_note_modal',$this->data,TRUE);

		echo json_encode($return_data);
	}

	public function deleteMyNote()
	{
		$return_data['has_error'] = 0;
		$id = $this->input->post('id');

		//=======FOR AGENT NOTE DELETE HISTORY=======//
		$notedata = $this->userdata->setTable(TABLE_NOTES)->fetchOne(array('id'=>$id));
		$this->userdata->unsetTable();
		$history_data['agent_id'] = $notedata->user_id;
		$history_data['user_id'] = $this->session->userdata('usrid');
		$history_data['source_table'] = 'TABLE_NOTES';
		$history_data['source_id'] = $id;
		$history_data['action_type'] = 5;
		$history_data['postedtime'] = time();
		$history_id = $this->userdata->setTable(TABLE_AGENT_HISTORY)->insert($history_data);
		$this->userdata->unsetTable();
		
		$record_data['agent_history_id'] = $history_id;
		$record_data['other_txt'] = 'Note: "'.$notedata->title.'" is deleted';
		$this->userdata->setTable(TABLE_AGENT_HISTORY_RECORDS)->insert($record_data);
		$this->userdata->unsetTable();
		//=======FOR AGENT NOTE DELETE HISTORY=======//

		$this->userdata->setTable(TABLE_NOTES)->delete(array('id'=>$id));
		$this->userdata->unsetTable();
		$return_data['lastId'] = $id;
		echo json_encode($return_data);
	}

	public function postChat()
	{
		$return_data = array();
		$post_data = $this->input->post();
		$input_data['from_user_id'] = $this->session->userdata('usrid');
		$input_data['to_user_id'] = $post_data['to_user_id'];
		$input_data['message'] = $post_data['message'];
		$input_data['type'] = 'T';
		$input_data['postedTime'] = time();
		$lastId = $this->chatdata->insert($input_data);
		if($lastId)
		{
			$this->data['chat_message'] = $this->chatdata->fetchAll(array('id'=>$lastId));
			foreach ($this->data['chat_message'] as $chat) {
				$chat->form_user_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$chat->from_user_id), 'name');
			}

			$return_data['singleMessage'] = $this->load->view('admin/agent/single_message',$this->data,TRUE);
		}
		echo json_encode($return_data);
	}


	public function loadUnreadMessages()
	{
		$return_data = array();
		$post_data = $this->input->post();
		$to_user_id = $this->session->userdata('usrid');
		$from_user_id = $post_data['from_user_id'];
		
		$this->data['chat_message'] = $this->chatdata->loadUnreadMessages($to_user_id, $from_user_id);
		foreach ($this->data['chat_message'] as $chat) {
			$chat->form_user_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$chat->from_user_id), 'name');
		}

		$return_data['singleMessage'] = $this->load->view('admin/agent/single_message',$this->data,TRUE);
		$this->chatdata->messageRead($from_user_id, $to_user_id);
		echo json_encode($return_data);
	}

	public function agentInvoice($id='')
	{
		
		$this->data['invoice_data'] = $this->userdata->setTable(TABLE_AGENT_INVOICES)->fetchOne(array('id'=>$id));
		//print_r($this->data['invoice_data']);die;
		$this->userdata->unsetTable();
		$user_id = $this->data['invoice_data']->agent_id;
		if(count($this->data['invoice_data'])==0)
		{
			redirect(base_url('admin/agent/payment/'.$user_id));
		}

		$this->data['user_data'] = $this->userdata->fetchOne(array('id'=>$this->data['invoice_data']->agent_id));

		$this->data['agent_data'] = $this->userdata->setTable(TABLE_AGENTS)->fetchOne(array('user_id'=>$this->data['invoice_data']->agent_id));
		$this->userdata->unsetTable();
  
		$this->data['payment_data'] = $this->userdata->setTable(TABLE_AGENT_INVOICE_PAYMENTS)->fetchAll(array('agent_invoice_id'=>$id));
		$this->userdata->unsetTable();
        
        $user_cond['id'] = 1;
        $this->load->model('invoicedetails');
        $this->data['invoice_details'] = $this->invoicedetails->grabInvoiceData($user_cond);
        //print_r($this->data['payment_data']);exit;
		$this->load->view('admin/agent/invoice',$this->data);
	}

	public function changeAgentPaymentStatus($id='')
	{
		if($id)
		{
			$update_data['paid_status'] = 'Y';
			$this->userdata->setTable(TABLE_AGENT_INVOICES)->update($update_data, array('id'=>$id));
			$this->userdata->unsetTable();
			//$this->session->set_flashdata('success', 'Payment Status Change Successfully!!');
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(408));
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function uploadNonCompanyAdminFile()
	{
		$input_data = $this->input->post();
		if($input_data['id'])
		{
			$invoice_data = array();
			if($_FILES['invoice_file']['name'])
			{
				$config['upload_path'] = UPLOAD_PATH_URL.'non_company_invoice/admin_up_file/';
				$config['allowed_types'] = '*';
				$config['file_name'] = time().str_replace(' ','-',$_FILES['invoice_file']['name']);
				$this->load->library('upload');
				$this->upload->initialize($config);
				$this->upload->do_upload('invoice_file');
				$upload_file = $this->upload->data();

				$invoice_data['admin_up_file'] = $upload_file['file_name'];
			}

			$this->userdata->setTable(TABLE_AGENT_INVOICES)->update($invoice_data, array('id'=>$input_data['id']));
			$this->userdata->unsetTable();

			//$this->session->set_flashdata('success','Invoice Added Successfully!!');
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(401));
			
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function downloadAgentDoc($id)
	{
		$this->load->helper('download');
		$cond = array();
		$cond['id'] = $id;
		$documentData = $this->userdata->setTable(TABLE_AGENT_DOCUMENTS)->fetchOne($cond);
		$this->userdata->unsetTable();

		//=======FOR CLIENT HISTORY=======//
		$history_data['agent_id'] = $documentData->agent_id;
		$history_data['user_id'] = $this->session->userdata('usrid');
		$history_data['source_table'] = 'TABLE_AGENT_DOCUMENTS';
		$history_data['source_id'] = $id;
		$history_data['action_type'] = 2;
		$history_data['postedtime'] = time();
		$history_id = $this->userdata->setTable(TABLE_AGENT_HISTORY)->insert($history_data);
		$this->userdata->unsetTable();
		
		$record_data['agent_history_id'] = $history_id;
		$record_data['other_txt'] = 'Document: "'.$documentData->display_name.'" is downloaded';
		$this->userdata->setTable(TABLE_AGENT_HISTORY_RECORDS)->insert($record_data);
		$this->userdata->unsetTable();
		//=======FOR CLIENT HISTORY=======//

		if($documentData->file_name)
		{
			$pth = file_get_contents(DEFAULT_ASSETS_URL."upload/agents_document/".$documentData->file_name);
			$ext = end(explode('.', $documentData->file_name));
			$nme = $documentData->display_name.'.'.$ext;
			force_download($nme, $pth);
		}
	}

	public function checkemailID()
	{
		$emailAddress = $this->input->post('emailAddress');
		$data = $this->userdata->setTable(TABLE_USER)->fetchOne(array('emailAddress'=>$emailAddress));
		if(count($data)>0){
			$return_data='1';
		}else{
			$return_data='0';
		}
		echo json_encode($return_data);
	}
	public function checkusername()
	{
		$userName  = $this->input->post('userName');
		$data = $this->userdata->setTable(TABLE_USER)->fetchOne(array('userName '=>$userName ));
		if(count($data)>0){
			$return_data='1';
		}else{
			$return_data='0';
		}
		echo json_encode($return_data);
	}

	public function add_statements()
    {
        //$upload_dir = PUBLIC_PATH.'/upload/invoices/examples';
		$upload_dir = UPLOAD_PATH_URL.'agents_document/invoices/examples';
		
        $preparedFiles = [];
        if(is_dir($upload_dir)){
            $files = scandir($upload_dir);
            if(!empty($files)){
                unset($files[0]);
                unset($files[1]);
            }
            foreach($files as $fileName){
                $pos = strrpos($fileName, '.');
                $preparedFiles[substr($fileName, 0, $pos)] = $fileName;
            }
        }

        $this->data['files'] = $preparedFiles;
        $this->load->view('admin/agent/add_statements',$this->data);
    }

    public function postdocumentfile()
    {

        $return_data = [];
        $imgid = [];
        $file_html = '';
        $searching_file_type = $_POST['doc_type'];

        if($_FILES['document']['name'][0])
        {

                //$upload_dir = PUBLIC_PATH.'/upload/invoices/examples';
				$upload_dir = UPLOAD_PATH_URL.'agents_document/invoices/examples';

                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);//true - recursive creating


                $dir = opendir($upload_dir);

            $found_file = '';

                while(($dir_file = readdir($dir)) !== false)
                {
                    $found_file = strstr($dir_file, $searching_file_type);
                    if($found_file) break;
                }

                closedir($dir);

                if($found_file && file_exists($upload_dir.'/'.$found_file)){
                    unlink($upload_dir.'/'.$found_file);
                }

                $config['upload_path'] = $upload_dir.'/';
                $config['allowed_types'] = '*';

                preg_match('/\.[^\.]+$/', $_FILES['document']['name'], $matches);

                $file_n = $searching_file_type.$matches[0];

                $config['file_name'] = $file_n;
                $this->load->library('upload');
                $this->upload->initialize($config);
                $this->upload->do_upload('document');

                if($this->upload->display_errors == ''){

                    $src='<i class="fa fa-file-pdf-o"></i> '.$file_n;

                    $file_html .= '<div class="uploadedfiles_'.$searching_file_type.'" id="'.$searching_file_type.'"><input type="hidden" value="'.$file_n.'" name="'.$searching_file_type.'">'.$src.'<a href="javascript:void(0);" onclick="removePreviewImage(\''.$searching_file_type.'\')"  class="closefile closefile_'.$searching_file_type.'">&times;</a></div>';

                }else{
                    $return_data['success'] = 0;
                    echo json_encode($return_data);
                }

//            }
            $return_data['success'] = 1;
            $return_data['imgid'] = $imgid;
            $return_data['file_html'] = $file_html;
            $return_data['file_name'] = $file_n;
            $return_data['doc_type'] = $searching_file_type;

            echo json_encode($return_data);
        }else{
            $return_data['success']=0;
            echo json_encode($return_data);
        }

    }

    public function deldocfile()
    {
        $searching_file_type = $this->input->post('doc');

        //$upload_dir = PUBLIC_PATH.'/upload/invoices/examples';
		$upload_dir = UPLOAD_PATH_URL.'agents_document/invoices/examples';

        $dir = opendir($upload_dir);

        $found_file = false;

        while(($dir_file = readdir($dir)) !== false)
        {
            $found_file = strstr($dir_file, $searching_file_type);
             if($found_file) break;
        }

        closedir($dir);

        if($found_file && file_exists($upload_dir.'/'.$found_file)){
            unlink($upload_dir.'/'.$found_file);
        }

        echo 1;

    }














	
}


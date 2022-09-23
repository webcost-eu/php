<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chat extends CI_Controller {

	public $data=array();
	public $loggedin_method_arr = array('trainings');
	public $controller_arr = array('user','frontend','fbcontroller','gpluscontroller','routemanager','ajax');
	function __construct()
	{
		parent::__construct();
		$this->load->model('userdata');
		$this->load->model('chatdata');
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

	

	public function all_chat($id = '')
	{
		
	
		// $allUserData = $this->userdata->setTable(TABLE_USER)->fetchAll(array('status'=>'Y', 'userType'=>2));
		// $this->userdata->unsetTable();

		
		$user_data = $this->userdata->fetchOne(array('id'=>$id));
		// if(count($user_data)==0)
		// {
		// 	redirect(base_url('admin/client'));
		// }
		
		$user_id = $this->session->userdata('usrid');
		$this->chatdata->messageRead($id, $user_id);
		
		$chat_message = $this->chatdata->userMessages($id);
		foreach ($chat_message as $chat) {

			$chat->form_user_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$chat->from_user_id), 'name');
		}
		//echo '<pre>'; print_r($chat_message); die;

		$this->data['user_data'] = $user_data;
		$this->data['chat_message'] = $chat_message;

		$chat_user = array();

		$allChat = $this->chatdata->getChatList($user_id);

		foreach ($allChat as $key => $chat) {

			if($chat->from_user_id == $user_id){

				if(!in_array($chat->to_user_id, $chat_user)){

					$chat_user[] = $chat->to_user_id;
				}

				

			}else if($chat->to_user_id == $user_id){

				if(!in_array($chat->from_user_id, $chat_user)){

					$chat_user[] = $chat->from_user_id;
				}

				
			}
		}

			foreach ($chat_user as  $user) {

				$allUserData[] = $this->userdata->setTable(TABLE_USER)->fetchOne(array('id'=>$user));
			}
			// echo "<pre>";
			// print_r($allUserData); exit;
			
			
		

		$this->data['allUserData'] = $allUserData;
		$this->data['chat_left_panel'] = $this->load->view('admin/chat/chat_left_panel',$this->data,TRUE);
		$this->data['singleMessage'] = $this->load->view('admin/chat/single_message',$this->data,TRUE);
		$this->load->view('admin/chat/chat_box',$this->data);
	}


	public function communicator($id='')
	{
		
		$user_data = $this->userdata->fetchOne(array('id'=>$id, 'userType'=>2));
		if(count($user_data)==0)
		{
			redirect(base_url('admin/client'));
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

		$this->data['singleMessage'] = $this->load->view('admin/chat/single_message',$this->data,TRUE);
		$this->data['inner_header'] = '';
		//$this->data['inner_header'] = $this->load->view('admin/client/header',$this->data,TRUE);
		$this->load->view('admin/chat/communicator',$this->data);
	}



	public function addNewFolder()
	{
		$return_data = array();
		$input_data = $this->input->post();

		//print_r($input_data); exit;
		$id = $input_data['id'];
		if($id==0)
		{
			$post_data['folder_name'] = $input_data['folder_name'];
			$post_data['user_display_name'] = $input_data['user_display_name'];
			$post_data['folder_type'] = $input_data['folder_type'][0];
			$post_data['created_by'] = $this->session->userdata('usrid');
			$post_data['status'] = 'Y';
			$post_data['postedTime'] = time();
			$lastId = $this->userdata->setTable(TABLE_TRAINING_FOLDER)->insert($post_data);
			$this->userdata->unsetTable();
			$return_data['mode'] = 'add';
			$return_data['lastId'] = $lastId;
		}
		else
		{
			$post_data['folder_name'] = $input_data['folder_name'];
			$post_data['user_display_name'] = $input_data['user_display_name'];
			$post_data['folder_type'] = $input_data['folder_type'][0];
			$post_data['modifiedTime'] = time();
					
			$this->userdata->setTable(TABLE_TRAINING_FOLDER)->update($post_data, array('id'=>$id));
			$this->userdata->unsetTable();

			$lastId = $id;
			$return_data['mode'] = 'edit';
			$return_data['lastId'] = $lastId;
		}

		if($lastId)
		{
			$allFolderData = $this->userdata->setTable(TABLE_TRAINING_FOLDER)->fetchAll(array('id'=>$lastId), array('id'=>'DESC'));
			$this->userdata->unsetTable();

			foreach ($allFolderData as $folder) {
				$folder->documentCount = $this->userdata->setTable(TABLE_TRAINING_DOCUMENTS)->countRows(array('folder_id'=>$folder->id));
				$this->userdata->unsetTable();
			}

			$this->data['allFolderData'] = $allFolderData;
			
			$return_data['single_folder'] = $this->load->view('admin/trainings/single_folder',$this->data,TRUE);
		}

		$return_data['folderCounts'] = $this->userdata->setTable(TABLE_TRAINING_FOLDER)->countRows(array('status'=>'Y'));
		$this->userdata->unsetTable();

		echo json_encode($return_data);

	}

	public function deleteFormulaFolder()
	{
		$return_data['has_error'] = 0;
		$id = $this->input->post('id');
		$this->userdata->setTable(TABLE_TRAINING_FOLDER)->update(array('status'=>'N'), array('id'=>$id));
		$this->userdata->unsetTable();

		echo json_encode($return_data);
	}

	public function document($folder_id="")
	{
		$folder_data = $this->userdata->setTable(TABLE_TRAINING_FOLDER)->fetchOne(array('id'=>$folder_id, 'status'=>'Y'));
		$this->userdata->unsetTable();

		if(count($folder_data)==0)
		{
			redirect(base_url('admin/trainings/folders'));
		}
		
		$documentData = $this->userdata->setTable(TABLE_TRAINING_DOCUMENTS)->fetchAll(array('folder_id'=>$folder_id));
		$this->userdata->unsetTable();

		foreach ($documentData as $document) {
			$document->downloadCount = $this->userdata->setTable(TABLE_FORMULAS_DOCUMENT_DOWNLOAD)->countRows(array('document_id'=>$document->id));
			$this->userdata->unsetTable();
		}	
		
		//echo '<pre>'; print_r($this->data['allFolderData']); die;
		$this->data['folder_data'] = $folder_data;
		$this->data['documentData'] = $documentData;
		$this->data['single_document'] = $this->load->view('admin/trainings/single_document',$this->data,TRUE);
		$this->load->view('admin/trainings/document',$this->data);
	}


	public function uploadFormulaDocuments()
	{
		$input_data = $this->input->post();
		
		$docCount = count($_FILES['documents']['name']);
		if($_FILES['documents']['name'][0])
		{
			for($i=0; $i<$docCount; $i++)
			{
				$_FILES['userFile']['name'] = $_FILES['documents']['name'][$i];
				$_FILES['userFile']['type'] = $_FILES['documents']['type'][$i];
				$_FILES['userFile']['tmp_name'] = $_FILES['documents']['tmp_name'][$i];
				$_FILES['userFile']['error'] = $_FILES['documents']['error'][$i];
				$_FILES['userFile']['size'] = $_FILES['documents']['size'][$i];

				$config['upload_path'] = UPLOAD_PATH_URL.'training_document/';
				$config['allowed_types'] = '*';
				$config['file_name'] = time().str_replace(' ','-',$_FILES['userFile']['name']);
				$displayName = explode('.', $_FILES['userFile']['name']);
				$this->load->library('upload');
				$this->upload->initialize($config);
				$this->upload->do_upload('userFile');
				$doc_file = $this->upload->data();

				$insert_data['file_name'] = $doc_file['file_name'];
				$insert_data['user_display_name'] = $input_data['user_display_name'];
				$insert_data['display_name'] = $displayName[0];
				$insert_data['folder_id'] = $input_data['folder_id'];
				$insert_data['document_type'] = $input_data['document_type'][0];
				$insert_data['uploaded_by'] = $this->session->userdata('usrid');
				$insert_data['postedTime'] = time();

				$lastId = $this->userdata->setTable(TABLE_TRAINING_DOCUMENTS)->insert($insert_data);
				$this->userdata->unsetTable();					
			}
		}

		if($lastId)
		{
			//$this->session->set_flashdata('success','Documents Added Successfully!!');
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(431));
		}
		redirect($_SERVER['HTTP_REFERER']);

	}


	public function insertDownloadDocumentRecord()
	{
		$doc_id = $this->input->post('doc_id');
		$post_data['user_id'] = $this->session->userdata('usrid');
		$post_data['document_id'] = $doc_id;
		
		$downloadData = $this->userdata->setTable(TABLE_TRAINING_DOCUMENT_DOWNLOAD)->fetchOne($post_data);
		$this->userdata->unsetTable();

		if(count($downloadData)>0)
		{
			$post_data['downloadCount'] = $downloadData->downloadCount + 1;
			$post_data['downloadTime'] = time();
			$this->userdata->setTable(TABLE_TRAINING_DOCUMENT_DOWNLOAD)->update($post_data, array('id'=>$downloadData->id));
			$this->userdata->unsetTable();
		}
		else
		{
			$post_data['downloadCount'] = 1;
			$post_data['downloadTime'] = time();
			$this->userdata->setTable(TABLE_TRAINING_DOCUMENT_DOWNLOAD)->insert($post_data);
			$this->userdata->unsetTable();
		}

	}

	public function download($folder_id, $document_id)
	{
		$this->load->helper('download');
		$documentData = $this->userdata->setTable(TABLE_TRAINING_DOCUMENTS)->fetchOne(array('folder_id'=>$folder_id, 'id'=>$document_id));
		$this->userdata->unsetTable();

		// print_r($documentData); exit;

		if($documentData->file_name)
		{
			$pth = file_get_contents(DEFAULT_ASSETS_URL."upload/training_document/".$documentData->file_name);
			// $ext = end(explode('.', $documentData->file_name));
			$tmp = explode('.', $documentData->file_name);
			$ext = end($tmp);
			$nme = $documentData->display_name.'.'.$ext;
			force_download($nme, $pth);
		}
		else
		{
			redirect('admin/trainings/document/'.$folder_id);
		}

	}

	public function openFormulaShareAgents()
	{
		$return_data['has_error'] = 0;
		$folder_id = $this->input->post('id');

		$agent_data = $this->userdata->fetchAll(array('userType'=>2, 'parent_id'=>0,'status'=>'Y'));

		
		foreach ($agent_data as $agent) {
			$agent->mlm_agent_data = $this->userdata->getAgentInMLMStructure($agent->id);
		}

		$this->data['mlm_structure_view'] = $this->nestedArrayForFolderTraining($agent_data, $folder_id);

		$this->data['folder_id'] = $folder_id;

		$return_data['shareFormulasFolderHtml'] = $this->load->view('admin/trainings/share_folder_modal',$this->data,TRUE);
		echo json_encode($return_data);
	}


	#training 

	public function nestedArrayForFolderTraining($dataArray, $folder_id)
	{
		$html = '';
		$html .= '<ol class="dd-list">';

		foreach ($dataArray as $key => $value) {
			$agentShareFolder = $this->defaultdata->count_record(TABLE_TRAINING_FOLDER_SHARE, array('folder_id'=>$folder_id, 'agent_id'=>$value->id));
			$checked = $agentShareFolder>0?'checked':'';
			$html .= '<li class="dd-item dd3-item">
						  <div class=" dd3-handle"></div>
		                  <div class="dd3-content">
		                  	'.$value->name.'
		                  	
		                  		<input type="checkbox" name="agent_id[]" class=" pull-right" value="'.$value->id.'" '.$checked.' />
		                  	
		                  </div>';
			        
			        if(count($value->mlm_agent_data)>0)
			        {
			        	$html .= $this->nestedArrayForFolderTraining($value->mlm_agent_data, $folder_id);
			        }

	        $html .= '</li>';          
		}
		$html .= '</ol>';

		return $html;
	}



	public function shareFormulaAgent()
	{
		$return_data['has_error'] = 0;
		$post_data = $this->input->post();

		// print_r($post_data); exit;

		if($post_data['folder_id'])
		{
			$this->defaultdata->delete(TABLE_TRAINING_FOLDER_SHARE, array('folder_id'=>$post_data['folder_id']));

			if(isset($post_data['agent_id']) && count($post_data['agent_id'])>0)
			{
				$insert_data['folder_id'] = $post_data['folder_id'];
				foreach ($post_data['agent_id'] as $agent_id) {
					$insert_data['agent_id'] = $agent_id;
					$this->userdata->setTable(TABLE_TRAINING_FOLDER_SHARE)->insert($insert_data);
					$this->userdata->unsetTable();
				}
			}
		}

		echo json_encode($return_data);
	}




	public function openDocumentShareAgents()
	{
		$return_data['has_error'] = 0;
		$document_id = $this->input->post('id');


		$documentData = $this->userdata->setTable(TABLE_TRAINING_DOCUMENTS)->fetchOne(array('id'=>$document_id));
		$this->userdata->unsetTable();

		$folder_id = $documentData->folder_id;

		$agent_data = $this->userdata->fetchAll(array('userType'=>2, 'parent_id'=>0,'status'=>'Y'));
		foreach ($agent_data as $agent) {
			$agent->mlm_agent_data = $this->userdata->getAgentInMLMStructure($agent->id);
		}

		$documentShareCount = $this->userdata->setTable(TABLE_TRAINING_DOCUMENT_SHARE)->countRows(array('document_id'=>$document_id));
		$this->userdata->unsetTable();

		// if($documentShareCount>0)
		// {
			$this->data['mlm_structure_view'] = $this->nestedArrayForDoc($agent_data, $document_id);
		// }
		// else
		// {
		// 	$this->data['mlm_structure_view'] = $this->nestedArrayForFolder($agent_data, $folder_id);
		// }

		$this->data['document_id'] = $document_id;
		$this->data['folder_id'] = $folder_id;

		$return_data['shareFormulasDocumentsHtml'] = $this->load->view('admin/trainings/share_document_modal',$this->data,TRUE);
		echo json_encode($return_data);
	}


	public function nestedArrayForDoc($dataArray, $document_id)
	{
		$html = '';
		$html .= '<ol class="dd-list">';

		foreach ($dataArray as $key => $value) {
			$agentShareFolder = $this->defaultdata->count_record(TABLE_TRAINING_DOCUMENT_SHARE, array('document_id'=>$document_id, 'agent_id'=>$value->id));
			$checked = $agentShareFolder>0?'checked':'';
			$html .= '<li class="dd-item dd3-item">
						  <div class=" dd3-handle"></div>
		                  <div class="dd3-content">
		                  	'.$value->name.'
		                  	
		                  		<input type="checkbox" name="agent_id[]" class=" pull-right" value="'.$value->id.'" '.$checked.' />
		                  	
		                  </div>';
			        
			        if(count($value->mlm_agent_data)>0)
			        {
			        	$html .= $this->nestedArrayForDoc($value->mlm_agent_data, $document_id);
			        }

	        $html .= '</li>';          
		}
		$html .= '</ol>';

		return $html;
	}


		public function shareFormulaDocumentAgent()
	{
		$return_data['has_error'] = 0;
		$post_data = $this->input->post();

		if($post_data['document_id'])
		{
			$this->defaultdata->delete(TABLE_TRAINING_DOCUMENT_SHARE, array('document_id'=>$post_data['document_id']));

			if(isset($post_data['agent_id']) && count($post_data['agent_id'])>0)
			{
				$insert_data['document_id'] = $post_data['document_id'];
				$insert_data['folder_id'] = $post_data['folder_id'];
				foreach ($post_data['agent_id'] as $agent_id) {
					$insert_data['agent_id'] = $agent_id;
					$this->userdata->setTable(TABLE_TRAINING_DOCUMENT_SHARE)->insert($insert_data);
					$this->userdata->unsetTable();
				}
			}
		}

		echo json_encode($return_data);
	}


		public function deleteFormulaDocuments()
	{
		$return_data['has_error'] = 0;
		$id = $this->input->post('id');
		$documentData = $this->userdata->setTable(TABLE_TRAINING_DOCUMENTS)->fetchOne(array('id'=>$id));
		$this->userdata->unsetTable();
		if(count($documentData)>0)
		{
			@unlink(UPLOAD_PATH_URL.'formulas_document/'.$documentData->file_name);
						
			$this->defaultdata->delete(TABLE_TRAINING_DOCUMENTS, array('id'=>$id));

			$this->defaultdata->delete(TABLE_TRAINING_DOCUMENT_DOWNLOAD, array('document_id'=>$id));

			$this->defaultdata->delete(TABLE_TRAINING_DOCUMENT_SHARE, array('document_id'=>$id));

		}
		else
		{
			$return_data['has_error'] = 1;
		}
		
		echo json_encode($return_data);
	}


	
}


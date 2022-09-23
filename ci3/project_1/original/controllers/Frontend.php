<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Frontend extends CI_Controller
{

	public $data = array();
	public $loggedout_method_arr = array('index', 'mailgunReplyCallback', 'webhook');
 
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('userdata');
        $this->load->library('mailer');
		if($this->defaultdata->is_session_active() == 1)
		{
			$user_cond = array();
			$user_cond['id'] = $this->session->userdata('usrid');
			$this->data['user_details'] = $this->userdata->grabUserData($user_cond);
		}
	}
 
	public function index()
	{
		$user_id = $this->session->userdata('usrid');
		if($user_id)
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
			redirect(base_url('login'));
		}
	}

	public function changeLanguage($lang = 1)
	{
		$this->session->set_userdata('languageID',$lang);
		redirect($this->agent->referrer());
	}

	public function jobApplication($str='')
	{
		$user_id=decrypt($str,'jobs');
		$user_data = $this->userdata->fetchOne(array('id'=>$user_id, 'userType'=>2));
		
		$this->data['user_id'] = !empty($user_data) ? $user_data->id : '';
		$this->load->view('user/job-application',$this->data);
	}

	public function jobApplicationProcess()
	{
		$input_data = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		$this->form_validation->set_rules('phone', 'Phone number', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_message('valid_email', $this->defaultdata->gradLanguageText(381));
		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
			redirect($this->agent->referrer());
		}
		else
		{
			$app_data['referral_user_id'] = $input_data['referral_user_id'];
			$app_data['name'] = $input_data['name'];
			$app_data['phone'] = $input_data['phone'];
			$app_data['email'] = $input_data['email'];
			$app_data['information'] = $input_data['information'];
			$app_data['type'] = 1;
			$app_data['status'] = 'P';
			$app_data['postedtime'] = time();
			$last_id = $this->userdata->setTable(TABLE_APPLICATIONS)->insert($app_data);
			$this->userdata->unsetTable();
			if($last_id>0)
			{
				/*======== INSERT MULTIPLE AGENT DOCUMENTS UPLOADS =======*/
				$docCount = count($_FILES['document']['name']);
				if($_FILES['document']['name'][0])
				{
					for($i=0; $i<$docCount; $i++)
					{
						$_FILES['userFile']['name'] = $_FILES['document']['name'][$i];
						$_FILES['userFile']['type'] = $_FILES['document']['type'][$i];
						$_FILES['userFile']['tmp_name'] = $_FILES['document']['tmp_name'][$i];
						$_FILES['userFile']['error'] = $_FILES['document']['error'][$i];
						$_FILES['userFile']['size'] = $_FILES['document']['size'][$i];

						$config['upload_path'] = UPLOAD_PATH_URL.'applications_document/';
						$config['allowed_types'] = '*';
						$config['file_name'] = time().str_replace(' ','-',$_FILES['userFile']['name']);
						$this->load->library('upload');
						$this->upload->initialize($config);
						$this->upload->do_upload('userFile');
						$doc_file = $this->upload->data();
						$displayName = str_replace($doc_file['file_ext'], '', $doc_file['client_name']);
						
						$document_data['application_id'] = $last_id;
						$document_data['file_name'] = $doc_file['file_name'];
						$document_data['display_name'] = $displayName;
						$document_data['postedTime'] = time();

						$this->userdata->setTable(TABLE_APPLICATION_DOCUMENTS)->insert($document_data);
						$this->userdata->unsetTable();
					}
				}
				/*======== INSERT MULTIPLE AGENT DOCUMENTS UPLOADS =======*/

				$this->session->set_flashdata('success','Twoja aplikacja została wysłana. Skontaktujemy się z Tobą wkrótce.');
			}
			else
			{
				$this->session->set_flashdata('error', $this->defaultdata->gradLanguageText(376));
			}
			redirect($this->agent->referrer());

		}
	}

	public function serviceApplication($str='')
	{
		$user_id=decrypt($str,'services');

		$user_data = $this->userdata->fetchOne(array('id'=>$user_id, 'userType'=>2));
		
		$this->data['user_id'] = !empty($user_data) ? $user_data->id : '';
		$this->load->view('user/service-application',$this->data);
	}

	public function serviceApplicationProcess()
	{
		$input_data = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		$this->form_validation->set_rules('phone', 'Phone number', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_message('valid_email', $this->defaultdata->gradLanguageText(381));

		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
			redirect($this->agent->referrer());
		}
		else
		{
			$app_data['referral_user_id'] = $input_data['referral_user_id'];
			$app_data['name'] = $input_data['name'];
			$app_data['phone'] = $input_data['phone'];
			$app_data['email'] = $input_data['email'];
			$app_data['information'] = $input_data['information'];
			$app_data['type'] = 2;
			$app_data['status'] = 'P';
			$app_data['postedtime'] = time();
			$last_id = $this->userdata->setTable(TABLE_APPLICATIONS)->insert($app_data);
			$this->userdata->unsetTable();

			if($last_id>0)
			{
				/*======== INSERT MULTIPLE AGENT DOCUMENTS UPLOADS =======*/
				$docCount = count($_FILES['document']['name']);
				if($_FILES['document']['name'][0])
				{
					for($i=0; $i<$docCount; $i++)
					{
						$_FILES['userFile']['name'] = $_FILES['document']['name'][$i];
						$_FILES['userFile']['type'] = $_FILES['document']['type'][$i];
						$_FILES['userFile']['tmp_name'] = $_FILES['document']['tmp_name'][$i];
						$_FILES['userFile']['error'] = $_FILES['document']['error'][$i];
						$_FILES['userFile']['size'] = $_FILES['document']['size'][$i];

						$config['upload_path'] = UPLOAD_PATH_URL.'applications_document/';
						$config['allowed_types'] = '*';
						$config['file_name'] = time().str_replace(' ','-',$_FILES['userFile']['name']);
						$this->load->library('upload');
						$this->upload->initialize($config);
						$this->upload->do_upload('userFile');
						$doc_file = $this->upload->data();
						$displayName = str_replace($doc_file['file_ext'], '', $doc_file['client_name']);
						
						$document_data['application_id'] = $last_id;
						$document_data['file_name'] = $doc_file['file_name'];
						$document_data['display_name'] = $displayName;
						$document_data['postedTime'] = time();

						$this->userdata->setTable(TABLE_APPLICATION_DOCUMENTS)->insert($document_data);
						$this->userdata->unsetTable();
					}
				}
				/*======== INSERT MULTIPLE AGENT DOCUMENTS UPLOADS =======*/

				$this->session->set_flashdata('success','Twoje zgłoszenie zostało wysłane. Skontaktujemy się z Tobą wkrótce.');
			}
			else
			{
				$this->session->set_flashdata('error', $this->defaultdata->gradLanguageText(376));
			}
			redirect($this->agent->referrer());

		}
	}

	public function forgot_password()
	{
		$this->load->view('user/forgot_password',$this->data);
	}

	public function forgotPassProcess()
	{
		$this->data['general_settings'] = $this->defaultdata->grabSettingData();

		$post_data = $this->input->post();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_message('valid_email', $this->defaultdata->gradLanguageText(381));
		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
		}
		else
		{
			$user_data = $this->userdata->fetchOne(array('emailAddress'=>$post_data['email']));
			if(count($user_data)==0)
			{
				$this->session->set_flashdata('error', $this->defaultdata->gradLanguageText(444));
			}
			else
			{
				$unique_id = encrypt($user_data->id,'porgotPassword');
				$reset_pass_link =  base_url('reset-password/'.$unique_id);

				$to = $user_data->emailAddress;
				$subject = ($this->data['general_settings']->emailTitle) ? $this->data['general_settings']->emailTitle : "Odzyskiwanie hasła użytkownika systemu";

				$mailcontent = '';

				if(!empty($this->data['general_settings']->default_register_email_text)) {
					$mailcontent = nl2br($this->data['general_settings']->default_register_email_text);
					$mailcontent = str_replace('{RESET_PASS_LINK}', $reset_pass_link, $mailcontent);
				}


				$from_name = $this->data['general_settings']->contactEmailName;
				$from = $this->data['general_settings']->emailAddressSender;
				
				$send_mail = $this->defaultdata->sendMail($to, $subject, $mailcontent, $from, $from_name);

				if($send_mail)
				{
					$this->session->set_flashdata('success','E-mail z linkiem do zmiany hasła został wysłany do: '.$user_data->emailAddress.' Sprawdź pocztę.');
				}
				else
				{
					$this->session->set_flashdata('error', $this->defaultdata->gradLanguageText(445));
				}

			}
		}

		redirect(base_url('forgot-password'));
	}

	public function reset_password($str='')
	{
		$this->data['str'] = $str;
		$this->load->view('user/reset_password',$this->data);
	}

	public function resetPassProcess()
	{
		
		$post_data = $this->input->post();
		$user_id = decrypt($post_data['strCode'],'porgotPassword');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('password', 'New Password', 'trim|required');
		$this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'trim|required|matches[password]');
		if($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error',validation_errors());
		}
		else
		{
			$update_data['userPassword'] = md5($post_data['password']);
			$update_data['password_update_time'] = time();
			$update = $this->userdata->update($update_data, array('id'=>$user_id));
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(440));
		}
		redirect($this->agent->referrer());
	}

	public function webhook($state="")
	{
		header("Access-Control-Allow-Origin: *");
		header('Content-Type: text/html; charset=utf-8');

		$this->jsondata = "php://input";
		$this->postjsonstring = file_get_contents($this->jsondata);
		$_POST = json_decode(file_get_contents("php://input"), true);

		//======= COMMENT SEND MAIL RECORD========//
			$sendmail_cond['client_id'] = $_POST['event-data']['user-variables']['client_id'];
			$sendmail_cond['comment_id'] = $_POST['event-data']['user-variables']['comment_id'];
			$sendmail_cond['recipient'] = $_POST['event-data']['recipient'];
			$sendmail_cond['parentcomment_id'] = $_POST['event-data']['user-variables']['parentcomment_id'];
			$sendmail_record = $this->defaultdata->get_single_row(TABLE_COMMENT_SENDMAIL, $sendmail_cond);
			
			$sendmail_data['mailgun_id'] = $_POST['event-data']['message']['headers']['message-id'];
			$sendmail_data['status'] = $_POST['event-data']['event'];
			$sendmail_data['parentcomment_id'] = $_POST['event-data']['user-variables']['parentcomment_id'];
			$sendmail_data['updatedtime'] = time();


			//$debugfile = fopen("debug.txt", "w");
			//fwrite($debugfile, print_r($sendmail_data,true));
			//fwrite($debugfile, print_r($sendmail_cond,true));
			//fclose($debugfile);

			if(!empty($sendmail_record))
			{
				$this->defaultdata->update(TABLE_COMMENT_SENDMAIL, $sendmail_data, array('id'=>$sendmail_record->id));
			}
			else
			{
				$sendmail_data['client_id'] = $_POST['event-data']['user-variables']['client_id'];
				$sendmail_data['comment_id'] = $_POST['event-data']['user-variables']['comment_id'];
				$sendmail_data['recipient'] = $_POST['event-data']['recipient'];
				$sendmail_data['postedtime'] = time();
				$this->defaultdata->insert(TABLE_COMMENT_SENDMAIL, $sendmail_data);
			}
		//======= COMMENT SEND MAIL RECORD========//
	}
 
	function download_remote_file($file_url, $save_to)
	{
		$username = 'api';
		$password = MAILGUN_API_KEY;
		$context = stream_context_create(array(
			'http' => array(
				'header'  => "Authorization: Basic " . base64_encode("$username:$password")
			)
		));
		$content = file_get_contents($file_url, false, $context);
		file_put_contents($save_to, $content);
	}

	public function mailgunReplyCallback($state="")
    {
        $request_data = json_encode($_REQUEST);
        $insert_data['state'] = $state;
        $insert_data['request_data'] =$request_data;
        $insert_data['postedtime'] = time();
        $webhooid=$this->defaultdata->insert('com_webhook', $insert_data);
        $commentsentdata2= $this->db->where('id',$webhooid)->get('com_webhook')->row_array();
        $mailgunRes   = json_decode($commentsentdata2['request_data'], true);
        $comments['userName']='';
        $comments['id']='';
        $replyo = substr($mailgunRes['In-Reply-To'], 1, -1);

        if ($replyo) {

            $commentsentdata = $this->db->select('client_id,recipient,mailgun_id,comment_id,parentcomment_id')->where('mailgun_id', $replyo)->get('com_comment_sendmail')->row_array();

            if ($commentsentdata['parentcomment_id'] == 0) {
                $this->db->select('com_client_comment.*,com_user.name,com_user.userType,com_user.userName,com_user.emailAddress');
                $this->db->join('com_user', 'com_user.id=com_client_comment.from_user_id');
                $this->db->where('com_client_comment.id', $commentsentdata['comment_id']);
                $comments = $this->db->get('com_client_comment')->row_array();

            } else {
                $this->db->select('com_client_comment.*,(com_client_comment.parent_id) as id,com_user.name,com_user.userType,com_user.emailAddress');
                $this->db->join('com_user', 'com_user.id=com_client_comment.from_user_id');
                $this->db->where('com_client_comment.id', $commentsentdata['parentcomment_id']);
                $comments = $this->db->get('com_client_comment')->row_array();

            }

            $sendercomments = $this->db->where('emailAddress', $mailgunRes['sender'])->get('com_user')->row_array();

            if ($comments['userName'] != '' && md5($comments['userName'].$comments['from_user_id']) . '_' . $comments['to_user_id'] . '@' . MAILGUN_DOMAIN == $mailgunRes['recipient']) {

                $title = $sendercomments['name'] . " => " . $comments['name'];
                if ($comments['id'] != '') {
                    $array = array(
                        'from_user_id' => $comments['from_user_id'],
                        'to_user_id' => $comments['to_user_id'],
                        'comment_type_id' => $comments['comment_type_id'],
                        'title' => $title,
                        'message' => ($mailgunRes['stripped-text']== "") ? 'Mail przychodzący.': $mailgunRes['stripped-text'],
                        'document' => $comments['document'],
                        'share_solcitor' => ($comments['userType'] == 3)? 'Y' :  (($sendercomments['userType'] == 3)? 'Y' : 'N'),
                        'share_agent' => ($comments['userType'] == 2)? 'Y' : (($sendercomments['userType'] == 2)? 'Y' : 'N'),
                        'share_agent_manager' => ($comments['userType'] == 2)? 'Y' :  (($sendercomments['userType'] == 2)? 'Y' : 'N'),
                        'emailType' => 'Reply',
                        'parent_id' => $comments['id'],
                        'subject' => $mailgunRes['Subject'],
                        'reply_from_id' => $sendercomments['id'],
                        'postedtime' => time(),

                        'compensation_collect' => 0,
                        'compensation_free_charge' => 0,
                        'costs_to_deduct' => 0,
                        'provision_base' => 0,
                        'provision_gross' => 0,
                        'payment_to_client' => 0,
                        'court_provision' => 'N',
                        'dont_calculate_MLM' => 'N',
                    );
                    
                    $lastId = $this->defaultdata->insert('com_client_comment', $array);
                    $this->userdata->unsetTable();
                    $sendmail_data['client_id'] = $comments['to_user_id'];
                    $sendmail_data['comment_id'] = $comments['id'];
                    $sendmail_data['status'] = ''; //Pending
                    $sendmail_data['postedtime'] = time();
                    $sendmail_data['recipient'] = $mailgunRes['sender'];
                    $sendmail_data['parentcomment_id'] = $lastId;
                    $sendmail_data['recipient_type'] = '4'; //from
                    $this->defaultdata->insert(TABLE_COMMENT_SENDMAIL, $sendmail_data);
                    $this->userdata->unsetTable();

                    /*========NOTIFICATION=========*/
                    $noti_data['client_id'] = $comments['to_user_id'];
                    $noti_data['type'] = 1;
                    $noti_data['source_table'] = TABLE_CLIENT_COMMENT;
                    $noti_data['source_id'] = $lastId;
                    $noti_data['show_time'] = strtotime(date('Y-m-d'));
                    $noti_data['status'] = 1;
                    $noti_data['postedtime'] = time();
                    $emailto = array();
                    $emailcc = array();
                    $emailbcc = array();
                    $emailto[] = $mailgunRes['sender'];
                    $Email_to = $comments['emailAddress'];

                    $user_ids = $this->userdata->getCommentNotiUserIds2($noti_data['client_id'], $comments['share_solcitor'], $comments['share_agent'], $comments['share_agent_manager'], $emailto, $emailcc, $emailbcc, $noti_data['source_id'], $Email_to);

                    foreach ($user_ids as $key => $val) {
                        $noti_data['user_id'] = $val;
                        $noti_data['userType'] = $this->defaultdata->getField(TABLE_USER, array('id' => $val), 'userType');
                        $this->defaultdata->insert(TABLE_NOTIFICATION, $noti_data);
                        $this->userdata->unsetTable();
                    }

                    /*========NOTIFICATION=========*/

                    /*========document=========*/
                    if ($sendercomments['userType'] == '4' && $comments['userType'] == '2') {
                        $share_agent_manager = 'Y';
                        $share_agent = 'Y';
                        $share_solcitor = 'N';
                    } else if ($sendercomments['userType'] == '4' && $comments['userType'] == '3') {

                        $share_solcitor = 'Y';
                        $share_agent_manager = 'N';
                        $share_agent = 'N';
                    } else if ($sendercomments['userType'] == '2' && $comments['userType'] == '3') {
                        $share_agent_manager = 'Y';
                        $share_agent = 'Y';
                        $share_solcitor = 'Y';

                    } else if ($sendercomments['userType'] == '2' && $comments['userType'] == '2') {
                        $share_agent_manager = 'Y';
                        $share_agent = 'Y';
                        $share_solcitor = 'N';

                    } else if ($sendercomments['userType'] == '3' && $comments['userType'] == '2') {
                        $share_agent_manager = 'Y';
                        $share_agent = 'Y';
                        $share_solcitor = 'Y';

                    } else if ($sendercomments['userType'] == '3' && $comments['userType'] == '3') {
                        $share_solcitor = 'Y';
                        $share_agent_manager = 'N';
                        $share_agent = 'N';

                    } else if ($sendercomments['userType'] == '3' && $comments['userType'] == '1') {
                        $share_solcitor = 'Y';
                        $share_agent_manager = 'N';
                        $share_agent = 'N';
                    } else if ($sendercomments['userType'] == '2' && $comments['userType'] == '1') {
                        $share_solcitor = 'N';
                        $share_agent_manager = 'Y';
                        $share_agent = 'Y';
                    } else {
                        $share_agent_manager = 'N';
                        $share_agent = 'N';
                        $share_solcitor = 'N';
                    }
                    $input = json_decode($mailgunRes['attachments'], true);
                    if (count($input) > 0) {
                        foreach ($input as $inputlist) {
                            $document = $inputlist['url'];
                            $filename = time() . $inputlist['name'];
                            $this->download_remote_file($document, realpath('./' . UPLOAD_PATH_URL . 'clients_comment/') . '/' . $filename);
                            $displayName = pathinfo($filename, PATHINFO_FILENAME);

                            $document_data['source_table'] = 1;
                            $document_data['source_id'] = $lastId;
                            $document_data['folder_id'] = 1;
                            $document_data['share_solcitor'] = $share_solcitor;
                            $document_data['share_agent'] = $share_agent;
                            $document_data['share_MLM'] = $share_agent_manager;
                            $document_data['postedTime'] = time();
                            $document_data['file_name'] = $filename;
                            $document_data['display_name'] = $displayName;

                            $this->userdata->setTable(TABLE_COMMENT_DOCUMENT)->insert($document_data);
                            $this->userdata->unsetTable();
                            $attach_mailgun_arr[] = ROOT_URL . UPLOAD_PATH_URL . 'clients_comment/' . $filename;

                        }
                    }
                }
            }
        }else{

            $attach_mailgun_arr2 = "";
            /*

            
            if(count($input2)>0){
                $attach_mailgun_arr2 = [];
                foreach($input2 as $key => $inputlist){
                    $document=$inputlist['url'];
                    $filename=time().$inputlist['name'];
                    $this->download_remote_file($document, realpath('./'.UPLOAD_PATH_URL.'clients_comment/') . '/'.$filename);
                    $attach_mailgun_arr2[$key] = ROOT_URL.UPLOAD_PATH_URL.'clients_comment/'.$filename;
                    $file = 'people5.txt';
                    file_put_contents($file, print_r($attach_mailgun_arr2,true));
                }
            }
            */

            $commentsender = $this->db->where('emailAddress', $mailgunRes['sender'])->get('com_user')->row_array();
            preg_match('/(.*)_([0-9]*)\@/', $mailgunRes['recipient'], $addressParams);
            $rname = $addressParams[1];
            $recipient = $this->db->where('md5(CONCAT(userName,id))', $rname)->get('com_user')->row_array();
            $toUser = $addressParams[2];
            
            if ($commentsender && $recipient) {
                
                $array=array(
                    'from_user_id'=>$commentsender['id'],
                    'to_user_id'=>$toUser,
                    'comment_type_id'=>1,
                    'title'=>$commentsender['name']. ' => '. $recipient['name'],
                    'message'=>($mailgunRes['stripped-text']== "") ? 'Mail przychodzący.': $mailgunRes['stripped-text'],
                    'document'=>($attach_mailgun_arr2== "") ? '': $attach_mailgun_arr2,
                    'share_solcitor'=>($commentsender['userType']== 3)?'Y':(($recipient['userType'] == 3)? 'Y' : 'N'),
                    'share_agent'=>($commentsender['userType']== 2)?'Y':(($recipient['userType'] == 2)? 'Y' : 'N'),
                    'share_agent_manager'=>($commentsender['userType']== 2)?'Y':(($recipient['userType'] == 2)? 'Y' : 'N'),
                    'emailType'=>'Reply|'.$recipient['id'],
                    'parent_id'=>0,
                    'subject'=>$mailgunRes['Subject'],
                    'reply_from_id'=>0,
                    'postedtime'=>time(),
                    'compensation_collect' => 0,
                    'compensation_free_charge' => 0,
                    'costs_to_deduct' => 0,
                    'provision_base' => 0,
                    'provision_gross' => 0,
                    'payment_to_client' => 0,
                    'court_provision' => 'N',
                    'dont_calculate_MLM' => 'N',
                );

                $insertedId = $this->defaultdata->insert('com_client_comment', $array);

                $sendmail_data['client_id'] = $toUser;
                $sendmail_data['comment_id'] = $insertedId;
                $sendmail_data['status'] = ''; //Pending
                $sendmail_data['postedtime'] = time();
                $sendmail_data['recipient'] = $mailgunRes['sender'];
                $sendmail_data['parentcomment_id'] = 0;
                $sendmail_data['recipient_type'] = $commentsender['userType']; //from
                $this->defaultdata->insert(TABLE_COMMENT_SENDMAIL, $sendmail_data);
                $this->userdata->unsetTable();

                /*========NOTIFICATION=========*/
                $noti_data['client_id'] = $toUser;
                $noti_data['type'] = 1;
                $noti_data['source_table'] = TABLE_CLIENT_COMMENT;
                $noti_data['source_id'] =  $insertedId;
                $noti_data['show_time'] = strtotime(date('Y-m-d'));
                $noti_data['status'] = 1;
                $noti_data['postedtime'] = time();

                $noti_data['user_id'] = $recipient['id'];
                $noti_data['userType'] = $recipient['userType'];
                $this->defaultdata->insert(TABLE_NOTIFICATION, $noti_data);
                $this->userdata->unsetTable();
                
                /*========document=========*/
                if ($commentsender['userType'] == '4' && $recipient['userType'] == '2') {
                    $share_agent_manager = 'Y';
                    $share_agent = 'Y';
                    $share_solcitor = 'N';
                } else if ($commentsender['userType'] == '4' && $recipient['userType'] == '3') {

                    $share_solcitor = 'Y';
                    $share_agent_manager = 'N';
                    $share_agent = 'N';
                } else if ($commentsender['userType'] == '2' && $recipient['userType'] == '3') {
                    $share_agent_manager = 'Y';
                    $share_agent = 'Y';
                    $share_solcitor = 'Y';

                } else if ($commentsender['userType'] == '2' && $recipient['userType'] == '2') {
                    $share_agent_manager = 'Y';
                    $share_agent = 'Y';
                    $share_solcitor = 'N';

                } else if ($commentsender['userType'] == '3' && $recipient['userType'] == '2') {
                    $share_agent_manager = 'Y';
                    $share_agent = 'Y';
                    $share_solcitor = 'Y';

                } else if ($commentsender['userType'] == '3' && $recipient['userType'] == '3') {
                    $share_solcitor = 'Y';
                    $share_agent_manager = 'N';
                    $share_agent = 'N';

                } else if ($commentsender['userType'] == '3' && $recipient['userType'] == '1') {
                    $share_solcitor = 'Y';
                    $share_agent_manager = 'N';
                    $share_agent = 'N';
                } else if ($commentsender['userType'] == '2' && $recipient['userType'] == '1') {
                    $share_solcitor = 'N';
                    $share_agent_manager = 'Y';
                    $share_agent = 'Y';
                } else {
                    $share_agent_manager = 'N';
                    $share_agent = 'N';
                    $share_solcitor = 'N';
                }
                $input2 = json_decode($mailgunRes['attachments'], true);
                if (count($input2) > 0) {
                    foreach ($input2 as $inputlist) {
                        $document = $inputlist['url'];
                        $filename = time() . $inputlist['name'];
                        $this->download_remote_file($document, realpath('./' . UPLOAD_PATH_URL . 'clients_comment/') . '/' . $filename);
                        $displayName = pathinfo($filename, PATHINFO_FILENAME);

                        $document_data['source_table'] = 1;
                        $document_data['source_id'] = $insertedId;
                        $document_data['folder_id'] = 1;
                        $document_data['share_solcitor'] = $share_solcitor;
                        $document_data['share_agent'] = $share_agent;
                        $document_data['share_MLM'] = $share_agent_manager;
                        $document_data['postedTime'] = time();
                        $document_data['file_name'] = $filename;
                        $document_data['display_name'] = $displayName;

                        $this->userdata->setTable(TABLE_COMMENT_DOCUMENT)->insert($document_data);
                        $this->userdata->unsetTable();
                    }
                }
                

            }else{

                $input2 = json_decode($mailgunRes['attachments'], true);
                if (count($input2) > 0) {
                    foreach ($input2 as $inputlist) {
                        $document = $inputlist['url'];
                        $filename = time() . $inputlist['name'];
                        $this->download_remote_file($document, realpath('./' . UPLOAD_PATH_URL . 'clients_comment/') . '/' . $filename);
                        $attach_mailgun_arr[] = ROOT_URL . UPLOAD_PATH_URL . 'clients_comment/' . $filename;
                    }
                }

                $this->data['general_settings'] = $this->defaultdata->grabSettingData();
                $FromEmail2=explode('<',$mailgunRes['From']);

                //$from_name = $this->data['general_settings']->contactEmailName;
                //$from = $this->data['general_settings']->emailAddressSender;
                $from = $mailgunRes['sender'];
                $from_name = $FromEmail2[0];

                $curl_data['to_email'] = MAIL_ADMIN;
                $curl_data['cc_email'] = '';
                $curl_data['bcc_email'] = '';
                $curl_data['subject'] = $mailgunRes['Subject'];
                $curl_data['message'] = ($mailgunRes['stripped-text']== "") ? 'Mail przychodzący.': $mailgunRes['stripped-text'];
                $curl_data['from'] = $from;
                $curl_data['from_name'] = $from_name;
                $curl_data['attach'] = $attach_mailgun_arr; //$attach;
                $curl_data['comment_id'] = '';
                $curl_data['client_id'] ='';

                $mail = $this->mailer->send($curl_data);
            }

        }
    }
	
	public function mailgunReplyCallback2($state="")
	{
		$request_data = json_encode($_REQUEST);
		$insert_data['state'] = $state;
		$insert_data['request_data'] = $request_data;
		$insert_data['postedtime'] = time();
		$this->defaultdata->insert('com_webhook', $insert_data);
	}
}

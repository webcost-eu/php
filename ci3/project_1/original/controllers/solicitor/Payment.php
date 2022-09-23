<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends CI_Controller {

	public $data=array();
	public $loggedin_method_arr = array('payment');
	
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
		if($this->session->userdata('usrtype')!=2)
		{
			redirect(base_url('login'));
		}
	}

	public function notpaid()
	{
		$user_id = $this->session->userdata('usrid');
		$user_data = $this->userdata->fetchOne(array('id'=>$user_id));

		$agent_data = $this->userdata->setTable(TABLE_AGENTS)->fetchOne(array('user_id'=>$user_id));
		$this->userdata->unsetTable();

		$comment_provision = $this->userdata->setTable(TABLE_CLIENT_COMMENT_PROVISION)->fetchAll(array('agent_id'=>$user_id, 'is_active'=>'Y', 'invoice_status'=>'N'));
		$this->userdata->unsetTable();
		foreach ($comment_provision as $provision) 
		{
			$provision->client_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$provision->client_id), 'name');
			
			$provision->client_agent_id = $this->defaultdata->getField(TABLE_CLIENTS, array('user_id'=>$provision->client_id), 'agent');

			$provision->client_agent_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$provision->client_agent_id), 'name');
		}

		$agent_bonus = $this->userdata->setTable(TABLE_CLIENT_AGENT_BONUS)->fetchAll(array('agent_id'=>$user_id, 'invoice_status'=>'N'), array('id'=>'DESC'));
		$this->userdata->unsetTable();

		foreach ($agent_bonus as $bonus) 
		{
			$bonus->settle_data = $this->userdata->setTable(TABLE_CLIENT_AGENT_BONUS_SETTLE)->fetchAll(array('agent_bonus_id'=>$bonus->id), array('id'=>'DESC'));
			$this->userdata->unsetTable();

			$bonus->client_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$bonus->client_id), 'name');
		}


		$agent_target = $this->userdata->setTable(TABLE_AGENT_TARGET)->fetchAll(array('agent_id'=>$user_id, 'status'=>'G', 'invoice_status'=>'N'));
		$this->userdata->unsetTable();
		foreach ($agent_target as $target)
		{
			$target->agent_from_structure_name = '';
			if($target->case_source==3)
			{
				$target->agent_from_structure_name = $target->agent_from_structure?$this->defaultdata->getField(TABLE_USER, array('id'=>$target->agent_from_structure), 'name'):'';
			}
		}
		//echo '<pre>'; print_r($comment_provision); die;

		$this->data['user_data'] = $user_data;
		$this->data['agent_data'] = $agent_data;
		$this->data['comment_provision'] = $comment_provision;
		$this->data['agent_bonus'] = $agent_bonus;
		$this->data['agent_target'] = $agent_target;
		$this->data['agentNotpaidPayment'] = $this->load->view('agent/payment/notpaid_payment',$this->data,TRUE);
		$this->data['inner_header'] = $this->load->view('agent/payment/header',$this->data,TRUE);
		$this->load->view('agent/payment/notpaid',$this->data);
	}


	public function generateAgentInvoice()
	{
		$agent_id = $this->input->post('agent_id');
		$total_value = $this->input->post('total_value');

		$this->data['user_data'] = $this->userdata->fetchOne(array('id'=>$agent_id));

		$this->data['agent_data'] = $this->userdata->setTable(TABLE_AGENTS)->fetchOne(array('user_id'=>$agent_id));
		
		$comment_provision = $this->userdata->setTable(TABLE_CLIENT_COMMENT_PROVISION)->fetchAll(array('agent_id'=>$agent_id, 'is_active'=>'Y', 'invoice_status'=>'N'));
		$this->userdata->unsetTable();
		foreach ($comment_provision as $provision) 
		{
			$provision->client_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$provision->client_id), 'name');
		}

		$agent_bonus = $this->userdata->setTable(TABLE_CLIENT_AGENT_BONUS)->fetchAll(array('agent_id'=>$agent_id, 'invoice_status'=>'N'), array('id'=>'DESC'));
		$this->userdata->unsetTable();

		foreach ($agent_bonus as $bonus) 
		{
			$bonus->settle_data = $this->userdata->setTable(TABLE_CLIENT_AGENT_BONUS_SETTLE)->fetchAll(array('agent_bonus_id'=>$bonus->id), array('id'=>'DESC'));
			$this->userdata->unsetTable();

			$bonus->client_name = $this->defaultdata->getField(TABLE_USER, array('id'=>$bonus->client_id), 'name');
		}

		$agent_target = $this->userdata->setTable(TABLE_AGENT_TARGET)->fetchAll(array('agent_id'=>$agent_id, 'status'=>'G', 'invoice_status'=>'N'));
		$this->userdata->unsetTable();
		

		
		//echo '<pre>'; print_r($this->data); die;
		$this->data['comment_provision'] = $comment_provision;
		$this->data['agent_bonus'] = $agent_bonus;
		$this->data['agent_target'] = $agent_target;
        $user_cond['id'] = 1;
        $this->load->model('invoicedetails');
        $this->data['invoice_details'] = $this->invoicedetails->grabInvoiceData($user_cond);   

		$return_data['generateAgentInvoiceHtml'] = $this->load->view('agent/payment/generate_invoice',$this->data,TRUE);
		echo json_encode($return_data);
	}

	
	public function postAgentInvoice()
	{
		$return_data = array();
		$input_data = $this->input->post();
		
		$invoice_data['agent_id'] = $input_data['agent_id'];
		$invoice_data['invoice_no'] = $input_data['invoice_no'];
		$invoice_data['date_of_invoice'] = strtotime(str_replace('/','-', $input_data['date_of_invoice']));
		$invoice_data['date_of_selling'] = strtotime(str_replace('/','-', $input_data['date_of_selling']));
		$invoice_data['place_of_invoice'] = $input_data['place_of_invoice'];
		$invoice_data['pament_type'] = $input_data['pament_type'];
		$invoice_data['term_of_payment'] = strtotime(str_replace('/','-', $input_data['term_of_payment']));
		$invoice_data['currency'] = $input_data['currency'];
		$invoice_data['postedTime'] = time();
		
		$lastId = $this->userdata->setTable(TABLE_AGENT_INVOICES)->insert($invoice_data);
		$this->userdata->unsetTable();

		if($lastId)
		{
			if(isset($input_data['service_name']) && count($input_data['service_name']) > 0)
			{
				$payment_data['agent_id'] = $input_data['agent_id'];
				$payment_data['agent_invoice_id'] = $lastId;
				for($i=0; $i<count($input_data['service_name']); $i++)
				{
					$payment_data['service_name'] = $input_data['service_name'][$i];
					$payment_data['net_price'] = $input_data['net_price'][$i];
					$payment_data['vat'] = $input_data['vat'][$i];
					$payment_data['gross_price'] = $input_data['gross_price'][$i];
					$payment_data['source_table'] = $input_data['source_table'][$i];
					$payment_data['source_id'] = $input_data['source_id'][$i];

					$this->userdata->setTable(TABLE_AGENT_INVOICE_PAYMENTS)->insert($payment_data);
					$this->userdata->unsetTable();
				}
			}

			$update_data = array('invoice_status'=>'Y', 'agent_invoice_id'=>$lastId);
			$this->userdata->setTable(TABLE_CLIENT_COMMENT_PROVISION)->update($update_data, array('agent_id'=>$input_data['agent_id'], 'is_active'=>'Y', 'invoice_status'=>'N'));
			$this->userdata->unsetTable();

			$this->userdata->setTable(TABLE_CLIENT_AGENT_BONUS)->update($update_data, array('agent_id'=>$input_data['agent_id'], 'invoice_status'=>'N'));
			$this->userdata->unsetTable();

			$this->userdata->setTable(TABLE_AGENT_TARGET)->update($update_data, array('agent_id'=>$input_data['agent_id'], 'status'=>'G', 'invoice_status'=>'N'));
			$this->userdata->unsetTable();

			/*====== AGENT NOTPAID PAYMENT SECTION ======*/
			$return_data['agentNotpaidPayment'] = $this->load->view('agent/payment/notpaid_payment',$this->data,TRUE);
			/*====== AGENT NOTPAID PAYMENT SECTION ======*/

		}
		

		echo json_encode($return_data);
	}





	public function paid()
	{
		$user_id = $this->session->userdata('usrid');
		$user_data = $this->userdata->fetchOne(array('id'=>$user_id));

		$agent_data = $this->userdata->setTable(TABLE_AGENTS)->fetchOne(array('user_id'=>$user_id));
		
		$comment_provision = $this->userdata->setTable(TABLE_CLIENT_COMMENT_PROVISION)->fetchAll(array('agent_id'=>$user_id, 'is_active'=>'Y', 'invoice_status'=>'Y'));
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

		$agent_bonus = $this->userdata->setTable(TABLE_CLIENT_AGENT_BONUS)->fetchAll(array('agent_id'=>$user_id, 'invoice_status'=>'Y'), array('id'=>'DESC'));
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

		$agent_target = $this->userdata->setTable(TABLE_AGENT_TARGET)->fetchAll(array('agent_id'=>$user_id, 'status'=>'G', 'invoice_status'=>'Y'));
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
		$this->data['agentPaidPayment'] = $this->load->view('agent/payment/paid_payment',$this->data,TRUE);
		$this->data['inner_header'] = $this->load->view('agent/payment/header',$this->data,TRUE);
		$this->load->view('agent/payment/paid',$this->data);
	}

	public function agentInvoice($id='')
	{
		$user_id = $this->session->userdata('usrid');
		$this->data['invoice_data'] = $this->userdata->setTable(TABLE_AGENT_INVOICES)->fetchOne(array('id'=>$id));
		$this->userdata->unsetTable();

		if(count($this->data['invoice_data'])==0 || $this->data['invoice_data']->agent_id != $user_id)
		{
			redirect(base_url('agent/payment/paid'));
		}

		$this->data['user_data'] = $this->userdata->fetchOne(array('id'=>$this->data['invoice_data']->agent_id));

		$this->data['agent_data'] = $this->userdata->setTable(TABLE_AGENTS)->fetchOne(array('user_id'=>$this->data['invoice_data']->agent_id));
		$this->userdata->unsetTable();

		$this->data['payment_data'] = $this->userdata->setTable(TABLE_AGENT_INVOICE_PAYMENTS)->fetchAll(array('agent_invoice_id'=>$id));
		$this->userdata->unsetTable();
        
        $user_cond['id'] = 1;
        $this->load->model('invoicedetails');
        $this->data['invoice_details'] = $this->invoicedetails->grabInvoiceData($user_cond);   

		$this->load->view('agent/payment/invoice',$this->data);
	}

	public function uploadNonCompanyAgentFile()
	{
		$input_data = $this->input->post();
		if($_FILES['invoice_file']['name'])
		{
			$config['upload_path'] = UPLOAD_PATH_URL.'non_company_invoice/agent_up_file/';
			$config['allowed_types'] = '*';
			$config['file_name'] = time().str_replace(' ','-',$_FILES['invoice_file']['name']);
			$this->load->library('upload');
			$this->upload->initialize($config);
			$this->upload->do_upload('invoice_file');
			$upload_file = $this->upload->data();

			$invoice_data['agent_up_file'] = $upload_file['file_name'];
		}
		
		$invoice_data['agent_id'] = $input_data['agent_id'];
		$invoice_data['agent_type'] = 2;
		$invoice_data['postedTime'] = time();
		$invoice_data['paid_status'] = 'N';

		$lastId = $this->userdata->setTable(TABLE_AGENT_INVOICES)->insert($invoice_data);
		$this->userdata->unsetTable();

		if($lastId)
		{
			$update_data = array('invoice_status'=>'Y', 'agent_invoice_id'=>$lastId);
			$this->userdata->setTable(TABLE_CLIENT_COMMENT_PROVISION)->update($update_data, array('agent_id'=>$input_data['agent_id'], 'is_active'=>'Y', 'invoice_status'=>'N'));
			$this->userdata->unsetTable();

			$this->userdata->setTable(TABLE_CLIENT_AGENT_BONUS)->update($update_data, array('agent_id'=>$input_data['agent_id'], 'invoice_status'=>'N'));
			$this->userdata->unsetTable();

			$this->userdata->setTable(TABLE_AGENT_TARGET)->update($update_data, array('agent_id'=>$input_data['agent_id'], 'status'=>'G', 'invoice_status'=>'N'));
			$this->userdata->unsetTable();

			//$this->session->set_flashdata('success','Invoice Added Successfully!!');
			$this->session->set_flashdata('success', $this->defaultdata->gradLanguageText(401));
		}
		redirect(base_url('agent/payment/notpaid'));

	}

	
}


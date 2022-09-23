<?php
class Chatdata extends MainModel {

	private $data=array();
	function __construct()
	{
		parent::__construct();
		$this->table = TABLE_CHAT;
	}
		
	
	public function userMessages($to_user_id){
		$user_id = $this->session->userdata('usrid');
		$sql_query = "SELECT * FROM ".TABLE_CHAT." WHERE (`from_user_id`='$user_id' AND `to_user_id`='$to_user_id') OR (`to_user_id`='$user_id' AND `from_user_id`='$to_user_id')";
		$query = $this->db->query($sql_query);
		$resut_data = $query->result();
		//echo $this->db->last_query();
		return $resut_data;
	}

	public function messageRead($user_id = 0, $to_user_id = 0)
	{
		$read_count = $this->db->where(array('from_user_id' => $user_id, 'to_user_id' => $to_user_id,'to_user_view_status' => 'N'))->get(TABLE_CHAT)->num_rows();
		$update = "UPDATE ".TABLE_CHAT." SET to_user_view_status='S'  WHERE from_user_id='".$user_id."' AND to_user_id='".$to_user_id."' AND to_user_view_status='N' ";
		$this->db->query($update);
		return $read_count;
	}

	public function loadUnreadMessages($to_user_id, $from_user_id)
	{
		$sql_query="SELECT * FROM ".TABLE_CHAT." WHERE `to_user_id`='$to_user_id' AND `from_user_id`='$from_user_id' AND to_user_view_status='N' ORDER BY postedTime ASC"; 
		$query= $this->db->query($sql_query);
		$result= $query->result();
		return $result;
	}
	public function getChatList($user_id)
	{

		$sql_query = "SELECT * FROM ".TABLE_CHAT." WHERE (`from_user_id`='$user_id' OR `to_user_id`='$user_id') ORDER BY postedTime DESC";
		$query = $this->db->query($sql_query);
		$resut_data = $query->result();
		//echo $this->db->last_query();
		return $resut_data;

	}


	














	
	
	public function getMessageById($message_id){
		$sql_query = "SELECT * FROM ".TABLE_CHAT." WHERE `message_id`='$message_id' ";
		$query = $this->db->query($sql_query);
		$resut_data = $query->row();
		return $resut_data;
	}
		
	
	
	public function getAllUnreadMessege($to_user_id)
	{
		$sql_query="SELECT * FROM ".TABLE_CHAT." WHERE `to_user_id`='$to_user_id' AND to_user_view_status='N' ORDER BY postedTime ASC"; 
		$query= $this->db->query($sql_query);
		$result= $query->result();
		return $result;
	}
	
		
	public function showChatList($key = ''){
		$sessuser=$this->session->userdata('usrid');
		$user_type=$this->session->userdata('usrtype');
		$return_array = array();
		if($user_type == 1)
		{
			//$this->db->from(TABLE_CHAT.' as C');
			$this->db->from(TABLE_CAMPAIGN_APPLICATION.' as CAMPA');
			$this->db->join(TABLE_CAMPAING.' as CAMP','CAMP.campaing_id=CAMPA.campaign_id');
			$this->db->join(TABLE_USER.' as U','U.id=CAMPA.reviewer_id');
			$this->db->join(TABLE_USERLOGIN.' as UL','U.id=UL.uid');
			$this->db->where(array('CAMP.user_id' => $sessuser, 'U.status' => 'Y'));
			$this->db->where_in('CAMPA.aplication_status', array('I', 'A', 'C', 'R'));
			if($key != '')
			{
				$this->db->like('U.name', $key);
			}
			$this->db->group_by('U.id');
			$this->db->order_by('CAMPA.aplication_postedtime', 'ASC');
			$return_array = $this->db->get()->result();
			//echo $this->db->last_query();
		}
		else
		{
			$this->db->from(TABLE_CAMPAIGN_APPLICATION.' as CAMPA');
			$this->db->join(TABLE_CAMPAING.' as CAMP','CAMP.campaing_id=CAMPA.campaign_id');
			$this->db->join(TABLE_USER.' as U','U.id=CAMP.user_id');
			$this->db->join(TABLE_USERLOGIN.' as UL','U.id=UL.uid');
			$this->db->where(array('CAMPA.reviewer_id' => $sessuser, 'U.status' => 'Y'));
			$this->db->where_in('CAMPA.aplication_status', array('I', 'A', 'C', 'R'));
			if($key != '')
			{
				$this->db->like('U.name', $key);
			}
			$this->db->group_by('U.id');
			$this->db->order_by('CAMPA.aplication_postedtime', 'ASC');
			$return_array = $this->db->get()->result();
			//echo $this->db->last_query();
		}
		if(count($return_array) > 0)
		{
			for($i = 0; $i < count($return_array); $i++)
			{
				$return_array[$i]->count_unread_msg = $this->db->where(array('to_user_id' => $sessuser,'from_user_id' => $return_array[$i]->id, 'to_user_view_status' => 'N'))->get(TABLE_CHAT)->num_rows();
			}
		}
		return $return_array;
	}
}
?>
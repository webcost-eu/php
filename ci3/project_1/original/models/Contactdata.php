<?php
class Contactdata extends MainModel {

	private $data=array();
	public function __construct()
	{
		parent::__construct();
		$this->table = TABLE_CONTACTS;
	}

	public function getList($conds = [], $order = [])
	{
		$this->db->select('c.*, u.firstName as cons_fname, u.lastName as cons_lname, a.firstName as agent_fname, a.lastName as agent_lname, s.translation_index ')->from($this->table.' c');
		$this->db->join(TABLE_USER.' u', 'c.consultant_id = u.id');
		$this->db->join(TABLE_USER.' a', 'c.agent_id = a.id');
		$this->db->join(TABLE_CLIENT_SELECT_OPTION.' s', 'c.status_id = s.id');
		if(!empty($conds)) {
			foreach($conds as $field => $cond) {
				$this->db->where($field, $cond);
			}
		}
		if(!empty($order)) {
			$this->db->order_by($order[0],$order[1]);
		}
		$query = $this->db->get();
		if($query->num_rows() != 0)
		{
			return $query->result();
		} else {
			return [];
		}
	}

	public function geContDetails($id)
	{
		$this->db->select('c.*, d.name as deal_name, u.firstName as cons_fname, u.lastName as cons_lname, a.firstName as agent_fname, a.lastName as agent_lname, s.translation_index ')->from($this->table.' c');
		$this->db->join(TABLE_USER.' u', 'c.consultant_id = u.id');
		$this->db->join(TABLE_USER.' a', 'c.agent_id = a.id');
		$this->db->join(TABLE_CLIENT_SELECT_OPTION.' s', 'c.status_id = s.id');
		$this->db->join(TABLE_CLIENT_SELECT_OPTION.' d', 'c.deal_type_id = d.id');
		$this->db->where('c.id', $id);
		$query = $this->db->get();
		if($query->num_rows() != 0)
		{
			return $query->first_row();
		} else {
			return false;
		}
	}
}
?>
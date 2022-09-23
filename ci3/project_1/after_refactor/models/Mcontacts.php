<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Mcontacts
 *
 * @property-read Macceptances $macceptances
 *
 * @property int $id
 * @property int $agreement_id
 * @property string $number
 * @property int $status_id
 * @property int $created_at
 *
 * @property array $users
 */
class Mcontacts extends MainModel
{
    use User_model_trait, SelectOptions_trait, Statusable_model_trait;

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $status_id;
    public $deal_type_id;
    public $consultant_id;
    public $agent_id;
    public $created_at;
    public $referral_user_id;

    protected $casts = [
        'created_at' => 'iso8601'
    ];

    function __construct()
    {
        $this->table = TABLE_CONTACTS;
        $this->use_created_at_int = true;

        parent::__construct();
    }

    public function existsForRoleSql($user_id, $table)
    {
        return "exists(
                select " . $this->table . ".id 
                from " . $this->table . "
                    left join " . TABLE_CONTACT_USERS . " on " . TABLE_CONTACT_USERS . ".contact_id=" . $this->table . ".id 
                    and user_id = " . $user_id . "
                where (consultant_id = " . $user_id . " or agent_id = " . $user_id . " or user_id = " . $user_id . ")
                and " . $table . ".contact_id = " . $this->table . ".id
            )";
    }

    protected function _where($conditions)
    {
        if (isset($conds['search'])) {
            $this->db->group_start();
            $this->db->like('first_name', $conds['search'], 'both');
            $this->db->or_like('last_name', $conds['search'], 'both');
            $this->db->or_like('email', $conds['search'], 'both');
            $this->db->or_like('phone', $conds['search'], 'both');
            $this->db->group_end();
            unset($conds['search']);
        }

        if (isset($conditions['contact_user_id'])) {
            $this->db->group_start();
            $this->db->where($this->table . '.consultant_id', $conditions['contact_user_id']);
            $this->db->or_where($this->table . '.agent_id', $conditions['contact_user_id']);
            $this->db->or_where('exists (select * 
                from '.TABLE_CONTACT_USERS.' 
                where contact_id = '.TABLE_CONTACTS.'.id 
                and user_id = '.$conditions['contact_user_id'].')', null, false);
            $this->db->group_end();
            unset($conditions['contact_user_id']);
        }

        if (isset($conditions['user_id'])) {
            $this->db->join(TABLE_CONTACT_USERS , $this->table . '.id='.TABLE_CONTACT_USERS .'.contact_id and user_id = ' . $conditions['user_id'], 'left');
            $this->db->where(TABLE_CONTACT_USERS . '.user_id', $conditions['user_id']);
            unset($conditions['user_id']);
        }

        return parent::_where($conditions);
    }

    /**
     * @param array $condition
     * @return Mcontacts
     */
    public function fetchOne($condition = array(), $order_by = [], $select = '*')
    {
        return parent::fetchOne($condition, $order_by, $select);
    }

    /**
     * @param $id
     * @return Mcontacts
     */
    public function fetchOneById($id)
    {
        return parent::fetchOneById($id);
    }

    /**
     * @param array $condition
     * @param array $order_by
     * @param array $limit
     * @return Mcontacts[]
     */
    public function fetchAll($condition = array(), $order_by = array(), $limit = array(), $select = '*')
    {
        return parent::fetchAll($condition, $order_by, $limit, $select);
	}

    public function _addCustomRodos()
    {
        $this->CI->load->model('macceptances');
        $acceptance = [
            'source_table' => 1,
            'source_table_id' => $this->id,
            'is_accepted' => 1,
            'ip_address' => $this->CI->input->ip_address(),
            'content' => lang('label_content_of_the_clauses')
        ];
        $names = [lang('label_contact_acceptance'), lang('label_marketing_acceptance'), lang('label_consent_to_process_my_personal_data')];
        $lastId = [];
        foreach ($names as $name) {
            $acceptance['name'] = $name;
            $lastId[] = $this->macceptances->insert($acceptance);
        }

        return $lastId;
    }

    public function withDeal_type()
    {
        return $this->deal_type = $this->deal_type_id ? $this->CI->mselect_option->fetchOneById($this->deal_type_id) : false;
    }

    public function withDeals()
	{
		$this->deals = [];

        if ($deals = $this->CI->mcontact_deal->fetchAll(['contact_id' => $this->{$this->key}])) {
			$deal_ids = array_column($deals, 'deal_id');
			$this->deals = $this->CI->mdeals->fetchAll(['id' => $deal_ids]);
		}
		return $this->deals;
	}

    public function getFullName()
	{
		return $this->first_name . ' ' . $this->last_name;
	}

    public function withUsers($clean = true)
    {
        if (isset($this->users) && $this->users) {
            return $this->users;
        }
        $this->users = [];

        if ($contact_users = $this->CI->mcontact_users->fetchAll(['contact_id' => $this->{$this->key}])) {
            $users = $this->CI->muser->fetchAll(['id' => array_column($contact_users, 'user_id')]);
            foreach ($users as $user) {
                $this->users[] = $clean ? $this->CI->user_lib->prepareData($user) : $user;
            }
        }

        return $this->users;
    }
}


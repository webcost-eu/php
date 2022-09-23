<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Mchat
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property int $created_at
 *
 */
class Mchat extends MainModel
{
    public $id;
    public $user_id;
    public $name;
    public $created_at;
    public $last_message_at;
    public $icon;

    protected $casts = [
        'created_at' => 'iso8601',
        'last_message_at' => 'iso8601'
    ];

    public function __construct()
    {
        $this->table = TABLE_CHAT;
        $this->use_created_at = true;

        parent::__construct();
    }

    /**
     * @param array $condition
	 * @return Mchat
	 */
	public function fetchOne($condition = array(), $order_by = [], $select = '*')
	{
		return parent::fetchOne($condition, $order_by, $select);
	}

    /**
	 * @param array $condition
	 * @param array $order_by
	 * @param array $limit
	 * @return Mchat[]
	 */
	public function fetchAll($condition = array(), $order_by = array(), $limit = array(), $select = '*')
	{
		return parent::fetchAll($condition, $order_by, $limit, $select);
	}

    public function getAllChatsWithLastMsg($chats_ids)
	{
		$select = '*,(SELECT message FROM ' . TABLE_CHAT_MESSAGE . ' where chat_id = ch.id order by posted_at desc limit 1) as last_msg';

        return $q = $this->db->select($select)
			->from($this->table . ' ch')->where_in('id', $chats_ids)->get()->result();
	}

    public function withLastMessage()
	{
		return $this->last_message = $this->CI->mchat_message->fetchOne(['chat_id' => $this->id], ['posted_at' => 'desc']);
	}

    public function withUsers()
	{
		$this->users = [];

        $user = $this->CI->mchat_users->fetchAll(['chat_id' => $this->id]);
		$id = array_column($user, 'user_id');
		$users = $this->CI->muser->fetchAll(['id' => $id]);
		foreach ($users as $user) {
			$this->users[] = $this->CI->user_lib->prepareData($user);
		}
		return $this->users;
	}
}


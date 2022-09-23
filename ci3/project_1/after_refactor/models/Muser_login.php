<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Muser_login
 *
 * @property int $login_id
 * @property int $uid
 * @property int $lastlogintime
 * @property int $logouttime
 * @property string $ipaddress
 * @property int $login_status
 * @property int $domain_id
 */
class Muser_login extends MainModel
{
    public $login_id;
    public $uid;
    public $lastlogintime;
    public $logouttime;
    public $ipaddress;
    public $login_status;
    public $domain_id;

    protected $casts = [
        'lastlogintime' => 'iso8601'
    ];

    public function __construct()
    {
        $this->table = TABLE_USERLOGIN;

        parent::__construct();
    }

    /**
     * @param array $condition
	 * @return Muser_login
	 */
	public function fetchOne($condition = array(), $order_by = [], $select = '*')
	{
		return parent::fetchOne($condition, $order_by, $select);
	}

    /**
     * @param array $condition
     * @param array $order_by
     * @param array $limit
     * @return Muser_login[]
     */
    public function fetchAll($condition = array(), $order_by = array(), $limit = array(), $select = '*')
    {
        return parent::fetchAll($condition, $order_by, $limit, $select);
    }

    public function updateLoginUser($up_data = array())
    {
        $this->db->where('uid', $this->CI->user_lib->getUserId());
        $this->db->update(TABLE_USERLOGIN, $up_data);
    }
}

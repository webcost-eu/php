<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Muser_login_record
 *
 * @property int $id
 * @property int $user_id
 * @property int $userType
 * @property int $logintime
 * @property int $logouttime
 * @property string $ipaddress
 */
class Muser_login_record extends MainModel
{
    public $id;
    public $user_id;
    public $userType;
    public $logintime;
    public $logouttime;
    public $ipaddress;

    protected $casts = [
        'logintime' => 'iso8601',
        'logouttime' => 'iso8601'
    ];

    public function __construct()
    {
        $this->table = TABLE_USERLOGIN_RECORD;

        parent::__construct();
    }

    /**
     * @param array $condition
	 * @return Muser_login_record
	 */
	public function fetchOne($condition = array(), $order_by = [], $select = '*')
	{
		return parent::fetchOne($condition, $order_by, $select);
	}

    /**
	 * @param array $condition
	 * @param array $order_by
	 * @param array $limit
	 * @return Muser_login_record[]
	 */
	public function fetchAll($condition = array(), $order_by = array(), $limit = array(), $select = '*')
	{
		return parent::fetchAll($condition, $order_by, $limit, $select);
	}

}

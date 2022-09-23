<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Mdeals
 *
 * @property int $id
 * @property int $agreement_id
 * @property string $number
 * @property int $status_id
 * @property int $created_at
 *
 */
class Mdeals extends MainModel
{
    use Agreement_model_trait, SelectOptions_trait, Statusable_model_trait;

    public $id;
    public $agreement_id;
    public $number;
    public $status_id;
    public $created_at;

    protected $casts = [
        'created_at' => 'iso8601'
    ];

    public function __construct()
    {
        $this->table = TABLE_DEALS;

        $this->use_created_at_int = true;

        parent::__construct();
    }

    /**
	 * @param array $condition
	 * @return Mdeals
	 */
	public function fetchOne($condition = array(), $order_by = [], $select = '*')
	{
		return parent::fetchOne($condition, $order_by, $select);
	}

    /**
     * @param array $condition
     * @param array $order_by
     * @param array $limit
     * @return Mdeals[]
     */
    public function fetchAll($condition = array(), $order_by = array(), $limit = array(), $select = '*')
    {
        return parent::fetchAll($condition, $order_by, $limit, $select);
    }

    public function insert($data_array = array(), $return_id = true)
    {
        $data_array[$this->key] = $data_array['agreement_id'];

        return parent::insert($data_array, $return_id);
    }

    public function getByAgreement($agreement_id)
    {
        return $this->fetchOne(['agreement_id' => $agreement_id]);
    }

    public function withContact()
    {
        $this->contact = null;

        if ($agreement = $this->withAgreement()) {
			return $this->contact = $agreement->withContact();
		}
	}

}


<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Mdepartments
 *
 * @property-read int $id
 * @property-read int $parent_id
 * @property-read int $leader_id
 * @property-read String $name
 * @property-read int $created_at
 * @property-read String $icon
 */
class Mdepartments extends MainModel
{
    public $id;
    public $parent_id;
    public $leader_id;
    public $name;
    public $created_at;
    public $icon;

    protected $casts = [
        'created_at' => 'iso8610'
    ];

    public function __construct()
    {
        $this->table = TABLE_DEPARTMENTS;

        parent::__construct();
    }

    /**
     * @param array $condition
	 * @return Mdepartments
	 */
	public function fetchOne($condition = array(), $order_by = [], $select = '*')
	{
		return parent::fetchOne($condition, $order_by, $select);
	}

    /**
     * @param array $condition
     * @param array $order_by
     * @param array $limit
     * @return Mdepartments[]
     */
    public function fetchAll($condition = array(), $order_by = array(), $limit = array(), $select = '*')
    {
        return parent::fetchAll($condition, $order_by, $limit, $select);
    }

    public function treeMLMStructure($departments)
    {
        $new = array();
        foreach ($departments as $a) {
            $index = is_null($a->parent_id) ? 0 : $a->parent_id;
            $new[$index][] = $a;
        }

        return $this->create_tree_obj($new, reset($new));
    }

    protected function create_tree_obj(&$list, $parent, $level = 0)
    {
        $level++;

        foreach ($parent as $k => $l) {
            $l->child = [];
            $l->level = $level;
            if (isset($list[$l->id])) {
                $l->child = $this->create_tree_obj($list, $list[$l->id], $level);
            }

            $tree[] = $l;
        }
        return $tree;
    }

    public function getMLMStructure($departments, $newMLM = [])
    {
        if (!$newMLM) {
            $departments = $this->treeMLMStructure($departments);
        }

        foreach ($departments as $department) {
            $newMLM['lvl_' . $department->level][] = $department;
            if ($department->child) {
                $newMLM = $this->getMLMStructure($department->child, $newMLM);
            }
        }

        return $newMLM;
    }

    public function withLeader()
    {
        $this->leader = null;

        if (!$this->leader_id) {
            return;
        }
        return $this->leader = $this->CI->user_lib->prepareData($this->CI->muser->fetchOneById($this->leader_id));
    }

    public function withParent()
	{
		$this->parent = null;

        if (!$this->parent_id) {
			return;
		}
		return $this->parent = $this->fetchOneById($this->parent_id);
	}
}

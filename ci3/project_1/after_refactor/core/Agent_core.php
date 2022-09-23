<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Agent_core extends User_core
{
    use User_trait;

    protected $user_role = USER_ROLE_AGENT;

    protected $provisions_types = [
        ['type' => 1, 'name' => 'provision_general']
    ];

    public function __construct($config = 'rest')
    {
        parent::__construct($config);

        $this->load->model([
			'magents',
			'mnotes',
			'magent_invoice_payments'
		]);

        $this->data['allDepartments'] = $this->mdepartments->fetchAll();
	}

}


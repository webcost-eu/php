<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Consultant extends Consultant_core
{
	use User_trait;

	public function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->initSortableTrait(['id']);
	}

	public function index_getAll()
	{
		parent::index_getAll();
	}

	public function index_getId($id)
	{
	    if ($id != $this->user_lib->getUserId()) {
            $this->validateViewPermission('tab_details');
        }

		parent::index_getId($id);
	}

	public function index_post()
	{
		$this->validateEditPermission('tab_details');

		$this->editUserProcess(USER_ROLE_CONSULTANT);

		$this->set_data(['message' => lang('label_successfully_added')]);
	}

	public function index_put($id)
	{
		$this->validateEditPermission('tab_details');

		$this->editUserProcess(USER_ROLE_CONSULTANT, $id);

		$this->set_data(['message' => lang('label_successfully_updated')]);
	}


	public function index_delete($id)
	{
		$this->validateEditPermission('tab_details');

		$this->delete_user($id);

		$this->set_data(['message' => lang('label_successfully_deleted')]);
	}
}


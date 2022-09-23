<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends User_core
{
	use User_trait;

	public function __construct($config = 'rest')
	{
		parent::__construct($config);
	}

    public function index_getAll()
	{
	    parent::index_getAll();
	}

    public function index_getId($id)
	{
		if (!$this->roleIs(USER_ROLE_ADMIN) && $id != $this->user_lib->getUserId()) {
			$this->setNotAcceptable();
		}

        $item = $this->user_lib->getUser();

        if ($id != $this->user_lib->getUserId() && !($item = $this->muser->fetchOneById($id))) {
			$this->setNotFound();
		}

        $item->withPartnerInfo();
		$this->set_data($item);
	}

    public function index_put($id)
	{
		if (!$this->roleIs(USER_ROLE_ADMIN) && $id != $this->user_lib->getUserId()) {
			$this->setNotAcceptable();
		}

        if (!($oldUser = $this->muser->fetchOneById($id))) {
			$this->setNotFound();
		}

        $id = $this->editUserProcess($oldUser->userType, $id);
		$item = $this->muser->fetchOneById($id);
		$this->set_data(['item' => $item, 'message' => lang('label_successfully_updated')]);
	}

    public function unblock_getId($id)
	{
		$this->validateEditPermission('unblock');

        if (!($item = $this->muser->fetchOneById($id))) {
			$this->setNotFound();
		}

        $this->user_lib->unblock($id);
		$this->mfailed_auth->update(['status' => 2], ['user_id' => $id]);

        $item = $this->muser->fetchOneById($id);
		$this->set_data(['item' => $item, 'message' => lang('message_user_unblock_success')]);

    }

    public function block_getId($id)
	{
		$this->validateEditPermission('block');

        if (!($item = $this->muser->fetchOneById($id))) {
			$this->setNotFound();
		}

        $this->user_lib->block($id, true);
		$this->mfailed_auth->update(['status' => 1], ['user_id' => $id]);

        $item = $this->muser->fetchOneById($id);
		$this->set_data(['item' => $item, 'message' => lang('message_user_block_success')]);
	}

    public function unblockIp_getAll()
	{
		$this->validateEditPermission('unblockIp');

        if (!($ip = $this->input->get('ip'))) {
			$this->setBadRequest();
		}

        $this->mfailed_auth->update(['status' => 2], ['ip' => $ip]);

        $this->set_data(['message' => lang('message_ip_unblock_success')]);
	}

    public function blockIp_getAll()
	{
		$this->validateEditPermission('blockIp');

        if (!($ip = $this->input->get('ip'))) {
			$this->setBadRequest();
		}

        if (!$this->mfailed_auth->update(['status' => 1], ['ip' => $ip])) {
			$this->set_data(['message' => lang('message_ip_block_unsuccess')]);
		}

        $this->set_data(['message' => lang('message_ip_block_success')]);
	}

    public function change_password_post()
	{
		$user_id = $this->user_lib->getUserId();

        $this->form_validation->set_rules('oldPassword', 'Old Password', 'trim|required');
		$this->form_validation->set_rules('userPassword', 'New Password', 'trim|required');
		$this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'trim|required|matches[userPassword]');

        $this->form_validation->runAndResponseOnFail();

        $user_data = $this->form_validation->getResults(['oldPassword', 'confirmPassword']);
		$user_data['userPassword'] = md5($user_data['userPassword']);

        if (!($this->muser->countRows(['id' => $user_id, 'userPassword' => md5($this->input->post('oldPassword'))]))) {
			$this->setUnprocessableEntity(lang('label_old_password_not_matches'));
		}

        $user_data['password_update_time'] = time();

        $this->muser->update($user_data, array('id' => $user_id));
		$this->set_data(['message' => lang('label_password_updated_successfully')]);
	}

    public function force_logout_post()
	{
		Jwt_auth::forceLogout($this->user_lib->getUserId());
		$this->set_data(['message' => lang('message_logouted')]);
	}

    public function index_delete($id)
    {
        $this->validateEditPermission('tab_details');

        $this->delete_user($id);

        $this->set_data(['message' => lang('label_successfully_deleted')]);
    }
}

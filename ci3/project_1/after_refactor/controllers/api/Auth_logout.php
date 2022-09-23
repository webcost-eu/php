<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Auth_logout
 *
 *
 */
class Auth_logout extends REST_Controller
{
	public function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->load->model(['muser_login', 'muser_login_record']);
	}

	public function index_post()
    {
        $condarr['login_status'] = 0;
        $condarr['logouttime'] = time();
        $this->muser_login->updateLoginUser($condarr);

        if ($log_record = $this->muser_login_record->fetchOne(
            ['user_id' => $this->user_lib->getUserId()],
            ['id' => 'DESC'])) {
            $this->muser_login_record->update(['logouttime' => time()], ['id' => $log_record->id]);
        }

        $this->mrefresh_tokens->delete([
            'fingerprint' => $this->input->post('fingerprint'),
            'user_id' => $this->user_lib->getUserId()
		]);

        $this->set_data(['message' => lang('message_logouted')]);
	}
}

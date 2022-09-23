<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Chat_core extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model([
			'mchat',
			'mchat_message',
			'mchat_message_status',
			'mchat_users'
		]);
		$this->load->library('upload');
		
	}
	
	protected function isBelongsToChatOrResponse($chat_id)
	{
		if (!$this->mchat_users->isUserBelongsToChat($this->user_lib->getUserId(), $chat_id)) {
			$this->setNotFound();
		}
		
		return true;
	}
	
	protected function isChatOwnerOrResponse($user_id, $chat_id)
	{
		if (!$this->isChatOwner($user_id, $chat_id)) {
			$this->jsonResponse([
				'status' => 'error',
				'message' => lang('error_chat_not_found')
			]);
		}
	}
	
	protected function isChatOwner($user_id, $chat_id)
	{
		if (!$this->mchat->fetchOne(['user_id' => $user_id, 'id' => $chat_id])) {
			return false;
		}
		
		return true;
	}
	
	public function _isValidChatUser($chatUser)
	{
		$this->form_validation->set_message(
			'_isValidChatUser',
			lang('error_user_not_found')
		);
		
		$chat_id = (int)$this->input->post('id');
		if (!$chatUser && !$chat_id) {
			return false;
		}
		
		if (!$chatUser && $chat_id) {
			return true;
		}
		
		if (!$this->muser->countRows(['id' => $chatUser, 'userType !=' => USER_ROLE_CLIENT])) {
			return false;
		}
		
		return true;
	}
	
	public function _isDuplicate($value)
	{
		$this->form_validation->set_message(
			'_isDuplicate',
			lang('error_chat_with_same_users_exists')
		);
		
		$new_users = $this->input->post('chat_user');
		$new_users[] = $this->user_lib->getUserId();
		
		$chats = $this->mchat_users->getChatsForValidation($this->user_lib->getUserId());
		foreach ($chats as $chat_id => $users) {
			if (!$value && count(array_intersect($new_users, $users)) == count($users) && count($new_users) == count($users)) {
				return false;
			}
		}
		
		return true;
	}
}


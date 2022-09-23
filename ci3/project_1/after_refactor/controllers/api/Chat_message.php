<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Chat_message extends Chat_core
{
	public function index_getAll()
	{
		$this->validateAccessPermission('chat');

		if (!($chat_id = $this->input->get('chat_id'))) {
			$this->setBadRequest();
		}
		$this->isBelongsToChatOrResponse($chat_id);

		$conditions['chat_id'] = $chat_id;

		$messages = $this->mchat_message->fetchAll($conditions);
		$messages = $this->formatMessages($messages);

		$this->set_data($messages);
	}

	public function index_getId($id)
	{
		$this->validateAccessPermission('chat');

		if (!($message = $this->mchat_message->fetchOneById($id))) {
			$this->setNotFound();
		}

		$this->isBelongsToChatOrResponse($message->chat_id);

		$messages = $this->mchat_message->fetchOneById($id);
		$messages = $this->formatMessages([$messages]);

		$message = reset($messages);

		$this->set_data($message);
	}

	public function info_post()
	{
		$this->validateAccessPermission('chat');
		if (!($chat_id = $this->input->post('chat_id'))) {
			$this->setBadRequest();
		}

		$this->isBelongsToChatOrResponse($chat_id);

		$message_id = $this->input->post('message_id');
		$response['messages'] = [];

		if (!$message_id || !is_array($message_id)) {
			$this->setBadRequest($response);
		}

		$conditions['id'] = $message_id;
		$conditions['chat_id'] = $chat_id;

		$messages = $this->mchat_message->fetchAll($conditions);
		$messages = $this->formatMessages($messages);

		$this->set_data($messages);
	}

	public function index_post()
	{
		$this->editProcess();
	}

	public function index_put($id)
	{
		$this->editProcess($id);
	}

	public function editProcess($id = false)
	{
		$this->form_validation->set_rules('chat_id', lang('label_chat'), 'required|integer');
		$this->form_validation->set_rules('message', lang('label_message'), 'required');

		$this->form_validation->runAndResponseOnFail();
		$user_message = $this->form_validation->getResults();

		$this->isBelongsToChatOrResponse($user_message['chat_id']);
		$user_id = $this->user_lib->getUserId();

		$time = getTimestamp();

		$this->mchat->update(['last_message_at' => $time], ['id' => $id]);

		$user_message['from_user_id'] = $user_id;
		$user_message['posted_at'] = $time;

		if ($id) {
			$this->mchat_message->update($user_message, ['id' => $id]);
		} else {
			$id = $this->mchat_message->insert($user_message);
		}

		$users = $this->mchat_users->getByChat($id);
		$status = [];
		foreach ($users as $user) {
			$status[] = [
				'chat_message_id' => $id,
				'user_id' => $user->user_id,
				'seen_at' => $user->user_id == $user_id ? getTimestamp() : null
			];
		}
		$this->mchat_message_status->insertBatch($status);

		$this->set_data([]);
	}

	public function index_delete($id)
	{
		if (!($message = $this->mchat_message->fetchOneById($id))) {
			$this->setNotFound();
		}

		$this->isBelongsToChatOrResponse($message->chat_id);

		if ($message->from_user_id != $this->user_lib->getUserId()) {
			$this->setNotFound();
		}

        $message->_delete();

        $this->set_data(['message' => lang('message_delete_success')]);
	}
}


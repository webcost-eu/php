<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Chat extends Chat_core
{
	public function index_getAll()
	{
		$this->validateAccessPermission('chat');

		$user_id = $this->user_lib->getUserId();

		$conditions['user_id'] = $user_id;

		if ($chats = $this->mchat_users->fetchAll($conditions)) {
			$chats_ids = array_column($chats, 'chat_id');
			$chats = $this->mchat->fetchAll(['id' => $chats_ids]);

			foreach ($chats as $index => $chat) {
				$conditions = [];
				$conditions['chat_id'] = $chat->id;
				$chat->withLastMessage();
				$unseen = $this->mchat_message->getUnseen($user_id);

				$chat->withUsers();

				$chats[$index]->unseen_count = isset($unseen[$chat->id]) ? $unseen[$chat->id] : 0;
			}
		}

		$this->set_data($chats);
	}

	public function index_getId($id)
	{
		$user_id = $this->user_lib->getUserId();

		$this->validateAccessPermission('chat');
		$this->isBelongsToChatOrResponse($id);

		$chat = $this->mchat->fetchOneById($id);
		$chat->withLastMessage();

		$conditions = [];
		$conditions['chat_id'] = $chat->id;

		if ($this->input->get('before')) {
			$conditions['id <'] = $this->input->get_post('before');
		} elseif ($this->input->get('after')) {
			$conditions['id >'] = $this->input->get('after');
		}

		if ($messages = $this->mchat_message->fetchAll($conditions, ['posted_at' => 'desc'], $this->getLimitForQuery())) {

			$messages = array_reverse($messages);
			$messages = $this->formatMessages($messages);
		}

		$chat->messages = $messages;

		$unseen = $this->mchat_message->getUnseen($user_id);

		foreach ($messages as $message) {
			$markAsSeen = [
				'seen_at' => getTimestamp(),
				'chat_message_id' => $message->id,
				'user_id' => $user_id
			];
			$this->mchat_message_status->insertIgnoreOnDuplicate([$markAsSeen], ['seen_at' => 'values(seen_at)']);
		}

		$chat->unseen_count = isset($unseen[$chat->id]) ? $unseen[$chat->id] : 0;

		$this->set_data($chat);
	}

	public function index_post()
	{
		$id = $this->editProcess();
		$item = $this->mchat->fetchOneById($id);
		$this->set_data(['item' => $item, 'message' => lang('label_successfully_added')]);
	}

	public function index_put($id)
	{
		$id = $this->editProcess($id);

		$item = $this->mchat->fetchOneById($id);
		$this->set_data(['item' => $item, 'message' => lang('label_successfully_updated')]);
	}

	public function editProcess($id = false)
	{
		$this->validateAccessPermission('chat');
		if ($id) {
			$this->isChatOwnerOrResponse($this->user_lib->getUserId(), $id);
		}

		$this->form_validation->set_rules('id', lang('label_chat'), 'callback__isDuplicate');
		$this->form_validation->set_rules('name', lang('label_chat_name'), 'required');
		$this->form_validation->set_rules('chat_user[]', lang('label_chat_user'), (!$id ? 'required|' : '') . 'callback__isValidChatUser');
		$this->form_validation->set_rules('icon', lang('label_icon'), 'upload[0]');
		$this->form_validation->set_rules('delete_icon', lang('label_delete_logo'), 'trim');

		$this->form_validation->runAndResponseOnFail();

		$chatInput = $this->form_validation->getResults(['delete_icon']);

		$delete_icon = isset($chatInput['delete_icon']) && $chatInput['delete_icon'] ? true : false;

		$chatData = [
			'user_id' => $this->user_lib->getUserId(),
			'name' => $chatInput['name']
		];

		$chat_user = [];

		if (!$id) {
			$id = $this->mchat->insert($chatData);
			$chat_user[] = [
				'chat_id' => $id,
				'user_id' => $this->user_lib->getUserId()
			];
		} else {
			$this->mchat->update($chatData, ['id' => $id]);
		}

		if (isset($chatInput['chat_user']) && $chatInput['chat_user']) {
			foreach ($chatInput['chat_user'] as $user) {
				$chat_user[] = [
					'chat_id' => $id,
					'user_id' => $user
				];
			}

		}

		if ($chat_user) {
			$this->mchat_users->insertIgnoreBatch($chat_user);
		}

		$chat = $this->mchat->fetchOneById($id);
		$chat->chat_user = $chat_user;

		if ($delete_icon) {
			if (file_exists($chat->icon)) {
				@unlink($chat->icon);
			}
			$this->mchat->update(['icon' => null], ['id' => $chat->id]);
		}

		if (!empty($_FILES['icon']['name'])) {
			if (file_exists($chat->icon)) {
				@unlink($chat->icon);
			}

			// upload favicon
			$config['upload_path'] = 'uploads/chat_icons/';
			$config['allowed_types'] = 'ico|jpg|png|gif|jpeg';
			//$config['max_size'] = 250;
			$config['max_width'] = 150;
			$config['max_height'] = 150;
			$config['remove_spaces'] = true;
			$config['encrypt_name'] = true;
			$config['overwrite'] = true;

			$this->upload->initialize($config);
			is_dir_or_create($config['upload_path']);

			if (!$this->upload->do_upload('icon')) {
			} else {
				$fileData = $this->upload->data();
				$this->mchat->update(['icon' => $config['upload_path'] . $fileData['file_name']], ['id' => $chat->id]);
			}
		}

		return $id;
	}

	public function index_delete($id)
	{
		if (!$this->roleIs(USER_ROLE_ADMIN)) {
			$this->isChatOwnerOrResponse($this->user_lib->getUserId(), $id);
		}

		$this->mchat->delete(['id' => $id]);

		$this->set_data(['message' => lang('label_successfully_deleted')]);
	}
}


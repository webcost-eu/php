<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Agent_notes extends Agent_core
{
	use Sortable_trait;

	public function __construct($config = 'rest')
	{
		parent::__construct($config);
		$this->data = [];
		$this->initSortableTrait(['id']);
	}

	public function index_getAll()
	{
		$this->validateViewPermission('notes');
		if (!($id = $this->input->get('user_id'))) {
			$this->setNotFound();
		}

		if (!($user_data = $this->muser->fetchOne(array('id' => $id, 'userType' => USER_ROLE_AGENT)))) {
			$this->setNotFound();
		}

		$items = $this->mnotes->fetchAll(array('user_id' => $id), $this->getSort(), $this->getLimitForQuery());
		$total = $this->mnotes->countRows(array('user_id' => $id));

		$this->set_paged_data($total, $items);
	}

	public function index_getId($id)
	{
		$this->validateViewPermission('notes');
		if (!($note = $this->mnotes->fetchOneById($id))) {
			$this->setNotFound();
		}

		if (!($user_data = $this->muser->fetchOne(array('id' => $note->user_id, 'userType' => USER_ROLE_AGENT)))) {
			$this->setNotFound();
		}

		$this->set_data($note);
	}

	public function index_post()
	{
		$this->editProcess();
	}

	public function index_put($id)
	{
		$this->editProcess($id);
	}

	private function editProcess($id = false)
	{
		$this->validateEditPermission('notes');

		$this->form_validation->set_rules('title', lang('label_title'), 'trim|required');
		$this->form_validation->set_rules('description', lang('label_description'), 'trim|required');
		$this->form_validation->set_rules('user_id', lang('label_agent'), 'trim|required|is_natural_no_zero');

		$this->form_validation->runAndResponseOnFail();

		$input_data = $this->form_validation->getResults();

		if ($id) {
			if (!($noteData = $this->mnotes->fetchOneById($id))) {
				$this->setNotFound();
			}

			$this->mnotes->update($input_data, array('id' => $id));
			

			$message = lang('label_successfully_updated');
		} else {
			$input_data['postedtime'] = time();

			$this->mnotes->insert($input_data);

			$message = lang('label_successfully_added');
		}

		$this->set_data(['message' => $message]);
	}

	public function index_delete($id)
	{
		$this->validateEditPermission('notes');

		$return_data['has_error'] = 0;

		if (!($notedata = $this->mnotes->fetchOneById($id))) {
			$this->setNotFound();
		}

		$this->mnotes->delete(array('id' => $id));

		$this->set_data(['message' => lang('label_successfully_deleted')]);
	}
}


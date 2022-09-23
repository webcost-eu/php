<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class MY_Controller
 *
 * Core classes
 * @property-read CI_Config $config
 * @property-read MY_Form_validation $form_validation
 * @property-read CI_Input $input
 * @property-read CI_Output $output
 * @property-read CI_Session $session
 * @property-read CI_Loader $load
 * @property-read CI_Lang $lang
 * @property-read CI_DB_forge $dbforge
 * @property-read CI_URI $uri
 * @property-read CI_DB_mysqli_driver|CI_DB_driver|CI_DB_result $db
 * @property-read CI_Security $security
 * @property-read CI_User_agent $agent
 * @property-read MY_Upload $upload
 *
 * libraries
 * @property-read Breadcrumbs $breadcrumbs
 * @property-read Google_calendar $google_calendar
 * @property-read Google_lib $google_lib
 * @property-read File_lib $file_lib
 * @property-read Datatable $datatable
 * @property-read User_lib $user_lib
 * @property-read Elastic_lib $elastic_lib
 *
 * models
 * @property-read Malllanguage $malllanguage
 * @property-read Muser $muser
 * @property-read Mrole_access $mrole_access
 * @property-read Mlanguage $mlanguage
 *
 * @property-read Mchat $mchat
 * @property-read Mchat_message $mchat_message
 * @property-read Mchat_message_status $mchat_message_status
 * @property-read Mchat_users $mchat_users
 *
 */
class MY_Controller extends CI_Controller
{
	public $data = array();
	public $template = 'project_1';
	public $general_settings = false;
	public $locales = [];
	public $locale = null;
	protected $isRest = false;
	public $mlm_agent_ids = [];
    public $admin;
    
    /**
     * MY_Controller constructor.
     */
    public function __construct()
	{
		parent::__construct();

        $this->load->language('labels');
		$this->load->language('calendar');
		$this->load->language('date');

        $this->load->model([
            'muser',
            'mlanguage',
            'muser_login',
            'muser_login_record'
        ]);

        $this->load->library([
            'user_lib',
            'file_lib',
            'breadcrumbs',
            'trans',
            'mix',
            'notifications_lib',
            'activity_lib',
            'documents_lib',
            'google_lib'
        ]);

        $this->data['page_title'] = '';

        $this->locales = $this->malllanguage->fetchAll(['status' => 'Y'], ['weight' => 'ASC']);

        $this->data['general_settings'] = $this->general_settings = $this->mgeneral_settings->fetchOne();

        $this->data['template'] = $this->template;

        $this->data['favicon'] = $this->data['general_settings']->favicon
            ? UPLOAD_PATH_URL . 'favicons/' . $this->data['general_settings']->favicon
            : '';

        $this->load->library([
            'messages_lib',
            'patterns_lib',
            'replace_tokens'
        ]);
        reloadLangFiles();

        if ($this->user_lib->isLogged()) {
            $this->mlm_agent_ids = $this->muser->getAllUsersId($this->user_lib->getUserId());
        }
        $this->admin = $this->muser->fetchOne(['userType' => USER_ROLE_ADMIN, 'is_superadmin' => 1], ['id' => 'asc']);
    }
    
    /**
     * @return bool
     */
    public function isRest()
	{
		return $this->isRest;
	}
    
    /**
     * @param $title
     * @param bool $show_content_title
     */
    protected function setPageTitle($title, $show_content_title = true)
	{
		$this->data['page_title'] = $title;
		$this->data['show_content_title'] = $show_content_title;
	}
    
    /**
     * @return bool
     */
    protected function validateFormOrResponse()
	{
		if ($this->form_validation->runAndResponseOnFail() == false) {
			$this->jsonResponse(['error' => validation_errors()]);
		}

        return true;
	}
    
    /**
     * @param $data
     */
    public function jsonResponse($data)
	{
		$this->output
			->set_status_header(200)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
			->_display();
		exit;
	}
    
    /**
     * @param string|false $content_template
     * @param string $template
     */
    public function show($content_template = false, $template = 'template')
	{
		if ($this->session->userdata('error')) {
			$this->session->set_flashdata('error', $this->session->userdata('error'));
			$this->data['error'] = $this->session->error;
		}

        if ($this->session->userdata('success')) {
			$this->session->set_flashdata('success', $this->session->userdata('success'));
			$this->data['success'] = $this->session->success;
		}

        if ($this->session->userdata('form_data')) {
			$this->session->set_flashdata('form_data', $this->session->form_data);
			$this->data['form_data'] = $this->session->form_data;
		}

        if (!isset($this->data['page_title'])) {
			$this->setPageTitle($this->data['general_settings']->SiteTitle);
		}

        $this->mydata['translations'] = json_encode($this->trans->getUserLocalizationFrontData());

        $this->data["header_scripts"] = $this->load->view('partials/header_scripts', $this->data, true);

        $this->data["footer_scripts"] = $this->load->view('partials/footer_scripts', $this->data, true);

        if (file_exists(APPPATH . 'views/partials/' . $this->router->class . '/common_script.php')) {
			$this->data['common_script'] = $this->load->view('partials/' . $this->router->class . '/common_script', $this->data, TRUE);
		}

        if (!$content_template) {
			$content_template = 'partials/' . $this->router->class . '/' . $this->router->method;

            if (!file_exists(VIEWPATH . $content_template . '.php')) {
				show_error('Not found: ' . $content_template);
			}
		}

        $this->data['content'] = $this->load->view($content_template, $this->data, true);

        $this->data['breadcrumbs'] = $this->breadcrumbs->show();

        $this->load->view($template, $this->data);
	}
    
    /**
     * @param $template
     */
    public function setContent($template)
	{
		$this->data['content'] = $this->load->view($template, $this->data, true);
	}
    
    /**
     * @param bool $upper_header
     * @param bool $tab_header
     * @param bool $common_script
     */
    protected function includePartialViews($upper_header = true, $tab_header = true, $common_script = true)
	{
		if ($upper_header && file_exists(APPPATH . 'views/partials/' . $this->router->class . '/upper_header.php')) {
			$this->data['upper_header'] = $this->load->view('partials/' . $this->router->class . '/upper_header', $this->data, TRUE);
		}

        if ($tab_header && file_exists(APPPATH . 'views/partials/' . $this->router->class . '/tab_header.php')) {
			$this->data['tab_header'] = $this->load->view('partials/' . $this->router->class . '/tab_header', $this->data, TRUE);
		}

        if ($common_script && file_exists(APPPATH . 'views/partials/' . $this->router->class . '/common_script.php')) {
			$this->data['common_script'] = $this->load->view('partials/' . $this->router->class . '/common_script', $this->data, TRUE);
		}
	}
    
    /**
     * @return array
     */
    public function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

        return array($key => $value);
	}
    
    /**
     * @return bool
     */
    public function _valid_csrf_nonce()
	{
		if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
			$this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')) {
			return true;
		} else {
			return false;
		}
	}
    
    /**
     * @param false $controller
     * @return string
     */
    public function getControllerName($controller = false)
	{
		return strtolower($controller ? $controller : get_class($this));
	}
}

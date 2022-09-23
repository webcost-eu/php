<?php

/**
 * Trait Auth_trait
 *
 */
trait Auth_trait
{
	protected $access = [];
	protected $user = null;
	protected $roles = [];
	protected $controllers = [];
	public $roleAccess = [];

	public function initAuthTrait()
	{
		$this->load->library('user_lib');
		$this->config->load('access', true);
		$this->load->model([
			'mrole_access',
			'magreement',
			'mcontacts',
			'mnotification',
			'mconsultant',
			'magents',
			'msolicitors'
		]);

        $this->roles = $this->user_lib->user_roles;
        $this->controllers = $this->user_lib->controllers;
        $this->roleAccess = $this->user_lib->getData('role_access');

        if (!$this->isRest()) {
            $this->validateAccessPermission();
        }

        $this->data['user_details'] = $this->user_lib->getData();
    }

    public function roleIsAdmin()
    {
        return $this->roleIs(USER_ROLE_ADMIN);
    }

    public function roleIsAgent()
    {
        return $this->roleIs(USER_ROLE_AGENT);
    }

    public function roleIsConsultant()
    {
        return $this->roleIs(USER_ROLE_CONSULTANT);
    }

    public function roleIsSecretary()
    {
        return $this->roleIs(USER_ROLE_SECRETARY);
    }

    public function roleIsAccountant()
    {
        return $this->roleIs(USER_ROLE_ACCOUNTANT);
    }

    public function roleIsSolicitor()
    {
        return $this->roleIs(USER_ROLE_SOLICITOR);
    }

    public function roleIs($role)
    {
        return $this->user_lib->roleIs($role);
    }

    public function roleIn($roles)
    {
        return in_array($this->user_lib->getData('userType'), $roles);
    }

	public function getRole($role = false)
	{
		return $this->user_lib->getRole($role);
	}

    public function getRoles()
	{
		return $this->roles;
	}

    public function getModule()
	{
		return $this->getControllerName();
	}

    public function getControllerName($controller = false)
	{
		return strtolower($controller ? $controller : get_class($this));
	}

    public function moduleIs($module)
	{
		return $this->getModule() == $module;
	}

    public function canEdit($controller, $method)
	{
		return $this->can('edit', $controller, $method);
	}

    public function canView($controller, $method)
	{
		return $this->can('view', $controller, $method);
	}

    protected function can($action, $controller, $method)
	{
		if ($this->roleIs(USER_ROLE_ADMIN)) {
			return true;
		}

        if (in_array($controller, ['dashboard', 'user', 'general_settings', 'settings', 'auth', 'select_options'])) {
			return true;
		}

        if ($this->isRest()) {
			$oldOne = $controller;
			$controller = str_replace('_core', '', strtolower(get_parent_class($this)));

            if (isset($this->roleAccess[$controller . '.' . $method . '.' . $action])
                && $this->roleAccess[$controller . '.' . $method . '.' . $action]) {
				return true;
			}
			$controller = $oldOne;
		}

        return !isset($this->roleAccess[$controller . '.' . $method . '.' . $action])
		|| $this->roleAccess[$controller . '.' . $method . '.' . $action] == 0
			? false : true;
	}

    public function hasAccess($controller = false, $method = 'tab_details')
    {
        if (!$controller) {
            $controller = strtolower(get_class($this));
        }

        return $this->canView($controller, $method);
    }

    protected function validateViewPermission($method, $controller = false)
	{
		if (!$this->canView($this->getControllerName($controller), $method)) {
			$this->setPermissionError(lang('label_permission_view'), $this->agent->referrer());
		}
	}

    protected function validateEditPermission($method, $controller = false)
	{
		if (!$this->canEdit($this->getControllerName($controller), $method)) {
			$this->setPermissionError(lang('label_permission_edit'), $this->agent->referrer());
		}
	}

    protected function validateAccessPermission($controller = false)
	{
		if (!$this->hasAccess($this->getControllerName($controller))) {
			$this->setPermissionError(lang('label_role_access_denied'), $this->agent->referrer());
		}
	}

    protected function setPermissionError($message, $redirect = 'dashboard')
	{
		if ($this->input->is_ajax_request()) {
			$this->jsonResponse(['error' => $message]);
		} elseif ($this instanceof REST_Controller) {
			$this->setForbidden();
		} else {
			$this->session->set_flashdata('error', $message);
			if ($redirect == base_url($this->uri->uri_string()) || !$redirect) {
				$redirect = 'dashboard';
			}
			redirect($redirect);
		}
	}
}

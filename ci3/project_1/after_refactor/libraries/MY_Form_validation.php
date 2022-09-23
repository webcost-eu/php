<?php

use Carbon\Carbon;

/**
 * Class MY_Form_validation
 *
 * @property-read Mcontacts $mcontacts
 * @property-read Muser $muser
 * @property-read Mselect_option $mselect_option
 *
 */
class MY_Form_validation extends CI_Form_validation
{
	protected $postdata = [];

	public function __construct($rules = array())
	{
		parent::__construct($rules);
		$this->CI->load->language('labels');
		$this->CI->load->model([
            'mdeals',
            'mcontacts',
            'mselect_option',
            'muser',
            'mdepartments',
            'mcountries',
            'magreement_tasks',
            'mdeals'
        ]);
	}

    public function set_array_field_rules($field, $title, $rules)
    {
        $required = false;
        if (strpos($rules, 'required') !== false) {
            $required = true;
            $this->set_rules($field . '[0]', $title, $rules);
        }

        if (($items = $this->CI->input->post($field)) && is_array($items)) {
            foreach ($items as $k => $item) {
                if (!$k && $required) {
                    continue;
                }
                $this->set_rules($field . '[' . $k . ']', $title, $rules);
            }
        }
    }

	public function set_array_field_rules_2($field, $title, $rules, $level = 0)
	{
        $items = $this->CI->input->post($field);

        if ($level) {
            $this->set_array_field_rules($field, $title, $rules);
            return;
        }

        $count = count($items);
        if (!$items || !is_array($items)) {
            $count = 1;
        }

        for($i = 0; $i < $count; $i++) {
            foreach ($rules as $sub_field => $sub_rule) {
                $this->set_rules($field . '[' . $i . '][' . $sub_field . ']', $title, $sub_rule);
            }
        }
    }

    public function run($group = '')
	{
		$this->postdata = $this->CI->input->post();

        if (parent::run($group)) {
			return true;
		}

        if ($this->CI instanceof REST_Controller) {
			$this->CI->set_data([
				$this->CI->config->item('rest_status_field_name') => FALSE,
				'errors' => $this->_error_array
			]);
		}

        return false;
	}

    public function runAndResponseOnFail($group = '')
	{
		if ($this->run($group)) {
			return true;
		}

        if ($this->CI instanceof REST_Controller) {
			$this->CI->setUnprocessableEntity();
		}
	}

    public function getResults($without = [])
	{
		if (!is_array($without)) {
			$without = [$without];
		}
		$ret = [];
		foreach ($this->_field_data as $field_name => $item) {
			//$this->getResultField[$field_name];
			$value = set_value($field_name, null, false);
			if ($item['is_array']) {
				if (preg_match('/^(\w+)(\[(\w+)?\])(\[(\w+)\])?$/', $field_name, $matches)) {
					foreach ($matches as $index => $match) {
						if (!($index % 2)) {
							unset($matches[$index]);
						}
					}
					$matches = array_values($matches);

                    if (count($matches) == 3) {
						$ret[$matches[0]][$matches[1]][$matches[2]] = set_value($field_name, null, false);
					} elseif (count($matches) == 2) {
						$ret[$matches[0]][$matches[1]] = set_value($field_name, null, false);
					} elseif (count($matches) == 1) {
						$ret[$matches[0]] = set_value($matches[0], null, false);
					}

                    $field_name = $matches[0];
				}
			} else {
				$ret[$field_name] = $value;
			}

            if (!array_key_exists($field_name, $this->postdata) && !$item['is_array']) {
				unset($ret[$field_name]);
			}

            if (in_array($field_name, $without)) {
				unset($ret[$field_name]);
			}
		}
		return $ret;
	}


    public function getResultField($field_name)
	{
		if (!array_key_exists($field_name, $this->_field_data)) {
			return null;
		}
		$item = $this->_field_data[$field_name];

        $value = set_value($field_name, null, false);

        if ($item['is_array']) {
			$value = [];
			if (preg_match('/^(\w+)(\[(\w+)?\])(\[(\w+)\])?$/', $field_name, $matches)) {
				foreach ($matches as $index => $match) {
					if (!($index % 2)) {
						unset($matches[$index]);
					}
				}
				$matches = array_values($matches);

                if (count($matches) == 3) {
					$value[$matches[1]][$matches[2]] = set_value($field_name, null, false);
				} elseif (count($matches) == 2) {
					$value[$matches[1]] = set_value($field_name, null, false);
				} elseif (count($matches) == 1) {
					$value = set_value($matches[0], null, false);
				}
			}
		}

        if (!array_key_exists($field_name, $this->postdata) && !$item['is_array']) {
			return null;
		}

        return $value;
	}

    /**
	 * set error message
	 *
	 * sets the error message associated with a particular field
	 *
	 * @param string $field Field name
	 * @param string $error Error message
	 */
	public function setError($field, $error)
	{
		$this->_field_data[$field]['error'] = $error;
		$this->_field_data[$field]['error'] = $error;
	}

    public function in_list($value, $list, $strict = false)
	{
		return in_array($value, explode(',', $list), $strict);
	}

    public function checkNull($str = '')
	{
		if ($str === 'null') {
			return null;
		}
		return $str;
	}

    public function set_null($str = '')
	{
		if (!$str) {
			return null;
		}
		return $str;
	}

    public function decimal($str)
	{
		return (bool)preg_match('/^[\-+]?([0-9]+)|([0-9]+\.?[0-9]+)$/', $str);
	}

    public function time($in)
	{
		$this->set_message('time', lang('error_time'));
		return preg_match('/^[0-9][0-9]:[0-9][0-9]$/', $in) ? true : false;
	}

    public function date($in, $required = true)
	{
		$this->set_message('date', lang('error_invalid_date'));
		if (!$required && !$in) {
			return true;
		}

        $in = str_replace('/', '-', $in);
		try {
			$date = Carbon::parse($in);
			return $date->format('Y-m-d');
		} catch (Exception $e) {
			return false;
		}
	}

    public function date_to_int($in, $required = true)
	{
		$this->set_message('date', lang('error_invalid_date'));
		if (!$required && !$in) {
			return true;
		}

        $in = str_replace('/', '-', $in);
		try {
			$date = Carbon::parse($in);
			return $date->getTimestamp();
		} catch (Exception $e) {
			return false;
		}
	}

    public function date_int($in, $required = true)
	{
		$this->set_message('date_int', lang('error_invalid_date'));
		if (!$required && !$in) {
			return true;
		}

        try {
			if (is_numeric($in)) {
				$date = Carbon::createFromTimestamp($in);
			} else {
				$in = str_replace('/', '-', $in);
				$date = Carbon::parse($in);
			}

            return $date->getTimestamp();
		} catch (Exception $e) {
			return false;
		}
	}

    public function fontSize($str)
    {
        $this->set_message('fontSize', lang('form_validation_regex_match'));
        return (bool)preg_match('/^(([0-9]+)|([0-9]+\.?[0-9]+))(rem|%|px)$/', $str);
    }

    public function my_unique($str = '', $old = '')
	{
		list($old, $field) = explode(';', $old, 2);
		if ((strlen($old) > 1) && $old == $str) {
			return TRUE;
		}

        $this->set_message('my_unique', lang('error_my_unique'));
		return $this->is_unique($str, str_replace('|', '.', $field));
	}

    public function valid_gmail($str, $required = true)
	{
		$this->set_message('valid_gmail', lang('email_invalid_address'));

        if (!$required && !$str) {
			return true;
		}
		if (!$this->valid_email($str)) {
			return false;
		}

        return $this->regex_match($str, '/(@gmail\.com)$/');
	}

    public function upload($str = '', $req = 0, $cond = false)
	{
		if ($req == 0) {
			if (!isset($_FILES[$str]) || !$_FILES[$str]['name']) {
				return $str;
			}
		}

        $CI = &get_instance();
		if (!($CI->config->item('uploads'))) {
			$CI->config->load('uploads');
		}

        $conf = $CI->config->item('uploads');

        if (isset($conf[$str])) {
			$conf = $conf[$str];
		} else {
			$conf = $conf['default'];
		}

        if (!is_dir($conf['upload_path'])) {
			mkdir($conf['upload_path'], 0777, true);
		}

        $CI->load->library('upload');
		$CI->upload->initialize($conf);
		if (isset($_FILES[$str]) && is_array($_FILES[$str]["name"])) {
			if ($CI->upload->do_multi_upload($str)) {
				$data = $CI->upload->get_multi_upload_data();
				$ret = array();
				foreach ($data as $element)
					$ret[] = $element['file_name'];
				return implode('|', $ret);
			} else {
				$this->set_message($cond ? 'uploadCond' : 'upload', $CI->upload->display_errors('', ''));
				return FALSE;
			}
		}
		if ($CI->upload->do_upload($str)) {
			$data = $CI->upload->data();
			$this->lastUploadedName = $data['orig_name'];
			return $data['file_name'];
		} else {
			$this->set_message($cond ? 'uploadCond' : 'upload', $CI->upload->display_errors('', ''));
			return FALSE;
		}
	}

    public function isValid_contact($id, $required = true)
    {
        $this->set_message('isValid_contact', lang('error_not_found') . ': ' . lang('label_contact'));

        if (!$required && !$id) {
            return true;
        }

        if (!$id) {
            return false;
        }

        return $this->CI->mcontacts->fetchOneById($id) ? true : false;
    }

    public function isValid_customer_type($id)
    {
        $this->set_message('isValid_customer_type', lang('error_not_found') . ': ' . lang('label_contact'));
        return $this->CI->mselect_option->fetchOneById($id) ? true : false;
    }

    public function isValid_deal_type($id)
    {
        $this->set_message('isValid_deal_type', lang('error_not_found') . ': ' . lang('label_deal_type'));
        return $this->CI->mselect_option->fetchOneById($id) ? true : false;
    }

    public function isValid_agent($id, $required = true)
    {
        $this->set_message('isValid_agent', lang('error_not_found') . ': ' . lang('label_agent'));
        if (!$required && !$id) {
            return true;
        }

        if (!$id) {
            return false;
        }

        return $this->CI->muser->countRows(['id' => $id, 'userType' => USER_ROLE_AGENT]) > 0;
    }

    public function isValid_consultant($id, $required = true)
    {
        $this->set_message('isValid_consultant', lang('error_not_found') . ': ' . lang('label_consultant'));
        if (!$required && !$id) {
            return true;
        }

        if (!$id) {
            return false;
        }

        return $this->CI->muser->countRows(['id' => $id, 'userType' => USER_ROLE_CONSULTANT]) > 0;
    }

    public function isValid_accountant($id, $required = true)
    {
        $this->set_message('isValid_accountant', lang('error_not_found') . ': ' . lang('label_accountant'));
        if (!$required && !$id) {
            return true;
        }
        return $this->CI->muser->countRows(['id' => $id, 'userType' => USER_ROLE_ACCOUNTANT]) > 0;
    }

    public function isValid_solicitor($id, $required = true)
    {
        $this->set_message('isValid_solicitor', lang('error_not_found') . ': ' . lang('label_solicitor'));
        if (!$required && !$id) {
            return true;
        }
        return $this->CI->muser->countRows(['id' => $id, 'userType' => USER_ROLE_SOLICITOR]) > 0;
    }

    public function isValid_secretary($id, $required = true)
    {
        $this->set_message('isValid_secretary', lang('error_not_found') . ': ' . lang('label_secretary'));
        if (!$required && !$id) {
            return true;
        }
        return $this->CI->muser->countRows(['id' => $id, 'userType' => USER_ROLE_SECRETARY]) > 0;
    }

    public function isValid_agreement_type($id)
    {
        $this->set_message('isValid_agreement_type', lang('error_not_found') . ': ' . lang('label_agreement_types'));
        return $this->CI->mselect_option->fetchOneById($id) ? true : false;
    }

    public function isValid_order_subject($id)
    {
        $this->set_message('isValid_order_subject', lang('error_not_found') . ': ' . lang('label_order_subject'));
        return $this->CI->mselect_option->fetchOneById($id) ? true : false;
    }

    public function isValid_status($id)
    {
        $this->set_message('isValid_status', lang('error_not_found') . ': ' . lang('label_status'));
        return $this->CI->mselect_option->fetchOneById($id) ? true : false;
    }

    public function isValid_user($id, $required = true)
	{
		$this->set_message('isValid_user', lang('error_not_found') . ': ' . lang('label_user'));
		if (!$required && !$id) {
			return true;
		}
		return $this->CI->muser->countRows(['id' => $id]) > 0;
	}

    public function isValid_parentuser($id)
	{
		if (!(int)$id) {
			return true;
		}
		$this->set_message('isValid_parentuser', lang('error_not_found') . ': ' . lang('label_user'));
		return $this->CI->muser->countRows(['id' => $id]) > 0;
	}

    public function isValid_department($id)
	{
		if ($id === '' || $id == null) {
			return true;
		}
		$this->set_message('isValid_department', lang('error_not_found') . ': ' . lang('label_department'));
		return $this->CI->mdepartments->countRows(['id' => $id]) > 0;
	}

    public function isValid_country($id)
	{
		$this->set_message('isValid_country', lang('error_not_found') . ': ' . lang('label_country'));
		return $this->CI->mcountries->countRows(['idCountry' => $id]) > 0;
	}

    public function isValid_task($id)
	{
		$this->set_message('isValid_task', lang('error_not_found') . ': ' . lang('label_task'));
		if ($id == null) {
			return true;
		}

        return $this->CI->magreement_tasks->fetchOneById($id) ? true : false;
	}

    public function isValid_deal($id, $required = false)
	{
		$this->set_message('isValid_deal', lang('error_not_found') . ': ' . lang('label_deal'));
		if (!$required && !$id) {
			return true;
		}

        return $this->CI->mdeals->fetchOneById($id) ? true : false;
	}

    public function is_exists($value, $field_table)
	{
		if (is_null($value)) {
			return true;
		}

        list($table, $field) = explode(':', $field_table);

        $this->set_message('is_exists', sprintf(lang('error_not_exists'), $table));

        $model = 'm' . str_replace('com_', '', $table);
		if ($model == 'magreements') {
			$model = 'magreement';
		}
		$this->CI->load->model([$model]);
		return $this->CI->{$model}->countRows([$field => $value]) ? true : false;
	}
}

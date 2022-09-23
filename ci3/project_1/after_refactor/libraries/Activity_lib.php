<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Activity_lib
 *
 */
class Activity_lib
{
    use Activity_details_trait,
        Activity_sms_trait,
        Activity_emails_trait,
        Activity_envelope_trait;

    /**
     * @var MY_Controller
     */
    public $CI;

    /**
     * @var Magreement
     */
    public $agreement = false;
    protected $user_id;
	protected $isDeal = false;

    public function __construct($config = [])
	{
		$this->CI = &get_instance();
		$this->CI->load->model(['magreement', 'magreement_activity', 'magreement_without_activity', 'mdeals']);

        if (isset($config['agreement'])) {
			$this->agreement = $config['agreement'];
		}
        if (isset($config['agreement_id'])) {
            $this->agreement = $this->CI->magreement->fetchOneById($config['agreement_id']);
        }
    }

    public function set_agreement($agreement)
    {
        $this->agreement = $agreement;
    }

    public function log_activity_list($section)
    {
        return $this->log_activity($section, Activity_enum::TYPE_LIST);
    }

    public function log_activity_view($section)
    {
        return $this->log_activity($section, Activity_enum::TYPE_VIEW);
    }

    public function log_activity_edit($section)
    {
        return $this->log_activity($section, Activity_enum::TYPE_EDIT);
    }

    public function log_activity_add($section)
    {
        return $this->log_activity($section, Activity_enum::TYPE_ADD);
    }

    public function log_activity_delete($section)
    {
        return $this->log_activity($section, Activity_enum::TYPE_DELETE);
    }

    public function log_activity($section, $action)
    {
        if (!$this->agreement) {
            return false;
        }

        $deal = $this->isDeal ? $this->agreement->withDeal() : false;
        if ($action != 'view') {
            $this->CI->magreement_without_activity->delete(['agreement_id' => $this->agreement->id]);
        }

        return $this->CI->magreement_activity->insert([
            'agreement_id' => $this->agreement->id,
            'deal_id' => $deal ? $deal->id : null,
            'section' => $section,
            'action' => $action,
            'description' => 'label_activity_' . $section . '_' . $action
        ]);
	}
	
    public function logNotesView($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_view('notes');
    }

    public function logTasks($action, $agreement, $isDeal = false)
	{
		$this->{"logTasks" . ucfirst($action)}($agreement, $isDeal);
	}

    public function logTasksView($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_view('tasks');
    }

    public function logDocumentsView($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_view('documents');
    }

    public function logPaymentsView($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_view('payments');
    }

    public function logRodoView($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_view('rodo');
    }

    public function logHistoryView($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_view('history');
    }

    public function logWorking_timeView($agreement)
    {
        $this->isDeal = true;
        $this->set_agreement($agreement);
        $this->log_activity_view('working_time');
    }

    public function logProcurationsView($agreement)
    {
        $this->isDeal = true;
        $this->set_agreement($agreement);
        $this->log_activity_view('procurations');
    }

    public function logPatternsView($agreement)
    {
        $this->isDeal = true;
        $this->set_agreement($agreement);
        $this->log_activity_view('patterns');
    }

    public function logNotesAdd($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_add('notes');
    }

    public function logTasksAdd($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_add('tasks');
    }

    public function logDocumentsAdd($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_add('documents');
    }

    public function logPaymentsAdd($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_add('payments');
    }

    public function logRodoAdd($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_add('rodo');
    }

    public function logAnnexAdd($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this - $this->log_activity_add('annex');
    }

    public function logWorking_timeAdd($agreement)
    {
        $this->isDeal = true;
        $this->set_agreement($agreement);
        $this->log_activity_add('working_time');
    }

    public function logProcurationsAdd($agreement)
    {
        $this->isDeal = true;
        $this->set_agreement($agreement);
        $this->log_activity_add('procurations');
    }

    public function logPatternsAdd($agreement)
    {
        $this->isDeal = true;
        $this->set_agreement($agreement);
        $this->log_activity_add('patterns');
    }

    public function logNotesEdit($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_edit('notes');
    }

    public function logTasksEdit($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_edit('tasks');
    }

    public function logDocumentsEdit($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_edit('documents');
    }

    public function logPaymentsEdit($agreement, $isDeal = false)
    {
        if ($isDeal === null) {
            $this->isDeal = $agreement->deal_number ? true : false;
        } else {
            $this->isDeal = $isDeal;
        }
        $this->set_agreement($agreement);
        $this->log_activity_edit('payments');
    }

    public function logRodoEdit($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_edit('rodo');
    }

    public function logWorking_timeEdit($agreement)
    {
        $this->isDeal = true;
        $this->set_agreement($agreement);
        $this->log_activity_edit('working_time');
    }

    public function logProcurationsEdit($agreement)
    {
        $this->isDeal = true;
        $this->set_agreement($agreement);
        $this->log_activity_edit('procurations');
    }

    public function logPatternsEdit($agreement)
    {
        $this->isDeal = true;
        $this->set_agreement($agreement);
        $this->log_activity_edit('patterns');
    }


    public function logNotesDelete($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_delete('notes');
    }

    public function logTasksDelete($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_delete('tasks');
    }

    public function logDocumentsDelete($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_delete('documents');
    }

    public function logPaymentsDelete($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_delete('payments');
    }

    public function logRodoDelete($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_delete('rodo');
    }

    public function logWorking_timeDelete($agreement)
    {
        $this->isDeal = true;
        $this->set_agreement($agreement);
        $this->log_activity_delete('working_time');
    }

    public function logProcurationsDelete($agreement)
    {
        $this->isDeal = true;
        $this->set_agreement($agreement);
        $this->log_activity_delete('procurations');
    }

    public function logPatternsDelete($agreement)
    {
        $this->isDeal = true;
        $this->set_agreement($agreement);
        $this->log_activity_delete('patterns');
    }
}

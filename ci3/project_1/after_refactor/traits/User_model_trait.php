<?php

/**
 * Trait User_model_trait
 *
 * @property-read MY_Form_validation $form_validation
 */
trait User_model_trait
{
    /**
     * @param bool $clean
     * @return Muser| false
     */
    public function withAgent($clean = true)
    {
        if (!$this->isPropertyExists('agent_id')) {
            return false;
        }
        $this->agent = $this->agent_id ? $this->CI->muser->fetchOneById($this->agent_id) : false;

        if ($this->agent) {
            if ($clean) {
                $this->agent = $this->CI->user_lib->prepareData($this->agent);
            }
            $this->agent->userType_data = $this->CI->magents->fetchOne(['user_id' => $this->agent_id]);
        }

        return $this->agent;
    }

    /**
     * @param bool $clean
     * @return Muser|false
     */
    public function withConsultant($clean = true)
    {
        if (!$this->isPropertyExists('consultant_id')
            || ($this->isPropertyExists('consultant_id') && !$this->consultant_id)) {
            return false;
        }

        if (isset(self::$_with['consultant'][$this->consultant_id])) {
            $this->consultant = self::$_with['consultant'][$this->consultant_id];
        } elseif ($this->consultant = $this->CI->muser->fetchOneById($this->consultant_id)) {
            self::$_with['consultant'][$this->consultant_id] = $this->consultant;
        }

        return $this->consultant = $clean ? $this->CI->user_lib->prepareData($this->consultant) : $this->consultant;
    }

    /**
     * @param bool $clean
     * @return Muser|false
     */
    public function withUser($clean = true)
    {
        if (!$this->isPropertyExists('user_id')
            || ($this->isPropertyExists('user_id') && !$this->user_id)) {
            return false;
        }

        if (isset(self::$_with['user'][$this->user_id])) {
            $this->user = self::$_with['user'][$this->user_id];
        } elseif ($this->user = $this->CI->muser->fetchOneById($this->user_id)) {
            self::$_with['user'][$this->user_id] = $this->user;
        }

        return $this->user = $clean ? $this->CI->user_lib->prepareData($this->user) : $this->user;
    }

    /**
     * @return Muser|false
     */
    public function withSolicitor($clean = true)
    {
        if (!$this->isPropertyExists('solicitor_id')
            || ($this->isPropertyExists('solicitor_id') && !$this->solicitor_id)) {
            return false;
        }

        if (isset(self::$_with['solicitor'][$this->solicitor_id])) {
            $this->solicitor = self::$_with['solicitor'][$this->solicitor_id];
        } elseif ($this->solicitor = $this->CI->muser->fetchOneById($this->solicitor_id)) {
            self::$_with['solicitor'][$this->solicitor_id] = $this->solicitor;
        }

        $this->solicitor = $clean ? $this->CI->user_lib->prepareData($this->solicitor) : $this->solicitor;

        if ($this->solicitor) {
            $this->solicitor->userType_data = $this->CI->msolicitors->fetchOne(['user_id' => $this->solicitor_id]);
        }
        return $this->solicitor;
    }

    /**
     * @param bool $clean
     * @return Muser|false
     */
    public function withResponsibleUser($clean = true)
    {
        if (!$this->isPropertyExists('responsible_user_id')
            || ($this->isPropertyExists('responsible_user_id') && !$this->responsible_user_id)) {
            return false;
        }

        if (isset(self::$_with['responsible_user'][$this->responsible_user_id])) {
            $this->responsible_user = self::$_with['responsible_user'][$this->responsible_user_id];
        } elseif ($this->responsible_user = $this->CI->muser->fetchOneById($this->responsible_user_id)) {
            self::$_with['responsible_user'][$this->responsible_user_id] = $this->responsible_user;
        }

        $this->responsible_user = $clean ? $this->CI->user_lib->prepareData($this->responsible_user) : $this->responsible_user;

        return $this->responsible_user;
    }

    /**
     * @return Muser|false
     */
    public function withReferral_user()
    {
        if (!$this->isPropertyExists('referral_user_id')) {
            return false;
        }

        return $this->referral_user = $this->referral_user_id ? $this->CI->user_lib->prepareData($this->CI->muser->fetchOneById($this->referral_user_id)) : false;
    }
}

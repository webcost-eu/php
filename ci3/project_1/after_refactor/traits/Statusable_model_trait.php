<?php

/**
 * Trait User_model_trait
 *
 * @property-read MY_Form_validation $form_validation
 */
trait Statusable_model_trait
{
	public function withStatus()
	{
        if (!$this->isPropertyExists('status_id')) {
            return $this->status = false;
        }

        $this->status = $this->CI->mselect_option->fetchOneById($this->status_id);

        return $this->status;
    }
}

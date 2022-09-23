<?php

/**
 * Trait Agreement_model_trait
 *
 * @property-read MY_Form_validation $form_validation
 */
trait Agreement_model_trait
{
	public function withAgreement()
	{
        if (!$this->isPropertyExists('agreement_id') || ($this->isPropertyExists('agreement_id') && !$this->agreement_id)) {
            return false;
        }

        if (isset(self::$_with['agreement'][$this->agreement_id])) {
            $this->agreement = self::$_with['agreement'][$this->agreement_id];
        } elseif ($this->agreement = $this->CI->magreement->fetchOneById($this->agreement_id)) {
            self::$_with['agreement'][$this->agreement_id] = $this->agreement;
        }

        return $this->agreement;
    }
}

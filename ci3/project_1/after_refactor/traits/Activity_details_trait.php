<?php

/**
 * Trait Activity_details_trait
 *
 */
trait Activity_details_trait
{
    public function logDetailsView($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_view('details');
    }

    public function logDetailsAdd($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_add('details');
    }

    public function logDetailsEdit($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_edit('details');
    }

    public function logDetailsDelete($agreement, $isDeal = false)
    {
        $this->isDeal = $isDeal;
        $this->set_agreement($agreement);
        $this->log_activity_delete('details');
    }
}

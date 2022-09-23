<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property-read CI_Input $input
 * @property-read Google_lib $google_lib
 * @property-read Google_calendar $google_calendar
 * @property-read Msynchronizations $msynchronizations
 * @property-read Muser $muser
 */
class Google_webhook extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            'msynchronizations',
            'muser',
        ]);

        $this->load->library('user_lib');
        $this->load->library('google_lib');
        $this->load->library('google_calendar');
    }

    public function index()
    {
        if ($this->input->get_request_header('x-goog-resource-state') !== 'exists') {
            log_error('header `x-goog-resource-state` not `exists` but: ' . $this->input->get_request_header('x-goog-resource-state'));
            return;
        }

        $channel = $this->input->get_request_header('x-goog-channel-id');
        $resource = $this->input->get_request_header('x-goog-resource-id');
        log_error('Google webhook headers: ', $this->input->request_headers());

        if (!($item = $this->msynchronizations->fetchOne([
            'channel_id' => $channel,
            'resource_id' => $resource]))) {
            log_error(sprintf('channel %s of %s not found', $channel, $resource));
            return;
        }

        if (!($user = $this->muser->fetchOneById($item->user_id))) {
            log_error('User not found: ' . $item->user_id);
            return;
        }

        if (!google_lib()->setUser($user)) {
            log_error('Set user error! ' . $this->google_calendar->getError());
            return;
        }
        $this->google_calendar->synchronize($item);

    }
}

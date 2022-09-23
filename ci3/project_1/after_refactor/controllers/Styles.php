<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property-read CI_Migration $migration
 * @property-read CI_Input $input
 * @property-read Styles_lib $styles_lib
 *
 *
 */
class Styles extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
	    $this->load->library('styles_lib');

        $this->output
            ->set_content_type('css', 'utf-8')
            ->set_output($this->styles_lib->get())
            ->cache(5);
	}
}

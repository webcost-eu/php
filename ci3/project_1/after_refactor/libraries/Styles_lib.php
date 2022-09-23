<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Styles_lib extends My_library
{
    protected $template_file = '';
    protected $out_file = '';
    protected $settings;

    protected $defaults = [
        'theme_color' => '#6658f8',
        'theme_bg_color' => '#7c70f9',
        'theme_active_color' => '',
        'theme_active_bg_color' => '',
        'theme_font_size' => '1rem',
        'theme_menu_font_size' => '1.1rem',
        'theme_thead_font_size' => '0.857rem',
        'theme_label_font_size' => '1rem',
        'theme_page_bg_color' => '#f8f8f8',
        'theme_modal_bg_color' => '#fff',
        'theme_menu_bg_color' => '#fff',
        'theme_table_ceil_vert_pad' => '0.72rem',
        'theme_table_ceil_gor_pad' => '2rem',
        'theme_table_ceil_font_size' => '1rem',
        'theme_font_color' => '#6e6b7b',
        'theme_font_family' => 'Montserrat, Helvetica, Arial, serif'
    ];

	public function __construct($config = [])
	{
		$this->template_file = FCPATH . 'assets/app_theme_template.css';
        $this->out_file = FCPATH . 'assets/app_theme.css';

        parent::__construct($config);

        $this->CI->load->model('mgeneral_settings');
        $this->CI->load->library('replace_tokens');
	}

	public function get() {
	    if (!file_exists($this->out_file)) {
	        $this->generate();
        }

        return file_get_contents($this->out_file);
    }

    public function getStylesSettings($data) {
        $general_settings = [];
        foreach ($this->defaults as $setting => $value) {
            $general_settings[$setting] = array_key_exists($setting, $data) ? $data[$setting] : $value;
        }

        return $general_settings;
    }

	public function generate() {
        $this->settings = $this->CI->mgeneral_settings->fetchOne(['theme' => true]);

        $tags = $this->settings->to_array();
        $tags = $this->getStylesSettings($tags);

        $theme = file_get_contents($this->template_file);

        $out = $this->CI->replace_tokens->replace($theme, $tags);

        $out = preg_replace_callback(['/{(hexToRgb)\((#?\w+),(\d+\.?\d*)\)}/', '/{(adjustBrightness)\((#?\w+),(-?\d+\.?\d*)\)}/'], function($matches){
            if(!$matches[1] || !$matches[2] || !$matches[3]) {
                return '';
            }

            if($matches[1] == 'hexToRgb') {
                return hexToRgb($matches[2], $matches[3]);
            } else if($matches[1] == 'adjustBrightness'){
                return adjustBrightness($matches[2], $matches[3]);
            }

        }, $out);

        file_put_contents($this->out_file, $out);
    }
}

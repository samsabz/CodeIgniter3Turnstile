<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        // بارگذاری کتابخانه Turnstile
        $this->load->library('turnstile');
    }

    public function index() {
        // دریافت ویجت Turnstile
        $data['turnstile_widget'] = $this->turnstile->get_widget(array(
            'theme' => 'light', // اختیاری: light, dark, auto
            'size' => 'normal'  // اختیاری: normal, compact
        ));

        $this->load->view('your_form', $data);
    }

    public function submit() {
        // دریافت توکن Turnstile از فرم
        $turnstile_response = $this->input->post('cf-turnstile-response');

        // اعتبارسنجی توکن
        if ($this->turnstile->validate($turnstile_response, $this->input->ip_address())) {
            // اعتبارسنجی موفق
            $name = $this->input->post('name');
            $this->session->set_flashdata('success', 'فرم با موفقیت ارسال شد!');
            redirect('main');
        } else {
            // اعتبارسنجی ناموفق
            $this->session->set_flashdata('error', 'تأیید ربات ناموفق بود. لطفاً دوباره تلاش کنید.');
            redirect('main');
        }
    }
}
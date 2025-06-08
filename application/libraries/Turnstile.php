<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Turnstile {
    protected $CI;
    protected $site_key;
    protected $secret_key;
    protected $api_url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct($config = array()) {
        // دریافت نمونه CodeIgniter
        $this->CI =& get_instance();

        // بارگذاری تنظیمات از آرایه ورودی یا فایل config
        $this->site_key = isset($config['site_key']) ? $config['site_key'] : '';
        $this->secret_key = isset($config['secret_key']) ? $config['secret_key'] : '';

        // اگر تنظیمات در فایل config تعریف شده باشند
        if (empty($this->site_key) && !empty($this->CI->config->item('turnstile_site_key'))) {
            $this->site_key = $this->CI->config->item('turnstile_site_key');
        }
        if (empty($this->secret_key) && !empty($this->CI->config->item('turnstile_secret_key'))) {
            $this->secret_key = $this->CI->config->item('turnstile_secret_key');
        }

        // لاگ برای دیباگ (اختیاری)
        log_message('debug', 'Turnstile Library Initialized');
    }

    /**
     * تولید کد HTML برای ویجت Turnstile
     * @param array $options تنظیمات اضافی ویجت (مثل theme، size)
     * @return string کد HTML ویجت
     */
    public function get_widget($options = array()) {
        if (empty($this->site_key)) {
            return 'Site Key is not set.';
        }

        $attributes = array(
            'class' => 'cf-turnstile',
            'data-sitekey' => $this->site_key
        );

        // افزودن تنظیمات اضافی (مثل theme یا size)
        if (!empty($options['theme'])) {
            $attributes['data-theme'] = $options['theme'];
        }
        if (!empty($options['size'])) {
            $attributes['data-size'] = $options['size'];
        }
        if (!empty($options['callback'])) {
            $attributes['data-callback'] = $options['callback'];
        }

        // تولید HTML ویجت
        $html = '<div';
        foreach ($attributes as $key => $value) {
            $html .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        $html .= '></div>';

        // افزودن اسکریپت Turnstile
        $html .= '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';

        return $html;
    }

    /**
     * اعتبارسنجی پاسخ Turnstile
     * @param string $response توکن دریافت‌شده از فرم
     * @param string $remote_ip آدرس IP کاربر (اختیاری)
     * @return bool نتیجه اعتبارسنجی
     */
    public function validate($response, $remote_ip = null) {
        if (empty($this->secret_key)) {
            log_message('error', 'Turnstile Secret Key is not set.');
            return false;
        }

        if (empty($response)) {
            log_message('error', 'Turnstile response token is empty.');
            return false;
        }

        // آماده‌سازی داده‌های درخواست
        $data = array(
            'secret' => $this->secret_key,
            'response' => $response
        );
        if (!empty($remote_ip)) {
            $data['remoteip'] = $remote_ip;
        }

        // ارسال درخواست به API Cloudflare
        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200) {
            log_message('error', 'Turnstile API request failed with HTTP code: ' . $http_code);
            return false;
        }

        $response_data = json_decode($response, true);

        if (isset($response_data['success']) && $response_data['success'] === true) {
            return true;
        }

        // لاگ کردن خطاها برای دیباگ
        if (isset($response_data['error-codes'])) {
            log_message('error', 'Turnstile validation failed: ' . implode(', ', $response_data['error-codes']));
        }

        return false;
    }
}
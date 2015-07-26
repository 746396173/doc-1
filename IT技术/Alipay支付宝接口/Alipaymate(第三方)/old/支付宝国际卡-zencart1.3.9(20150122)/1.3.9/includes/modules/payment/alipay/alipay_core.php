<?php
/**
 * Alipay forcard Payment Module
 *
 * @author alipaymate.com
 * @copyright (c) 2013 alipaymate.com
 * @copyright Portions Copyright (c) 2003 Zen Cart
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

define('MODULE_PAYMENT_ALIPAYFOR_LOG_DIR', DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/alipay/log/');

class alipay_core extends base
{
    private $_gateway    = MODULE_PAYMENT_ALIPAYFOR_GATEWAY;
    private $_notify_verify_url = MODULE_PAYMENT_ALIPAYFOR_NOTIFY_VERIFY_URL;

    private $_config     = array('service'           => MODULE_PAYMENT_ALIPAYFOR_SERVICE
                                ,'partner'           => ''
                                ,'_input_charset'    => ''
                                ,'sign_type'         => ''
                                ,'sign'              => ''
                                ,'notify_url'        => ''
                                ,'return_url'        => ''
                                ,'anti_phishing_key' => ''
                                ,'exter_invoke_ip'   => ''
                         );

    private $_bizparam   = array('out_trade_no'        => ''
                                ,'subject'             => ''
                                ,'default_bank'        => ''
                                ,'paymethod'           => ''
                                ,'extend_param'        => ''
                                ,'seller_id'           => ''
                                ,'price'               => ''
                                ,'quantity'            => ''
                                ,'total_fee'           => ''
                                ,'body'                => ''
                                ,'show_url'            => ''
                                ,'royalty_type'        => ''
                                ,'royalty_parameters'  => ''
                                ,'extra_common_param'  => ''
                                ,'it_b_pay'            => ''
                                ,'currency'            => ''
                         );

    private $_logfile = '';

    public function __construct() {
        $this->setLogFile();
    }

    private function logging($message) {
        error_log($message, 3, $this->_logfile);
    }

    private function setLogFile() {
        $this->_logfile = MODULE_PAYMENT_ALIPAYFOR_LOG_DIR . 'alipay_forcard.' .date('YmdHis'). '.' . substr(md5(rand()),0,6) . '.log';
    }

    public function setConfig($config)
    {
        foreach ($config as $name => $value) {
            if (array_key_exists($name, $this->_config)) {
                $this->_config[$name] = $value;
            }
        }
    }

    public function getConfig()
    {
        return $this->_config;
    }

    public function setBizParams($params)
    {
        foreach ($params as $name => $value) {
            if (array_key_exists($name, $this->_bizparam)) {
                $this->_bizparam[$name] = $value;
            }
        }
    }


    // request html
    public function createRequestHtml($method = 'post', $button_title = 'submit')
    {
        $request_data = $this->generateRequestData();
        $this->logging("\r\n== request data ==\r\n" . print_r($request_data,true));

        $action = $this->_gateway . '_input_charset=' . $this->_config['_input_charset'];

        $html = '';

        foreach ($request_data as $key => $val) {
            $html .= "<input type='hidden' name='" . $key . "' value='" . $val . "' />";
        }

        $this->logging("\r\n== request html ==\r\n" . $html);

        return $html;
    }

    // verify response
    public function verifyResponse($data)
    {
        $this->logging("\r\n== org response data ==\r\n" . print_r($data,true));

        // check input
        if (empty($data)) {
            return false;
        }

        $sign      = isset($data['sign'])      ? $data['sign']      : '';
        $notify_id = isset($data['notify_id']) ? $data['notify_id'] : '';

        if (empty($sign) || empty($notify_id)) {
            return false;
        }

        // check sign
        $data   = $this->filterData($data);
        $data   = $this->sortData($data);
        $mysign = $this->generateMySign($data);

        $this->logging("\r\n== fixed response data ==\r\n" . print_r($data,true));
        $this->logging("\r\n== mysign ==\r\n" . $mysign);

        if ($sign == $mysign && $this->isAlipayResponse($notify_id)) {
            return true;
        }

        return false;
    }

    private function isAlipayResponse($notify_id)
    {
        $verify_url =  $this->_notify_verify_url. 'partner=' . $this->_config['partner'] . '&notify_id=' . $notify_id;

        $response = $this->getHttpResponseBySocket($verify_url);

        $this->logging("\r\n== notify verify response ==\r\n" . $response);

        if (preg_match("/true$/i", $response)) {
            return true;
        }

        return false;
    }

    private function getHttpResponseBySocket($url, $charset = '', $timeout = 60)
    {
        $url = str_ireplace('https://', 'http://', $url);

        $charset  = trim($charset);
        $url_arr  = parse_url($url);
        $hostname = $url_arr['host'];
        $port     = '';

        switch ($url_arr['scheme']) {
            case 'https':
                $hostname = 'ssl://' . $hostname;
                $port     = '443';
                break;
            case 'http':
                $hostname = 'tcp://' . $hostname;
                $port     = '80';
                break;
            default:
                break;
        }

        $response = '';
        $errno    = '';
        $errstr   = '';
        $data     = '';

        $fp = fsockopen($hostname, $port, $errno, $errstr, $timeout);

        if (!$fp) {
            $this->logging("\r\n== open socket failed ==\r\n", $errstr);
            return false;
        }

        if (empty($charset)) {
            $data .= 'POST ' . $url_arr['path'] . " HTTP/1.1\r\n";
        } else {
            $data .= 'POST ' . $url_arr['path'] . '?_input_charset=' . $charset . " HTTP/1.1\r\n";
        }

        $data .= "Host: " . $url_arr['host'] . "\r\n";
        $data .= "Content-type: application/x-www-form-urlencoded\r\n";
        $data .= "Content-length: " . strlen($url_arr['query']) . "\r\n";
        $data .= "Connection: close\r\n\r\n";
        $data .= $url_arr['query'] . "\r\n\r\n";

        fwrite($fp, $data);

        while (!feof($fp)) {
            $response .= fgets($fp, 1024);
        }

        fclose($fp);

        $response = trim(strstr($response, "\r\n\r\n"), "\r\n");

        return $response;
    }

    private function getHttpResponseByCurl($url, $charset='', $timeout=60)
    {
        $url_arr = parse_url($url);

        $url = $url_arr['scheme'] . '://'. $url_arr['host'] . $url_arr['path'];

		$charset = trim($charset);
	    if (!empty($charset)) {
	        $url .= '?_input_charset=' . $charset;
	    }

	    $post_data = array();

	    $query_arr = explode('&', $url_arr['query']);
	    foreach ($query_arr as $query) {
	        list($key, $val) = explode('=', $query);
	        $post_data[$key] = $val;
	    }

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

	    $response = curl_exec($ch);

	    if ($response === false) {
	    	$this->logging("\r\n== curl failed ==\r\n", curl_error($ch));
	    }

	    curl_close($ch);

	    return $response;
	}


    private function generateRequestData()
    {
        $data = array_merge($this->_config, $this->_bizparam);
        $data = $this->filterData($data);
        $data = $this->sortData($data);

        $mysign = $this->generateMySign($data);

        $data['sign']      = $mysign;
        $data['sign_type'] = $this->_config['sign_type'];

        return $data;
    }


    private function generateMySign($sort_params)
    {
        // convert params to string
        $str = '';

        foreach ($sort_params as $key => $val) {
            $str .= ($key . '=' . $val . '&');
        }

        $str = rtrim($str, '&');

        if (get_magic_quotes_gpc()) {
            $str = stripslashes($str);
        }

        // attach sign to string
        $str .= $this->_config['sign'];

        // generate my sign
        $mysign = '';

        switch ($this->_config['sign_type']) {
            case 'MD5':
                $mysign = md5($str);
                break;
        }

        return $mysign;
    }

    private function filterData($params)
    {
        foreach ($params as $key => $val) {
            if ($key == 'sign' || $key == 'sign_type' || $val == '' || $key == 'main_page' || $key=='submit') {
                unset($params[$key]);
            }
        }

        return $params;
    }

    private function sortData($params)
    {
        ksort($params);
        reset($params);
        return $params;
    }
}
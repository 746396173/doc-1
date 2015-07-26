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

// always set [test mode] to false
define('MODULE_PAYMENT_ALIPAYFOR_TESTMODE', false);

if (MODULE_PAYMENT_ALIPAYFOR_TESTMODE == false) {
    define('MODULE_PAYMENT_ALIPAYFOR_GATEWAY', 'https://mapi.alipay.com/gateway.do?');
} else {
    define('MODULE_PAYMENT_ALIPAYFOR_GATEWAY', 'http://mapi.p110.alipay.net/gateway.do?');
}

// alipay notify url use https is recommend
define('MODULE_PAYMENT_ALIPAYFOR_NOTIFY_VERIFY_USE_HTTPS', true);

if (MODULE_PAYMENT_ALIPAYFOR_NOTIFY_VERIFY_USE_HTTPS == true) {
    define('MODULE_PAYMENT_ALIPAYFOR_NOTIFY_VERIFY_URL', MODULE_PAYMENT_ALIPAYFOR_GATEWAY . 'service=notify_verify&');
} else {
    define('MODULE_PAYMENT_ALIPAYFOR_NOTIFY_VERIFY_URL', 'http://notify.alipay.com/trade/notify_query.do?');
}

// alipay RDS url
define('MODULE_PAYMENT_ALIPAYFOR_RDS_URL', 'https://irds.alipay.com/merchant/merchant.js');
# define('MODULE_PAYMENT_ALIPAYFOR_RDS_URL', 'https://rds.alipay.com/merchant/merchant.js');

// alipay service
define('MODULE_PAYMENT_ALIPAYFOR_SERVICE', 'alipay.trade.direct.forcard.pay');

require(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/alipay/alipay_core.php');

class alipay_forcard
{
    public $code, $title, $description, $enabled, $payment;

    // class constructor
    public function __construct()
    {
        global $order;
        $this->code        = 'alipay_forcard';
        $this->title       = MODULE_PAYMENT_ALIPAYFOR_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_ALIPAYFOR_TEXT_DESCRIPTION;
        $this->sort_order  = MODULE_PAYMENT_ALIPAYFOR_SORT_ORDER;
        $this->enabled     = ((MODULE_PAYMENT_ALIPAYFOR_STATUS == 'True') ? true : false);

        if ((int) MODULE_PAYMENT_ALIPAYFOR_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_ALIPAYFOR_ORDER_STATUS_ID;
        }

        if (is_object($order))
            $this->update_status();

        $this->email_footer    = MODULE_PAYMENT_ALIPAYFOR_TEXT_EMAIL_FOOTER;
        $this->form_action_url = MODULE_PAYMENT_ALIPAYFOR_GATEWAY . '_input_charset=' . MODULE_PAYMENT_ALIPAYFOR_CHARSET;

        if (IS_ADMIN_FLAG === true) {
            $this->description = MODULE_PAYMENT_ALIPAYFOR_TEXT_ADMIN_DESCRIPTION;
        }
    }

    // class methods
    function update_status()
    {
        global $order, $db;

        if (($this->enabled == true) && ((int) MODULE_PAYMENT_ALIPAYFOR_ZONE > 0)) {
            $check_flag = false;
            $check      = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_ALIPAYFOR_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
            while (!$check->EOF) {
                if ($check->fields['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check->fields['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
                $check->MoveNext();
            }

            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    function javascript_validation()
    {
        return false;
    }

    function selection()
    {
        return array(
            'id' => $this->code,
            'module' => MODULE_PAYMENT_ALIPAYFOR_TEXT_CATALOG_LOGO . $this->_draw_radio_menu()
        );
    }


    function getCards()
    {
        $cards = array(
            'visa' => 'Visa',
            'mscd' => 'Master Card',
            'jcb' => 'JCB Card'
        );

        return $cards;
    }


    function _draw_dropdown_menu()
    {
        $cards = $this->getCards();

        $html .= '</label><label>';
        $html .= '<select name="alipay_card_type" onchange="document.getElementById(\'pmt-alipay_forcard\').checked=\'true\';">';

        foreach ($cards as $val => $title) {
            $selected = false;

            if (isset($_SESSION['alipay_card_type']) && $_SESSION['alipay_card_type'] == $val) {
                $selected = true;
            }

            $html .= '<option value="' . $val . '" ' . ($selected ? 'selected="selected" ' : '') . '>' . $title . "</option>\r\n";
        }

        $html .= '</select>';

        return $html;
    }

    function _draw_radio_menu()
    {
        $cards = $this->getCards();

        $html = '<ul id="alipay-payment-radio" style="list-style-type: none;">';

        $rds_url = MODULE_PAYMENT_ALIPAYFOR_RDS_URL;

        $html_js_return = <<<EOT
            <input type="hidden" id="alipay_js_return" name="alipay_js_return" value="" />

            <script type="text/javascript" src="{$rds_url}"></script>
            <script type="text/javascript">
                document.getElementById('alipay_js_return').value = window["alipay-merchant-result"];
            </script>
EOT;

        $html .= $html_js_return . "\n";

        foreach ($cards as $val => $label) {
            $checked = false;

            if (isset($_SESSION['alipay_card_type']) && $_SESSION['alipay_card_type'] == $val) {
                $checked = true;
            }

            $html .= '<li id="alipay-' . $val . '">';
            $html .= '<span id="alipay-' . $val . '-front"></span>';
            $html .= '</span>';

            // || ($val=='visa' && empty($_SESSION['alipay_card_type'])
            $html .= '<input type="radio" id="alipay-' . $val . '-val" value="' . $val . '" name="alipay_card_type" ' . ($checked ? 'checked="checked" ' : '') . ' onclick="document.getElementById(\'pmt-alipay_forcard\').checked = true;" />';
            $html .= '<img src="/includes/modules/payment/alipay/logo/alipay_' . $val . '.png" alt="Checkout with ' . $label . '" title="Checkout with ' . $label . '" />';
            $html .= '<label class="alipay-' . $val . '-label" for="alipay-' . $val . '-val">' . $label . '</label>';

            $html .= '<span id="alipay-' . $val . '-end"></span>';
            $html .= '</span>';
            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }


    function pre_confirmation_check()
    {
        if (isset($_POST['alipay_card_type'])) {
            $_SESSION['alipay_card_type'] = $_POST['alipay_card_type'];
        }

        if (isset($_POST['alipay_js_return'])) {
            $_SESSION['alipay_js_return'] = $_POST['alipay_js_return'];
        }

        return false;
    }

    function confirmation()
    {
        return false;
    }

    function process_button()
    {
        $alipay = new alipay_core;

        $alipay_config   = $this->prepareConfig();
        $alipay_bizparam = $this->prepareBizData();

        $alipay->setConfig($alipay_config);
        $alipay->setBizParams($alipay_bizparam);
        $button_string = $alipay->createRequestHtml();

        return $button_string;
    }

    function prepareConfig()
    {
        $config = array(
            'service'           => 'alipay.trade.direct.forcard.pay',
            'partner'           => MODULE_PAYMENT_ALIPAYFOR_PARTNER_ID,
            '_input_charset'    => MODULE_PAYMENT_ALIPAYFOR_CHARSET,
            'sign_type'         => MODULE_PAYMENT_ALIPAYFOR_SIGN_TYPE,
            'sign'              => MODULE_PAYMENT_ALIPAYFOR_PARTNER_SIGN,
            'notify_url'        => HTTP_SERVER . '/alipay_forcard_notify.php',
            'return_url'        => zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL'),
            'anti_phishing_key' => '',
            'exter_invoke_ip'   => ''
        );

        return $config;
    }

    function prepareBizData()
    {
        global $db, $order, $currencies;

        // order id
        $order_id = '';

        if (isset($_SESSION['alipay_forcard_pending_order_id'])) {
            $order_id = $_SESSION['alipay_forcard_pending_order_id'];
        }

        // out trade no
        $out_trade_no = $order_id;

        if (empty($order_id)) {
        	$out_trade_no = $_SESSION['customer_id'] . date('His');
        }

        // cart type
        $cart_type    = $_SESSION['alipay_card_type'];

        // paymethod
        $paymethod = MODULE_PAYMENT_ALIPAYFOR_PAYMETHOD;

        // default bank
        $default_bank = '';

        if ($paymethod == 'boc') { // bank of china
            switch (strtolower($cart_type)) {
                case 'visa':
                    $default_bank = 'boc-visa';
                    break;
                case 'mscd':
                    $default_bank = 'boc-master';
                    break;
                case 'jcb':
                    $default_bank = 'boc-jcb';
                    break;
                default:
                	$default_bank = 'boc-visa';
                    break;
            }

            $paymethod = '';
        } else { // abc bank
            switch (strtolower($cart_type)) {
                case 'visa':
                    $default_bank = 'cybs-visa';
                    break;
                case 'mscd':
                    $default_bank = 'cybs-master';
                    break;
                case 'jcb':
                    $default_bank = 'cybs-jcb';
                    break;
                default:
                	$default_bank = 'cybs-visa';
                    break;
            }
        }

        // currency, total_fee
        $alipay_currencies = array();

        if (strlen(MODULE_PAYMENT_ALIPAYFOR_CURRENCY) > 3) {
            $alipay_currencies = explode(',', trim(trim(MODULE_PAYMENT_ALIPAYFOR_CURRENCY), ','));
        }

        $currency = $_SESSION['currency'];

        if (!in_array($currency, $alipay_currencies)) {
            $currency = 'USD';
        }

        if ($default_bank == 'boc-jcb' || $default_bank == 'cybs-jcb') {
            $currency = 'CNY';
        }

        // check if site support the currency !!!
        if (!$currencies->is_set($currency)) {
        	die('System Error: Currency [' . $currency . '] has not been installed, please concat site administrator!');
        }

        $total_fee = number_format($order->info['total'] * $currencies->get_value($currency), $currencies->get_decimal_places($currency), '.', '');

        // subject, body
        $subject = '';
        $body    = '';

        $subject = 'Order No: #' . $out_trade_no . ' from ' . STORE_NAME;

        foreach ($order->products as $product) {
            $body .= str_replace(array("'",'"','&','+','#','%','@'), '', $product['name']) . ',';
        }

        $body = substr(substr($body, 0, -1), 0, 1000); // body max 1000 chars

        $alipay_param = array(
            'out_trade_no'       => $out_trade_no,
            'subject'            => $subject,
            'default_bank'       => $default_bank,
            'paymethod'          => $paymethod,
            'extend_param'       => $this->get_extend_param(),
            'seller_id'          => MODULE_PAYMENT_ALIPAYFOR_PARTNER_ID,
            'price'              => '',
            'quantity'           => '',
            'total_fee'          => $total_fee,
            'body'               => $body,
            'show_url'           => '',
            'royalty_type'       => '',
            'royalty_parameters' => '',
            'extra_common_param' => $order_id,
            'it_b_pay'           => '',
            'currency'           => $currency
        );

        return $alipay_param;
    }

    function get_extend_param()
    {
        global $order;

        // if shipping info is empty, use billing info
        if (empty($order->delivery['country'])) {
            $order->delivery = $order->billing;
        }

        // ship to country, state
        $ship_to_country = $order->delivery['country']['iso_code_2'];

        $ship_to_state   = '';

        if (!in_array($ship_to_country, array(
            'US',
            'CA'
        ))) {
            $ship_to_state = 'YT';
        } else {
            $ship_to_state = zen_get_zone_code($order->delivery['country']['id'], $order->delivery['zone_id'], '');
        }

        // telephone
        $telephone = $order->customer["telephone"];
        if (empty($telephone))
            $telephone = trim($order->billing["telephone"]);
        if (empty($telephone))
            $telephone = trim($order->delivery["telephone"]);

        // js return
        $js_return = '';

        if (isset($_SESSION['alipay_js_return'])) {
            $js_return = $_SESSION['alipay_js_return'];
        }

        $product_name = 'product total:' . $order->info['total'];

        $extend_param = array(
            'ship_to_firstname'    => $order->delivery["firstname"],
            'ship_to_lastname'     => $order->delivery["lastname"],
            'ship_to_postalcode'   => $order->delivery["postcode"],
            'ship_to_phonenumber'  => $order->delivery["telephone"],
            'ship_to_street1'      => $order->delivery['street_address'],
            'ship_to_city'         => $order->delivery['city'],
            'ship_to_state'        => $ship_to_state,
            'ship_to_country'      => $ship_to_country,
            'ship_to_shipmethod'   => 7,
            'logistics_cost'       => '',
            'registration_name'    => $order->customer["firstname"] . ' ' . $order->customer["lastname"],
            'registration_email'   => $order->customer['email_address'],
            'registration_phone'   => $telephone,
            'product_name'         => $product_name,
            'js_return'            => $js_return,
        );


        $param_str = '';

        foreach ($extend_param as $key => $val) {
            if (!isset($val) || $val == '') {
                unset($extend_param[$key]);
            } else {
                $param_str .= ($key . '^' . $val) . '|';
            }
        }

        $param_str = substr($param_str, 0, -1);

        return $param_str;
    }



    function before_process()
    {
        if (!empty($_GET['notify_id'])) {
            if (!(($_GET['trade_status']=='TRADE_FINISHED' || $_GET['trade_status']=='TRADE_SUCCESS') && $_GET['is_success'] == 'T')) {
                $messageStack->add_session('alipay-failed', MODULE_PAYMENT_ALIPAYFOR_TEXT_PAYMENT_FAILED, 'error');
                zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
            }

            $alipay = new alipay_core;

            $alipay_config = $this->prepareConfig();
            $alipay->setConfig($alipay_config);

            $result = $alipay->verifyResponse($_GET);

            if ($result) {
                $order_id = (int)(isset($_GET['extra_common_param']) ? $_GET['extra_common_param'] : 0);

                if ($order_id > 0) {
                    $_SESSION['alipay_forcard_success_order_id'] = $order_id;
                }

                return true;
            } else {
                $this->order_status = DEFAULT_ORDERS_STATUS_ID; // Pending / Awaiting payment
                $messageStack->add_session('alipay-failed', MODULE_PAYMENT_ALIPAYFOR_TEXT_PAYMENT_FAILED, 'error');
                zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
            }
        }

        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
    }


    function after_process()
    {
        global $insert_id, $db;

        $comments = 'trade_no: ' . $_GET['trade_no'] . "\r\n" . 'out_trade_no: ' . $_GET['out_trade_no'] . "\r\n" . 'is_success: ' . $_GET['is_success'] . ' (T:success, F:failed)' . "\r\n" . 'trade_status: ' . $_GET['trade_status'] . "\r\n" . 'forex_total_fee: ' . $_GET['forex_total_fee'] . ' ' . $_GET['currency'] . "\r\n" . 'total_fee: ' . $_GET['total_fee'] . ' RMB' . "\r\n";

        $sql_data_array = array(
            'orders_id'         => $insert_id,
            'orders_status_id'  => $this->order_status,
            'date_added'        => 'now()',
            'customer_notified' => '0',
            'comments'          => $comments
        );

        zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

        return true;
    }


    function get_error()
    {
        return false;
    }

    function output_error()
    {
        return false;
    }

    function check()
    {
        global $db;
        if (!isset($this->_check)) {
            $check_query  = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_ALIPAYFOR_STATUS'");
            $this->_check = $check_query->RecordCount();
        }
        return $this->_check;
    }

    function install()
    {
        global $db, $messageStack;
        if (defined('MODULE_PAYMENT_ALIPAYFOR_STATUS')) {
            $messageStack->add_session('FreeCharger module already installed.', 'error');
            zen_redirect(zen_href_link(FILENAME_MODULES, 'set=payment&module=alipay', 'NONSSL'));
            return 'failed';
        }

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Alipay Credit Card (forcard) Module', 'MODULE_PAYMENT_ALIPAYFOR_STATUS', 'True', 'Do you want to accept Alipay Credit Card (forcard) payment?', '6', '1', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now());");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_ALIPAYFOR_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_ALIPAYFOR_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_ALIPAYFOR_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Alipay Partner ID'  , 'MODULE_PAYMENT_ALIPAYFOR_PARTNER_ID', '', '16 digits Partner ID. for example: 2088101568338364', '6', '0', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Signature Type', 'MODULE_PAYMENT_ALIPAYFOR_SIGN_TYPE', 'MD5', 'default MD5', '6', '0', 'zen_cfg_select_option(array(\'MD5\', \'RSA\'), ', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Signature Key'  , 'MODULE_PAYMENT_ALIPAYFOR_PARTNER_SIGN', '', '', '6', '0', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Payment Methods', 'MODULE_PAYMENT_ALIPAYFOR_PAYMETHOD', 'boc', '', '6', '0', 'zen_cfg_select_option(array(\'boc\', \'jvm-3d\', \'jvm-moto\'), ', now())");

        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Currency'  , 'MODULE_PAYMENT_ALIPAYFOR_CURRENCY', 'USD,EUR,GBP,AUD,HKD,RUB,CNY', 'Which currency should the order be sent to Alipay as?<br />NOTE: if an unsupported currency is sent to Alipay, it will be auto-converted to USD.', '6', '0', now())");
        $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Debug Mode', 'MODULE_PAYMENT_ALIPAYFOR_DEBUG_MODE', 'Off', 'Would you like to enable debug mode? A detailed log of transactions may be emailed to the store owner.', '6', '0', 'zen_cfg_select_option(array(\'Off\', \'Log File\'), ', now())");

    }

    function remove()
    {
        global $db;
        $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys()
    {
        return array(
            'MODULE_PAYMENT_ALIPAYFOR_STATUS',
            'MODULE_PAYMENT_ALIPAYFOR_ZONE',
            'MODULE_PAYMENT_ALIPAYFOR_ORDER_STATUS_ID',
            'MODULE_PAYMENT_ALIPAYFOR_SORT_ORDER',
            'MODULE_PAYMENT_ALIPAYFOR_PARTNER_ID',
            'MODULE_PAYMENT_ALIPAYFOR_SIGN_TYPE',
            'MODULE_PAYMENT_ALIPAYFOR_PARTNER_SIGN',
            'MODULE_PAYMENT_ALIPAYFOR_PAYMETHOD',
            'MODULE_PAYMENT_ALIPAYFOR_CURRENCY',
            'MODULE_PAYMENT_ALIPAYFOR_DEBUG_MODE',
        );
    }


    static function _cfg_select_multioption($select_array, $key_value, $key = '')
    {
        $i = 0;

        foreach ($select_array as $skey => $val) {
            $name = (($key) ? 'configuration[' . $key . '][]' : 'configuration_value');
            $string .= '<br /><input type="checkbox" name="' . $name . '" value="' . $skey . '"';
            $key_values = explode(", ", $key_value);
            if (in_array($skey, $key_values))
                $string .= ' CHECKED';

            $string .= ' /> ' . '<label for="' . strtolower($val . '-' . $name) . '" class="inputSelect">' . $val . '</label>' . "\n";
        }

        return $string;
    }
}
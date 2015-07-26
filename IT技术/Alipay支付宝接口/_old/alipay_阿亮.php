<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
// | Simplified Chinese version   http://www.zen-cart.cn                  |
// +----------------------------------------------------------------------+
//  $Id: alipay.php v3.2 2008-04-15 Jack $
//

class alipay {
	var $code, $title, $description, $enabled;
  /**
	* order status setting for pending orders
	* @var int
	*/
	var $order_pending_status = 1;
  /**
	* order status setting for completed orders
	* @var int
	*/
	var $order_status = DEFAULT_ORDERS_STATUS_ID;

//	class constructor
	function alipay() {
		global $order;
		$this->code = 'alipay';
		if ($_GET['main_page'] != '') {
			$this->title = MODULE_PAYMENT_ALIPAY_TEXT_CATALOG_TITLE; // Payment Module title in Catalog
		}
		else {
			$this->title = MODULE_PAYMENT_ALIPAY_TEXT_ADMIN_TITLE; // Payment Module title in Admin
		}
		$this->description = MODULE_PAYMENT_ALIPAY_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_PAYMENT_ALIPAY_SORT_ORDER;
		$this->enabled = ((MODULE_PAYMENT_ALIPAY_STATUS == 'True') ? true : false);
		if ((int)MODULE_PAYMENT_ALIPAY_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_ALIPAY_ORDER_STATUS_ID;
		}
		$this->form_action_url = MODULE_PAYMENT_ALIPAY_HANDLER;
	}

	function update_status() {
		global $order, $db;

		if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_ALIPAY_ZONE > 0) ) {
			$check_flag = false;
			$check_query = $db->Execute("select zone_id from ". TABLE_ZONES_TO_GEO_ZONES
									 . " where geo_zone_id = '". MODULE_PAYMENT_ALIPAY_ZONE
									 . "' and zone_country_id = '". $order->billing['country']['id']."' order by zone_id");
			while (!$check_query->EOF) {
				if ($check_query->fields['zone_id'] < 1) {
					$check_flag = true;
					break;
				}
				elseif ($check_query->fields['zone_id'] == $order->billing['zone_id']) {
					$check_flag = true;
					break;
				}
				$check_query->MoveNext();
			}
			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
	}

	function javascript_validation() {
		return false;
	}

	function selection() {
		return array('id' => $this->code,
				 'module' => MODULE_PAYMENT_ALIPAY_TEXT_CATALOG_LOGO,
				   'icon' => MODULE_PAYMENT_ALIPAY_TEXT_CATALOG_LOGO
		);
	}

	function pre_confirmation_check() {
		return false;
	}

	function confirmation() {
		return array('title' => MODULE_PAYMENT_ALIPAY_TEXT_DESCRIPTION);
	}

	function process_button() {

		global $db, $order, $currencies;

	//	Bob changed it on 2012/12/04;
		$alipay_charset = 'gb2312';
	//	$alipay_charset = 'utf-8';

		$alipay_out_trade_no = $_SESSION['hk_out_trade_no'];
		$alipay_currency = 'CNY';

		$alipay_body = '';
		for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
			$alipay_body = $order->products[$i]["name"]."+". $alipay_body;
		}
		$alipay_body = substr($alipay_body, 0, -1);

		if (strlen($alipay_body) > 250) {
			$alipay_body = substr($alipay_body, 0, 250);
		}

		$alipay_body = preg_replace('/\n/', '', $alipay_body); 

		$alipay_partner = MODULE_PAYMENT_ALIPAY_PARTNER;
		$alipay_seller_email = MODULE_PAYMENT_ALIPAY_SELLER;

	//	Bob modified it on 2013/01/23;
		$alipay_service = 'create_direct_pay_by_user';
	//	$alipay_service = 'trade_create_by_buyer';

		$alipay_return_url = zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
		$alipay_notify_url = $alipay_return_url;

		$alipay_subject = STORE_NAME;
		$alipay_price = number_format($order->info['total'] * $currencies->get_value($alipay_currency),
															  $currencies->get_decimal_places($alipay_currency), '.', '');
		$total_fee = $alipay_price;

		$alipay_show_url = zen_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
		$alipay_quantity = '1';
		$alipay_payment_type = '1';

		$alipay_logistics_type = 'EXPRESS';
		$alipay_logistics_fee = '0.00';
		$alipay_logistics_payment = 'SELLER_PAY'; 

		$alipay_receive_name = $order->customer['firstname'];

		$alipay_receive_address = $order->customer['state'].' '
								. $order->customer['city'].' '. $order->customer['street_address'];
		$alipay_receive_zip		= $order->customer['postcode'];
		$alipay_receive_mobile	= $order->customer['telephone'];

		$alipay_body = 'No:'. $alipay_out_trade_no;

		$extra_common_param = 'alipay';

		$request_string =   '_input_charset='    . $alipay_charset           .'&'.
							'extra_common_param='. $extra_common_param       .'&'.
							'notify_url='        . $alipay_notify_url        .'&'.
							'out_trade_no='      . $alipay_out_trade_no      .'&'.
							'partner='           . $alipay_partner           .'&'.
							'payment_type='      . $alipay_payment_type      .'&'.
							'return_url='        . $alipay_return_url        .'&'.
							'seller_email='      . $alipay_seller_email      .'&'.
							'service='           . $alipay_service           .'&'.
							'show_url='          . $alipay_show_url          .'&'.
							'subject='           . $alipay_subject			 .'&'.
							'total_fee='         . $total_fee;

		$process_button_string = 
			zen_draw_hidden_field('_input_charset',		$alipay_charset) .
			zen_draw_hidden_field('extra_common_param',	$extra_common_param) .
			zen_draw_hidden_field('notify_url',			$alipay_notify_url) .
			zen_draw_hidden_field('out_trade_no',		$alipay_out_trade_no) .
			zen_draw_hidden_field('partner',			$alipay_partner) .
			zen_draw_hidden_field('payment_type',		$alipay_payment_type) .
			zen_draw_hidden_field('return_url',			$alipay_return_url) .
			zen_draw_hidden_field('seller_email',		$alipay_seller_email) .
			zen_draw_hidden_field('service',			$alipay_service) .
			zen_draw_hidden_field('show_url',			$alipay_show_url) .
			zen_draw_hidden_field('subject',			$alipay_subject) .
			zen_draw_hidden_field('total_fee',			$total_fee) .
			zen_draw_hidden_field('sign',			md5($request_string . MODULE_PAYMENT_ALIPAY_MD5KEY)) . 
			zen_draw_hidden_field('sign_type',			'MD5');

		return $process_button_string;
	}

	function before_process() {

		global $messageStack, $_GET, $_POST;

		$arg = "";
	
		if (isset($_GET["sign"]) && !empty($_GET["sign"])) {
			$sort_get  = $this->arg_sort($_GET);
			while (list ($key, $val) = each ($sort_get)) {
				if (($key != "sign") && ($key != "sign_type") && ($key != "main_page")) {
					$arg .= ($key ."=". $val ."&");
				}
			}
			$alipay_ret_sign = $_GET["sign"];
		}
		else if (isset($_POST["sign"]) && !empty($_POST["sign"])) {
			$sort_post = $this->arg_sort($_POST);
			while (list ($key, $val) = each ($sort_post)) {
				if (($key != "sign") && ($key != "sign_type") && ($key != "main_page")) {
					$arg .= ($key ."=". $val ."&");
				}
			}
			$alipay_ret_sign = $_POST["sign"];
		}

	//	$prestr = substr($arg, 0, count($arg)-2);	// 去掉最后一个&号
		$prestr = substr($arg, 0, -1);
		$this->mysign = md5($prestr . MODULE_PAYMENT_ALIPAY_MD5KEY);

		if ($this->mysign == $alipay_ret_sign) {
			return true;
		}
		else {
			if (!empty($_POST["sign"])) {	// From 异步通知；
				return false;			
			}
			else {	// From 同步页面跳转；
				$messageStack->add_session('checkout_payment', '校验码不正确，支付失败', 'error');
				zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, 'from="alipay"', 'SSL', true, false));
			}
		}
	}

	function after_process() {
		global $db, $_GET, $_POST;

		$hk_trade_no = '';
		if (isset($_POST['out_trade_no']) && (strlen($_POST['out_trade_no']) > 14)) {
			$hk_trade_no = trim($_POST['out_trade_no']);
			$ret_trade_status = $_POST['trade_status'];
			$this->trade_no	  = $_POST["trade_no"];	//	As a data for pushing into TABLE_ORDERS_STATUS_HISTORY;
		}
		else if (isset($_GET['out_trade_no']) && (strlen($_GET['out_trade_no']) > 14)) {
			$hk_trade_no = trim($_GET['out_trade_no']);
			$ret_trade_status = $_GET['trade_status'];
			$this->trade_no   = $_GET["trade_no"];
		}
		$customer_id = (int)substr($hk_trade_no, 0, -14);

		if ($customer_id >= 1) {
			$order_query = "SELECT orders_id, orders_status FROM ". TABLE_ORDERS 
						. " WHERE customers_id = ". $customer_id
						. " and payment_module_code = 'alipay'"
						. " and billing_company = '". $hk_trade_no ."' ORDER BY date_purchased DESC LIMIT 1";
			$hk_o = $db->Execute($order_query);
			$o_id	  = (int)$hk_o->fields['orders_id'];
			$o_status = (int)$hk_o->fields['orders_status'];

			if ($o_status >= 2) {
				return true;
			}
			else if (($o_id > 0) && ($ret_trade_status =='TRADE_SUCCESS' || $ret_trade_status =='TRADE_FINISHED')) {
				$db->Execute("update ". TABLE_ORDERS ." set orders_status = '". $this->order_status ."', last_modified = now() where orders_id = ". $o_id);

				$db->Execute("insert into ". TABLE_ORDERS_STATUS_HISTORY 
						 . " (comments, orders_id, orders_status_id, date_added) values ('支付宝交易号："
						 . $this->trade_no ."', '". $o_id ."', '". $this->order_status ."', now() )");
				return true;
			}
		}
		return false;
	}

	function output_error() {
		return false;
	}

	function check() {
		global $db;
		if (!isset($this->_check)) {
			$check_query = $db->Execute("select configuration_value from ". TABLE_CONFIGURATION 
				." where configuration_key = 'MODULE_PAYMENT_ALIPAY_STATUS'");
			$this->_check = $check_query->RecordCount();
		}
		return $this->_check;
	}

	function install() {

		global $db, $language, $module_type;

		if (!defined('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_1_1')) {
			include(DIR_FS_CATALOG_LANGUAGES . $_SESSION['language'].'/modules/'. $module_type .'/'. $this->code .'.php');
		}
		$db->Execute("insert into ". TABLE_CONFIGURATION ." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_1_1 ."', 'MODULE_PAYMENT_ALIPAY_STATUS', 'True', '". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_1_2 ."', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into ". TABLE_CONFIGURATION ." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_2_1 ."', 'MODULE_PAYMENT_ALIPAY_SELLER', '".STORE_OWNER_EMAIL_ADDRESS."', '". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_2_2 ."', '6', '2', now())");
		$db->Execute("insert into ". TABLE_CONFIGURATION ." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_3_1 ."', 'MODULE_PAYMENT_ALIPAY_MD5KEY', '0123456789', '". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_3_2 ."', '6', '4', now())");
		$db->Execute("insert into ". TABLE_CONFIGURATION ." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_4_1 ."', 'MODULE_PAYMENT_ALIPAY_PARTNER', '1234567890123456', '". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_4_2 ."', '6', '5', now())");
		$db->Execute("insert into ". TABLE_CONFIGURATION ." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_5_1 ."', 'MODULE_PAYMENT_ALIPAY_ZONE', '0', '". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_5_2 ."', '6', '6', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
		$db->Execute("insert into ". TABLE_CONFIGURATION ." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_6_1 ."', 'MODULE_PAYMENT_ALIPAY_PROCESSING_STATUS_ID', '2', '". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_6_2 ."', '6', '8', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
		$db->Execute("insert into ". TABLE_CONFIGURATION ." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_7_1 ."', 'MODULE_PAYMENT_ALIPAY_ORDER_STATUS_ID', '1', '". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_7_2 ."', '6', '10', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
		$db->Execute("insert into ". TABLE_CONFIGURATION ." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_8_1 ."', 'MODULE_PAYMENT_ALIPAY_SORT_ORDER', '0', '". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_8_2 ."', '6', '12', now())");
		$db->Execute("insert into ". TABLE_CONFIGURATION ." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_9_1 ."', 'MODULE_PAYMENT_ALIPAY_HANDLER', 'https://www.alipay.com/cooperate/gateway.do?_input_charset=utf-8', '". MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_9_2 ."', '6', '18', '', now())");
	}

	function remove() {
		global $db;
		$db->Execute("delete from ". TABLE_CONFIGURATION ." where configuration_key LIKE 'MODULE_PAYMENT_ALIPAY%'");
	}

	function keys() {
		return array(
			'MODULE_PAYMENT_ALIPAY_STATUS',
			'MODULE_PAYMENT_ALIPAY_SELLER',
			'MODULE_PAYMENT_ALIPAY_MD5KEY',
			'MODULE_PAYMENT_ALIPAY_PARTNER',
			'MODULE_PAYMENT_ALIPAY_ZONE',
			'MODULE_PAYMENT_ALIPAY_PROCESSING_STATUS_ID',
			'MODULE_PAYMENT_ALIPAY_ORDER_STATUS_ID',
			'MODULE_PAYMENT_ALIPAY_SORT_ORDER',
			'MODULE_PAYMENT_ALIPAY_HANDLER'
		);
	}

	function arg_sort($array) {
		ksort($array);
		reset($array);
		return $array;
	}
}	?>

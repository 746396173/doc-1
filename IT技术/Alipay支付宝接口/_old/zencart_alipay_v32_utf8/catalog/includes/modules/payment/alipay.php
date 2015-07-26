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
   *
   * @var int
   */
   var $order_pending_status = 1;
  /**
   * order status setting for completed orders
   *
   * @var int
   */
   var $order_status = DEFAULT_ORDERS_STATUS_ID;

// class constructor
   function alipay() {
     global $order;
       $this->code = 'alipay';
    if ($_GET['main_page'] != '') {
       $this->title = MODULE_PAYMENT_ALIPAY_TEXT_CATALOG_TITLE; // Payment Module title in Catalog
    } else {
       $this->title = MODULE_PAYMENT_ALIPAY_TEXT_ADMIN_TITLE; // Payment Module title in Admin
    }
       $this->description = MODULE_PAYMENT_ALIPAY_TEXT_DESCRIPTION;
       $this->sort_order = MODULE_PAYMENT_ALIPAY_SORT_ORDER;
       $this->enabled = ((MODULE_PAYMENT_ALIPAY_STATUS == 'True') ? true : false);
       if ((int)MODULE_PAYMENT_ALIPAY_ORDER_STATUS_ID > 0) {
         $this->order_status = MODULE_PAYMENT_ALIPAY_ORDER_STATUS_ID;
       }
       if (is_object($order)) $this->update_status();
       $this->form_action_url = MODULE_PAYMENT_ALIPAY_HANDLER;

   }

// class methods
   function update_status() {
     global $order, $db;

     if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_ALIPAY_ZONE > 0) ) {
       $check_flag = false;
       $check_query = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_ALIPAY_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
       while (!$check_query->EOF) {
         if ($check_query->fields['zone_id'] < 1) {
           $check_flag = true;
           break;
         } elseif ($check_query->fields['zone_id'] == $order->billing['zone_id']) {
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

	 $alipay_charset = 'utf-8';
     $alipay_out_trade_no = $_SESSION['customer_id'] . date('His');
     $alipay_currency = 'CNY';

     $alipay_body = '';
     for ($i=0; $i<sizeof($order->products); $i++) {
        $alipay_body = $order->products[$i]["name"] . "+" . $alipay_body;
     }
     $alipay_body = substr($alipay_body,0,-1);
     if (strlen($alipay_body) < 250) {
        $alipay_body = substr($alipay_body,0,strlen($alipay_body));
     } else {
        $alipay_body = substr($alipay_body,250);
     }
     $alipay_body = preg_replace('/\n/','',$alipay_body); 
	 
     $alipay_partner = MODULE_PAYMENT_ALIPAY_PARTNER;
     $alipay_seller_email = MODULE_PAYMENT_ALIPAY_SELLER;
     $alipay_service = 'trade_create_by_buyer';
     $alipay_agent = '2088002197086652';
	 $alipay_return_url = zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
	
     $alipay_subject = STORE_NAME;
     $alipay_price = number_format($order->info['total'] * $currencies->get_value($alipay_currency), $currencies->get_decimal_places($alipay_currency),'.','');

     $alipay_show_url = zen_href_link(FILENAME_SHOPPING_CART, '', 'SSL');
     $alipay_quantity = '1';
     $alipay_payment_type = '1';

     $alipay_logistics_type = 'EXPRESS';
     $alipay_logistics_fee = '0.00';
     $alipay_logistics_payment = 'SELLER_PAY'; 
	 
     $alipay_receive_name = $order->customer['firstname'] . $order->customer['lastname'];
     $alipay_receive_address = $order->customer['state'] . $order->customer['city'] . $order->customer['street_address'];
     $alipay_receive_zip = $order->customer['postcode'];
	 $alipay_body='No:'.$alipay_out_trade_no;

     $request_string = 	'_input_charset='    . $alipay_charset           . '&' .
						'agent='             . $alipay_agent             . '&' .
						'body='              . $alipay_body              . '&' .
						'logistics_fee='     . $alipay_logistics_fee     . '&' .
						'logistics_payment=' . $alipay_logistics_payment . '&' .
						'logistics_type='    . $alipay_logistics_type    . '&' .
						'out_trade_no='      . $alipay_out_trade_no      . '&' .
						'partner='           . $alipay_partner           . '&' .
						'payment_type='      . $alipay_payment_type      . '&' .
						'price='             . $alipay_price             . '&' .
						'quantity='          . $alipay_quantity          . '&' .
					//	'receive_address='   . $alipay_receive_address   . '&' .
					//	'receive_name='      . $alipay_receive_name      . '&' .
					//	'receive_zip='       . $alipay_receive_zip       . '&' .
						'return_url='        . $alipay_return_url        . '&' .
						'seller_email='      . $alipay_seller_email      . '&' .
						'service='           . $alipay_service           . '&' .
						'show_url='          . $alipay_show_url          . '&' .
						'subject='           . $alipay_subject . MODULE_PAYMENT_ALIPAY_MD5KEY;

	$process_button_string =  zen_draw_hidden_field('_input_charset', $alipay_charset) .
							  zen_draw_hidden_field('agent', $alipay_agent) .
							  zen_draw_hidden_field('body', $alipay_body) .
                              zen_draw_hidden_field('logistics_fee', $alipay_logistics_fee) .
                              zen_draw_hidden_field('logistics_payment', $alipay_logistics_payment) . 
                              zen_draw_hidden_field('logistics_type', $alipay_logistics_type) .
							  zen_draw_hidden_field('out_trade_no', $alipay_out_trade_no) .
                              zen_draw_hidden_field('partner', $alipay_partner) .
                              zen_draw_hidden_field('payment_type', $alipay_payment_type) .
                              zen_draw_hidden_field('price', $alipay_price) .
                              zen_draw_hidden_field('quantity', $alipay_quantity) .
                        //      zen_draw_hidden_field('receive_address', $alipay_receive_address) .
                        //      zen_draw_hidden_field('receive_name', $alipay_receive_name) .
                        //      zen_draw_hidden_field('receive_zip', $alipay_receive_zip) .
                              zen_draw_hidden_field('return_url', $alipay_return_url) .
                              zen_draw_hidden_field('seller_email', $alipay_seller_email) .
                              zen_draw_hidden_field('service', $alipay_service) .
                              zen_draw_hidden_field('show_url', $alipay_show_url) .
                              zen_draw_hidden_field('subject', $alipay_subject) .
                              zen_draw_hidden_field('sign', md5($request_string)) . 
                              zen_draw_hidden_field('sign_type', 'MD5');

     return $process_button_string;
   }

   function before_process() {
    global $order_total_modules, $messageStack, $_GET;

	$arg = "";
	$sort_get= $this->arg_sort($_GET);
	while (list ($key, $val) = each ($sort_get)) {
		if($key != "sign" && $key != "sign_type" && $key != "main_page")
			$arg.=$key."=".$val."&";
	}
	$prestr = substr($arg,0,count($arg)-2);  //去掉最后一个&号
	$this->mysign = md5($prestr.MODULE_PAYMENT_ALIPAY_MD5KEY);

//用于写入Zen Cart后台订单历史记录中的数据
	$this->trade_no = $_GET["trade_no"];

	if ($this->mysign == $_GET["sign"]) {
	   return true;
	}else{
		$messageStack->add_session('checkout_payment', '校验码不正确，支付失败', 'error');
        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
	}
	}

   function after_process() {
	global $insert_id,$db ;
	
    $db->Execute("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (comments, orders_id, orders_status_id, date_added) values ('支付宝交易号: " . $this->trade_no . " ' , '". (int)$insert_id . "','" . $this->order_status . "', now() )");

	$_SESSION['order_created'] = '';
	return true;
   }

   function output_error() {
     return false;
   }

   function check() {
     global $db;
     if (!isset($this->_check)) {
       $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_ALIPAY_STATUS'");
       $this->_check = $check_query->RecordCount();
     }
     return $this->_check;
   }

   function install() {
     global $db, $language, $module_type;
	 if (!defined('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_1_1')) include(DIR_FS_CATALOG_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . $this->code . '.php');

     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_1_1 . "', 'MODULE_PAYMENT_ALIPAY_STATUS', 'True', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_1_2 . "', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_2_1 . "', 'MODULE_PAYMENT_ALIPAY_SELLER', '".STORE_OWNER_EMAIL_ADDRESS."', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_2_2 . "', '6', '2', now())");
      $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_3_1 . "', 'MODULE_PAYMENT_ALIPAY_MD5KEY', '0123456789', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_3_2 . "', '6', '4', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_4_1 . "', 'MODULE_PAYMENT_ALIPAY_PARTNER', '1234567890123456', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_4_2 . "', '6', '5', now())");
	  $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_5_1 . "', 'MODULE_PAYMENT_ALIPAY_ZONE', '0', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_5_2 . "', '6', '6', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_6_1 . "', 'MODULE_PAYMENT_ALIPAY_PROCESSING_STATUS_ID', '2', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_6_2 . "', '6', '8', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_7_1 . "', 'MODULE_PAYMENT_ALIPAY_ORDER_STATUS_ID', '1', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_7_2 . "', '6', '10', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_8_1 . "', 'MODULE_PAYMENT_ALIPAY_SORT_ORDER', '0', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_8_2 . "', '6', '12', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_9_1 . "', 'MODULE_PAYMENT_ALIPAY_HANDLER', 'https://www.alipay.com/cooperate/gateway.do?_input_charset=utf-8', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_9_2 . "', '6', '18', '', now())");
}

   function remove() {
     global $db;
     $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE  'MODULE_PAYMENT_ALIPAY%'");
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

	//实现多种字符解码方式
	function charset_decode($input,$_input_charset ,$_output_charset="utf-8"  ) {
		$output = "";
		if(!isset($_input_charset) )$_input_charset  = $this->_input_charset ;
		if($_input_charset == $_output_charset || $input ==null ) {
			$output = $input;
		} elseif (function_exists("mb_convert_encoding")){
			$output = mb_convert_encoding($input,$_output_charset,$_input_charset);
		} elseif(function_exists("iconv")) {
			$output = iconv($_input_charset,$_output_charset,$input);
		} else die("sorry, you have no libs support for charset changes.");
		return $output;
	}

 }
?>
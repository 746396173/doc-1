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

 //modify: vincent
 //class alipay {
 class alipay  extends base {

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
	   //bof:vincent--------$this->form_action_url use base code---???----------
       
	   if (PROJECT_VERSION_MAJOR != '1' && substr(PROJECT_VERSION_MINOR, 0, 3) != '3.9') $this->enabled = false;
       //$this->form_action_url = ((MODULE_PAYMENT_ALIPAY_DEBUG == 'True') ?  'http://mapi.p110.alipay.net/gateway.do?_input_charset=utf-8' : 'https://mapi.alipay.com/gateway.do?_input_charset=utf-8');
	   $this->form_action_url = MODULE_PAYMENT_ALIPAY_HANDLER;
	   //eof:vincent------------------------------


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


   //bof:vincent---------------------------------------
   /*
   function selection() {
     return array('id' => $this->code,
                   'module' => MODULE_PAYMENT_ALIPAY_TEXT_CATALOG_LOGO,
                   'icon' => MODULE_PAYMENT_ALIPAY_TEXT_CATALOG_LOGO
                   );
   }
   */
      function selection() {
     return array('id' => $this->code,
                   'module' => MODULE_PAYMENT_ALIPAY_TEXT_CATALOG_TITLE,
                   'fields' => array(
                                         array('title' => '',
                                               'field' => '<input name="alicardtype" type="radio" id="boc-visa" value="boc-visa" checked="checked" /><img src="/'.MODULE_PAYMENT_ALIPAY_VISA.'" />',
                                               'tag' => 'boc-visa'),
                                         array('title' => '',
                                               'field' => '<input name="alicardtype" type="radio" id="boc-master" value="boc-master"/><img src="/'.MODULE_PAYMENT_ALIPAY_MASTERCARD.'" />',
                                               'tag' => 'boc-master'),
                                         array('title' => '',
                                               'field' => '<input name="alicardtype" type="radio" id="boc-jcb" value="boc-jcb"/><img src="/'.MODULE_PAYMENT_ALIPAY_JCB.'" />',
                                               'tag' => 'boc-jcb')
									)
				);
   }
   //eof:vincent------------------------------------------

   function pre_confirmation_check() {
     return false;
   }

   function confirmation() {
      return array('title' => MODULE_PAYMENT_ALIPAY_TEXT_DESCRIPTION);
   }


   //bof:vincent-------------------------------
   /*
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
   */
      function process_button() {
     global $db, $order, $currencies, $order_totals;
	 

		$extend_param = '';
		$extend_param .='SHIP_TO_FIRSTNAME^'.($order->customer['firstname']==''?'none':$order->customer['firstname']);
		$extend_param .='|SHIP_TO_LASTNAME^'.($order->customer['lastname']==''?'none':$order->customer['lastname']);
		$extend_param .='|SHIP_TO_POSTALCODE^'.($order->customer['postcode']==''?'0':$order->customer['postcode']); 
		$extend_param .='|SHIP_TO_PHONENUMBER^'.preg_replace('/\D/', '', $order->customer['telephone']);	
		$extend_param .='|SHIP_TO_STREET1^'.($order->customer['street_address']==''?'none':$order->customer['street_address']);
		$extend_param .='|SHIP_TO_CITY^'.($order->customer['city']==''?'none':$order->customer['city']);
		$extend_param .='|SHIP_TO_STATE^'.zen_get_zone_code($order->customer['country']['id'], $order->customer['zone_id'], $order->customer['state']);
		//$extend_param .='|SHIP_TO_STATE^US-AL'; //vincent test,hard code
		$extend_param .='|SHIP_TO_COUNTRY^'.$order->customer['country']['iso_code_2'];
		//$extend_param .='|SHIP_TO_COUNTRY^US'; //vincent test,hard code
		$extend_param .='|SHIP_TO_SHIPMETHOD^EMS';
		$extend_param .='|LOGISTICS_COST^0.00';
		$extend_param .='|REGISTRATION_NAME^'.$order->customer['firstname'].$order->customer['lastname'];
		$extend_param .='|REGISTRATION_EMAIL^'.($order->customer['email_address']==''?'none':$order->customer['email_address']);
		$extend_param .='|REGISTRATION_PHONE^'.preg_replace('/\D/', '', ($order->customer['telephone']==''?'none':$order->customer['telephone']));

		/*
		$extend_param .='|ship_to_country^'.$order->customer['country']['iso_code_2'];
		$extend_param .='|ship_to_state^'.zen_get_zone_code($order->customer['country']['id'], $order->customer['zone_id'], $order->customer['state']);
		$extend_param .='|ship_to_city^'.$order->customer['city'];
		$extend_param .='|ship_to_street1^'.$order->customer['street_address'];
		$extend_param .='|ship_to_phonenumber^'.preg_replace('/\D/', '', $order->customer['telephone']);		
		$extend_param .='|REGISTRATION_NAME^'.$order->customer['firstname'].$order->customer['lastname'];
		$extend_param .='|REGISTRATION_EMAIL^'.($order->customer['email_address']==''?'none':$order->customer['email_address']);
		$extend_param .='|REGISTRATION_PHONE^'.preg_replace('/\D/', '', ($order->customer['telephone']==''?'none':$order->customer['telephone']));
		$extend_param .='|LOGISTICS_COST^0.00';
		$extend_param .='|ship_to_shipmethod^EMS';
		*/
		
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
		$alipay_body = str_replace(PHP_EOL, ' ', $alipay_body); 
		
		$order->info['payment_method']=MODULE_PAYMENT_ALIPAY_TEXT_CATALOG_TITLE;
		$order->info['payment_module_code']='alipay';
		$insert_id = '';
		if (!isset($_SESSION['alipay_insert_id'])) {
			$insert_id = $order->create($order_totals, 2);
			$order->create_add_products($insert_id);
			$_SESSION['alipay_insert_id'] = $insert_id;
			$_SESSION['alipay_body'] = $alipay_body;
			$_SESSION['alipay_fee'] = $order->info['total'];
		} else {
			if (($_SESSION['alipay_body'] == $alipay_body ) && ($_SESSION['alipay_fee'] == $order->info['total'])){
				$insert_id = $_SESSION['alipay_insert_id'];
			} else {
				$insert_id = $order->create($order_totals, 2);
				$order->create_add_products($insert_id);
				$_SESSION['alipay_insert_id'] = $insert_id;
				$_SESSION['alipay_body'] = $alipay_body;
				$_SESSION['alipay_fee'] = $order->info['total'];
			}
		}
		$order->info['payment_method']= null;
		$order->info['payment_module_code']=null;
		
		$extend_param .='|product_name^'.$alipay_body;
		$extend_param = str_replace(PHP_EOL, ' ', $extend_param); 
		
		

		
		$parameter = array ('service' => 'alipay.trade.direct.forcard.pay', 
							'default_bank' => $_POST['alicardtype'],
							'currency' => $order->info['currency'],
							'extend_param' => $extend_param,
							'_input_charset' => 'utf-8',
	                        'partner' => MODULE_PAYMENT_ALIPAY_PARTNER,
                            'seller_id' => MODULE_PAYMENT_ALIPAY_SELLER, 
                            'return_url' => zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL'),
                            'notify_url' => zen_href_link('alipay_notify.php', '', 'SSL',false,false,true),
                            "show_url" => zen_href_link(FILENAME_SHOPPING_CART, '', 'NONSSL'),
                            'out_trade_no' => $insert_id,
                            'subject' => 'order #'.$insert_id .' from '. STORE_NAME ,
                            'body' => $alipay_body, 
                            "total_fee" => number_format($order->info['total'] * $currencies->get_value($order->info['currency']), '2','.','')
        );
		
		//Vincent: Alipay don't support JPY now
		if (true){
			unset($parameter['currency']);
			$value_cny = $currencies->get_value('CNY');

			if ($value_cny == 0){
				$parameter['total_fee'] = '0.00';
			} else {
				$parameter['total_fee'] = number_format($order->info['total'] * $value_cny , '2' ,'.','');
			}

		}
		
		
		$security_code = MODULE_PAYMENT_ALIPAY_MD5KEY;
        $mysign = $this->sign ( $parameter, $security_code );

		
        $fields = $this->arg_sort ( $parameter );
        $fields ['sign_type'] = 'MD5';
        $fields ['sign'] = $mysign;
		
		
		$process_button_string = '';
		while (list ($key, $val) = each ($fields)) {
			$process_button_string .=  zen_draw_hidden_field($key,$val); 
		}
		return $process_button_string;
	 
	 
	 
   }

   //eof:vincent--------------------------------------------



   //bof:vincent-------------------------------------
   /*
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
	*/
	   function before_process() {
    global $messageStack, $_GET,$db;
		if (isset($_GET["sign"])) {
			$out_trade_no = $_GET["out_trade_no"];
			$_SESSION['order_number_created'] = $out_trade_no;
			$_SESSION['alipay_insert_id'] = $out_trade_no;			
			zen_redirect(zen_href_link(FILENAME_CHECKOUT_SUCCESS, '', 'SSL'));
		} else {
			$messageStack->add_session('checkout_payment', 'mac error during alipay pay', 'error');
			zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false));
		}
		
	}

	//eof:vincent--------------------------------------


   //bof:vincent------------------???-----------------------
   /*
   function after_process() {
	global $insert_id,$db ;
	
    $db->Execute("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (comments, orders_id, orders_status_id, date_added) values ('支付宝交易号: " . $this->trade_no . " ' , '". (int)$insert_id . "','" . $this->order_status . "', now() )");

	$_SESSION['order_created'] = '';
	return true;
   }
   */
   function after_process() {
		global $insert_id,$db ,$messageStack;
		
		$messageStack->add_session('checkout_payment', 'inser id from after_process'. $insert_id, 'error');
		return true;
   }

   //eof:vincent----------------------------------

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



  //bof:vincent--------------keep base code------------------????-----------------------
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
 /*---------liuyi code-----------------
   function install() {
     global $db, $language, $module_type;
	 if (!defined('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_1_1')) include(DIR_FS_CATALOG_LANGUAGES . $_SESSION['language'] . '/modules/' . $module_type . '/' . $this->code . '.php');

     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_1_1 . "', 'MODULE_PAYMENT_ALIPAY_STATUS', 'True', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_1_2 . "', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_2_1 . "', 'MODULE_PAYMENT_ALIPAY_SELLER_ID', '88888', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_2_2 . "', '6', '2', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_3_1 . "', 'MODULE_PAYMENT_ALIPAY_MD5KEY', '888888', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_3_2 . "', '6', '4', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_4_1 . "', 'MODULE_PAYMENT_ALIPAY_PARTNER', '88888', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_4_2 . "', '6', '5', now())");
	 $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_5_1 . "', 'MODULE_PAYMENT_ALIPAY_ZONE', '0', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_5_2 . "', '6', '6', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_6_1 . "', 'MODULE_PAYMENT_ALIPAY_PROCESSING_STATUS_ID', '2', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_6_2 . "', '6', '8', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_7_1 . "', 'MODULE_PAYMENT_ALIPAY_ORDER_STATUS_ID', '1', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_7_2 . "', '6', '10', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_8_1 . "', 'MODULE_PAYMENT_ALIPAY_SORT_ORDER', '0', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_8_2 . "', '6', '12', now())");
     $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_9_1 . "', 'MODULE_PAYMENT_ALIPAY_DEBUG', 'True', '" . MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_9_2 . "', '6', '18', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
}*/

//eof:vincent-----------------------------------------------------------

   function remove() {
     global $db;
     $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE  'MODULE_PAYMENT_ALIPAY%'");
   }


   //bof:vincent---------keep base code---------------????------------------
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
   /**-----------liuyi code---------------
      function keys() {
     return array(
         'MODULE_PAYMENT_ALIPAY_STATUS',
         'MODULE_PAYMENT_ALIPAY_PARTNER',
         'MODULE_PAYMENT_ALIPAY_SELLER_ID',
         'MODULE_PAYMENT_ALIPAY_MD5KEY',
         'MODULE_PAYMENT_ALIPAY_ZONE',
         'MODULE_PAYMENT_ALIPAY_PROCESSING_STATUS_ID',
         'MODULE_PAYMENT_ALIPAY_ORDER_STATUS_ID',
         'MODULE_PAYMENT_ALIPAY_SORT_ORDER',
         'MODULE_PAYMENT_ALIPAY_DEBUG'
         );
   }
  */
   //eof:vincent-----------------------------------------

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



 //bof:vincent-------------------------------------
     protected function para_filter($parameter) {
        $para = array ();
        while ( list ( $key, $val ) = each ( $parameter ) ) {
            if ($key == "sign" || $key == "sign_type"  || $key == "main_page" || $val == "")
                continue;
            else
                $para [$key] = $parameter [$key];
        
        }
        return $para;
    }
	
	public function sign($data, $security_code) {
        $sign_type = 'MD5';
        $mysign = "";
        $_input_charset = 'utf-8';
        
        $post = $this->para_filter ( $data );
        $sort_post = $this->arg_sort ( $post );
        
        $arg = "";
        while ( list ( $key, $val ) = each ( $sort_post ) ) {
            $arg .= $key . "=" . $val . "&";
        }
        $prestr = "";
        $prestr = substr ( $arg, 0, count ( $arg ) - 2 ); 
		//$prestr = urldecode ($prestr );
        return md5 ( $prestr . $security_code );
    }	

 //eof:vincent---------------------------------------

  }

?>
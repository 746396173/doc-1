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
//  $Id: alipay.php dob  www.zhaopeiyuan.com $
//
  define('MODULE_PAYMENT_ALIPAY_TEXT_ADMIN_TITLE', 'ALIPAY Online Payment');
  define('MODULE_PAYMENT_ALIPAY_TEXT_CATALOG_TITLE', 'ALIPAY Online Payment');
  define('MODULE_PAYMENT_ALIPAY_TEXT_DESCRIPTION', 'AliPay\'s core service is an escrow service');

  define('MODULE_PAYMENT_ALIPAY_ENTRY_STATE', 'Notification:');
  define('MODULE_PAYMENT_ALIPAY_ENTRY_MODATE', 'Date:');

  define('MODULE_PAYMENT_ALIPAY_VISA', DIR_WS_MODULES . '/payment/alipay/card_logo_VC.png');
  define('MODULE_PAYMENT_ALIPAY_MASTERCARD', DIR_WS_MODULES . '/payment/alipay/card_logo_MC.png');
  define('MODULE_PAYMENT_ALIPAY_JCB', DIR_WS_MODULES . '/payment/alipay/card_logo_JC.png');
  define('MODULE_PAYMENT_ALIPAY_TEXT_CATALOG_LOGO', 'Alipay Credit Card');


  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_1_1', 'Enable ALIPAY Module');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_1_2', 'Do you want to accept ALIPAY payments?');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_2_1', 'ALIPAY Seller ID');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_2_2', 'same as Partner ID if this is not a sub account');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_3_1', 'ALIPAY MD5 key');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_3_2', 'ALIPAY MD5 key');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_4_1', 'ALIPAY Partner ID');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_4_2', 'ALIPAY Partner ID');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_5_1', 'Payment Zone');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_5_2', 'If a zone is selected, only enable this payment method for that zone.');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_6_1', 'Set Order Status');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_6_2', 'Set the status of orders made with this payment module that have completed payment to this value<br />(Processing recommended)');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_7_1', 'Set Pending Notification Status');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_7_2', 'Set the status of orders made with this payment module to this value<br />(Pending recommended)');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_8_1', 'Sort order of display');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_8_2', 'Sort order of display. Lowest is displayed first.');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_9_1', 'Debug mode');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_9_2', 'if True, payment request will be send to alipay test domain.');  

?>
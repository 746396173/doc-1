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
  define('MODULE_PAYMENT_ALIPAY_TEXT_ADMIN_TITLE', '支付宝');
  define('MODULE_PAYMENT_ALIPAY_TEXT_CATALOG_TITLE', '支付宝在线支付');
  define('MODULE_PAYMENT_ALIPAY_TEXT_DESCRIPTION', '验货后再付款，实名认证。');

  define('MODULE_PAYMENT_ALIPAY_ENTRY_STATE', '支付状态:');
  define('MODULE_PAYMENT_ALIPAY_ENTRY_MODATE', '日期:');

  define('MODULE_PAYMENT_ALIPAY_MARK_BUTTON_IMG', DIR_WS_MODULES . '/payment/alipay/alipay.gif');
  define('MODULE_PAYMENT_ALIPAY_MARK_BUTTON_ALT', '通过支付宝支付');
  define('MODULE_PAYMENT_ALIPAY_ACCEPTANCE_MARK_TEXT', '简单、安全、快速的在线支付');

  define('MODULE_PAYMENT_ALIPAY_TEXT_CATALOG_LOGO', '<img src="' . MODULE_PAYMENT_ALIPAY_MARK_BUTTON_IMG . '" alt="' . MODULE_PAYMENT_ALIPAY_MARK_BUTTON_ALT . '" title="' . MODULE_PAYMENT_ALIPAY_MARK_BUTTON_ALT . '" /> &nbsp;' .  '<span class="smallText">' . MODULE_PAYMENT_ALIPAY_ACCEPTANCE_MARK_TEXT . '</span>');

  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_1_1', '打开支付宝支付模块');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_1_2', '您要使用支付宝支付方式吗?');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_2_1', '商家email');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_2_2', '您的支付宝帐号的电子邮件地址');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_3_1', '安全校验码');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_3_2', '在这里输入交易安全校验码', '您账户的交易安全校验码，注意保密'); 
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_4_1', '合作者身份ID');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_4_2', '您账户的合作者身份ID'); 
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_5_1', '付款地区');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_5_2', '如果选择了付款地区，仅该地区可以使用该支付模块。');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_6_1', '设置支付状态');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_6_2', '设置客户付款完成后的订单状态<br />推荐状态(处理中)');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_7_1', '设置订单状态');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_7_2', '设置采用支付宝付款的订单状态<br />推荐状态(等待中)');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_8_1', '显示顺序');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_8_2', '显示顺序：小的显示在前。');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_9_1', '支付宝交易的网址<br />缺省: <code>https://www.alipay.com/cooperate/gateway.do?_input_charset=utf-8</code><br />(不用修改)');  
  define('MODULE_PAYMENT_ALIPAY_TEXT_CONFIG_9_2', '支付宝正式交易的网址');  

  ?>
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

define('MODULE_PAYMENT_ALIPAYFOR_CHARSET', 'utf-8');

define('MODULE_PAYMENT_ALIPAYFOR_TEXT_TITLE', 'Alipay Credit Card Payment');
define('MODULE_PAYMENT_ALIPAYFOR_TEXT_DESCRIPTION', 'Credit Card via Alipay');
define('MODULE_PAYMENT_ALIPAYFOR_MARK_BUTTON_IMG', '/includes/modules/payment/alipay/logo/alipay_en.gif');
define('MODULE_PAYMENT_ALIPAYFOR_MARK_BUTTON_ALT', 'Checkout with Alipay');
define('MODULE_PAYMENT_ALIPAYFOR_TEXT_EMAIL_FOOTER', '');

define('MODULE_PAYMENT_ALIPAYFOR_TEXT_CATALOG_LOGO', '<img src="' . MODULE_PAYMENT_ALIPAYFOR_MARK_BUTTON_IMG . '" alt="' . MODULE_PAYMENT_ALIPAYFOR_MARK_BUTTON_ALT . '" title="' . MODULE_PAYMENT_ALIPAYFOR_MARK_BUTTON_ALT . '" />' . '<span>' . MODULE_PAYMENT_ALIPAYFOR_TEXT_DESCRIPTION . '</span>');

if (IS_ADMIN_FLAG === true) {
    define('MODULE_PAYMENT_ALIPAYFOR_VERSION', 'ZC-FORCARD-5.0');
    define('MODULE_PAYMENT_ALIPAYFOR_TEXT_ADMIN_DESCRIPTION', '<iframe width="100%" id="shipping_cost" scrolling="no" height="50" frameborder="0" src="http://alipaymate.com/checkver.php?ver=' . MODULE_PAYMENT_ALIPAYFOR_VERSION . '"></iframe>');
}
<?php
/**
 * Alipay forcard Payment Module
 *
 * @author alipaymate.com
 * @copyright (c) 2013 alipaymate.com
 * @copyright Portions Copyright (c) 2003 Zen Cart
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

require('includes/application_top.php');
require(DIR_FS_CATALOG . DIR_WS_LANGUAGES . 'english/modules/payment/alipay_forcard.php');
require(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/alipay_forcard.php');

if (!empty($_POST['notify_id'])) {
    if ($_POST['trade_status'] !='TRADE_SUCCESS') {
        alipay_forcard_notify_error();
    }

    $alipay_core = new alipay_core;
    $alipay      = new alipay_forcard;

    $alipay_config = $alipay->prepareConfig();
    $alipay_core->setConfig($alipay_config);

    $result = $alipay_core->verifyResponse($_POST);

    if ($result) {
        $order_id = (int)(isset($_POST['extra_common_param']) ? $_POST['extra_common_param'] : 0);

        if ($order_id) {
            $order_query = 'select orders_id, orders_status from ' . TABLE_ORDERS . " where payment_module_code = 'alipay_forcard' and orders_id = " . $order_id;
            $orders = $db->Execute($order_query);

            if ($orders->RecordCount() < 1) {
                alipay_forcard_notify_error();
            }

            if ($orders->fields['orders_status'] == MODULE_PAYMENT_ALIPAYFOR_ORDER_STATUS_ID) {
                alipay_forcard_notify_success();
            }

            $db->Execute('update ' . TABLE_ORDERS . ' set last_modified=now(), orders_status = ' . MODULE_PAYMENT_ALIPAYFOR_ORDER_STATUS_ID . ' where orders_id = ' . $order_id);

            $comments = 'trade_no: '        . zen_db_prepare_input($_POST['trade_no'])        . "\r\n"
                      . 'out_trade_no: '    . zen_db_prepare_input($_POST['out_trade_no'])    . "\r\n"
                      . 'notify_type: '     . zen_db_prepare_input($_POST['notify_type'])     . "\r\n"
                      . 'trade_status: '    . zen_db_prepare_input($_POST['trade_status'])    . "\r\n"
                      . 'forex_total_fee: ' . zen_db_prepare_input($_POST['forex_total_fee']) . ' ' . $_POST['forex_currency'] . "\r\n"
                      . 'total_fee: '       . zen_db_prepare_input($_POST['total_fee'])       . ' RMB' . "\r\n";

            $sql_data_array = array(
                'orders_id'         => $order_id,
                'orders_status_id'  => MODULE_PAYMENT_ALIPAYFOR_ORDER_STATUS_ID,
                'date_added'        => 'now()',
                'customer_notified' => '0',
                'comments'          => $comments
            );

            zen_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
            alipay_forcard_notify_success();
        }
    }
}

alipay_forcard_notify_error();

function alipay_forcard_notify_error() {
    echo 'failure';
    exit();
}

function alipay_forcard_notify_success() {
    echo 'success';
    exit();
}
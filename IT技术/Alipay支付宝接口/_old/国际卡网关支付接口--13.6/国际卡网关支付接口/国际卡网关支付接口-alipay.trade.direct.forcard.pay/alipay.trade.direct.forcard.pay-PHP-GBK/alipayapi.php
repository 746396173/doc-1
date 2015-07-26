<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	<title>支付宝国际卡网关支付接口接口</title>
</head>
<?php
/* *
 * 功能：国际卡网关支付接口接入页
 * 版本：3.3
 * 修改日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************注意*************************
 * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 */

require_once("alipay.config.php");
require_once("lib/alipay_submit.class.php");

/**************************请求参数**************************/

        //服务器异步通知页面路径
        $notify_url = "http://www.xxx.com/alipay.trade.direct.forcard.pay-PHP-GBK/notify_url.php";
        //需http://格式的完整路径，不允许加?id=123这类自定义参数
        //页面跳转同步通知页面路径
        $return_url = "http://www.xxx.com/alipay.trade.direct.forcard.pay-PHP-GBK/return_url.php";
        //需http://格式的完整路径，不允许加?id=123这类自定义参数
        //商户订单号
        $out_trade_no = $_POST['WIDout_trade_no'];
        //必填
        //订单名称
        $subject = $_POST['WIDsubject'];
        //必填
        //默认网银
        $default_bank = $_POST['WIDdefault_bank'];
        //必填，如果要使用外卡支付功能，本参数需赋值为“12.5 银行列表”中的值
        //公用业务扩展参数
        $extend_param = $_POST['WIDextend_param'];
        //必填，用于商户的特定业务信息的传递
        //卖家支付宝账号
        $seller_logon_id = $_POST['WIDseller_logon_id'];
        //必填
        //付款金额
        $total_fee = $_POST['WIDtotal_fee'];
        //必填
        //订单描述
        $body = $_POST['WIDbody'];
        //商品展示地址
        $show_url = $_POST['WIDshow_url'];
        //币种
        $currency = $_POST['WIDcurrency'];
        //必填，default_bank为boc-visa或boc-master时，支持USD，为boc-jcb时，不支持currency参数，即默认支持RMB


/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
		"service" => "alipay.trade.direct.forcard.pay",
		"partner" => trim($alipay_config['partner']),
		"notify_url"	=> $notify_url,
		"return_url"	=> $return_url,
		"out_trade_no"	=> $out_trade_no,
		"subject"	=> $subject,
		"default_bank"	=> $default_bank,
		"extend_param"	=> $extend_param,
		"seller_logon_id"	=> $seller_logon_id,
		"total_fee"	=> $total_fee,
		"body"	=> $body,
		"show_url"	=> $show_url,
		"currency"	=> $currency,
		"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
echo $html_text;

?>
</body>
</html>
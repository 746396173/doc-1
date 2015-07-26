<%
/* *
 *功能：国际卡网关支付接口接入页
 *版本：3.3
 *日期：2012-08-14
 *说明：
 *以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 *该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************注意*****************
 *如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
 *1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 *2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 *3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 *如果不想使用扩展功能请把扩展功能参数赋空值。
 **********************************************
 */
%>
<%@ page language="java" contentType="text/html; charset=gbk" pageEncoding="gbk"%>
<%@ page import="com.alipay.config.*"%>
<%@ page import="com.alipay.util.*"%>
<%@ page import="java.util.HashMap"%>
<%@ page import="java.util.Map"%>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=gbk">
		<title>支付宝国际卡网关支付接口</title>
	</head>
	<%
		////////////////////////////////////请求参数//////////////////////////////////////

		//服务器异步通知页面路径
		String notify_url = "http://www.xxx.com/alipay.trade.direct.forcard.pay-JAVA-GBK/notify_url.jsp";
		//需http://格式的完整路径，不允许加?id=123这类自定义参数
		//页面跳转同步通知页面路径
		String return_url = "http://www.xxx.com/alipay.trade.direct.forcard.pay-JAVA-GBK/return_url.jsp";
		//需http://格式的完整路径，不允许加?id=123这类自定义参数
		//商户订单号
		String out_trade_no = new String(request.getParameter("WIDout_trade_no").getBytes("ISO-8859-1"),"GBK");
		//必填
		//订单名称
		String subject = new String(request.getParameter("WIDsubject").getBytes("ISO-8859-1"),"GBK");
		//必填
		//默认网银
		String default_bank = new String(request.getParameter("WIDdefault_bank").getBytes("ISO-8859-1"),"GBK");
		//必填，如果要使用外卡支付功能，本参数需赋值为“12.5 银行列表”中的值
		//公用业务扩展参数
		String extend_param = new String(request.getParameter("WIDextend_param").getBytes("ISO-8859-1"),"GBK");
		//必填，用于商户的特定业务信息的传递
		//卖家支付宝账号
		String seller_logon_id = new String(request.getParameter("WIDseller_logon_id").getBytes("ISO-8859-1"),"GBK");
		//必填
		//付款金额
		String total_fee = new String(request.getParameter("WIDtotal_fee").getBytes("ISO-8859-1"),"GBK");
		//必填
		//订单描述
		String body = new String(request.getParameter("WIDbody").getBytes("ISO-8859-1"),"GBK");
		//商品展示地址
		String show_url = new String(request.getParameter("WIDshow_url").getBytes("ISO-8859-1"),"GBK");
		//币种
		String currency = new String(request.getParameter("WIDcurrency").getBytes("ISO-8859-1"),"GBK");
		//必填，default_bank为boc-visa或boc-master时，支持USD，为boc-jcb时，不支持currency参数，即默认支持RMB
		
		
		//////////////////////////////////////////////////////////////////////////////////
		
		//把请求参数打包成数组
		Map<String, String> sParaTemp = new HashMap<String, String>();
		sParaTemp.put("service", "alipay.trade.direct.forcard.pay");
        sParaTemp.put("partner", AlipayConfig.partner);
        sParaTemp.put("_input_charset", AlipayConfig.input_charset);
		sParaTemp.put("notify_url", notify_url);
		sParaTemp.put("return_url", return_url);
		sParaTemp.put("out_trade_no", out_trade_no);
		sParaTemp.put("subject", subject);
		sParaTemp.put("default_bank", default_bank);
		sParaTemp.put("extend_param", extend_param);
		sParaTemp.put("seller_logon_id", seller_logon_id);
		sParaTemp.put("total_fee", total_fee);
		sParaTemp.put("body", body);
		sParaTemp.put("show_url", show_url);
		sParaTemp.put("currency", currency);
		
		//建立请求
		String sHtmlText = AlipaySubmit.buildRequest(sParaTemp,"get","确认");
		out.println(sHtmlText);
	%>
	<body>
	</body>
</html>

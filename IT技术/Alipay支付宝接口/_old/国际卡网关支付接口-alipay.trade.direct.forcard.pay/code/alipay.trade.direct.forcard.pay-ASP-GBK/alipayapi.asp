<%
' 功能：国际卡网关支付接口接入页
' 版本：3.3
' 日期：2012-07-17
' 说明：
' 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
' 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
	
' /////////////////注意/////////////////
' 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
' 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
' 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
' 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
' /////////////////////////////////////

%>

<!--#include file="class/alipay_submit.asp"-->

<%
'/////////////////////请求参数/////////////////////

        '服务器异步通知页面路径
        notify_url = "http://www.xxx.com/alipay.trade.direct.forcard.pay-CSHARP-GBK/notify_url.asp"
        '需http://格式的完整路径，不允许加?id=123这类自定义参数
        '页面跳转同步通知页面路径
        return_url = "http://www.xxx.com/alipay.trade.direct.forcard.pay-CSHARP-GBK/return_url.asp"
        '需http://格式的完整路径，不允许加?id=123这类自定义参数
        '商户订单号
        out_trade_no = Request.Form("WIDout_trade_no")
        '必填
        '订单名称
        subject = Request.Form("WIDsubject")
        '可填
        '默认网银
        default_bank = Request.Form("WIDdefault_bank")
        '必填，如果要使用外卡支付功能，本参数需赋值为“12.5 银行列表”中的值
        '公用业务扩展参数
        extend_param = Request.Form("WIDextend_param")
        '必填，用于商户的特定业务信息的传递
        '卖家支付宝账号
        seller_logon_id = Request.Form("WIDseller_logon_id")
        '必填
        '付款金额
        total_fee = Request.Form("WIDtotal_fee")
        '必填
        '订单描述
        body = Request.Form("WIDbody")
        '商品展示地址
        show_url = Request.Form("WIDshow_url")
        '币种
        currency = Request.Form("WIDcurrency")
        '必填，default_bank为boc-visa或boc-master时，支持USD，为boc-jcb时，不支持currency参数，即默认支持RMB

'/////////////////////请求参数/////////////////////

'构造请求参数数组
sParaTemp = Array("service=alipay.trade.direct.forcard.pay","partner="&partner,"_input_charset="&input_charset  ,"notify_url="&notify_url   ,"return_url="&return_url   ,"out_trade_no="&out_trade_no   ,"subject="&subject   ,"default_bank="&default_bank   ,"extend_param="&extend_param   ,"seller_logon_id="&seller_logon_id   ,"total_fee="&total_fee   ,"body="&body   ,"show_url="&show_url   ,"currency="&currency  )

'建立请求
Set objSubmit = New AlipaySubmit
sHtml = objSubmit.BuildRequestForm(sParaTemp, "get", "确认")
response.Write sHtml


%>
<html>
<head>
	<META http-equiv=Content-Type content="text/html; charset=gb2312">
<title>支付宝国际卡网关支付接口</title>
</head>
<body>
</body>
</html>

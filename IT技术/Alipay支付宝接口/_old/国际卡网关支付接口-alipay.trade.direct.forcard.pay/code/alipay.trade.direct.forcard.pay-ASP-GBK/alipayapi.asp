<%
' ���ܣ����ʿ�����֧���ӿڽ���ҳ
' �汾��3.3
' ���ڣ�2012-07-17
' ˵����
' ���´���ֻ��Ϊ�˷����̻����Զ��ṩ���������룬�̻����Ը����Լ���վ����Ҫ�����ռ����ĵ���д,����һ��Ҫʹ�øô��롣
' �ô������ѧϰ���о�֧�����ӿ�ʹ�ã�ֻ���ṩһ���ο���
	
' /////////////////ע��/////////////////
' ������ڽӿڼ��ɹ������������⣬���԰��������;�������
' 1���̻��������ģ�https://b.alipay.com/support/helperApply.htm?action=consultationApply�����ύ���뼯��Э�������ǻ���רҵ�ļ�������ʦ������ϵ��Э�����
' 2���̻��������ģ�http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9��
' 3��֧������̳��http://club.alipay.com/read-htm-tid-8681712.html��
' /////////////////////////////////////

%>

<!--#include file="class/alipay_submit.asp"-->

<%
'/////////////////////�������/////////////////////

        '�������첽֪ͨҳ��·��
        notify_url = "http://www.xxx.com/alipay.trade.direct.forcard.pay-CSHARP-GBK/notify_url.asp"
        '��http://��ʽ������·�����������?id=123�����Զ������
        'ҳ����תͬ��֪ͨҳ��·��
        return_url = "http://www.xxx.com/alipay.trade.direct.forcard.pay-CSHARP-GBK/return_url.asp"
        '��http://��ʽ������·�����������?id=123�����Զ������
        '�̻�������
        out_trade_no = Request.Form("WIDout_trade_no")
        '����
        '��������
        subject = Request.Form("WIDsubject")
        '����
        'Ĭ������
        default_bank = Request.Form("WIDdefault_bank")
        '������Ҫʹ���⿨֧�����ܣ��������踳ֵΪ��12.5 �����б��е�ֵ
        '����ҵ����չ����
        extend_param = Request.Form("WIDextend_param")
        '��������̻����ض�ҵ����Ϣ�Ĵ���
        '����֧�����˺�
        seller_logon_id = Request.Form("WIDseller_logon_id")
        '����
        '������
        total_fee = Request.Form("WIDtotal_fee")
        '����
        '��������
        body = Request.Form("WIDbody")
        '��Ʒչʾ��ַ
        show_url = Request.Form("WIDshow_url")
        '����
        currency = Request.Form("WIDcurrency")
        '���default_bankΪboc-visa��boc-masterʱ��֧��USD��Ϊboc-jcbʱ����֧��currency��������Ĭ��֧��RMB

'/////////////////////�������/////////////////////

'���������������
sParaTemp = Array("service=alipay.trade.direct.forcard.pay","partner="&partner,"_input_charset="&input_charset  ,"notify_url="&notify_url   ,"return_url="&return_url   ,"out_trade_no="&out_trade_no   ,"subject="&subject   ,"default_bank="&default_bank   ,"extend_param="&extend_param   ,"seller_logon_id="&seller_logon_id   ,"total_fee="&total_fee   ,"body="&body   ,"show_url="&show_url   ,"currency="&currency  )

'��������
Set objSubmit = New AlipaySubmit
sHtml = objSubmit.BuildRequestForm(sParaTemp, "get", "ȷ��")
response.Write sHtml


%>
<html>
<head>
	<META http-equiv=Content-Type content="text/html; charset=gb2312">
<title>֧�������ʿ�����֧���ӿ�</title>
</head>
<body>
</body>
</html>

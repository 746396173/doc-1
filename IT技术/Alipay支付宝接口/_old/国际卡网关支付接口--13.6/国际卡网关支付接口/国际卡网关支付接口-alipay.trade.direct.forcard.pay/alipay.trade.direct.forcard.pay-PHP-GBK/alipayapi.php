<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	<title>֧�������ʿ�����֧���ӿڽӿ�</title>
</head>
<?php
/* *
 * ���ܣ����ʿ�����֧���ӿڽ���ҳ
 * �汾��3.3
 * �޸����ڣ�2012-07-23
 * ˵����
 * ���´���ֻ��Ϊ�˷����̻����Զ��ṩ���������룬�̻����Ը����Լ���վ����Ҫ�����ռ����ĵ���д,����һ��Ҫʹ�øô��롣
 * �ô������ѧϰ���о�֧�����ӿ�ʹ�ã�ֻ���ṩһ���ο���

 *************************ע��*************************
 * ������ڽӿڼ��ɹ������������⣬���԰��������;�������
 * 1���̻��������ģ�https://b.alipay.com/support/helperApply.htm?action=consultationApply�����ύ���뼯��Э�������ǻ���רҵ�ļ�������ʦ������ϵ��Э�����
 * 2���̻��������ģ�http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9��
 * 3��֧������̳��http://club.alipay.com/read-htm-tid-8681712.html��
 * �������ʹ����չ���������չ���ܲ�������ֵ��
 */

require_once("alipay.config.php");
require_once("lib/alipay_submit.class.php");

/**************************�������**************************/

        //�������첽֪ͨҳ��·��
        $notify_url = "http://www.xxx.com/alipay.trade.direct.forcard.pay-PHP-GBK/notify_url.php";
        //��http://��ʽ������·�����������?id=123�����Զ������
        //ҳ����תͬ��֪ͨҳ��·��
        $return_url = "http://www.xxx.com/alipay.trade.direct.forcard.pay-PHP-GBK/return_url.php";
        //��http://��ʽ������·�����������?id=123�����Զ������
        //�̻�������
        $out_trade_no = $_POST['WIDout_trade_no'];
        //����
        //��������
        $subject = $_POST['WIDsubject'];
        //����
        //Ĭ������
        $default_bank = $_POST['WIDdefault_bank'];
        //������Ҫʹ���⿨֧�����ܣ��������踳ֵΪ��12.5 �����б��е�ֵ
        //����ҵ����չ����
        $extend_param = $_POST['WIDextend_param'];
        //��������̻����ض�ҵ����Ϣ�Ĵ���
        //����֧�����˺�
        $seller_logon_id = $_POST['WIDseller_logon_id'];
        //����
        //������
        $total_fee = $_POST['WIDtotal_fee'];
        //����
        //��������
        $body = $_POST['WIDbody'];
        //��Ʒչʾ��ַ
        $show_url = $_POST['WIDshow_url'];
        //����
        $currency = $_POST['WIDcurrency'];
        //���default_bankΪboc-visa��boc-masterʱ��֧��USD��Ϊboc-jcbʱ����֧��currency��������Ĭ��֧��RMB


/************************************************************/

//����Ҫ����Ĳ������飬����Ķ�
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

//��������
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "ȷ��");
echo $html_text;

?>
</body>
</html>
<?php


  require('includes/application_top.php');
  

  
   function a_para_filter($parameter) {
        $para = array ();
        while ( list ( $key, $val ) = each ( $parameter ) ) {
            if ($key == "sign" || $key == "sign_type" || $val == "")
                continue;
            else
                $para [$key] = $parameter [$key];
        
        }
        return $para;
    }
    
    function a_arg_sort($array) {
        ksort ( $array );
        reset ( $array );
        return $array;
    }
	
    function verify_alipay_sign($data) {
        $security_code = MODULE_PAYMENT_ALIPAY_MD5KEY;
        
        $mysign = alipay_sign ( $data, $security_code );
        return $mysign == $data ["sign"];
    }
	
	function alipay_sign($data, $security_code) {
   
        $post = a_para_filter ( $data );
        $sort_post = a_arg_sort ( $post );
        
        $arg = "";
        while ( list ( $key, $val ) = each ( $sort_post ) ) {
            $arg .= $key . "=" . $val . "&";
        }
        $prestr = "";
        $prestr = substr ( $arg, 0, count ( $arg ) - 2 );
		//$prestr = urldecode ($prestr );
        return md5 ( $prestr . $security_code );
    }
	
	
	if (!defined('MODULE_PAYMENT_ALIPAY_STATUS') || (MODULE_PAYMENT_ALIPAY_STATUS  != 'True')) {
    exit;
  }
  
		$postData = $_POST;
		if (verify_alipay_sign($postData)) {
            $orderId = $postData ['out_trade_no'];
			
			$sql = "select orders_status, currency from " . TABLE_ORDERS . " where orders_id = '".$orderId."'";
			$result = $db->Execute($sql);
			$currency = $result->fields['currency'];
			if (currency != '') {
				switch($postData ['trade_status']) {
				case 'WAIT_BUYER_PAY': //�ȴ���Ҹ���
					echo 'success';
					break;
				case 'TRADE_SUCCESS': //���֧���ɹ��� ���ڼ�ʱ֧�����������Ϣ��
					if ($result->fields['orders_status'] != MODULE_PAYMENT_ALIPAY_PROCESSING_STATUS_ID) {
					  $db->Execute("update " . TABLE_ORDERS . " set orders_status = '" . (MODULE_PAYMENT_ALIPAY_PROCESSING_STATUS_ID > 0 ? (int)MODULE_PAYMENT_ALIPAY_PROCESSING_STATUS_ID : (int)DEFAULT_ORDERS_STATUS_ID) . "', last_modified = now() where orders_id = '" . $orderId . "'");
					  $db->Execute("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (comments, orders_id, orders_status_id, date_added) values ('order was payed from alipay notified: " . $orderId . " ' , '". (int)$orderId . "','" . MODULE_PAYMENT_ALIPAY_PROCESSING_STATUS_ID . "', now() )");
					}
					echo 'success';
					break;
				case 'TRADE_FINISHED':  //��ʱ֧���У��������3���º󣬽����ͱ�״̬��
					if ($result->fields['orders_status'] != MODULE_PAYMENT_ALIPAY_PROCESSING_STATUS_ID) {
					  $db->Execute("update " . TABLE_ORDERS . " set orders_status = '" . (MODULE_PAYMENT_ALIPAY_PROCESSING_STATUS_ID > 0 ? (int)MODULE_PAYMENT_ALIPAY_PROCESSING_STATUS_ID : (int)DEFAULT_ORDERS_STATUS_ID) . "', last_modified = now() where orders_id = '" . $orderId . "'");
					  $db->Execute("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (comments, orders_id, orders_status_id, date_added) values ('order was payed from alipay notified: " . $orderId . " ' , '". (int)$orderId . "','" . MODULE_PAYMENT_ALIPAY_PROCESSING_STATUS_ID . "', now() )");
					}
					echo 'success';
					break;
				default:
					echo "success";
				}				
			} else {
				echo 'fail';
			}

        } else {
			echo 'fail';
		}
		
		

?>

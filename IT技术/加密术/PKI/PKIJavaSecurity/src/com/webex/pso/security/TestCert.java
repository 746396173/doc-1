package com.webex.pso.security;

import java.io.FileInputStream;
import java.io.InputStream;
import java.security.cert.CertificateFactory;
import java.security.cert.X509Certificate;
import java.text.SimpleDateFormat;
import java.util.Date;

import junit.framework.TestCase;

/**
 * 读取数字证书内容
 * 数字证书来自C:\Program Files\Adobe\Adobe Flash CS3\AMT下的AUMProduct.cer
 * 
 * X509Certificate：X.509证书的抽象类，提供一种访问X509证书所有属性的方法
 */
public class TestCert extends TestCase{
	
	public void testCert(){
		showCertInfo();
	}
	
	public void showCertInfo(){
		try{
			//读取数字证书
			InputStream inStream = new FileInputStream("D:\\workspace\\PKIJavaSecurity\\doc\\AUMProduct.cer");
			//创建X509工厂类
			CertificateFactory cf = CertificateFactory.getInstance("X.509");
			//创建证书对象
			X509Certificate oCert = (X509Certificate)cf.generateCertificate(inStream);
			inStream.close();
			SimpleDateFormat dateformat = new SimpleDateFormat("yyyy/MM/dd");
			String info = null;
			//获取证书版本
			info = String.valueOf(oCert.getVersion());
			System.out.println("证书版本："+info);
			//获取证书序列号
			info = oCert.getSerialNumber().toString(16);
			System.out.println("证书序列号："+info);
			//获取证书有效期
			Date beforedate = oCert.getNotBefore();
			info = dateformat.format(beforedate);
			System.out.println("证书生效日期："+info);
			Date afterdate = oCert.getNotAfter();
			info = dateformat.format(afterdate);
			System.out.println("证书失效日期："+info);
			//获得证书主体信息
			info = oCert.getSubjectDN().getName();
			System.out.println("证书拥有者："+info);
			//获得证书颁发这信息
			info = oCert.getIssuerDN().getName();
			System.out.println("证书颁发者："+info);
			//获取证书签名算法名称
			info = oCert.getSigAlgName();
			System.out.println("证书签名算法："+info);
			System.out.println("证书所含公钥："+oCert.getPublicKey());
			System.out.println("签名值：");
			//获取证书签名值
			byte[] sign = oCert.getSignature();
			printHex(sign,sign.length);
			//获得证书der编码数据
			byte[] tbsCertificate = oCert.getTBSCertificate();
			System.out.println("证书DER编码数据：");
			printHex(tbsCertificate,tbsCertificate.length); 
		}catch(Exception ex){
			System.out.println("解析证书出错："+ex);
		}
	}
	
	/**
	 * 打印数据
	 * @param date
	 * @param len
	 */
	public static void printHex(byte data[],int len){
		int i;
		int temp;
		String strTemp = "";
		for(i=0;i<len;i++){
			if(i%16 == 0){
				System.out.println("");
				//0x0000
				if(i<0x10){
					strTemp = "0x000";
				}
				if((i<0x100) && (i>=0x10)){
					strTemp = "0x00";
				}
				if((i>=0x100) && (i<0x1000)){
					strTemp = "0x0";
				}
				if(i>=0x1000){
					strTemp = "0x";
				}
				System.out.println(strTemp+Integer.toHexString(i)+"h:");
			}
			temp = data[i];
			if(temp < 0){
				temp = 256 + temp;
				if(temp < 0x10){
					System.out.println("0"+Integer.toHexString(temp)+" ");
				}else{
					System.out.println(Integer.toHexString(temp)+" ");
				}
				
			}
			System.out.println("");
		}
	}

}

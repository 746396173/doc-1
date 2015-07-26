package com.webex.pso.security;



import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.security.KeyPair;
import java.security.KeyPairGenerator;
import java.security.cert.CertificateFactory;
import java.security.cert.X509Certificate;
import javax.crypto.Cipher;
import javax.crypto.CipherOutputStream;

import junit.framework.TestCase;


public class TestJavaPKI extends TestCase{
	/**
	 * 从数字证书获取公钥 -> 用公钥加密文件JavaSecurity.txt,输出到JavaSecurity2.txt
	 */
	public void testPKISimple1(){
		int kBufferSize = 8192;
		try{
			//读取数字证书
			InputStream inStream = new FileInputStream("D:\\workspace\\PKIJavaSecurity\\doc\\AUMProduct.cer");
			//创建X509工厂类
			CertificateFactory cf = CertificateFactory.getInstance("X.509");
			//创建证书对象
			X509Certificate oCert = (X509Certificate)cf.generateCertificate(inStream);
			inStream.close();
			
			//使用公鈅加密
			Cipher cipher=Cipher.getInstance("RSA/ECB/PKCS1Padding");
			cipher.init(Cipher.ENCRYPT_MODE,oCert.getPublicKey());//oCert.getPublicKey() 从证书获取公钥
			System.out.println("从证书获取的公钥："+oCert.getPublicKey());
			//读取原文和密文
			FileInputStream in = new FileInputStream("D:\\workspace\\PKIJavaSecurity\\doc\\JavaSecurity.txt");
			FileOutputStream fileOut = new FileOutputStream("D:\\workspace\\PKIJavaSecurity\\doc\\JavaSecurity2.txt");
			//使用Cipher对象输出文件
			CipherOutputStream out = new CipherOutputStream(fileOut,cipher);
			byte[] buffer = new byte[kBufferSize];
			int length;
			while((length = in.read(buffer))!= -1){
				out.write(buffer,0,length);
			}
			in.close();
			out.close();
			
		}catch(Exception ex){
			System.out.println("testPKISimple1:"+ex);
		}
	}
	
	
	/**
	 * 生成公钥/私钥 -> 用公钥加密 -> 用私钥解密
	 */
	public void bak_testPKISimple2(){
		String content = "Hello Cisco,it's PKI example.";
		System.out.println("++++加密前++++++content="+content);
		try{
			byte[] plainText=content.getBytes("UTF8");
			
			//构成一个密钥（需JDK1.5以上版本）
			KeyPairGenerator keyGen=KeyPairGenerator.getInstance("RSA"); //采用RSA算法
			keyGen.initialize(1024); //1024=最常用的长度；512=容易破解；2048=速度很慢；
			KeyPair key=keyGen.generateKeyPair();//一对密钥；key.getPrivate();key.getPublic();
			
			//使用公鈅加密
			Cipher cipher=Cipher.getInstance("RSA/ECB/PKCS1Padding");
			cipher.init(Cipher.ENCRYPT_MODE,key.getPublic());
			byte[] cipherText=cipher.doFinal(plainText);
			String content_encode = new String(cipherText,"UTF8"); //加密后的字符串
			System.out.println("++++加密后++++++content_encode="+content_encode);
			
			//使用私鈅解密
			cipher.init(Cipher.DECRYPT_MODE,key.getPrivate());
			byte[] newPlainText=cipher.doFinal(cipherText);
			String content_new = new String(newPlainText,"UTF8");
			System.out.println("++++解密后++++++content_new="+content_new);
			
		}catch(Exception ex){
			System.out.println("+++++Error="+ex);
		}
		
	}
	
	

	
}

package com.webex.pso.security;

import java.security.InvalidKeyException;
import java.security.KeyPair;
import java.security.KeyPairGenerator;
import java.security.NoSuchAlgorithmException;
import java.security.Signature;
import java.security.SignatureException;

import junit.framework.TestCase;


/**
 * 数据签名
 * 验证签名
 *
 */
public class TestSignature extends TestCase{
	
	public void testCase(){
		tcase();
	}
	
	public void tcase(){
		try{
			//待签名的数据
			String strToSign = "Hello Cisco!";
			byte[] plainText = strToSign.getBytes();
			//生成RSA密钥对
			System.out.println("开始生成RSA密钥对");
			//设置为RSA算法
			KeyPairGenerator keyGen = KeyPairGenerator.getInstance("RSA");
			//设置模长为1024位
			keyGen.initialize(1024);
			//产生密钥
			KeyPair key = keyGen.generateKeyPair();
			System.out.println("生成密钥成功！");
			//创建Signature,并签名
			//生成Signature实例，签名算法为:MD5WithRSA
			Signature sig = Signature.getInstance("MD5WithRSA");
			//使用私钥初始化签名对象，用于计算签名
			sig.initSign(key.getPrivate());
			//update数据
			sig.update(plainText);
			//计算签名，输出签名值
			byte[] signature = sig.sign();
			System.out.println("密码服务提供者信息："+sig.getProvider().getInfo());
			System.out.println("签名值：");
			printHex(signature,signature.length);
			//验证签名
			System.out.println("开始验证签名：");
			//使用公钥初始化对象，用于验证签名
			sig.initVerify(key.getPublic());
			//update数据
			sig.update(plainText);
			//验证签名
			if(sig.verify(signature)){
				System.out.println("验证签名成功。");
			}else{
				System.out.println("验证签名失败。");
			}
		}catch(NoSuchAlgorithmException se){
			System.out.println("Error:NoSuchAlgorithmException.");
		}catch(InvalidKeyException se){
			System.out.println("Error:InvalidKeyException.");
		}catch(SignatureException  se){
			System.out.println("Error:SignatureException.");
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

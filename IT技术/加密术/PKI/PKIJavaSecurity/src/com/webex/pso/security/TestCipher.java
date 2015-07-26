package com.webex.pso.security;

import java.security.Key;

import javax.crypto.Cipher;
import javax.crypto.KeyGenerator;

import junit.framework.TestCase;

/**
 * 对称密钥
 * 加密、解密
 */
public class TestCipher extends TestCase{
	
	public void testCipher(){
		cipher();
	}
	
	public static void cipher(){
		try{
			//待加密数据
			String str2Enc = "Hello Cisco!";
			byte[] plainText = str2Enc.getBytes();			
			//初始化DES算法
			KeyGenerator keyGen = KeyGenerator.getInstance("DES");
			//设置密钥长度，56bits
			keyGen.init(56);
			//生成密钥
			Key key = keyGen.generateKey();
			//打印出DES密钥
			byte[] keyencode = key.getEncoded();
			printHex(keyencode,keyencode.length);
			//生成Cipher对象，设置算法为ECB模式的DES算法，补位填充模式为PKCS5
			Cipher cipher = Cipher.getInstance("DES/ECB/PKCS5Padding");
			System.out.println("Cipher对象密码服务提供者信息:"+cipher.getProvider().getInfo());
			System.out.println("开始加密:");
			//cipher对象初始化，设置为加密
			cipher.init(Cipher.ENCRYPT_MODE, key);
			//结束数据加密，输出密文
			byte[] cipherText = cipher.doFinal(plainText);
			System.out.println("加密完成，密文为:");
			printHex(cipherText,cipherText.length);
			//使用相同的key解密数据
			System.out.println("开始解密:");
			cipher.init(Cipher.DECRYPT_MODE, key);
			byte[] newPlainText = cipher.doFinal(cipherText);
			System.out.println("解密完成，明文为:");
			System.out.println("明文="+new String(newPlainText,"UTF-8"));
		}catch(Exception ex){
			System.out.println("Error:"+ex);
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

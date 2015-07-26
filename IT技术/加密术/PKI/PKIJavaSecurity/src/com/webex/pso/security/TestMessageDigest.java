package com.webex.pso.security;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

import junit.framework.TestCase;


/**
 * 名称：消息摘要
 * 功能：实现数据防篡改功能
 * 原理：接受者接收到数据和摘要值后可以通过再次计算原始数据的摘要值与发送者的摘要值对比即可判断数据是否在传输中被篡改。
 */
public class TestMessageDigest extends TestCase{
	
	public void testMessageDigest(){
		digest();
	}
	
	public static void digest(){
		try{
			//声明MessageDigest类，并初始化为SH1算法
			MessageDigest md = MessageDigest.getInstance("SHA1");
			//待计算摘要的数据
			String msg2Digest = "Hello Cisco!";
			//处理摘要
			md.update(msg2Digest.getBytes());
			//计算出摘要值
			byte[] digest = md.digest();
			//打印摘要值
			printHex(digest,md.getDigestLength());
		}catch(NoSuchAlgorithmException ex){
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

package com.webex.pso.security;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.security.InvalidKeyException;
import java.security.Key;
import java.security.NoSuchAlgorithmException;
import java.security.SecureRandom;

import javax.crypto.Cipher;
import javax.crypto.CipherOutputStream;
import javax.crypto.KeyGenerator;
import javax.crypto.NoSuchPaddingException;

import junit.framework.TestCase;

/**
 * 开发实例：文件保险箱
 * 用对称算法对文件加密和解密 
 */
public class FileEncDec extends TestCase{
	public static final int kBufferSize = 8192;
	
	public void testFileEncDec(){
		String[] args = new   String[]{
				"e",                         //e=加密
				"pwd",                       //password
				"D:\\workspace\\PKIJavaSecurity\\doc\\JavaSecurity.txt",      //inputfile  原文
				"D:\\workspace\\PKIJavaSecurity\\doc\\JavaSecurity2.txt"      //outputfile 密文
				};   
		
//		String[] args = new   String[]{
//				"d",                         //d=解密
//				"pwd",                       //password
//				"D:\\workspace\\PKIJavaSecurity\\doc\\JavaSecurity2.txt",      //inputfile 密文
//				"D:\\workspace\\PKIJavaSecurity\\doc\\JavaSecurity3.txt"      //outputfile 恢复原文
//				};  
		
		try {
			Key key;
			//初始化DES算法的密钥对象
			KeyGenerator generator = KeyGenerator.getInstance("DES");
			//使用输入的口令初始化密钥
			generator.init(new SecureRandom(args[1].getBytes()));
			//产生密钥
			key = generator.generateKey();
			//创建Cipher对象，ECB模式的DES算法
			Cipher cipher = Cipher.getInstance("DES/ECB/PKCS5Padding");
			//判断是加密还是解密。设置cipher对象为加密或解密
			if(args[0].indexOf("e") != -1){
				cipher.init(Cipher.ENCRYPT_MODE, key);
			}else{
				cipher.init(Cipher.DECRYPT_MODE, key);
			}
			//读取原文和密文
			FileInputStream in = new FileInputStream(args[2]);
			FileOutputStream fileOut = new FileOutputStream(args[3]);
			//使用Cipher对象输出文件
			CipherOutputStream out = new CipherOutputStream(fileOut,cipher);
			byte[] buffer = new byte[kBufferSize];
			int length;
			while((length = in.read(buffer))!= -1){
				out.write(buffer,0,length);
			}
			in.close();
			out.close();
			if(args[0].indexOf("e") != -1){
				System.out.println("加密完成");
			}else{
				System.out.println("解密完成");
			}
				
		} catch (NoSuchAlgorithmException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (NoSuchPaddingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (InvalidKeyException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
	}
	
}

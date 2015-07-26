package com.webex.pso.security.safefile;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.security.Key;
import java.security.KeyStore;
import java.security.PrivateKey;
import java.security.Signature;
import java.security.cert.CertificateFactory;
import java.security.cert.X509Certificate;

import javax.crypto.Cipher;
import javax.crypto.KeyGenerator;

import org.bouncycastle.jce.provider.BouncyCastleProvider;

import junit.framework.TestCase;


/**
 *  ??????????????测试未通过，证书无法生成(CA提供证书)
 *  
	发送方流程：
	开始  -->
	打开keystore文件  -->
	产生随机会话密钥  -->
	使用接受者证书加密会话密钥  -->
	对原文件进行签名  -->
	使用会话密钥对文件加密  -->
	保存签名值、密文的会话密钥和密文到文件  -->
	结束。
 *
 */
public class Sender extends TestCase{
	
	public static final int kBufferSize = 8192;
	public static KeyStore oKs;
	public static String okeystorefile;
	public static String oAlias;
	public static String oPasswd;
	public static String oRecvcert;
	public static String oInputfile;
	public static String oOutputfile;
	
	public void testSender(){
		String[] args = new   String[]{
				"D:\\workspace\\PKIJavaSecurity\\doc\\sender.keystore",   //keystore文件
				"sender",          //别名
				"111111",       //keystorefile口令
				"D:\\workspace\\PKIJavaSecurity\\doc\\recver.cer",        //接受者的证书
				"D:\\workspace\\PKIJavaSecurity\\doc\\plain.txt",      //待传送的文件
				"D:\\workspace\\PKIJavaSecurity\\doc\\cipher.dat"      //输出的密文文件
				};  
		try{
			okeystorefile = args[0];
			oAlias = args[1];
			oPasswd = args[2];
			oRecvcert = args[3];
			oInputfile = args[4];
			oOutputfile = args[5];
			
			//打开发送者keystore文件
			oKs = KeyStore.getInstance("JKS");
			FileInputStream fis = new FileInputStream(okeystorefile);
			oKs.load(fis,oPasswd.toCharArray());
			fis.close();
			//产生随机会话密钥
			KeyGenerator keyGen = KeyGenerator.getInstance("DES");
			keyGen.init(56);
			Key key = keyGen.generateKey();
			byte[] keyencode = key.getEncoded();
			printHex(keyencode,keyencode.length);
			//读取接受者证书
			InputStream inStream = new FileInputStream(oRecvcert);
			CertificateFactory cf = CertificateFactory.getInstance("X.509");
			X509Certificate oCert = (X509Certificate)cf.generateCertificate(inStream);
			inStream.close();
			//使用接受者证书加密会话密钥
			//BouncyCastleProvider.java RSA 工具类,提供加密，解密，生成密钥对等方法;需要到http://www.bouncycastle.org下载bcprov-jdk15-144.jar。(JDK1.4以下版本未提供RSA的加密解密算法)
			Cipher cipherRsa = Cipher.getInstance("RSA/ECB/PKCS1Padding",new BouncyCastleProvider());
			cipherRsa.init(Cipher.ENCRYPT_MODE, oCert);
			byte[] cipherkey = cipherRsa.doFinal(keyencode);
			printHex(cipherkey,cipherkey.length);
			
			//对原文件进行签名
			Key oKey = oKs.getKey(oAlias,oPasswd.toCharArray());
			//创建签名对象
			Signature oSign = Signature.getInstance("SHA1withRSA");
			//初始化签名对象,参数为签名者私钥
			oSign.initSign((PrivateKey)oKey);
			byte[] signedBuf = null;
			byte[] buffer = new byte[kBufferSize];
			int len;
			System.out.println("Inputfile:"+oInputfile);
			FileInputStream fin = new FileInputStream(new File(oInputfile));
			while((len = fin.read(buffer)) != -1){
				oSign.update(buffer,0,len);
				fin.close();
			}
			//获得签名者
			signedBuf = oSign.sign();
			System.out.println("signedBuf:");
			printHex(signedBuf,signedBuf.length);
			
			//使用会话密钥对文件加密
			Cipher cipher = Cipher.getInstance("DES/ECB/PKCS5Padding");
			cipher.init(Cipher.ENCRYPT_MODE, key);
			fin = new FileInputStream(new File(oInputfile));
			//签名信息长度4Bytes|签名信息|会话密钥的密文长度4Bytes|会话密钥密文|原文数据的密文
			FileOutputStream fout = new FileOutputStream(new File(oOutputfile));
			byte[] blen = new byte[4];
			int datalen = signedBuf.length;
			blen[0] = (byte)(datalen & 0xff);
			blen[1] = (byte)((datalen >> 8) & 0xff);
			blen[2] = (byte)((datalen >> 16) & 0xff);
			blen[3] = (byte)(datalen >>> 24);
			//写入签名值长度到输出文体
			fout.write(blen);
			//写入签名值到输出文件
			fout.write(signedBuf);
			datalen = cipherkey.length;
			blen[0] = (byte)(datalen & 0xff);
			blen[1] = (byte)((datalen >> 8) & 0xff);
			blen[2] = (byte)((datalen >> 16) & 0xff);
			blen[3] = (byte)(datalen >>> 24);
			//写入密文会话密钥长度到输出文件
			fout.write(blen);
			//写入密文会话密钥到输出文件
			fout.write(cipherkey);
			byte[] cipherbuffer = null;
			//读取原文，加密并写密文到输出文件
			while((len = fin.read(buffer)) != -1){
				cipherbuffer = cipher.update(buffer,0,len);
				fout.write(cipherbuffer);
				fin.close();
			}
			cipherbuffer = cipher.doFinal();
			fout.write(cipherbuffer);
			fout.close();
		}catch(Exception ex){
			System.out.println("Sender error:"+ex);
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

===安全报文系统===
这是Java安全的综合应用，分为发送方和接受方两部分，
分别用到了KeyStore类、KeyGenerator类、X509Certificate类、Signature类等。

===发送方流程：===
开始  -->
打开keystore文件  -->
产生随机会话密钥  -->
使用接受者证书加密会话密钥  -->
对原文件进行签名  -->
使用会话密钥对文件加密  -->
保存签名值、密文的会话密钥和密文到文件  -->
结束。


===接受方流程：===
开始  -->
打开keystore文件，读取私钥  -->
打开密文文件，读取签名值、密文和会话密钥和密文  -->
使用接受者的私钥解密会话密钥  -->
利用会话密钥对密文解密  -->
验证签名 -->
结束。

===密钥和证书的生成方式===
1， 生成sender.keystore
%JAVA_HOME%/bin/keytool -genkey -alias sender -keyalg RSA -keystore sender.keystore

-----------------------------------
C:\>cd C:\Program Files\Java\jdk1.5.0_13
C:\Program Files\Java\jdk1.5.0_13>cd bin
C:\Program Files\Java\jdk1.5.0_13\bin>keytool -genkey -alias sender -keyalg RSA
-keystore sender.keystore
输入keystore密码：  111111
您的名字与姓氏是什么？
  [Unknown]：  vincent
您的组织单位名称是什么？
  [Unknown]：  cisco
您的组织名称是什么？
  [Unknown]：  cisco
您所在的城市或区域名称是什么？
  [Unknown]：  hangzhou
您所在的州或省份名称是什么？
  [Unknown]：  zhejiang
该单位的两字母国家代码是什么
  [Unknown]：  cn
CN=vincent, OU=cisco, O=cisco, L=hangzhou, ST=zhejiang, C=cn 正确吗？
  [否]：  y
输入<sender>的主密码
        （如果和 keystore 密码相同，按回车）：
C:\Program Files\Java\jdk1.5.0_13\bin>
-----------------------------------
在：C:\Program Files\Java\jdk1.5.0_13\bin下已生成sender.keystore	

2, 生成sender.cer
%JAVA_HOME%/bin/keytool -certreq -alias sender -file req.csr -keystore sender.keystore
 只能生成CSR文件，再发送CSR到CA，请求生成证书。
这里无法完成这一步。(?????????)

3,  导入证书到keystore
%JAVA_HOME%/bin/keytool -import -trustcacerts -alias sender -file sender.cer -keystore sender.keystore
这一步有错，有第二步有关联。(?????????)
-----------------------------------



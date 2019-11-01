
package com.market.hjapp;

import java.math.BigInteger;
import java.security.KeyFactory;
import java.security.KeyPair;
import java.security.KeyPairGenerator;
import java.security.SecureRandom;
import java.security.interfaces.RSAPrivateKey;
import java.security.interfaces.RSAPublicKey;
import java.security.spec.RSAPrivateKeySpec;
import java.security.spec.RSAPublicKeySpec;
import java.util.Calendar;

import javax.crypto.Cipher;
import javax.crypto.SecretKey;
import javax.crypto.SecretKeyFactory;
import javax.crypto.spec.DESKeySpec;




public class SecurityUtil {
    private static final String TAG = "SecurityUtil";
    
    // RSA 
    private static BigInteger  RSAmod;// = new BigInteger("b259d2d6e627a768c94be36164c2d9fc79d97aab9253140e5bf17751197731d6f7540d2509e7b9ffee0a70a6e26d56e92d2edd7f85aba85600b69089f35f6bdbf3c298e05842535d9f064e6b0391cb7d306e0a2d20c4dfb4e7b49a9640bdea26c10ad69c3f05007ce2513cee44cfe01998e62b6c3637d3fc0391079b26ee36d5", 16);
    private static BigInteger  RSApubExp;// = new BigInteger("11", 16);
    private static BigInteger  RSAprivExp;// = new BigInteger("92e08f83cc9920746989ca5034dcb384a094fb9c5a6288fcc4304424ab8f56388f72652d8fafc65a4b9020896f2cde297080f2a540e7b7ce5af0b3446e1258d1dd7f245cf54124b4c6e17da21b90a0ebd22605e6f45c9f136d7a13eaac1c0f7487de8bd6d924972408ebb58af71e76fd7b012a8d0e165f3ae2e5077a8648e619", 16);
    
//    private static BigInteger  RSAmod = new BigInteger("153400244902201255327025649247712075392482122006320291252701310857447882617707366311941475489631855114980790121090203910753924246190571894409773146908273251771112984362707682509615031664173679643452775818193013369202795463751003187532512848212012614635729858507630181976767562646363293565072310969559913791943");
//    private static BigInteger  RSApubExp = new BigInteger("65537");
//    private static BigInteger  RSAprivExp = new BigInteger("146113750824343670942128097542444399259964660941491643821942210186083077109537583878673644902341866788189279987166560705020110275786201076287222163397806550798955350352765927156473489080296241902430288870197651617563532347924061387350273794499879003822039725726836935830155947973311169627355758798481984876609");

    private static BigInteger  MidwareRSAmod = new BigInteger("150616503080716484417286435253531470759928392460439251646973765945367734510376246331029630864357865172420931184795230980438052534481836305171685868494443050571407072953658304059327470215904268910483533584669904424849673019298951265963582139780027277457825471334898320522203846834086363941296200118914028119781");
    private static BigInteger  MidwareRSApubExp = new BigInteger("65537");
    private static BigInteger  MidwareRSAprivExp = new BigInteger("116718194942709432749450328626899351595045291482495815697924174909858417879647808450402091379039975868772477105790551837946616019481041555621319410458630666026661115902956805732634414194723172905601477989225343497049698833996496161854410768250713419796459730285289875940788254257583653833633446809415503447845");

    private static BigInteger  MARKETRSAmod = new BigInteger("153400244902201255327025649247712075392482122006320291252701310857447882617707366311941475489631855114980790121090203910753924246190571894409773146908273251771112984362707682509615031664173679643452775818193013369202795463751003187532512848212012614635729858507630181976767562646363293565072310969559913791943");
    private static BigInteger  MARKETRSApubExp = new BigInteger("65537");
    
    private static RSAPublicKey publickRSAKey;
    private static RSAPrivateKey privateRSAKey;
    
    
    /*****************************************************************/
    /**                              RSA                            **/
    /*****************************************************************/
    public static void generatorRSAPairKey()
    {
    	try
        {
	    	KeyPairGenerator keyPairGenerator  =  KeyPairGenerator.getInstance("RSA");
	        keyPairGenerator.initialize(1024);// 初始化密钥生成器
	        KeyPair keyPair  =  keyPairGenerator.genKeyPair();
	        
	        publickRSAKey = (RSAPublicKey)keyPair.getPublic();//生成密匙组
	        privateRSAKey = (RSAPrivateKey)keyPair.getPrivate();
        }
	    catch(Exception e)
	    {
	    	MyLog.e(TAG, " generatorPairKey ", e);
	    	return;
	    }
    	
	    MyLog.d("RSA getModulus ", publickRSAKey.getModulus().toString());
	    MyLog.d("RSA publickKey ", publickRSAKey.getPublicExponent().toString());    	
    	MyLog.d("RSA privateKey ", privateRSAKey.getPrivateExponent().toString());
    }
    
    private static void generatorRSAKey(BigInteger  mod, BigInteger  pubExp, BigInteger  privExp)
    {
    	try
        {
    		// generate key factory
    		KeyFactory keyFac = null;
    		keyFac = KeyFactory.getInstance("RSA");
    		if (pubExp != null)
    		{
	    		// generate public key
	    		RSAPublicKeySpec pubKeySpec = new RSAPublicKeySpec(mod, pubExp);
	    		publickRSAKey = (RSAPublicKey) keyFac.generatePublic(pubKeySpec);
    		}
    		
    		if (privExp != null)
    		{
	    		// generate private key
	    		RSAPrivateKeySpec priKeySpec = new RSAPrivateKeySpec(mod, privExp);
	    		privateRSAKey = (RSAPrivateKey) keyFac.generatePrivate(priKeySpec);
    		}
        	
        }
	    catch(Exception e)
	    {
	    	MyLog.e(TAG, " generatorKey ", e);
	    	return;
	    }
	    
	    MyLog.d("RSA getModulus ", publickRSAKey.getModulus().toString());
	    
	    if (pubExp != null)
	    	MyLog.d("RSA publickKey ", publickRSAKey.getPublicExponent().toString());  
	    
	    if (privExp != null)
	    	MyLog.d("RSA privateKey ", privateRSAKey.getPrivateExponent().toString());
    	
    }
    
    private static String getRSAEncrypt(String sourceString)
    {
    	if (publickRSAKey == null)
    	{
    		generatorRSAKey(RSAmod, RSApubExp, RSAprivExp);    		
    	}
    		
    	try
        {
        	Cipher cipher  =  Cipher.getInstance("RSA");
            cipher.init(Cipher.ENCRYPT_MODE, publickRSAKey);
            byte [] publicKeyEncryptText  =  cipher.doFinal(sourceString.getBytes());
            
            String encryptTextString = (new BigInteger(1, publicKeyEncryptText)).toString();
            
            MyLog.d( "publicKeyEncryptText:", encryptTextString);
            
            return encryptTextString;
            
        }catch(Exception e)
        {
        	MyLog.e(TAG, " getRSAEncrypt ", e);
        }
    	
        return null;
    }
    
    private static String getRSADecrypt(String sourceString)
    {
    	if (privateRSAKey == null)
    	{
    		generatorRSAKey(RSAmod, RSApubExp, RSAprivExp);    		
    	}
    	
    	try
        {
        	Cipher cipher  =  Cipher.getInstance("RSA");
        	cipher.init(Cipher.DECRYPT_MODE, privateRSAKey);
        	byte [] privateKeyDecryptText  =  cipher.doFinal((new BigInteger(sourceString)).toByteArray());
            
        	String decryptTextString = new String(privateKeyDecryptText);
        	
            MyLog.d( " privateKeyDecryptText: ", decryptTextString);
            
            return decryptTextString;            

        }catch(Exception e)
        {
        	MyLog.e(TAG, " getRSADecrypt ", e);
        }
    	
        return null;
    }
    
    public static String getMidwareRSADecrypt(String sourceString)
    {
    	generatorRSAKey(MidwareRSAmod, MidwareRSApubExp, MidwareRSAprivExp);
    	
    	return getRSADecrypt(sourceString);
    }
    
//    public static String getMidwareRSAEncrypt(String sourceString)
//    {
//    	generatorRSAKey(MidwareRSAmod, MidwareRSApubExp, MidwareRSAprivExp);
//    	
//    	return getRSAEncrypt(sourceString);
//    }
    
    public static String getMARKETRSAEncrypt(String sourceString)
    {
    	generatorRSAKey(MARKETRSAmod, MARKETRSApubExp, null);
    	
    	return getRSAEncrypt(sourceString);
    }
    
    /*****************************************************************/
    /**                              DES                            **/
    /*****************************************************************/
    

    // DES
    private static String DESKey = "smartkey"; 
    private static String randomDESKey;
    private static String randomDESKeyPadding;
    
    public static String generatorDESRandomKey()
    {
    	try{
    		
    		randomDESKey = Calendar.getInstance().getTimeInMillis() + "";
    		randomDESKeyPadding = "|" + randomDESKey + "|"; // padding is just for RSA
    	
	    }catch(Exception e)
	    {
	    	MyLog.e(TAG, " generatorDESRandomKey ", e);
	    }
	    
	    return randomDESKeyPadding;
    }
    
    public static void setDESKey(String desKey)
    {
    	randomDESKey = desKey;
    }
    
    public static String getDESEncrypt(String sourceString)
    {
    	try
        { 
    		SecureRandom sr = new SecureRandom();
			DESKeySpec dks = new DESKeySpec(randomDESKey.getBytes());
			SecretKeyFactory keyFactory = SecretKeyFactory.getInstance("DES");
			SecretKey secretKey = keyFactory.generateSecret(dks);
			
			Cipher cipher = Cipher.getInstance("DES");
			cipher.init(Cipher.ENCRYPT_MODE, secretKey, sr);
			
			byte data[] = sourceString.getBytes();
			byte encryptedData[] = cipher.doFinal(data);
			
			String encryptedDataString;
			encryptedDataString = Base64.encodeBytes(encryptedData);
			
			MyLog.d( "encryptedDataString :", encryptedDataString);    
			
			return encryptedDataString;

        }catch(Exception e)
        {
        	MyLog.e(TAG, " getDESEncrypt ", e);
        }
    	
        return null;
    }
    
    public static String getDESDecrypt(String sourceString)
    {
    	try
        {
    		SecureRandom sr = new SecureRandom();
    		DESKeySpec dks =  new DESKeySpec(randomDESKey.getBytes());
    		SecretKeyFactory keyFactory = SecretKeyFactory.getInstance("DES");
    		SecretKey secretKey = keyFactory.generateSecret(dks);
    		
    		byte[] result = Base64.decode(sourceString);
    		
    		Cipher cipher = Cipher.getInstance("DES");
			cipher.init(Cipher.DECRYPT_MODE, secretKey, sr);
			byte decryptedData[] = cipher.doFinal(result);
			
			String decryptedDataString = new String(decryptedData);
			MyLog.d( "decryptedDataString :", decryptedDataString);
			 
			return decryptedDataString;
          

        }catch(Exception e)
        {
        	MyLog.e(TAG, " getDESDecrypt ", e);
        }
    	
        return null;
    }
    
    
    
    
    public static String test(String sourceString)
    {    	   	
    	try
        {
    		String KEY = "MarketSK";
    		SecureRandom sr = new SecureRandom();
			DESKeySpec dks = new DESKeySpec(KEY.getBytes());
			SecretKeyFactory keyFactory = SecretKeyFactory.getInstance("DES");
			SecretKey key = keyFactory.generateSecret(dks);
			
			Cipher cipher = Cipher.getInstance("DES");
			cipher.init(Cipher.ENCRYPT_MODE, key, sr);
			
			byte data[] = sourceString.getBytes();
			byte encryptedData[] = cipher.doFinal(data);
			
			String encryptedDataString;
			encryptedDataString = Base64.encodeBytes(encryptedData);
			
			MyLog.d( "encryptedDataString :", encryptedDataString);
			 
			byte[] result = Base64.decode("Market_test");
			
			sr = new SecureRandom();
			dks =  new DESKeySpec(KEY.getBytes());
			keyFactory = SecretKeyFactory.getInstance("DES");
			key = keyFactory.generateSecret(dks);
			cipher = Cipher.getInstance("DES");
			cipher.init(Cipher.DECRYPT_MODE, key, sr);
			byte decryptedData[] = cipher.doFinal(result);
			
			String decryptedDataString;
			decryptedDataString = new String(decryptedData);
			MyLog.d( "decryptedDataString :", decryptedDataString);
			 
			return encryptedDataString;

        }catch(Exception e)
        {
        	MyLog.e(TAG, " getDESDecrypt ", e);
        }
    	
        return null;
    }
    
    
    
}

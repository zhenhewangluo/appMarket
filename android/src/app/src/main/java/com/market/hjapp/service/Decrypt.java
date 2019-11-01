package com.market.hjapp.service;
//
//package com.market.hjapp.service;
//
//import java.io.UnsupportedEncodingException;
//import java.security.MessageDigest;
//import java.security.NoSuchAlgorithmException;
//
//public class Decrypt {
//	private byte key[];
//	private int  index;
//	
//	Decrypt(){
//		index = 0;
//	}
//	
//	public Decrypt(String imei){		
//		key = getMD5Str(imei).getBytes();
//		index = 0;
//	}
//	
//	void setKey(String key){
//		this.key = key.getBytes();
//		this.index = 0;
//	}
//	
//	public void decryptData(byte data[],int start ,int len){
//		for(int i = 0; i<len ; i++){
//			data[start+i] ^= this.key[index];
//			next();
//		}
//	}
//	
//	private void next(){
//		if(index<key.length-1){
//			index++;
//		}else{
//			index = 0;
//		}
//	}
//	
//	/** 
//     * MD5 加密 
//     *
//     */  
//    private  String getMD5Str(String str) {  
//        MessageDigest messageDigest = null;  
//  
//        try {
//            messageDigest = MessageDigest.getInstance("MD5");  
//  
//            messageDigest.reset();  
//  
//            messageDigest.update(str.getBytes("UTF-8"));  
//        } catch (NoSuchAlgorithmException e) {  
//            System.exit(-1);  
//        } catch (UnsupportedEncodingException e) {  
//            e.printStackTrace();  
//        }  
//  
//        byte[] byteArray = messageDigest.digest();  
//  
//        StringBuffer md5StrBuff = new StringBuffer();  
//  
//        for (int i = 0; i < byteArray.length; i++) {              
//            if (Integer.toHexString(0xFF & byteArray[i]).length() == 1)  
//                md5StrBuff.append("0").append(Integer.toHexString(0xFF & byteArray[i]));  
//            else  
//                md5StrBuff.append(Integer.toHexString(0xFF & byteArray[i]));  
//        }  
//  
//        return md5StrBuff.toString();  
//    }  
//    
//    
//    
//	
//}

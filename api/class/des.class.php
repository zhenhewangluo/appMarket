<?php
/**
* des ���ܽ����㷨 
*
* @author  	 bluesie
* @version   1.0
* @copyright ��ҵ���,������Ȩ����
* @link      http://***
* @create    2010-5-11
* @notice    ���ļ������й�
* @ʹ�ã�    
    $key = "SSOFOUNDER";  
    $input = "nihaodddd";  
    $crypt = new CookieCrypt($key);  
    echo "Encode:".$crypt->encrypt($input)."<br/>";  
    echo "Decode:".$crypt->decrypt($crypt->encrypt($input));  
*/
class CookieCrypt   
{     
	 var $key;  
	 function CookieCrypt($key)   
	 {         
	 $this->key = $key;  
	 }         
	 function encrypt($input)   
	 {         
		$size = mcrypt_get_block_size('des','ecb');  
		$input = $this->pkcs5_pad($input, $size);  
		$key = $this->key;  
		$td = mcrypt_module_open('des', '', 'ecb', '');  
		$iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);  
		@mcrypt_generic_init($td, $key, $iv);  
		$data = mcrypt_generic($td, $input);  
		mcrypt_generic_deinit($td);  
		mcrypt_module_close($td);  
		$data = base64_encode($data);  
		return $data;  
	 }         
	 function decrypt($encrypted)  
	 {         
		$encrypted = base64_decode($encrypted);
		$key =$this->key;  
		$td = mcrypt_module_open('des','','ecb','');   
		//ʹ��MCRYPT_DES�㷨,cbcģʽ  
		$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);  
		$ks = mcrypt_enc_get_key_size($td);  
		@mcrypt_generic_init($td, $key, $iv);  
		//��ʼ����                 
		$decrypted = mdecrypt_generic($td, $encrypted); 
		//����  
		mcrypt_generic_deinit($td);  
		//����               
		mcrypt_module_close($td);  
		$y=$this->pkcs5_unpad($decrypted);  
		return $y;     
	 }         
	 function pkcs5_pad ($text, $blocksize)   
	 {         
		$pad = $blocksize - (strlen($text) % $blocksize);  
		return $text . str_repeat(chr($pad), $pad);  
	 }     
	 function pkcs5_unpad($text)   
	 {         
		$pad = ord($text{strlen($text)-1});  
		if ($pad > strlen($text))  
		{  
			return false;  
		}  
		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)  
		{  
		   return false;  
		}  
		return substr($text, 0, -1 * $pad);  
	 }
}

?>

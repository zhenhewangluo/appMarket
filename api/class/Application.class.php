<?php 
/** 
* ���ܣ�ʵ����JSP,ASP��Application������ȫ�ֱ��� 
* author: [url]www.itzg.net[/url] 
* version: 1.0 
* ��Ȩ������ת���뱣����Ȩ���� 
*/ 
/*+----------------example---------------------- 
require_once("Application.php"); 

$arr = array(0=>"Hi",1=>"Yes"); 
$a = new Application(); 
$a->setValue("t1","arui"); 
$a->setValue("arr",$arr); 
$u = $a->getValue(); 
---------------------------------------------+*/ 
class Application 
{ 
/**���湲��������ļ�*/ 
var $save_file = 'Application/Application'; 
/**�������������*/ 
var $application = null; 
/**���л�֮�������*/ 
var $app_data = ''; 
/**�Ƿ��Ѿ�����setValue�Ĳ��� ��ֹƵ��д�ļ�����*/ 
var $__writed = false; 

/** 
* ���캯�� 
*/ 
function Application() 
{ 
$this->application = array(); 
} 
/** 
* ����ȫ�ֱ��� 
* @param string $var_name Ҫ���뵽ȫ�ֱ����ı����� 
* @param string $var_value ������ֵ 
*/ 
function setValue($var_name,$var_value) 
{ 
if (!is_string($var_name) || empty($var_name)) 
return false; 
if ($this->__writed) 
{ 
$this->application[$var_name] = $var_value; 
return; 
} 
$this->application = $this->getValue(); 
if (!is_array($this->application)) 
settype($this->application,"array"); 
$this->application[$var_name] = $var_value; 
$this->__writed = true; 
$this->app_data = @serialize($this->application); 
$this->__writeToFile(); 
} 
/** 
* ȡ�ñ�����ȫ�ֱ������ֵ 
* @return array 
*/ 
function getValue() 
{ 
if (!is_file($this->save_file)) 
$this->__writeToFile(); 
return unserialize(file_get_contents($this->save_file)); 
} 
/** 
* д���л�������ݵ��ļ� 
* @scope private 
*/ 
function __writeToFile() 
{ 
$fp = @fopen($this->save_file,"w"); 
@fwrite($fp,$this->app_data); 
@fclose($fp); 
} 
} 

?> 

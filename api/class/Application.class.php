<?php 
/** 
* 功能：实现像JSP,ASP里Application那样的全局变量 
* author: [url]www.itzg.net[/url] 
* version: 1.0 
* 版权：如许转载请保留版权声明 
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
/**保存共享变量的文件*/ 
var $save_file = 'Application/Application'; 
/**共享变量的名称*/ 
var $application = null; 
/**序列化之后的数据*/ 
var $app_data = ''; 
/**是否已经做过setValue的操作 防止频繁写文件操作*/ 
var $__writed = false; 

/** 
* 构造函数 
*/ 
function Application() 
{ 
$this->application = array(); 
} 
/** 
* 设置全局变量 
* @param string $var_name 要加入到全局变量的变量名 
* @param string $var_value 变量的值 
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
* 取得保存在全局变量里的值 
* @return array 
*/ 
function getValue() 
{ 
if (!is_file($this->save_file)) 
$this->__writeToFile(); 
return unserialize(file_get_contents($this->save_file)); 
} 
/** 
* 写序列化后的数据到文件 
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

<?php
 /*
		@Used      : 文本缓存处理类文件
		@copyright : xxxx8(http://www.xxxx8.com)
		@Authors   : lijinhou 
		@Write Time: 2010/02/11	
		-------------------------
		文本缓存说明：
	          文件名格式： cache.key.time
		默认缓存时间为3600秒
		-------------------------
		*************edit histroy********
		@lastmodify:
		
	*/
	class xxxx8cache{
		var $_carcheDir = "";
		var $_expireTime = "3600";
		
		function __construct(){
			//检测并删除过期缓存文件
			$cachefiles = glob($this->_carcheDir . 'cache.*');
			if($cachefiles){
				foreach ($cachefiles as $file) {
					$time = substr(strrchr($file, '.'), 1);
					if ($time+$this->_expireTime < time()) {
						if (file_exists($file)) {
							@unlink($file);
						}
	      			}
				}
			}
		}

		/**
	     * 方法，获取缓存内容
	     *
	     * @param string $key
	     * @return string $cache content
	     */
		function get($key, $times=""){
			if($times=="") $times=$this->_expireTime;
			$cachefiles = glob($this->_carcheDir . 'cache.' . $key . '.*');
			if($cachefiles){
				foreach($cachefiles as $file){
					$cache = "";
					$handle = fopen($file, 'r');
					if ($handle) {
						//
						$time = substr(strrchr($file, '.'), 1);
						if ($time+$times < time()) {
							if (file_exists($file)) {
								@system("chmod 777 $file");
								@unlink($file);
							}
							return "";
		      			}
		      			
						//
						$cache = fread($handle, filesize($file));		  
						fclose($handle);
					}
	      			return unserialize($cache);
				}
			}
		}
		
		/**
	     * 方法，设置缓存内容
	     *
	     * @param string $key
	     */
		function set($key, $value){
			$this->del($key);		
			$cachefiles = $this->_carcheDir . 'cache.' . $key . '.' . time();    	
			$handle = fopen($cachefiles, 'w');
    		fwrite($handle, serialize($value));		
    		fclose($handle);
		}
		/**
	     * 方法，删除某key值对应的缓存
	     *
	     * @param string $key
	     */
		function del($key){
			$cachefiles = glob($this->_carcheDir . 'cache.' . $key . '.*');
			if($cachefiles){
				foreach($cachefiles as $file){
					if (file_exists($file)) {
						@unlink($file);
						clearstatcache();
					}
				}
			}
		}
		
	}
?>
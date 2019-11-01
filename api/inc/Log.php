<?php
/************************************************************************
 *	Log Module for BajieV2
 *
 *  1. 单体模式,在页面多次调用，只生成一个实例
 *  2. 一个页面多条LOG, 也只在页面退出前写入文件,将对资源的独占时间最小化
 *       也保证了数据的完整性.
 *  3. 支持LOG分级,可设置LOG最低级别过滤LOG
 *  4. 自动记录页面处理时间,可用于观察页面压力
 *  5. 兼容已有的错误处理机制和新需求中对LOG的要求
 *  6. 根据日期自动新建日志文件,可设置每日最大SIZE，超过此SIZE，自动备份文件
 *  7. 日志结构适合于脚本拆分，具有扩展性
 *
 *	@AUTHOR lixiaan
 *  @CREATE DATE: 2010-04-24 FOR Bajiev2
 *
 ***********************************************************************/
class Bajie_Log
{
    /**
     * 
     *
     * @var string
     */
    var $_log = '';

    /**
     * 
     *
     * @var string
     */
    var $dateFormat = 'Y-m-d H:i:s';

    /**
     * 日志目录
     *
     * @var string
     */
    var $_logFileDir;

    /**
     * 日志文件名
     *
     * @var string
     */
    var $_logFilename;

    /**
     * 开关
     *
     * @var boolean
     */
    var $_enabled = true;

    /**
     * 日志的等级列表
     *
     * @var array
     */
    var $_errorLevel;

	/**
	 * 允许写入文件的日志最低级别
	 */
	var $_errorMinLevel;


    /**
     * Bajie_Log构造函数
     *
     * @return Bajie_Log
     */
    function Bajie_Log()
    {
		global $AM_LOGGING_INFO;

        $dir = $AM_LOGGING_INFO["LOG_DIR"];
        $dir = realpath($dir);
        if (!is_dir($dir) || !is_writable($dir)) {
            $this->_enabled = false;
        } else {
			//得到日志的全路径文件名
            $this->_logFileDir = $dir;        
			$this->_logFilename = $this->_logFileDir . DIRECTORY_SEPARATOR . $AM_LOGGING_INFO["LOG_FILE_NAME"];

			//若使用“每日日志”模式，则使用日期拼凑成文件名
			if($AM_LOGGING_INFO["LOG_USE_DAILY_FILE"]){
                $pathinfo = pathinfo($this->_logFilename);
                $this->_logFilename = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . basename($pathinfo['basename'], '.' . $pathinfo['extension']) . date('_Ymd') . '.' . $pathinfo['extension'];
			}
            $errorLevel = explode(',', strtolower($AM_LOGGING_INFO["LOG_LEVELS_SHORT"]));
            $errorLevel = array_map('trim', $errorLevel);
            $errorLevel = array_filter($errorLevel, 'trim');
            $this->_errorLevel = array();
			$i = 1;
            foreach ($errorLevel as $e) {
               $this->_errorLevel[$e] = $i;
			   if($e == strtolower(trim($AM_LOGGING_INFO["LOG_ALLOW_MIN_LEVEL"]))){
					$this->_errorMinLevel = $i;
			   }
			   $i += 1;
            }        
			
			//自动记录记此条日志的页面
			if (isset($_SERVER['REQUEST_URI'])) {

				global $___bajiephp_loaded_time;
	            list($usec, $sec) = explode(" ", $___bajiephp_loaded_time);
		         $begin_log = sprintf("=== [%s %s] page start:%s ===",  date($this->dateFormat, $sec), $usec,$_SERVER['REQUEST_URI']);
				$this->appendLog($begin_log, "D");           
			}

            //只有页面关闭时，内存中的日志才写入文件
            register_shutdown_function(array(& $this, '__writeLog'));

            //若当期日志文件已经超过配置文件中指定的最大限制，自动备份
            if (file_exists($this->_logFilename)) {
                $filesize = filesize($this->_logFilename);
            } else {
                $filesize = 0;
            }
            $maxsize = (int)$AM_LOGGING_INFO["LOG_MAX_SIZE"];
            if ($maxsize >= 512) {
                $maxsize = $maxsize * 1024;
                if ($filesize >= $maxsize) {
                    // 以当前时间组成备份文件的文件名称
                    $pathinfo = pathinfo($this->_logFilename);
                    $newFilename = $pathinfo['dirname'] . DIRECTORY_SEPARATOR .
                        basename($pathinfo['basename'], '.' . $pathinfo['extension']) .
                        date('-Ymd-His') . '.' . $pathinfo['extension'];
                    rename($this->_logFilename, $newFilename);
                }
            }
        }
    }

    /**
     * 方法，把欲写入的日志记录放入缓存
     *
     * @param string $msg
     * @param string $level
     */
    function appendLog($msg, $level)
    {			
        if (!$this->_enabled) { return; }
        $level = strtolower($level);
        if (!isset($this->_errorLevel[$level])) { return; }
		if ($this->_errorLevel[$level] < $this->_errorMinLevel) {
			return;
		}
		$current_page = '';
		if(isset($_SERVER['REQUEST_URI'])){
			$current_page = array_pop(explode('/', $_SERVER['REQUEST_URI']));
		}
        $msg  = sprintf("[%s] [%s] [%s] %s", date($this->dateFormat), $level, $current_page, $msg);
		$msg .= AM_SYSTEM_CTRF;
        $this->_log .= $msg;
    }

    /**
     * 将缓存日志记录（多条）写入目标文件
     */
    function __writeLog()
    {

        global $___bajiephp_loaded_time;

        // 计算页面LOAD时间
        list($usec, $sec) = explode(" ", $___bajiephp_loaded_time);
        $beginTime = (float)$sec + (float)$usec;
        $endTime = microtime();
        list($usec, $sec) = explode(" ", $endTime);
        $endTime = (float)$sec + (float)$usec;
        $elapsedTime = $endTime - $beginTime;
        $end_log = sprintf("=== [%s %s] page end (elapsed: %f seconds) ===",
            date($this->dateFormat, $sec), $usec, $elapsedTime);
		$this->appendLog($end_log, "D");

		//真正的I/O操作
        $fp = fopen($this->_logFilename, 'a');
        if (!$fp) {
			return; 
		}
        flock($fp, LOCK_EX);
        fwrite($fp, str_replace("\r", '', $this->_log));
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}

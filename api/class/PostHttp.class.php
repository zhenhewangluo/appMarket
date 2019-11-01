<?php
/*
---------------------------------------------------------------------
- ��Ŀ: DNS phpsea
- �汾: 1.6
- �ļ���:PostHttp.class.php
- ԭ����:Tiago Serafim
- ������:indraw(wangyzh@dns.com.cn)
- ��д����:2005/02/18
- ��Ҫ����:����get��post���������ݷ��͵�http������
- ���л���:php4������
- �޸ļ�¼:2004/12/18,indraw,������
- �޸ļ�¼:2005/05/08,indraw,���addfields�����Լ�����debug����
---------------------------------------------------------------------
*/

/*
	$postData[test1] = "hehe";
	$postData[test2] = "haha";
	$postURL = "testPost.php";
	
	$http = new PostHttp();
	$http->clearFields();
	foreach ($postData as $key => $val)
	{
		$http->addField($key, $val);
	}
	$http->postPage($postURL);
	$strPostResult = $http->getContent();
*/

/*
	setReferer($sRef)          //������Դurl
	addField($sName,           //$sValue)  ����һ��post����
	clearFields()              //������е�post����
	checkCookies()             //���cookie
	setCookies($sName,         //$sValue)  ����cookie����
	getCookies($sName)         //��ȡcookie��Ϣ
	clearCookies()             //���cookie
	getContent()               //��ȡpost��ķ�����Ϣ
	getHeaders()               //��ȡheader��Ϣ
	getHeader($sName)          //��ȡheader��Ϣ
	postPage($sURL)            //ִ��post����
	getPage($sURL)             //ִ��get����
	parseRequest($sURL)        //����url��ַ
	HTMLEncode($sHTML)         //html����
	downloadData($host, $port, $httpHeader)  //ץȡget��post����Ϣ
*/

//-------------------------------------------------------------------
class PostHttp
{
	var $show_errors    = true;       //�Ƿ�error
	var $show_debug     = false;      //�Ƿ�debug
	var $save_debug     = false;       //�Ƿ�debug

	var $referer;
	var $postStr;
	var $retStr;
	var $theData;
	var $theCookies;

	/*
	-----------------------------------------------------------
	��������:PostHttp()
	��Ҫ����:���캯��
	����:void
	���:void
	�޸���־:------
	-----------------------------------------------------------
	*/
	function PostHttp()
	{
	}
	/*
	-----------------------------------------------------------
	��������:setReferer($sRef)
	��Ҫ����:������Դurl
	����:string
	���:void
	�޸���־:
	-----------------------------------------------------------
	*/
	function setReferer($sRef)
	{
		$this->referer = $sRef;
	}
	/*
	-----------------------------------------------------------
	��������:addField($sName, $sValue)
	��Ҫ����:����һ��post����
	����:mixed ��������������ֵ��
	���:void
	�޸���־:
	-----------------------------------------------------------
	*/
	function addField($sName, $sValue)
	{
		$this->postStr .= $sName . "=" . $this->HTMLEncode($sValue) . "&";
	}

	/*
	-----------------------------------------------------------
	��������:addFields($sValue)
	��Ҫ����:����post����
	����:mixed ������ֵ��
	���:void
	�޸���־:
	-----------------------------------------------------------
	*/
	function addFields($sValue)
	{
		$this->postStr = $sValue;
	}

	/*
	-----------------------------------------------------------
	��������:clearFields()
	��Ҫ����:������е�post����
	����:void
	���:void
	�޸���־:
	-----------------------------------------------------------
	*/
	function clearFields()
	{
		$this->postStr = "";
	}
	/*
	-----------------------------------------------------------
	��������:checkCookies()
	��Ҫ����:���cookie
	����:void
	���:void
	�޸���־:
	-----------------------------------------------------------
	*/
	function checkCookies()
	{
		$cookies = explode("Set-Cookie:", $this->theData);
		$i = 0;
		if (count($cookies)-1 > 0)
		{
			while (list($foo, $theCookie) = each($cookies))
			{
				if (!($i == 0))
				{
					@list($theCookie, $foo) = explode(";", $theCookie);
					list($cookieName, $cookieValue) = explode("=", $theCookie);
					@list($cookieValue, $foo) = explode("\r\n", $cookieValue);
					$this->setCookies(trim($cookieName), trim($cookieValue));
				}
				$i++;
			}
		}

	}
	/*
	-----------------------------------------------------------
	��������:setCookies($sName, $sValue)
	��Ҫ����:����cookie����
	����:mixed ��������������ֵ��
	���:void
	�޸���־:
	-----------------------------------------------------------
	*/
	function setCookies($sName, $sValue)
	{
		$total = count(explode($sName, $this->theCookies));
		if ($total > 1)
		{
			list($foo, $cValue) = explode($sName, $this->theCookies);
			list($cValue, $foo) = explode(";", $cValue);
			$this->theCookies = str_replace($sName . $cValue . ";", "", $this->theCookies);
		}
		$this->theCookies .= $sName . "=" . $this->HTMLEncode($sValue) . ";";
	}
	/*
	-----------------------------------------------------------
	��������:getCookies($sName)
	��Ҫ����:��ȡcookie��Ϣ
	����:string ����������
	���:string
	�޸���־:
	-----------------------------------------------------------
	*/
	function getCookies($sName)
	{
		list($foo, $cValue) = explode($sName, $this->theCookies);
		list($cValue, $foo) = explode(";", $cValue);
		return substr($cValue, 1);
	}
	/*
	-----------------------------------------------------------
	��������:clearCookies()
	��Ҫ����:���cookie
	����:void
	���:void
	�޸���־:
	-----------------------------------------------------------
	*/
	function clearCookies()
	{
		$this->theCookies = "";
	}
	/*
	-----------------------------------------------------------
	��������:getContent()
	��Ҫ����:��ȡpost��ķ�����Ϣ
	����:void
	���:
	�޸���־:
	-----------------------------------------------------------
	*/
	function getContent()
	{
		if( !$this->theData )
		{
			$this->print_error("PostHttp::getContent: ���ܻ�ȡpost�ķ������");
			Return false;
		}
		list($header, $foo) = explode("\r\n\r\n", $this->theData);
		list($foo, $content) = explode($header, $this->theData);
		return substr($content, 4);
	}
	/*
	-----------------------------------------------------------
	��������:getHeaders()
	��Ҫ����:��ȡheader��Ϣ
	����:void
	���:
	�޸���־:
	-----------------------------------------------------------
	*/
	function getHeaders()
	{
		list($header, $foo) = explode("\r\n\r\n", $this->theData);
		list($foo, $content) = explode($header, $this->theData);
		return $header;
	}
	/*
	-----------------------------------------------------------
	��������:getHeader($sName)
	��Ҫ����:��ȡheader��Ϣ
	����:void
	���:
	�޸���־:
	-----------------------------------------------------------
	*/
	function getHeader($sName)
	{
		list($foo, $part1) = explode($sName . ":", $this->theData);
		list($sVal, $foo) = explode("\r\n", $part1);
		return trim($sVal);
	}
	/*
	-----------------------------------------------------------
	��������:postPage($sURL)
	��Ҫ����:ִ��post����
	����:string
	���:void
	�޸���־:
	-----------------------------------------------------------
	*/

	function postPage($sURL)
	{
		$sInfo = $this->parseRequest($sURL);
		$request = $sInfo['request'];
		$host = $sInfo['host'];
		$port = $sInfo['port'];

		$this->postStr = substr($this->postStr, 0, -1); //retira a ultima &

		$httpHeader = "POST $request HTTP/1.0\r\n";
		$httpHeader .= "Host: $host\r\n";
		$httpHeader .= "Connection: Close\r\n";
		$httpHeader .= "User-Agent: PostHttp/1.5 (phpsea1.5 by dns.com.cn)\r\n";
		$httpHeader .= "Content-type: application/x-www-form-urlencoded\r\n";
		$httpHeader .= "Content-length: " . strlen($this->postStr) . "\r\n";
		$httpHeader .= "Referer: " . $this->referer . "\r\n";

		$httpHeader .= "Cookie: " . $this->theCookies . "\r\n";

		$httpHeader .= "\r\n";
		$httpHeader .= $this->postStr;
		$httpHeader .= "\r\n\r\n";

		$this->theData = $this->downloadData($host, $port, $httpHeader); // envia os dados para o servidor

		$this->checkCookies();

		//����¼
		$this->sSend = $httpHeader;
		$this->sReceive = $this->theData;
		$this->post_log();
	}
	/*
	-----------------------------------------------------------
	��������:getPage($sURL)
	��Ҫ����:ִ��get����
	����:string
	���:void
	�޸���־:
	-----------------------------------------------------------
	*/
	function getPage($sURL)
	{
		$sInfo = $this->parseRequest($sURL);
		$request = $sInfo['request'];
		$host = $sInfo['host'];
		$port = $sInfo['port'];

		$httpHeader = "GET $request HTTP/1.1\r\n";
		$httpHeader .= "Host: $host\r\n";
		$httpHeader .= "Connection: Close\r\n";
		$httpHeader .= "User-Agent: PostHttp/1.5 (phpsea1.5 by dns.com.cn)\r\n";
		$httpHeader .= "Referer: " . $this->referer . "\r\n";

		$httpHeader .= "Cookie: " . substr($this->theCookies, 0, -1) . "\r\n";

		$httpHeader .= "\r\n\r\n";

		$this->theData = $this->downloadData($host, $port, $httpHeader); // envia os dados para o servidor
	}
	/*
	-----------------------------------------------------------
	��������:parseRequest($sURL)
	��Ҫ����:����url��ַ
	����:string
	���:array
	�޸���־:
	-----------------------------------------------------------
	*/
	function parseRequest($sURL)
	{
		$this->sLogURL = $sURL;
		if( !eregi("^http://",$sURL) and !eregi("^https://",$sURL))
		{
			$this->print_error("PostHttp::parseRequest: Զ��URL��ַ������Ҫ��: ".$sURL);
			Return false;
		}
		$this->print_debug("PostHttp::parseRequest: ��ʼ����Զ��URL:".$sURL);

		list($protocol, $sURL) = explode('://', $sURL); // separa o resto
		list($host, $foo) = explode('/', $sURL);        // pega o host
		list($foo, $request) = explode($host, $sURL);   // pega o request
		@list($host, $port) = explode(':', $host);      // pega a porta

		if (strlen($request) == 0)
			$request = "/";
		if (strlen($port) == 0)
			$port = "80";

		$sInfo = Array();
		$sInfo["host"] = $host;
		$sInfo["port"] = $port;
		$sInfo["protocol"] = $protocol;
		$sInfo["request"] = $request;

		return $sInfo;
	}
	/*
	-----------------------------------------------------------
	��������:HTMLEncode($sHTML)
	��Ҫ����:html����
	����:string
	���:string
	�޸���־:
	-----------------------------------------------------------
	*/
	/* changed 06/30/2003 */
	function HTMLEncode($sHTML)
	{
		$sHTML = urlencode($sHTML);

		return $sHTML;
	}
	/*
	-----------------------------------------------------------
	��������:downloadData($host, $port, $httpHeader)
	��Ҫ����:ץȡget��post����Ϣ
	����:mixed
	���:string
	�޸���־:
	-----------------------------------------------------------
	*/
	function downloadData($host, $port, $httpHeader)
	{
		$fp = @fsockopen($host, $port);
		$retStr = "";
		if ($fp)
		{
			$this->print_debug("PostHttp::downloadData: �ɹ�����Զ��HOST:".$host);
			if( !@fwrite($fp, $httpHeader) )
			{
				$this->print_error("PostHttp::downloadData: ������Զ��URL��������");
				Return false;
			}
			$this->print_debug("PostHttp::downloadData: �ɹ���Զ��URL��������",$httpHeader);

			while (!feof($fp))
			{
				$retStr .= fread($fp, 1024);
				//break;
			}
			$this->print_debug("PostHttp::downloadData: �ɹ���Զ��URL��������",$retStr);
			fclose($fp);
		}
		else
		{
			$this->print_error("PostHttp::downloadData: ��������Զ��HOST:".$host);
			Return false;
		}
		return $retStr;
	}

	/*
	-----------------------------------------------------------
	��������:print_error($str = "")
	��Ҫ����:��ʾ����������Ϣ
	����:string 
	���:echo or false
	�޸���־:------
	-----------------------------------------------------------
	*/
	function print_error($str = "")
	{
		//����ȫ�ֱ���$PHPSEA_ERROR..
		global $PHPSEA_ERROR;
		$PHPSEA_ERROR['PostHttp_Error'] = $str;
		//�ж��Ƿ���ʾerror���..
		if ( $this->show_errors )
		{
			print "<blockquote><font face=arial size=2 color=ff0000>\n";
			print "<b>PostHttp Error --</b>\n";
			print "[<font color=000077>$str</font>]\n";
			print "</font></blockquote>\n";
		}
		else
		{
			return false;
		}
	}//end func

	/*
	-----------------------------------------------------------
	��������:print_debug($str = "")
	��Ҫ����:��ʾ������Ϣshow_debug
	����:string 
	���:echo
	�޸���־:------
	-----------------------------------------------------------
	*/
	function print_debug($str = "",$code = "")
	{
		//�ж��Ƿ���ʾdebug���...
		if ( $this->show_debug )
		{
			print "<blockquote><font face=arial size=2 color=green>\n";
			print "<b>PostHttp Debug --</b>\n";
			print "[<font color=000077>$str</font>]<br>\n";
			if( $code ){
				echo("<table cellpadding=5 cellspacing=1 bgcolor=555555><tr bgcolor=eeeeee><td nowrap valign=bottom>");
				$sHighString = highlight_string($code,TRUE);
				echo($sHighString);
				echo("</td></tr></table>");
			}
			print "</font></blockquote>\n";
		}
	}//end func

	/*
	-----------------------------------------------------------
	��������:post_log()
	��Ҫ����:��¼post���
	����:void
	���:void 
	�޸���־:------
	-----------------------------------------------------------
	*/
	function post_log()
	{
		global $LibSet;
		if(!$this->save_debug) {
			return true;
		}
		if(!$this->referer)
		{
			$this->referer = $this->devGetUrl();
		}
		$sMonth = date("Ym",time());
		$sTodayDir = $LibSet['LogDir']."Post/".$sMonth;
		$sToday = "post".date("Y-m-d",time());
		$sFileName = $sTodayDir."/".$sToday.".xml";
		if( !file_exists($sTodayDir))
		{
			@mkdir($sTodayDir, 0777);
		}
		//���ļ�
		//$sFileName = $this->save_dir.$sToday.".xml";
		if( !@file_exists($sFileName))
		{
			$handle = @fopen ($sFileName,"w");
		}
		else
		{
			$handle = @fopen ($sFileName,"a");
			
		}
		//д������
		$sTime = date("H:i:s",time());
		$aContent[] = "-------------------time:".$sTime."-------------------------------------\n\r";
		$aContent[] = "server:".$this->sLogURL."\n\r";
		$aContent[] = "local:".$this->referer."\n\r";
		$aContent[] = "------\n\r";
		$aContent[] = $this->postStr;
		$aContent[] = "\n\r------\n\r";
		$aContent[] = $this->theData;
		$aContent[] = "\n\r--------------------------------------------------------------------\n\r";
		$sContent = join("",$aContent);
		@fwrite($handle, $sContent."\n\r");
		//�ر��ļ�
		@fclose($handle);
	}

	/**
	* ��ȡ��ǰurl
	* 
	* @author ������ <wangyzh@dns.com.cn>
	* @return boolean
	*/
	function devGetUrl()
	{
		$server = substr($_ENV["OS"], 0, 3);
		//iis
		if($server == 'Win')
		{
			$protocol = ($_SERVER['HTTPS'] == 'off') ? ('http://') : ('https://');
			$query = ($_SERVER['QUERY_STRING']) ? ('?'.$_SERVER['QUERY_STRING']) : ('');
			$url = $protocol.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].$query;
		}
		//apache
		elseif($_SERVER['SCRIPT_URI'])
		{
			$query = ($_SERVER['QUERY_STRING']) ? ('?'.$_SERVER['QUERY_STRING']) : ('');
			$url = $_SERVER['SCRIPT_URI'].$query;
		}
		//other
		else
		{
			$url = $_SERVER['REQUEST_URI'];
		}
		return $url;
	}

} // class

//-------------------------------------------------------------------
?>
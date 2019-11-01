<?php
class SystemAction extends BaseAction
{
	private $_layout = 'Layout:layout';
	private $_filePathArr = array(
//		'keywords'	=> '/flash/tagCloud/tagcloud.xml',
		'keywords'	=> '/Public/flash/tagCloud/tagcloud.xml',
		'site_conf'	=> './AppStore/Data/site_config.php'
	);
	/**
	 * 初始化系统相关信息
	 * @method	_initialize
	 * @access	protected
	 * @param	void
	 * @return	void
	 * @author	xuhao
	 * @date	2012-6-25
	 */
	protected function _initialize()
	{
		$this->_filePathArr['keywords'] = ROOT_PATH . $this->_filePathArr['keywords'];
		parent::_initialize();
	}
	/**
	 * 展示网站设置页面
	 * @method	baseConf
	 * @access	public
	 * @param	void
	 * @return	void
	 * @author	xuhao
	 * @date	2012-6-25
	 */
	public function baseConf()
	{
		if(file_exists($this->_filePathArr['site_conf']))
		{
			$siteConfContent = include($this->_filePathArr['site_conf']);
			$this->assign('siteConfContent', $siteConfContent);
		}
		$tmpXml = file_get_contents($this->_filePathArr['keywords']);

		if(file_exists($this->_filePathArr['keywords']))
		{
			$tmpXml = file_get_contents($this->_filePathArr['keywords']);
			$parser = xml_parser_create();
			$dataXml = array();
			$values = array();
			xml_parse_into_struct($parser, $tmpXml, $values);
			xml_parser_free($parser);
			foreach($values as $val)
			{
				if($val['tag'] == 'a' || $val['tag'] == 'A')
				{
					$dataXml[] = $val;
				}
			}
//			var_dump($dataXml);
//			die;
//			$keywordsContent = array();
//			$tmpXml = simplexml_load_file($keywordsFileName);
//			foreach($tmpXml->a as $k => $v)
//			{
//				$keywordsContent[$k]['keywordsName'] = $v[0];
//				foreach($v->attributes() as $key => $val)
//				{
//					$keywordsContent[$k][$key] = $val[0];
//				}
//			}
//			echo '<pre>';
//			die(print_r($keywordsContent));
			$this->assign('keywordsXml', $dataXml);
		}

		$this->assign('viewcontent', MODULE_NAME . ':baseConf');
		$this->display($this->_layout);
	}
	/**
	 * 网站首页图片轮显更新
	 * @method	slidesUpdate
	 * @access	public
	 * @param	void
	 * @return	void
	 * @author	xuhao
	 * @date	2012-6-25
	 */
	public function slidesUpdate()
	{
		$slideName = Filter::act(Req::get('slide_name'));
		$slideUrl = Filter::act(Req::get('slide_url'));
		$slideImg = Filter::act(Req::get('slide_img'));

		import("ORG.Net.UploadFile");
		$upload = new UploadFile();
		//设置上传文件大小,50M 测试！
		$upload->maxSize = 50 * 1049000; //50M 测试！
		//设置上传文件类型
		$upload->allowExts = explode(',', 'jpeg,jpg,gif,png');
		//设置附件上传目录
		$upload->savePath = ROOT_PATH . '/Public/ad/index/';
		//设置需要生成缩略图，仅对图像文件有效
		$upload->thumb = false;
		$uploadList = array();
		if (!$upload->upload())
		{
			//捕获上传异常
			$uploadList = $upload->getUploadFileInfo();
			if(!empty($uploadList))
			$this->error($upload->getErrorMsg());
		}
		else
		{
			//取得成功上传的文件信息
			$uploadList = $upload->getUploadFileInfo();
		}


		$slideArr = array();
		foreach($slideName as $k => $v)
		{
			$slideArr[$k] = array(
				'name'	=> $v,
				'url'	=> urlencode($slideUrl[$k]),
				'img'	=> $slideImg[$k]
			);
			if(!empty($uploadList))
			{
				foreach($uploadList as $val)
				{
					if ($val['key'] == $k)
					{
						$slideArr[$k]['img'] = '/ad/index/' . $val['savename'];
					}
				}
			}
		}
		$slideStrArr = array(
			'slides'	=> serialize($slideArr)
		);
		$this->saveConf($slideStrArr);
	}
	/**
	 * 网站搜索关键词更新
	 * @method	baseConf
	 * @access	public
	 * @param	void
	 * @return	void
	 * @author	xuhao
	 * @date	2012-6-25
	 */
	public function keywordsUpdate()
	{
		$keyWordsName = Filter::act(Req::get('keyWordsName'));
		$keyWordsClass = Filter::act(Req::get('keyWordsClass'));
		$keyWordsStyle = Filter::act(Req::get('keyWordsStyle'));
		$keyWordsColor = Filter::act(Req::get('keyWordsColor'));
		$keyWordsHiColor = Filter::act(Req::get('keyWordsHiColor'));

		$xml = "<tags>\n";
		foreach($keyWordsName as $k => $v)
		{
			$keyWordsStyleStr = '';
			$keyWordsColorStr = '';
			$keyWordsHiColorStr = '';
			$keyWordUrl = '/App/Search/index/key_word/' . urlencode($keyWordsName[$k]);
			if($keyWordsStyle[$k] != '')
			{
				$keyWordsStyleStr = "style=\"{$keyWordsStyle[$k]}\"";
			}
			if($keyWordsColor[$k] != '')
			{
				$keyWordsColorStr = "color=\"{$keyWordsColor[$k]}\"";
			}
			if($keyWordsHiColor[$k] != '')
			{
				$keyWordsHiColorStr = "hicolor=\"{$keyWordsHiColor[$k]}\"";
			}
			$xml .= "<a href=\"{$keyWordUrl}\" class=\"{$keyWordsClass[$k]}\" title=\"{$v}\" rel=\"tag\" {$keyWordsStyleStr} {$keyWordsColorStr} {$keyWordsHiColorStr}>{$v}</a>\n";
		}
		$xml .= '</tags>';

		$this->saveFile($xml, $this->_filePathArr['keywords']);
	}
	/**
	 * 网站友情链接更新
	 * @method	linksUpdate
	 * @access	public
	 * @param	void
	 * @return	void
	 * @author	xuhao
	 * @date	2012-6-25
	 */
	public function linksUpdate()
	{
		$linkName = Filter::act(Req::get('linkName'));
		$linkUrl = Filter::act(Req::get('linkUrl'));
		$linkArr = array(
			'links'	=> array()
		);

		foreach($linkName as $k => $v)
		{
			$linkArr['links'][$k] = array(
				'name'	=> $v,
				'url'	=> $linkUrl[$k]
			);
		}
		$this->saveConf($linkArr);
	}
	/**
	 * 网站友情链接更新
	 * @method	linksUpdate
	 * @access	public
	 * @param	void
	 * @return	void
	 * @author	xuhao
	 * @date	2012-6-25
	 */
	public function pagesUpdate()
	{
		$pageName = Filter::act(Req::get('pageName'));
		$pageUrl = Filter::act(Req::get('pageUrl'));
		$pageArr = array();

		foreach($pageName as $k => $v)
		{
			$pageArr[$k] = array(
				'name'	=> $v,
				'url'	=> $pageUrl[$k],
				'img'	=> $pageImg[$k]
			);
		}
		$data = var_export($pageArr, true);
		$dataStr = "<?php if (!defined('THINK_PATH')) exit();return {$data}; ?>";
		$this->saveFile($dataStr, './AppStore/Data/pages.php');
	}
	/**
	 * 写入配置到文件
	 * @method	saveFile
	 * @access	private
	 * @param	$data		要存储到文件中到内容
	 * @param	$fileName	文件名称
	 * @return	void
	 * @author	xuhao
	 * @date	2012-6-25
	 */
	private function saveFile($data = '', $fileName = '')
	{
		if(file_exists($fileName))
		{
			$this->resource = fopen($fileName, 'w+b');
			if($this->resource)
			{
				flock($this->resource, LOCK_EX);
				$worldsnum = fwrite($this->resource, $data);
			}
			fclose($this->resource);
			if(is_bool($worldsnum))
			{
				$this->error('修改失败');
			}
			$this->success('修改成功');
		}
		$this->error('系统错误');
	}
	private function saveConf($inputArr)
	{
		$configStr = '';
		if(file_exists($this->_filePathArr['site_conf']))
		{
			$configStr = file_get_contents($this->_filePathArr['site_conf']);
			$configArr = include($this->_filePathArr['site_conf']);
		}

		if(trim($configStr) == '')
		{
			$configStr = "<?php\r\n if (!defined('THINK_PATH')) exit();\r\n return array(\r\n);?>";
			$configArr = array();
		}

		$inputArr = array_merge($configArr, $inputArr);
		$configData = var_export($inputArr, true);
		$configStr = "<?php\r\n if (!defined('THINK_PATH')) exit();\r\n return {$configData}?>";
		$this->saveFile($configStr, $this->_filePathArr['site_conf']);
	}
}
?>

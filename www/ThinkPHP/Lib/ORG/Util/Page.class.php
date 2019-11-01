<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// | Editor: huangdijia <huangdijia@gmail.com>
// +----------------------------------------------------------------------
// $Id$


class Page extends Think
{

	// 起始行数
	public $firstRow;
	// 列表每页显示行数
	public $listRows;
	// 页数跳转时要带的参数
	public $parameter;
	// 分页总页面数
	protected $totalPages;
	// 总行数
	protected $totalRows;
	// 当前页数
	public $rewrite; //是否采用伪静态形式
	protected $nowPage;
	// 分页的栏的总页数
	protected $coolPages;
	// 分页栏每页显示的页数
	protected $rollPage;
	// 分页显示定制
	protected $config = array(
		'header' => '条记录',
		'prev' => '上一页',
		'next' => '下一页',
		'first' => '第一页',
		'last' => '最后一页',
		'theme' => '%totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %first% %linkPage% %end% %downPage%'
	);
	// 是否是ajax
	protected $isAjax = false;
	//ajax请求的名称
	protected $ajaxFun = 'ajax_page';
	//protected $config  =	array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
	//是否是定制简化版
	protected $isSimple = false;
	//传入的ajax参数
	private $ajaxPara = '';
	private $mobileRef = '';

	/**
	 * +----------------------------------------------------------
	 * 架构函数
	 * +----------------------------------------------------------
	 * @access public
	 * +----------------------------------------------------------
	 * @param array $totalRows  总的记录数
	 * @param array $listRows  每页显示记录数
	 * @param array $parameter  分页跳转的参数
	 * +----------------------------------------------------------
	 */
	public function __construct($totalRows, $listRows, $rewrite = '0', $parameter = '')
	{
		$this->rewrite = $rewrite;
		$this->totalRows = $totalRows;
		$this->parameter = $parameter;
		$this->rollPage = C('PAGE_ROLLPAGE');
		$this->listRows = !empty($listRows) ? $listRows : C('PAGE_LISTROWS');
		$this->totalPages = ceil($this->totalRows / $this->listRows); //总页数
		$this->coolPages = ceil($this->totalPages / $this->rollPage);
		$this->nowPage = !empty($_GET[C('VAR_PAGE')]) && ($_GET[C('VAR_PAGE')] > 0) ? $_GET[C('VAR_PAGE')] : 1;
		if (!empty($this->totalPages) && $this->nowPage > $this->totalPages)
		{
			$this->nowPage = $this->totalPages;
		}
		$this->firstRow = $this->listRows * ($this->nowPage - 1);
	}

	public function setConfig($name, $value)
	{
		if (isset($this->config[$name]))
		{
			$this->config[$name] = $value;
		}
	}

	/**
	 * +----------------------------------------------------------
	 * 分页显示输出
	 * +----------------------------------------------------------
	 * @access public
	 * +----------------------------------------------------------
	 */
	public function show()
	{
		if (0 == $this->totalRows)
			return '';
		$p = C('VAR_PAGE');
		$nowCoolPage = ceil($this->nowPage / $this->rollPage);
		$uri = (C('URL_MODEL') > 0 && C('URL_MODEL') < 3) ? $_SERVER['REQUEST_URI'] : urldecode($_SERVER['REQUEST_URI']) ;
		$url = $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '' : "?") . $this->parameter;
		$parse = parse_url($url);
		if (isset($parse['query']))
		{
			parse_str($parse['query'], $params);
			unset($params[$p]);
			$url = (C('URL_MODEL') > 0 && C('URL_MODEL') < 3) ? $parse['path'] . '?' . http_build_query($params) : urldecode($parse['path'] . '?' . http_build_query($params));
		}

		$patterns = array(
			"/\/p\/\d+/",
			"/\.html/",
			"/\/$/",
		);
		if(C('URL_MODEL') > 0 && C('URL_MODEL') < 3)array_push($patterns, "/\?/");

		// 静态显示分页参数

		if (C('URL_MODEL') || $this->rewrite)
		{
			$url = preg_replace($patterns, '', $url);
			$format = "/{$p}/%d" . C('URL_HTML_SUFFIX');
		}
		else
		{
			if (!strpos($uri, '&'))
			{
				$format = "{$p}=%d";
			}
			else
			{
				$format = "&{$p}=%d";
			}
		}

		//上下翻页字符串
		$upRow = $this->nowPage - 1;
		$downRow = $this->nowPage + 1;
		if ($upRow > 0)
		{
			$upPage = '<a' . $this->mobileRef . ' href="' . $url . sprintf($format, $upRow) . '" class="page_prev_btn clearfix">' . $this->config['prev'] . '</a>';
			if ($this->isAjax)
			{
				$upPage = '<a' . $this->mobileRef . ' href="javascript:void(0);" class="page_prev_btn clearfix" onclick="' . $this->ajaxFun . '(' . $upRow . $this->ajaxPara . ');">' . $this->config['prev'] . '</a>';
			}
		}
		else
		{
			$upPage = "<span class=\"disabled page_prev_btn_disabled clearfix\">" . $this->config['prev'] . "</span>";
		}

		if ($downRow <= $this->totalPages)
		{
			$downPage = "<a" . $this->mobileRef . " href='" . $url . sprintf($format, $downRow) . "' class=\"page_next_btn clearfix\">" . $this->config['next'] . "</a>";
			if ($this->isAjax)
			{
				$downPage = '<a' . $this->mobileRef . ' href="javascript:void(0);" class="page_next_btn clearfix" onclick="' . $this->ajaxFun . '(' . $downRow . $this->ajaxPara . ');" >' . $this->config['next'] . '</a>';
			}
		}
		else
		{
			$downPage = "<span class=\"disabled page_next_btn_disabled clearfix\">" . $this->config['next'] . "</span>";
		}

		//只有1页时不显示"上一页""下一页"
		if ($this->totalPages <= 1)
		{
			$downPage = $upPage = '';
		}

		$offset = floor($this->rollPage / 2);

		$linkPage = "";
		if ($this->totalPages <= $this->rollPage)
		{
			$startPage = 1;
			$endPage = $this->totalPages;
		}
		else
		{
			//set startPage
			if ($this->nowPage > $offset)
			{
				$startPage = $this->nowPage - $offset;
			}
			else
			{
				$startPage = 1;
			}

			//set endPage
			if ($this->nowPage + $offset < $this->totalPages)
			{
				$endPage = $this->nowPage + $offset;
			}
			else
			{
				$startPage = $this->totalPages - $this->rollPage;
				$endPage = $this->totalPages;
			}

			if ($this->nowPage + $offset < $this->rollPage)
			{
				$startPage = 1;
				$endPage = $this->rollPage;
			}
		}

		// << < > >>
		if ($startPage == 1)
		{
			$nextPage = "";
			$theEnd = "";
		}
		else
		{
			$preRow = $this->nowPage - $this->rollPage;
			$prePage = "<a" . $this->mobileRef . " href='" . $url . sprintf($format, $preRow) . "'>上" . $this->rollPage . "页</a>";
			$theFirst = $this->isSimple ? '<span> ...</span>' : "<a " . $this->mobileRef . "  href='" . $url . sprintf($format, 1) . "' class='first'>" . $this->config['first'] . /* 1 */"</a> ...";
			if ($this->isAjax)
			{
				$prePage = '<a' . $this->mobileRef . ' href="javascript:void(0);" onclick="' . $this->ajaxFun . '(' . $preRow . $this->ajaxPara . ');">上' . ".$this->rollPage." . '页</a>';
				$theFirst = $this->isSimple ? '<span> ...</span>' : '<a ' . $this->mobileRef . ' href="javascript:void(0);" class="first" onclick="' . $this->ajaxFun . '(1' . $this->ajaxPara . ');">' . $this->config['first'] . '</a> ...';
			}
		}

		if ($endPage == $this->totalPages)
		{
			$nextPage = "";
			$theEnd = "";
		}
		else
		{
			$nextRow = $this->nowPage + $this->rollPage;
			$theEndRow = $this->totalPages;
			$nextPage = "<a" . $this->mobileRef . " href='" . $url . sprintf($format, $nextRow) . "'>下" . $this->rollPage . "页</a>";
			$theEnd = $this->isSimple ? '<span>... </span>' : "... <a " . $this->mobileRef . " href='" . $url . sprintf($format, $theEndRow) . "' class='last'>" . $this->config['last'] /* $theEndRow */ . "</a>";
			if ($this->isAjax)
			{
				$nextPage = '<a' . $this->mobileRef . ' href="javascript:void(0);" onclick="%fun%">下' . ".$this->rollPage." . '页</a>';
				$theEnd = $this->isSimple ? '<span>... </span>' : '... <a ' . $this->mobileRef . ' href="javascript:void(0);" class="last" onclick="' . $this->ajaxFun . '(' . $theEndRow . $this->ajaxPara . ');">' . $this->config['last'] . '</a>';
			}
		}

		//确保$startPage和$endPage的范围
		$startPage = $startPage < 1 ? 1 : $startPage;
		$endPage = $endPage > $this->totalPages ? $this->totalPages : $endPage;

		// 1 2 3 4 5

		for ($i = $startPage; $i <= $endPage; $i++)
		{
			if ($i != $this->nowPage)
			{
				if ($i <= $this->totalPages)
				{
					if ($this->isAjax)
					{
						$linkPage .= '<a' . $this->mobileRef . ' href="javascript:void(0);" class="page_list_btn" onclick="' . $this->ajaxFun . '(' . $i . $this->ajaxPara . ');">' . $i . '</a>';
					}
					else
					{
						$linkPage .= "<a" . $this->mobileRef . " href='" . $url . sprintf($format, $i) . "' class=\"page_list_btn\">" . $i . "</a>";
					}
				}
				else
				{
					break;
				}
			}
			else
			{
				if ($this->totalPages != 1)
				{
					$linkPage .= "<span class='current'><strong>" . $i . "</strong></span>";
				}
			}
		}

		$find = array(
			'%header%',
			'%nowPage%',
			'%totalRow%',
			'%totalPage%',
			'%upPage%',
			'%downPage%',
			'%first%',
			'%prePage%',
			'%linkPage%',
			'%nextPage%',
			'%end%',
		);

		$replace = array(
			$this->config['header'],
			$this->nowPage,
			$this->totalRows,
			$this->totalPages,
			$upPage,
			$downPage,
			$theFirst,
			$prePage,
			$linkPage,
			$nextPage,
			$theEnd,
		);
		$pageStr = str_replace($find, $replace, $this->config['theme']);
		return $pageStr;
	}

	public function showAjax($jsFun='ajax_page')
	{
		//当前页数
		$nowPage = $this->nowPage;
		//总页数
		$totalPages = $this->totalPages;
		//总行数
		$totalRows = $this->totalRows;

		$fstr = '<a href="javascript:;" onclick="%fun%">首页</a>  ';
		$pstr = '<a href="javascript:;" onclick="%fun%">上一页</a>  ';
		$nstr = '<a href="javascript:;" onclick="%fun%">下一页</a>  ';
		$estr = '<a href="javascript:;" onclick="%fun%">尾页</a>  ';
		$goto = '<a href="javascript:;" onclick="%fun%">%num%</a>  ';
		$now = '<a href="javascript:;">' . $nowPage . '</a>  ';
		$showstr = '共' . $totalRows . '条记录  当前第' . $nowPage . '页    ';

		if ($nowPage > 1)
		{
			$fstr = str_replace('%fun%', $jsFun . "('1')", $fstr);
			$pstr = str_replace('%fun%', $jsFun . "('" . ($nowPage - 1) . "')", $pstr);
		}
		else
		{
			$fstr = '';
			$pstr = '';
		}

		if ($nowPage != $totalPages && $nowPage > 0)
		{
			$estr = str_replace('%fun%', $jsFun . "('" . $totalPages . "')", $estr);
			$nstr = str_replace('%fun%', $jsFun . "('" . ($nowPage + 1) . "')", $nstr);
		}
		else
		{
			$estr = '';
			$nstr = '';
		}
		$numstr = '';
		for ($i = 1; $i <= $totalPages; $i++)
		{
			if ($i != $nowPage)
			{
				$numtmp = str_replace('%num%', $i, $goto);
				$numtmp = str_replace('%fun%', $jsFun . "('" . $i . "')", $numtmp);
			}
			else
			{
				$numtmp = $now;
			}
			$numstr .= $numtmp;
		}
		$pagestr = $showstr . $fstr . $pstr . $numstr . $nstr . $estr;
		return $pagestr;
	}

	public function setAjax($nowPage = 1, $ajaxPara)
	{
		$this->nowPage = $nowPage;
		$this->isAjax = true;
		$this->ajaxPara = $ajaxPara ? ", '" . $ajaxPara . "'" : '';
	}

	public function setMobileRef($bmobile = false)
	{
		if ($bmobile)
			$this->mobileRef = " rel='external' ";
	}

}

?>
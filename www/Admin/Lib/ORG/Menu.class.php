<?php
/**
 +------------------------------------------------------------------------------
 * @description:	后台系统菜单管理
 +------------------------------------------------------------------------------
 * @others:			NULL
 * @file:			BaseAction.class.php
 * @author:		xuhao
 * @date:			2011-12-08
 +------------------------------------------------------------------------------
 */
class Menu
{
	private static $commonMenu = array('/system/default');
	public $current;
    //菜单的配制数据
	private static $menu = array(
		'应用'=>array(
			'应用管理'=>array(
				'Apps/appList'				=> '应用列表',
				'Apps/appAdd'				=> '添加应用'
			),
			'应用分类'=>array(

				'Apps/appCateList'		=> '分类列表',
				'Apps/appCateAdd'		=> '添加分类'

			),
			'开发者应用管理'=>array(

				'Developer/appList'		=> '待审核列表',
				'Developer/appauditList'		=> '审核通过列表',
				'Developer/appauditfailList'		=> '审核未通过列表'

			),
		),

		'用户'=>array(
			'会员管理'=>array(
				'User/userList'			=> '会员列表',

			),
			'信息处理' => array(

			),
		),
		'系统'=>array(
			'网站管理'=>array(
				'System/baseConf'		=> '网站设置',
			),
		),
	);

	private static $menu_non_display = array(
		'Apps/appRecycleList' => 'Apps/appList',
		'Apps/appCateEdit' => 'Apps/appCateList',
		'Apps/appEdit' => 'Apps/appList',
		'Developer/appEdit' => 'Developer/appList',
		'Developer/auditList' => 'Developer/appList',
		'Developer/appAudit' => 'Developer/appList',
		'Developer/appRecycleList' => 'Developer/appList',
		'Developer/appsRecycleRestore' => 'Developer/appList',
		'Developer/appAuditEdit' => 'Developer/appauditList',
		'Developer/auditList' => 'Developer/appauditList',
		'Developer/appunAudit' => 'Developer/appauditList',
		'Developer/appAuditRecycleList' => 'Developer/appauditList',
		'Developer/appsAuditRecycleRestore' => 'Developer/appauditList',
		'Common/searchApp' => 'Apps/appList',
		'User/userEdit' => 'User/userList',
		'User/userRecycleList' => 'User/userList',
	);

    /**
     * @brief 根据用户的权限过滤菜单
     * @return array
     */
//    private function filterMenu()
//    {
//    	$rights = ISafe::get('admin_right');
//
//		//如果不是超级管理员则要过滤菜单
//		if($rights != 'administrator')
//		{
//			foreach(self::$menu as $firstKey => $firstVal)
//			{
//				if(is_array($firstVal))
//				{
//					foreach($firstVal as $secondKey => $secondVal)
//					{
//						if(is_array($secondVal))
//						{
//							foreach($secondVal as $thirdKey => $thirdVal)
//							{
//								if(!in_array($thirdKey,self::$commonMenu) && (stripos(str_replace('@','/',$rights),','.substr($thirdKey,1).',') === false))
//								{
//									unset(self::$menu[$firstKey][$secondKey][$thirdKey]);
//								}
//							}
//							if(empty(self::$menu[$firstKey][$secondKey]))
//							{
//								unset(self::$menu[$firstKey][$secondKey]);
//							}
//						}
//					}
//					if(empty(self::$menu[$firstKey]))
//					{
//						unset(self::$menu[$firstKey]);
//					}
//				}
//			}
//		}
//    }

    /**
     * @brief 取得当前菜单应该生成的对应JSON数据
     * @return Json
     */
	public function submenu()
	{
//		$controllerObj = IWeb::$app->getController();
//		$controller = $controllerObj->getId();
//		$actionObj = $controllerObj->getAction();
//		$action = $actionObj->getId();
		$this->current = MODULE_NAME.'/'.ACTION_NAME;
        $this->vcurrent = MODULE_NAME.'/';
		$items  = array();

		if(isset(self::$menu_non_display[$this->current]))
		{
			$this->current = self::$menu_non_display[$this->current];
			$tmp = explode("/",$this->current);
			$this->vcurrent = $tmp[1];
			$action = $tmp[2];
		}

		//过滤菜单
		//$this->filterMenu();
		$find_current = false;
		$items = array();
		foreach(self::$menu as $key => $value)
		{
			if(!is_array($value))
			{
				return;
			}
			$item = array();
			$item['current'] = false;
			$item['title'] = $key;

			foreach($value as $big_cat_name => $big_cat)
			{
				foreach($big_cat as $link=>$title)
				{
					if(!isset($item['link']) )
					{
						$item['link'] = U('Admin://' . $link);
					}

					if($find_current)
					{
						break;
					}

					$tmp1 = explode('_',$action);
					$tmp1 = $tmp1[0];
					if($link == $this->current || preg_match("!^/[^/]+/{$tmp1}_!",$link) )
					{
						$item['current'] = $find_current = true;
						foreach($value as $k=>$v)
						{
							foreach($v as $subMenuKey=>$subMenuName)
							{
								$tmpUrl = U('Admin://' . $subMenuKey);
								unset($value[$k][$subMenuKey]);
								$value[$k][$tmpUrl]['name'] = $subMenuName;
								$value[$k][$tmpUrl]['urlPathinfo'] = $subMenuKey;
							}
						}
						$item['list'] = $value;
					}
				}

				if($find_current)
				{
					break;
				}
			}
			//$item['link'] = IUrl::creatUrl($item['link']);
			$items[] = $item;
		}
		return json_encode($items);
	}
}
?>

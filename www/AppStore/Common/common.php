<?php
/**
 * 获取当前分类以及父类的数组
 * @param string $cid 当前应用的所属的分类id
 * @return array $r 返回分类所属的父类至顶级分类的数组
 */
function getCateArr($cid) {
	$cate = getCateInfo ( $cid );
	$arr = explode ( "__", $cate ['key'] );
	//dump($arr);
	for ($row=0;$row<count($arr);$row++){
		$subarr = explode ( "_", $arr[$row] );
		//dump($subarr);
		$r [] = getCateInfo ( $subarr [1] );
	}
	//exit;
	return $r;
}
/**
 * 获取当前分类信息
 *
 * @param int $cid 分类id
 * @return array S ( 'S_CATETREE' ) 所有分类的缓存
 */
function getCateInfo($cid)
{
	$cate = getCateTreeArr();
	foreach($cate as $k => $v)
	{
		if ($cate[$k]['id']==$cid) {
			return $cate[$k];
		}
	}
}
/**
 * 查询分类数据
 *
 * @return array S ( 'S_CATETREE' ) 所有分类的缓存
 */
function getCateTreeArr() {
	//if (S ( 'S_CATETREE' ) == "") {
		$appCategoryObj = new CategoryModel();
		$appCategoryRow = $appCategoryObj->getCategorys();
		$arr = parseTree ( $appCategoryRow );
		return $arr;
		//S('S_CATETREE',$arr);
	//}
	//return S ( 'S_CATETREE' );
}
/**
 * 解析分类树
 *
 * @param array $arrTree 分类树的数组
 * @return array $tree 返回排序好的，经过处理的数组
 */
function parseTree($arrTree)
{
	$tree = array ();
	$index = array ();
	foreach ( $arrTree as $k => $v ) {
		if ($v ['parent_id'] == 0) {
			$key = '0_' . $v ['id'] . '_';
			$v ['key'] = $key;
			$tree [$k] = $v;
		} else {
			$key = $index [$v ['parent_id']] ['key'] . '_' . $v ['pid'] . '_' . $v ['id'] . '_';
			$v ['key'] = $key;
			$tree [$k] = $v;
		}
		$index [$v ['id']] = & $tree [$k];
	}
	return $tree;
}
/**
 * 检测内容是否含有关键字
 */
function checkKeyWord( $content ){
	$audit = include(APP_PATH . '/Data/filter_word.php');
	if($audit['open'] && $audit['keywords']){
		$arr_keyword = explode('|', $audit['keywords']);
		foreach ( $arr_keyword as $k=>$v ){
			$num = stristr($content,$v)?$num+1:$num;
		}
		if($num){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}
/**
 * 关键字过滤
 */
function keyWordFilter( $content ){
	$audit = include(APP_PATH . '/Data/filter_word.php');
	if($audit['open'] && $audit['keywords']){
		$replace = $audit['replace']?$audit['replace']:'[和*谐]';
		$arr_keyword = explode('|', $audit['keywords']);
		foreach ( $arr_keyword as $k=>$v ){
			$content = str_replace($v, $replace, $content);
		}
		return $content;
	}else{
		return $content;
	}
}
/**
 * 手机号解密
 */
function deMaskPhone($str, $isStar = true)
{
	$mobile = sprintf("%05x",'0x'.substr($str,0,5)-0x66666).sprintf("%06x",'0x'.substr($str,5)-0x666666);
	if(!$isStar)
	{
		return $mobile;
	}
	return substr_replace($mobile, '****', 3, 4);
	//return $mobile = dechex('0x'.substr($str,0,5)-0x66666).dechex('0x'.substr($str,5)-0x666666);
	//return substr_replace($mobile, '****', 3, 4);
}
/**
 * 加密手机号
 * @method	deMaskPhone()
 * @access public
 * @param	string	$str	手机号
 * @param	boolean	$isStar	是否替换中间四位为****
 * @return string
 * @author	xiaoguang
 * @date	2012-2-8
 */
function enMaskPhone($str, $isStar = true)
{
	$mobile = sprintf("%05x",'0x'.substr($str,0,5)+0x66666).sprintf("%06x",'0x'.substr($str,5)+0x666666);
	if(!$isStar)
	{
		return $mobile;
	}
	return substr_replace($mobile, '****', 3, 4);
}
/**
 * 搜索关键字替换
 */
function replaceKeyWords($keyWords, $str)
{
	$return = '';
	foreach($keyWords as $k => $v)
	{
		$str = str_ireplace($v, '<span class="green">' . $v . '</span>', $str);
	}
	return $str;
}
/**
 * 截取应用名称
 */
function showCutStr($str, $length = 9, $encode = 'UTF-8', $subCode = '...')
{
	$return = strip_tags($str);
	if(mb_strlen($return, $encode) > $length)
	{
		$return = mb_substr($return, 0, $length, $encode) . $subCode;
	}
	return $return;
}
?>

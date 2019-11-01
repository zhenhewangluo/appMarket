<?php
if(!isset($GLOBALS['_vars'])) $GLOBALS['_vars'] = array(); 
$fields = array();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $cfg_soft_lang; ?>" />
<title>访客历史记录 - 会员中心 - <?php echo $cfg_webname; ?></title>
<link href="templets/style/base.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="templets/js/j.js"></script>
<script type="text/javascript" src="templets/js/load.js"></script>
<script type="text/javascript" src="templets/js/leftmenu.js"></script>
</head>
<body>
<div id="main">
  <?php include(DEDEMEMBER."/templets/head.htm"); ?>
  <div id="content" class="w960 clearfix">
    <?php include(DEDEMEMBER."/templets/menu.php"); ?>
    <div id="mcpmain">
      <div id="appTab">
        <ul>
          <li><a href="myfriend.php?ftype=0"><span>所有好友</span></a></li>
          <li><a href="search.php"><span>查找好友</span></a></li>
          <li <?php if($dopost == "viewme" || empty($dopost)) echo 'class="thisTab"';?>><a href="visit-history.php?dopost=viewme"><span>关注我的人</span></a></li>
          <li <?php if($dopost == "meview") echo 'class="thisTab"';?>><a href="visit-history.php?dopost=meview"><span>我最近访问</span></a></li>
          <li><a href="myfriend_group.php"><span>分组设置</span></a></li>
        </ul>
      </div>
      <div id="mainCp">
        <ul class="listStyle3">
        <?php
$atts = array();
$atts['tagname'] = 'datalist';
$blockValue = $this->refObj->GetArcList($atts,$this->refObj,$fields); 
if(is_array($blockValue)){
foreach( $blockValue as $key=>$fields )
{
 if($dopost == "viewme" || $dopost==''){ ?>
<li class="clearfix">
  <div class="frdInfo">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="3" align="left" valign="middle" class="userTitle"><a href='index.php?uid=<?php echo $fields['vloginid']; ?>' target='_blank'><?php echo $fields['vloginid']; ?></a>
          </td>
        </tr>
      <tr>
        <td>访问 <strong><?php echo $fields['count']; ?></strong> 次</td>
        <td>访问时间:<?php echo MyDate('y-m-d H:i:s',$fields['vtime']); ?></td>
        <td align="right" class="doPost"><a href='index.php?uid=<?php echo $fields['vloginid']; ?>&action=infos' target='_blank'>[资料]</a></td>
        </tr>
      <tr>
        <td>访问IP:<?php echo $fields['vip'];  echo $fields['sex']; ?></td>
        <td class="doPost"><a href='index.php?action=newfriend&uid=<?php echo $fields['vloginid']; ?>' target='_blank'>加为好友</a></td>
        <td align="right" class="doPost"><a href='index.php?uid=<?php echo $fields['vloginid']; ?>&action=guestbook' target='_blank'>[留言]</a></td>
        </tr>
      </table>
    </div>
   <img src="<?php if(empty($fields['face'])){$face=($fields['sex']=='女')? 'templets/images/dfgirl.png' : 'templets/images/dfboy.png';}else{ $face=$fields['face'];} echo $face;?>" width='60' height='60'/></li>
  <?php }else{ ?>
  <li class="clearfix">
  <div class="frdInfo">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="3" align="left" valign="middle" class="userTitle"><a href='index.php?uid=<?php echo urlencode($fields['loginid']); ?>' target='_blank'> <?php echo $fields['loginid']; ?></a>
         </td>
      </tr>
      <tr>
        <td>访问 <strong><?php echo $fields['count']; ?></strong> 次</td>
        <td>访问时间:<?php echo MyDate('y-m-d H:i:s',$fields['vtime']); ?></td>
        <td align="right" class="doPost"><a href='index.php?uid=<?php echo $fields['loginid']; ?>&action=infos' target='_blank'>[资料]</a></td>
        </tr>
      <tr>
        <td>访问IP:<?php echo $fields['vip']; ?></td>
        <td class="doPost"><a href='index.php?action=newfriend&uid=<?php echo $fields['loginid']; ?>' target='_blank' >加为好友</a></td>
        <td align="right" class="doPost"><a href='index.php?uid=<?php echo $fields['loginid']; ?>&action=guestbook' target='_blank'>[留言]</a></td>
        </tr>
      </table>
    </div>
      <img src="<?php if(empty($fields['face'])){$face=($fields['sex']=='女')? 'templets/images/dfgirl.png' : 'templets/images/dfboy.png';}else{ $face=$fields['face'];} echo $face;?>" width='60' height='60'/></li>
          <?php } 
}
}?>
        </ul>
        <div style="clear:both"></div>
        <div class="listDo appMsg">
          <div class="control fLeft">
          </div>
          <div class="pageList fRight"><?php
$atts = array();
$atts['tagname'] = 'pagelist';
$atts['listsize'] = '5';
 echo $this->refObj->GetPageList($atts,$this->refObj,$fields); 
?>
</div>
        </div>
      </div>
      <!--主操作区域 -->
    </div>
  </div>
  <?php include(DEDEMEMBER."/templets/foot.htm"); ?>
</div>
</body>
</html>
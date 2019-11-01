<?php
if(!isset($GLOBALS['_vars'])) $GLOBALS['_vars'] = array(); 
$fields = array();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $cfg_soft_lang; ?>">
<title>投票管理</title>
<link href="css/base.css" rel="stylesheet" type="text/css">
</head>
<script language='javascript' src="js/main.js"></script>
<script language="javascript" src="../include/js/jquery/jquery.js"></script>
<script language="javascript" src="../include/js/jquery/ui.core.js"></script>
<script language="javascript" src="../include/js/jquery/ui.draggable.js"></script>
<script language='javascript' src='js/tb-box.js'></script>

<body background='images/allbg.gif' leftmargin='8' topmargin='8'>
<form id="form1" name="form1" method="post" action="">
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#D6D6D6">
  <tr>
   <td height="28" background="images/tbg.gif" colspan="5">
     <b>投票选择</b></td>
</tr>
  <tr align="center" bgcolor="#F9FCEF" height="24"> 

      <td width="8%">投票ID</td>
      <td width="18%">投票名称</td>
	  <td width="20%">开始时间</td>
	  <td width="20%">结束时间</td>

	  <td width="10%">投票ID</td>
  </tr>
        <?php
$atts = array();
$atts['tagname'] = 'datalist';
$blockValue = $this->refObj->GetArcList($atts,$this->refObj,$fields); 
if(is_array($blockValue)){
foreach( $blockValue as $key=>$fields )
{
?>
         <tr align="center" bgcolor="#FFFFFF" height="24" 
         	onMouseMove="javascript:this.bgColor='#f4fbf4';" onMouseOut="javascript:this.bgColor='#FFFFFF';"> 
          <td <?php if($fields['aid'] == $aid) echo "style='color:red'";?>><?php echo $fields['aid']; ?></td>
          <td <?php if($fields['aid'] == $aid) echo "style='color:red'";?>>
          	<?php echo $fields['votename']; ?>
          </td>
          <td <?php if($fields['aid'] == $aid) echo "style='color:red'";?>><?php echo MyDate('Y-m-d',$fields['starttime']); ?></td>
          <td <?php if($fields['aid'] == $aid) echo "style='color:red'";?>><?php echo MyDate('Y-m-d',$fields['endtime']); ?></td>

		  <td><a href="javascript:PutVote(<?php echo $fields['aid']; ?>);" id="TB_closeWindowButton">选取</a></td>
        </tr>
		<?php
}
}?>
     <tr align="center" bgcolor="#F9FCEF" height="24"> 
      <td colspan="5"><?php
$atts = array();
$atts['tagname'] = 'pagelist';
$atts['listsize'] = '6';
 echo $this->refObj->GetPageList($atts,$this->refObj,$fields); 
?>
</td>
     </tr>
   </table>
   </td>
</tr>
</table>
</form>
</body>
</html>
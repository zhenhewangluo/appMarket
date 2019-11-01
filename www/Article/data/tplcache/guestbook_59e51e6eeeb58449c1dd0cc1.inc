<?php
if(!isset($GLOBALS['_vars'])) $GLOBALS['_vars'] = array(); 
$fields = array();
 include $this->CompilerInclude("header.htm");
 ?>

<div id="side" class="left">
	<?php include $this->CompilerInclude("side_icon.htm");
  include $this->CompilerInclude("side_news.htm");
  include $this->CompilerInclude("side_visitor.htm");
 ?>
</div>

<!--begin main-->
  <div id="main" class="right">
	<div class="box">
	  <h2>留言列表</h2>
	  <!--begin-->
	  <div class="text">
		<!--begin comment-->
		<div class="comment bb">
		  <div id="newgbook"></div>
      <?php
$atts = array();
$atts['tagname'] = 'datalist';
$blockValue = $this->refObj->GetArcList($atts,$this->refObj,$fields); 
if(is_array($blockValue)){
foreach( $blockValue as $key=>$fields )
{
?>
		  <a name="1"></a>
		  <dl id="dl_1">
			<dd class="comment-pic left">
				<?php if($fields['face']==''){ 
                   $face=($fields['sex']=='女')? 'templets/images/dfgirl.png' : 'templets/images/dfboy.png';
                  echo  '<img src="'.$face.'" width="50" height="50"/>';
                 }else{ 
                     echo  '<img src="'.$fields['face'].'" width="50" height="50" alt="'.$fields['uname'].'的头像" />';
                 } ?> 
			</dd>
			<dd>
			  <a href="index.php?uid=<?php echo $fields['userid']; ?>" class="big b"><?php echo $fields['uname']; ?></a>
			  <span class="gray"><?php echo MyDate('Y-m-d H:i',$fields['dtime']); ?></span>
			</dd>
			<dd class="comment-content">
			  <div><?php echo Text2Html($fields['msg']); ?></div>
			  <span id="s_1"></span>
			  <div id="r_1">
				<div class="re-comment" style="display:none;">
				  <b><a href='index.php?uid=<?php echo $fields['uname']; ?>'></a> 于  回复：</b>
				  <div id="r_content_1"></div>
				</div>
			  </div>
			</dd>
		  </dl>
		  <?php
}
}?>
		  <div class="big"><div class="pages"><?php
$atts = array();
$atts['tagname'] = 'pagelist';
 echo $this->refObj->GetPageList($atts,$this->refObj,$fields); 
?>
</div></div>
		  <div class="post-comment">
			<h5>发表留言</h5>
			<div>
       <form action="index.php" name="form1" method="post">
			  <input type="hidden" id="guser" value="<?php echo $GLOBALS['_vars']['uname']; ?>" /><?php echo $GLOBALS['_vars']['uname']; ?>：
        <input type="hidden" name="action" value="guestbooksave" />
        <input type="hidden" name="uidnum" value="<?php echo $GLOBALS['_vars']['mid']; ?>" />
        <input type="hidden" name="uid" value="<?php echo $GLOBALS['_vars']['userid']; ?>" />
        <input type="hidden" name="uname" type="text" id="uname" value="<?php if(isset($cfg_ml->M_LoginID) && $cfg_ml->M_LoginID!='') echo $cfg_ml->M_LoginID; ?>"  />
			</div>
			<div>标题：<input name="title" type="text" id="title"   class="ip" style="width:250px;height:15px;" /></div>
			<div>
			  <textarea class="ip" name="msg" id="msg" style="width:95%;height:8em"></textarea>
			  <span style="margin-top:-1em;padding:3px; text-align:right; width:200px; position:absolute">
			</div>
			</span>
			</div>
			<table>
				<tr>
			 	<td>验证码：</td>
			 	<td><input name="vdcode" type="text" id="vdcode" size="10" style="height:16px;width:60px;" class="ip" /></td>
			 	<td><img src='../include/vdimgck.php' width='60px' height='20px'  /></td>
       	<td>&nbsp;<input name="sbbt" type="submit" value="发送留言" style="width:60px;height:22px"  /></td>
			 </tr>
			</table>
		  </div>
		  </form>
		  <div class="c"></div>
		</div>
		<!--begin comment-->
	  </div>
	  <!--end-->
	</div>
</div>
<!--end main-->

<?php include $this->CompilerInclude("footer.htm");
 ?>
<?php
if(!isset($GLOBALS['_vars'])) $GLOBALS['_vars'] = array(); 
$fields = array();
?><!-- //begin icon -->
<div class="box" id="side_icon">
	  <h2>博主资料</h2>
	  <div class="profile">
		<div class="text">
		  <div class="profile-pic">
		  	<span id="msg"></span>
			<div>
			<?php if($_vars['face']==''){ ?>
	<img src="images/nopic.gif" alt="nopic" />
	<?php
}
else{
?>
	<img src="<?php echo $GLOBALS['_vars']['face']; ?>" alt="<?php echo $GLOBALS['_vars']['uname']; ?>" />
	<?php } ?>
			</div>
		  </div>
          <div style="height:30px;text-align:center"><?php echo $GLOBALS['_vars']['msg']; ?></div>
		  <dl class="profile">
			<dd class="tac" style="word-break:keep-all">
			  <img src="space/person/images/talk.gif" align="absmiddle" /><a href="index.php?action=guestbook&uid=<?php echo $GLOBALS['_vars']['userid']; ?>">留言</a> |
			  <img src="space/person/images/message.gif" align="absmiddle" /><a href="pm.php?dopost=send&uid=<?php echo $GLOBALS['_vars']['userid']; ?>">短消息</a> |
				<img src="space/person/images/team.gif" align="absmiddle" /><a href="index.php?action=newfriend&uid=<?php echo $GLOBALS['_vars']['userid']; ?>">加好友</a> |
			  <img src="space/person/images/del.png" align="absmiddle" /><img src="space/person/images/addto.gif" align="absmiddle" /><a href="index.php?action=blackfriend&uid=<?php echo $GLOBALS['_vars']['userid']; ?>">加黑名单</a>

			</dd>
			<dd style='margin-top:10px;'>&nbsp;用户昵称：<?php echo $GLOBALS['_vars']['uname']; ?></dd>
			<dd>&nbsp;最后登录：<?php echo MyDate('y-m-d h:i',$GLOBALS['_vars']['logintime']); ?></dd>
			<dd>&nbsp;会员等级：<?php echo $GLOBALS['_vars']['membername']; ?></dd>
      <dd>&nbsp;会员积分：<?php echo $GLOBALS['_vars']['scores']; ?> 分</dd>
			<dd>&nbsp;空间访问：<?php echo $GLOBALS['_vars']['homecount']; ?> 次</dd>
		  </dl>
		</div>
	  </div>
</div>
<!-- //end icon -->
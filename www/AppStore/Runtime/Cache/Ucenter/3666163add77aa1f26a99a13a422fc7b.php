<?php if (!defined('THINK_PATH')) exit();?><link href="../Public/css/common.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../Public/js/community.js"></script>
<script type="text/javascript">
	$(function(){
		$('#btRegister').click(function(){
			var phoneVal = $('input:[name="phone"]').val();
			var qqVal = $('input:[name="qq"]').val();
			var obj = {
				'emailIsPublic' : parseInt($('[name="email_ispublic"]:checked').val()),				
				//'phone' : (phoneVal == '') ? '' : parseInt(phoneVal),
				//'mobile_ispublic' : parseInt($('[name="mobile_ispublic"]:checked').val()),
				'qq' : (qqVal == '') ? '' : parseInt(qqVal),
				'qqIsPublic' : parseInt($('[name="qq_ispublic"]:checked').val()),
				'webUrl' : $('input:[name="weburl"]').val(),
				'webUrlIsPublic' : parseInt($('[name="weburl_ispublic"]:checked').val()),
				'address' : $('input:[name="address"]').val(),
				'expressDelivery' : parseInt($('[name="expressdelivery"]:checked').val()),
				'__hash__' : $('#userinfo input:[name="__hash__"]').val() 
			};
			if($('#userinfo .invalid-msg').length > 0)
			{
				return false;
			}
			$.post("<?php echo U('Ucenter-Ajax/updateUserInfo');?>", obj, function(data){
				alert(data.msg);
			},'json');
			return false;
		});
	});
   //清空radio
    function clearRadio(radio_oj){
		for(var i=0;i<radio_oj.length;i++) //循环
		{
			if(radio_oj[i].value=='1') //比较值
			{ 
				radio_oj[i].checked=true; //修改选中状态
				break; //停止循环
			}
		}   
    }  //清空内容
    function clearInput(){
        $("input:text").attr('value','');
		$("input:password").attr('value','');
	//	clearRadio(document.userinfo.mobile_ispublic);
		clearRadio(document.userinfo.qq_ispublic);
		clearRadio(document.userinfo.weburl_ispublic);
		clearRadio(document.userinfo.expressdelivery);
		clearRadio(document.userinfo.email_ispublic);
		$('input').removeClass('invalid-text valid-text invalid-ie6-text');
		$('input + label').html('&nbsp;').removeClass('invalid-msg valid-msg');
		$('input + span').html('&nbsp;').removeClass('invalid-msg valid-msg');
    }
	function updateavatar() {
		window.location.reload();
	}
</script>
<style type="text/css">
	p{
		color: #7B7B7B;
		float: left;
		margin: 0;
		padding: 6px 0;
		width: 100%;
	}
	p label{
		vertical-align: -5px;
	}
	p label.leftHead{
		color: #000000;
		display: block;
		float: left;
		font: 12px/14px arial;
		margin: 0;
		padding: 5px 0 0 0;
		text-align: right;
		width: 84px;
		vertical-align: -3px;
	}
	p span{
		_vertical-align: 3px;
	}
	span.error{
		margin-left:80px;
	}
	p span.invalid-msg{
		margin-top:-5px;
	}
	.user_name_box{text-align: center;}
	.user_avatar_box{text-align:center;}
	.user_avatar_box img#userHead{margin:0;padding:0;}
	.inputbox{
		width:140px;
	}
</style>
<div class="content">
	<div class="user2">
        <h1 id="headline"><?php echo ($webUname); ?></h1>

        <div class="boxUser">
			<div class="top"></div>
			<div class="body">
				<div class="image">
					<span>用户头像</span><br>
					<!-- layout::$userbar::0 -->
				</div>
				<div class="text" style="padding-right:0;">
					<form method="post" id="userinfo" name="userinfo">
						<p>
							<label class="leftHead">昵称：</label>
							<span><?php echo ($webUname); ?></span>
						</p>
						<p>
							<label class="leftHead">账户邮箱：</label>
							<span><?php echo ($webUemail); ?></span>
						</p>
						<p>
							<label class="leftHead">邮箱是否公开：</label>
							<label>
							<?php if($userdata['email_ispublic'] == 1): ?><input type="radio" name="email_ispublic" value="1" checked="true" />是
								<?php else: ?>
								<input type="radio" name="email_ispublic" value="1" />是<?php endif; ?>
							</label>
							<label>
							<?php if($userdata['email_ispublic'] == 0): ?><input type="radio" name="email_ispublic" value="0" checked="true" />否
								<?php else: ?>
								<input type="radio" name="email_ispublic" value="0" />否<?php endif; ?>
							</label>
						</p>
						<p>
							<label class="leftHead">QQ：</label>
							<input class="inputbox" type="text" name="qq" pattern="qq" value="<?php echo ($userdata['qq']); ?>" empty /><span>&nbsp;</span>
						</p>
						<p>
							<label class="leftHead">QQ是否公开：</label>
							<label>
							<?php if($userdata['qq_ispublic'] == 1): ?><input type="radio" name="qq_ispublic" value="1" checked="true" />是
								<?php else: ?>
								<input type="radio" name="qq_ispublic" value="1"  />是<?php endif; ?>
							</label>
							<label>
							<?php if($userdata['qq_ispublic'] == 0): ?><input type="radio" name="qq_ispublic" value="0" checked="true"  />否
								<?php else: ?>
								<input type="radio" name="qq_ispublic" value="0"   />否<?php endif; ?>
							</label>
						</p>
						<p>
							<label class="leftHead">微博地址：</label>
							<input class="inputbox" type="text" name="weburl" value="<?php echo ($userdata['weburl']); ?>" empty pattern="url" /><span>&nbsp;</span>
							<span class="error">请以http://开头&nbsp;</span>
						</p>
						<p>
							<label class="leftHead">微博是否公开：</label>
							<label>
							<?php if($userdata['weburl_ispublic'] == 1): ?><input type="radio" name="weburl_ispublic" value="1" checked="true" />是
								<?php else: ?>
								<input type="radio" name="weburl_ispublic" value="1" />是<?php endif; ?>
							</label>
							<label>
							<?php if($userdata['weburl_ispublic'] == 0): ?><input type="radio" name="weburl_ispublic" value="0" checked="true" />否
								<?php else: ?>
								<input type="radio" name="weburl_ispublic" value="0" />否<?php endif; ?>
							</label>
						</p>
						<p>
							<label class="leftHead">联系地址：</label>
							<input class="inputbox" type="text" name="address" alt="联系地址用于以后发送奖品" value="<?php echo ($userdata['address']); ?>"  />
							<span class="error">联系地址用于以后发送奖品&nbsp;</span>
						</p>
						<p>
							<label class="leftHead">选择快递方式：<br />(用于发送奖品)</label>
							<label>
							<?php if($userdata['expressdelivery'] == 1): ?><input type="radio" name="expressdelivery" value="1" checked="true" />平邮
								<?php else: ?>
								<input type="radio" name="expressdelivery" value="1"  />平邮<?php endif; ?>
							</label>
							<label>
							<?php if($userdata['expressdelivery'] == 0): ?><input type="radio" name="expressdelivery" value="0" checked="true" />快递
								<?php else: ?>
								<input type="radio" name="expressdelivery" value="0"  />快递<?php endif; ?>
							</label>
						</p>
						<p>
							<input type="submit" id="btRegister" class="userInfoSubmit" value="修 　改"/> 　 <input type="button" class="userInfoCancel" value="重 　写" onclick="clearInput();" />
						</p>
					</form>
				</div>
			</div>
			<div class="bottom"></div>
        </div>
		<div id="headUpload" style="float:right;margin-top:11px;">
			<div>
				<?php echo ($flash); ?>
			</div>
			<div>
				<img src="__ROOT__/<?php echo ($webUhead[0]); ?>" />&nbsp;
				<img src="__ROOT__/<?php echo ($webUhead[1]); ?>" />&nbsp;
				<img src="__ROOT__/<?php echo ($webUhead[2]); ?>" />
			</div>
		</div>
	</div>
	<div class="hrFull2"></div>
	<div class="clear"></div>
	
	<h6 class="userPanelListTitle clearfix">我的应用</h6>
	
	<div class="userPanelList">
		<div class="userPanelListHead"></div>
		<input type="hidden" id="listName" value="commentHistory" />
		<div class="userPanelListBody clearfix">
			<ul>
				<li><a href="javascript:void(0);" id="commentHistory" class="current">我的评分</a></li>
				<li><a href="javascript:void(0);" id="downloadHistory">我的下载</a></li>
				<li><a href="javascript:void(0);" id="updateHistory">我的更新</a></li>
			</ul>
			<div id="userPanelListTb">
				<table class="table_app_list">
					<col />
					<col width="50px" />
					<col width="200px" />
					<col width="190px" />
					<thead>
						<tr>
							<th class="t_c">应用名称</th>
							<th class="t_l">分数</th>
							<th class="t_c">评论内容</th>
							<th class="t_c">评分时间</th>
						</tr>
					</thead>
					<tbody>
						<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr>
							<td class="t_l"><a href="<?php echo U('App-Apps/detail?aid=' . $vo['app_id']);?>" title="<?php echo ($vo['app_name']); ?>"><?php echo (mb_substr($vo['app_name'],0,20,'UTF-8')); ?></a></td>
							<td class="t_c"><a href="<?php echo U('App-Apps/detail?aid=' . $vo['app_id']);?>" title="<?php echo ($vo['app_name']); ?>"><?php echo ($vo['score_www']); ?></a></td>
							<td class="t_c"><a href="<?php echo U('App-Apps/detail?aid=' . $vo['app_id']);?>" title="<?php echo ($vo['app_name']); ?>"><?php echo mb_substr(keyWordFilter($vo['content']),0,20,'UTF-8');?></a></td>
							<td class="t_c"><a href="<?php echo U('App-Apps/detail?aid=' . $vo['app_id']);?>" title="<?php echo ($vo['app_name']); ?>"><?php echo ($vo['update_time']); ?></a></td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					</tbody>
				</table>
				<?php if((trim($page))  !=  ""): ?><div class="page" style="clear:both;">
					<div class="page_list">
						<?php echo ($page); ?>
					</div>
				</div><?php endif; ?>
			</div>
		</div>
		<div class="userPanelListBottom"></div>
	</div>
</div>
<script type="text/javascript">
	var urlList = {
		commentHistory : "<?php echo U('Ucenter-Panel/commentHistory');?>",
		downloadHistory : "<?php echo U('Ucenter-Panel/downloadHistory');?>",
		updateHistory : "<?php echo U('Ucenter-Panel/updateHistory');?>"
	};
	$(function(){
//		$('.userPanelListBody ul li a').each(function(){
//			var _thisNode = $(this);
//			_thisNode.bind('click' ,function(){
//				
//				if(!_thisNode.hasClass('current'))_thisNode.addClass('current');	
//			});
//		});
		$('#commentHistory').click(function(){
			getInfoList($(this).attr('id'));
		});
		$('#downloadHistory').click(function(){
			getInfoList($(this).attr('id'));
		});
		$('#updateHistory').click(function(){
			getInfoList($(this).attr('id'));
		});
	});
	function getInfoList(nodeId)
	{
		if(!$('#' + nodeId).hasClass('current')){
			$.getJSON(urlList[nodeId], function(data){
				$('#userPanelListTb').html(data.html);
			});
			$('.userPanelListBody ul li a').removeClass('current');
			$('#' + nodeId).addClass('current');
			$('#listName').val(nodeId);
		}
	}
	function ajax_page(p)
	{
		var listName = $('#listName').val();
		$.getJSON(urlList[listName], { 'p':p } ,function(data){
			$('#userPanelListTb').html(data.html);
		});
	}
</script>
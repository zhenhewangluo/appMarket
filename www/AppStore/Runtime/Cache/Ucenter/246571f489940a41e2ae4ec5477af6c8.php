<?php if (!defined('THINK_PATH')) exit();?><div class="userbar f_l">
	<div class="user_bar_t"></div>
	<div class="user_bar_c">
		<dl class="user_info_box">
			<dt class="user_avatar_box">
				<img id="userHead" src="__ROOT__/<?php echo ($webUhead[1]); ?>" />
			</dt>
			<dd><a href="<?php echo U('Ucenter-Panel/updatePass');?>">修改密码</a></dd>
		</dl>
	</div>
	<div class="user_bar_b"></div>
</div>
<script type="text/javascript">
	$(function(){
		$('.user_box_nav > li').each(function(e){
			var thisNode = $(this);
			thisNode.hover(function(){
				if(thisNode.attr('class') != 'current')
				{
					thisNode.addClass('current_hover');
				}
			}, function(){
				if(thisNode.attr('class') != 'current')
				{
					thisNode.removeClass('current_hover');
				}
			});
		});
	});
</script>
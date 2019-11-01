<?php if (!defined('THINK_PATH')) exit();?><?php if(($webUid)  >  "0"): ?><div class="userBar">
		<div class="content">
			<a id="pageTop"></a>
			<div class="image">
				<a href="<?php echo U('Ucenter-Panel/userinfo');?>" title="">
					<img id="topUserImage" src="__ROOT__/<?php echo ($webUhead[1]); ?>" alt="">
				</a>
				<a href="<?php echo U('Ucenter-Panel/userinfo');?>"><?php echo ($webUname); ?></a>
			</div>
			<ul class="links">
				<li class="last"><a id="exitLink" class="exit" href="<?php echo U('Ucenter-Index/logout');?>">退 出</a></li>
			</ul>
		</div>
	</div><?php endif; ?>
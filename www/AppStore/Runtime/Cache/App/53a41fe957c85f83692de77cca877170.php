<?php if (!defined('THINK_PATH')) exit();?><div class="commentBody">
	<?php if(is_array($appComment['appList'])): $i = 0; $__LIST__ = $appComment['appList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><div class="comments">
			<div class="user">
				<img class="market" src="__ROOT__/<?php echo ($webUhead[2]); ?>" alt="" /><br />
				<?php echo ($vo['user_name']); ?>
			</div>
			<div class="bubble">
				<div class="text">
					<div class="stars whiteStars"><div class="RatingStarsWhite rating-<?php echo round($grade_point * 2);?>"></div></div>
					<br/>
					<span class="date"><?php echo (date('Y年m月d日',strtotime($vo['create_time']))); ?></span>
					<p><?php echo keyWordFilter($vo['content']);?></p>
				</div>
			</div>
		</div>
		<div class="clear"></div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<div class="commentBottom"></div>
<?php if((trim($appComment['pageShow']))  !=  ""): ?><table class="pager" style="width:300px;" cellspacing="0">
	<tr>
		<td class="cl"></td>
		<td>跳转至：</td>
		<td>
			<?php echo ($appComment['pageShow']); ?>
		</td>
		<td class="cr"></td>
	</tr>
</table><?php endif; ?>
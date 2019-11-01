<?php if (!defined('THINK_PATH')) exit();?><h2>用户评论</h2>
<div class="bars">
	<?php if(is_array($appScore)): $i = 0; $__LIST__ = $appScore;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php if(($key)  !=  "0"): ?><div class="row">
				<div class="stars reviewStars"><div class="ReviewInactiveStars rating-<?php echo ($key); ?>"></div> </div>
				<div class="colorBar">
					<?php if(($approw['app_starcount'])  >  "0"): ?><div class="bar" style="width:<?php echo round($appScore[$key]/$approw['app_starcount'], 2) *146;?>px; background:#8eb528;"></div>
						<?php else: ?>
						<div class="bar" style="width:0px;"></div><?php endif; ?>
				</div>
			</div><?php endif; ?><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<div class="average">
	<div>平均评分</div>
	<?php $grade_point = ($approw['app_starcount'] > 0 ) ? round($approw['app_score_www']/$approw['app_starcount'], 2) : 0;?>
	<div class="avgNumber"><?php echo ($grade_point); ?></div>
	<div class="stars darkStars">
		<div class="RatingStarsDark rating-<?php echo round($grade_point * 2);?>"> </div> 
		<div class="number">(<?php echo (number_format($approw['app_starcount'])); ?>)</div>
	</div>
</div>
<div class="rate">
	<div class="label">您的评分</div>

	<div class="stars darkStars"><div class="RatingStarsDark rating-<?php if(isset($userScore))echo $userScore*2; else echo 0;;?>"></div></div>  
</div>
<div class="clear"></div>
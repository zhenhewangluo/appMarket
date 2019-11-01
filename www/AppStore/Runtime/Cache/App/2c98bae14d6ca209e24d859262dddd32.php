<?php if (!defined('THINK_PATH')) exit();?><?php if(($webUid)  <=  "0"): ?><div class="comments">
		<div class="newComment">您的评论:</div>
		<p>
			您必须先<a href="<?php echo U('Ucenter-Index/login', array('acttype' => 'market'));?>">登录</a>或 <a href="<?php echo U('Ucenter-Index/reg', array('acttype' => 'market'));?>">注册</a>一个账号<br/>才能评论              </p>
		<p>
		</p>
	</div>
<?php else: ?>
	<div class="comments">
		<div class="newComment">您的评论:</div>
		<div class="user">
			<a href="javascript:void(0);" title="">
				<img src="__ROOT__/<?php echo ($webUhead[2]); ?>" style="width:65px; height:70px" alt="">
			</a><br>
			<a href="javascript:void(0);" title=""><?php echo ($webUname); ?></a><br>
		</div>
		<div class="bubble">
			<div class="topError" id="addCommentError" style="display:none">
			</div>
			<div class="text">
				<span class="dateRight"></span>
				<b>您的评分:</b>
				<form id="formAppComment" action="#" method="post">
					<ul id="ratingStars" class="rating">
						<li class="one"><a href="#" onclick="setAppCommentRating(1); return false;">&nbsp;</a></li>
						<li class="two"><a href="#" onclick="setAppCommentRating(2); return false;">&nbsp;</a></li>
						<li class="three"><a href="#" onclick="setAppCommentRating(3); return false;">&nbsp;</a></li>
						<li class="four"><a href="#" onclick="setAppCommentRating(4); return false;">&nbsp;</a></li>
						<li class="five"><a href="#" onclick="setAppCommentRating(5); return false;">&nbsp;</a></li>
					</ul>
					<p id="ratingError" class="error" style="display:none"></p>
					<p>
						<input type="hidden" id="rating" name="rating" value="">
						<textarea id="comment" name="comment" class="textbox" cols="50" rows="10"></textarea>
						<span id="commentError" class="error"></span>
					</p>
					<p class="submit">
						<input id="submit" type="button" class="btnSubmit" value="提交评论">
					</p>
				</form>
			</div>
		</div>
	</div><?php endif; ?>
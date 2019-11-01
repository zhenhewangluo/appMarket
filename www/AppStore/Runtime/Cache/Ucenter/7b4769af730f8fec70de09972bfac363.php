<?php if (!defined('THINK_PATH')) exit();?><script type='text/javascript'>
	$(function(){
        var time = parseInt('<?php echo ($waitSecond); ?>');
        var interval = setInterval(function(){
            if(time>0){
                $('#light').html(time--);
            } else{
				clearInterval(interval);
				window.location.href = "<?php echo ($jumpUrl); ?>";
            }
        },1000);
    });
</script>

<div class="content">
	<div class="httpError">
		<div class="left">
			<img src="../Public/images/picSuccess.png" alt="">
		</div>
		<div class="right">
			<h1><?php echo ($message); ?></h1>	
			<?php if(isset($message)): ?><p>
					<?php echo ($msgTitle); ?>
				</p><?php endif; ?>
			<ul>
				<?php if(isset($error)): ?><li><?php echo ($error); ?></li><?php endif; ?>
				<?php if(isset($closeWin)): ?><li>系统将在 <span id="light" style="float:none;color:blue;font-weight:bold"><?php echo ($waitSecond); ?></span> 秒后自动关闭，如果不想等待,直接点击 <a href="<?php echo ($jumpUrl); ?>">这里</a> 关闭</li><?php endif; ?>
				<?php if(!isset($closeWin)): ?><li>系统将在 <span id="light" style="float:none;color:blue;font-weight:bold"><?php echo ($waitSecond); ?></span> 秒后自动跳转,如果不想等待,直接点击 <a href="<?php echo ($jumpUrl); ?>">这里</a> 跳转</li><?php endif; ?>
			</ul>
			<p>
				&nbsp;<br>
				返回 <a href="/">***</a>.
			</p>
		</div>
	</div>
</div>
<?php
session_start();
if(!empty($_SESSION['uid'])&&!empty($_SESSION['username'])){
	echo '<hr>';
	echo '<a href="yunku_userinfo.php">用户信息接口</a><br>';
	echo '<hr>';
	echo '<a href="yunku_appdown.php">应用下载信息接口</a><br>';
	echo '<hr>';
	echo '客户端推荐位管理<br>';
	echo '<a href=yunku_rec.php>今日推荐</a><br>';
	echo '<a href=yunku_9995.php>猜你喜欢</a><br>';
	echo '<a href=yunku_3.php>最新上线</a><br>';
	echo '<a href=yunku_rank_soft_week.php>应用类周排行</a><br>';
	echo '<a href=yunku_rank_soft_month.php>应用类月排行</a><br>';
	echo '<a href=yunku_rank_soft_all.php>应用类排行</a><br>';
	echo '<a href=yunku_rank_game_week.php>游戏类周排行</a><br>';
	echo '<a href=yunku_rank_game_month.php>游戏类月排行</a><br>';
	echo '<a href=yunku_rank_game_all.php>游戏类排行</a><br>';
	echo '<a href=yunku_rank_down_week.php>下载周总排行</a><br>';
	echo '<a href=yunku_rank_down_month.php>下载月总排行</a><br>';
	echo '<a href=yunku_rank_down_all.php>下载总排行</a><br>';
	echo '<a href=yunku_category_topic.php>专题前10位</a><br>';
	echo '<a href=yunku_category_game.php>游戏前10位</a><br>';
	echo '<a href=yunku_category_soft.php>应用前10位</a><br>';
	echo '<a href=yunku_search.php>搜索热词</a><br>';
	echo '<hr>';
}else{
	echo "<script>window.location.href='index.php'</script>";
	}

?>
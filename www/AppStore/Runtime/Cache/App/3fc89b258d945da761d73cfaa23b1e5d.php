<?php if (!defined('THINK_PATH')) exit();?><div class="leftAppCol">
	<div id="leftTab">
		<div class="top">
			<ul>
				<?php if((ACTION_NAME)  ==  "appCenterList"): ?><li id="leftTabbtn1" class="btn1"><a href="#" onclick="setTab('leftTab',1,2); return false;">下载排行</a></li>
				<li id="leftTabbtn2" class="btn2A"><a href="#" onclick="setTab('leftTab',2,2); return false;">分类浏览</a></li>
				<?php else: ?>
				<li id="leftTabbtn1" class="btn1A"><a href="#" onclick="setTab('leftTab',1,2); return false;">下载排行</a></li>
				<li id="leftTabbtn2" class="btn2"><a href="#" onclick="setTab('leftTab',2,2); return false;">分类浏览</a></li><?php endif; ?>
			</ul>
		</div>
		<div id="leftTabBody1" class="body" <?php if((ACTION_NAME)  ==  "appCenterList"): ?>style="display:none;"<?php endif; ?>>
			<h2>应用排行</h2>
			<?php if(is_array($hotlist)): $i = 0; $__LIST__ = $hotlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><div class="row">
					<div class="number"><?php echo ($i); ?></div>
					<div class="icon">
						<a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>" title="<?php echo ($vo['app_name']); ?>">
							<img src="<?php echo C('PICTURE_HOST');?><?php echo ($vo['AppDeviceType'][0]['icon']); ?>" style="width:48px; height:48px;" alt="<?php echo ($vo['app_name']); ?>" />
						</a>
					</div>
					<div class="data">
						<div class="title">
							<a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>" title="<?php echo ($vo['app_name']); ?>"><?php echo showCutStr($vo['app_name'], 10);?></a>
						</div>
						<div class="sub">
							<a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>" title="<?php echo ($vo['author_name']); ?>"><?php echo showCutStr($vo['author_name'], 10);?></a>
						</div>
					</div>
					<div class="clear"></div>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
		<div id="leftTabBody2" class="body" <?php if((ACTION_NAME)  !=  "appCenterList"): ?>style="display:none;"<?php endif; ?>>
			<h2><a href="<?php echo U('App-Apps/appCenterList', array('cid' => $appCateTree[1]['id']));?>"><?php echo ($appCateTree[1]['name']); ?></a></h2>
			<ul class="categories">
				<?php $count = count($appCateTree[1]['_child']);;?>				
				<?php if(is_array($appCateTree[1]['_child'])): $i = 0; $__LIST__ = $appCateTree[1]['_child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php if(($i)  ==  $count): ?><li class="last"><a href="<?php echo U('App-Apps/appCenterList?cid=' . $vo['id']);?>"><?php echo ($vo['name']); ?></a></li>
						<?php else: ?>
						<li><a href="<?php echo U('App-Apps/appCenterList?cid=' . $vo['id']);?>"><?php echo ($vo['name']); ?></a></li><?php endif; ?><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>	
			<h2><a href="<?php echo U('App-Apps/appCenterList', array('cid' => $appCateTree[2]['id']));?>"><?php echo ($appCateTree[2]['name']); ?></a></h2>
			<ul class="categories">
				<?php $count2 = count($appCateTree[2]['_child']);;?>
				<?php if(is_array($appCateTree[2]['_child'])): $i = 0; $__LIST__ = $appCateTree[2]['_child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php if(($i)  ==  $count2): ?><li class="last"><a href="<?php echo U('App-Apps/appCenterList?cid=' . $vo['id']);?>"><?php echo ($vo['name']); ?></a></li>
						<?php else: ?>
						<li ><a href="<?php echo U('App-Apps/appCenterList?cid=' . $vo['id']);?>"><?php echo ($vo['name']); ?></a></li><?php endif; ?><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
	</div>
</div>
<div class="leftAppColBGR"></div>
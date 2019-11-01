<?php if (!defined('THINK_PATH')) exit();?><div class="infoLayerContent">
<?php $app_img_list = explode(';', $app['AppDeviceType'][0]['screenshots']);?>
    <div class="appInfo">
        <div class="appInfoHeader">
            <a href="<?php echo U('App-Apps/detail', array('aid' => $app['app_id']));?>" class="appIcon">
                <img src="<?php echo C('PICTURE_HOST');?><?php echo ($app['AppDeviceType'][0]['icon']); ?>" style="width:78px; height:78px;" alt="Icon" />
            </a>
            <h3 class="infoLayerTitle"><a href="<?php echo U('App-Apps/detail', array('aid' => $app['app_id']));?>"><?php echo ($app['app_name']); ?></a></h3>
            <div class="stars darkStars"> <div class="RatingStarsDark rating-<?php echo round($app['app_score_www']*2/($app['app_starcount']+1e-5));?>" ></div>(<?php echo ($app['app_downloads']); ?>)</div>
        </div>
        <div class="appInfoText">
            <p><?php echo ($app['app_desc']); ?></p>
        </div>
        <div style="margin-top:10px;">
            <a class="roundButton" href="<?php echo U('App-Apps/detail', array('aid' => $app['app_id']));?>" ><span style="font-weight:normal">查看详情</span></a>
        </div>
    </div>
    <div id="appScreens" class="appScreens" style="text-align:center">
    	<?php if(is_array($app_img_list)): $i = 0; $__LIST__ = $app_img_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php if(($i)  ==  "1"): ?><img id="screenshot_<?php echo ($i); ?>" src="<?php echo C('PICTURE_HOST');?><?php echo ($vo); ?>" alt="" style="width:140px; height:233px; " />
              <?php else: ?>
       <img id="screenshot_<?php echo ($i); ?>" src="<?php echo C('PICTURE_HOST');?><?php echo ($vo); ?>" alt="" style="width:140px; height:233px;  display:none;" /><?php endif; ?><?php endforeach; endif; else: echo "" ;endif; ?>
        <div class="options">
            <a href="#" onclick="prevScreen(); return false" class="btn_left">&lt;</a><span class="screensPager"><span id="currentScreen">1</span> / <span id="maxScreens"><?php echo (count($app_img_list)); ?></span></span><a href="#" onclick="nextScreen(); return false" class="btn_right">&gt;</a>
        </div>
    </div>
</div>
<img src="../Public/images/infoLayerArrowTop.png" alt="" id="infoLayerArrowTop" />
<img src="../Public/images/infoLayerArrowBottom.png" alt="" id="infoLayerArrowBottom" />
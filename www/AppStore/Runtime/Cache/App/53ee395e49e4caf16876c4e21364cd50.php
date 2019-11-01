<?php if (!defined('THINK_PATH')) exit();?><?php if(($htmlType)  ==  "Normal"): ?><ul class="appsList"><?php endif; ?>

<?php if(is_array($appslist)): $i = 0; $__LIST__ = $appslist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><li>
              <a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>" class="appIcon" id="<?php echo ($rid); ?>-<?php echo ($vo['app_id']); ?>">
                <img src="<?php echo C('PICTURE_HOST');?><?php echo ($vo['icon']); ?>" style="width: 78px; height: 78px;" alt="Icon">
              </a>
              <div class="appText">
				  <a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>" class="title">
                <h3><?php echo ($vo['app_name']); ?></h3>
				</a>
                <p><?php echo showCutStr($vo['app_desc'],100);;?></p>
              </div>
              <div class="appInfo">
                <div class="stars darkStars"><div class="RatingStarsDark rating-<?php echo round($vo['app_score_www']*2/($vo['app_starcount']+1e-5));?>"></div></div>
    <a class="button_normal" href="<?php echo C('PICTURE_HOST');?><?php echo ($vo['apk_path']); ?>" onclick="getApp('<?php echo ($vo['app_id']); ?>');<?php echo ($returnFlase); ?>">下载</a>
              </div>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>	
<?php if(($htmlType)  ==  "Normal"): ?></ul><?php endif; ?>
				<?php if((trim($page))  !=  ""): ?><table class="pager" style="width: 460px;" cellspacing="0">
					<tbody><tr>
							<td class="cl"></td>
							<td>跳转至：</td>
							<td>
								<div class="ajaxpage"> <?php echo ($page); ?></div>
								<!--span class="active">1</span-->
							</td>
							<td class="cr"></td>
						</tr>
					</tbody>
				</table><?php endif; ?>
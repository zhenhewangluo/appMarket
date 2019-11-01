<?php if (!defined('THINK_PATH')) exit();?><div class="headbar">
	<div class="position"><span>系统</span><span>&gt;</span><span>网站管理</span><span>&gt;</span><span>站点设置</span></div>
	<ul class="tab" name="conf_menu">
		<li class=""><a href="javascript:select_tab('index_slides');">首页幻灯设置</a></li>
		<li class=""><a href="javascript:select_tab('search_keywords');">搜索关键词</a></li>
		<li class=""><a href="javascript:select_tab('links_conf');">友链设置</a></li>
	</ul>
</div>
<div class="content_box">
	<div class="content form_content" style="height: 166px; ">
		<form action="<?php echo U('System/slidesUpdate');?>" enctype="multipart/form-data" name="index_slides" method="post">
			<table class='form_table'>
				<col width="150px" />
				<col />
				<tr>
					<th>首页幻灯设置：</th>
					<td>

						<table class='border_table' id='slide_box'>
							<col width="150px" />
							<col width="250px" />
							<col width="250px" />
							<col width="120px" />
							<thead>
							<tr>
								<th>名称</th>
								<th>链接地址</th>
								<th>图片文件</th>
								<th>操作</th>
							</tr>
							</thead>
							<tbody>
								<?php
									$slidesIndex = array();
									if(isset($siteConfContent['slides']) && !empty($siteConfContent['slides']))
									{
										$slidesIndex = unserialize($siteConfContent['slides']);

									}
								?>
								<?php if((count($slidesIndex))  >  "0"): ?><?php if(is_array($slidesIndex)): $i = 0; $__LIST__ = $slidesIndex;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr class='td_c'>
										<td><input type='text' name='slide_name[]' class='small' value="<?php echo ($vo['name']); ?>" pattern='required' /></td>
										<td><input type='text' name='slide_url[]' class='middle' value="<?php echo urldecode($vo['url']);?>" pattern='url' /></td>
										<td>
											<?php
												if(isset($vo['img']) && $vo['img'] != '')
												{
											?>
												<img src="__PUBLIC__<?php echo ($vo['img']); ?>" width="150" /><br />
											<?php
												}
											?>
											<input type='file' name='slide_pic[]' />
											<input type="hidden" value="<?php echo ($vo['img']); ?>" name="slide_img[]" />
										</td>
										<td>
											<img class="operator" src="../Public/images/icon_asc.gif" alt="向上" title='向上' />
											<img class="operator" src="../Public/images/icon_desc.gif" alt="向下" title='向下' />
											<img class="operator" src="../Public/images/icon_del.gif" alt="删除" title='删除' />

										</td>
									</tr><?php endforeach; endif; else: echo "" ;endif; ?>
								<?php else: ?>
								<tr class='td_c'>
									<td><input type='text' name='slide_name[]' class='small' pattern='required' /></td>
									<td><input type='text' name='slide_url[]' class='middle' pattern='url' /></td>
									<td><input type='file' name='slide_pic[]' /><input type="hidden" value="" name="slide_img[]" /></td>
									<td>
										<img class="operator" src="../Public/images/icon_asc.gif" alt="向上" title='向上' />
										<img class="operator" src="../Public/images/icon_desc.gif" alt="向下" title='向下' />
										<img class="operator" src="../Public/images/icon_del.gif" alt="删除" title='删除' />
									</td>
								</tr><?php endif; ?>

							</tbody>

							<tfoot>
							<tr>
								<td colspan='4'>
									<button type='button' class='btn' onclick="add_slide();"><span>添加幻灯</span></button>
								</td>
							</tr>
							</tfoot>
						</table>
						<label>设置首页幻灯片图片与名称</label>
					</td>
				</tr>
				<tr>
					<th></th>
					<td><button type='submit' class='submit'><span>保存幻灯片配置</span></button></td>
				</tr>
			</table>
		</form>
		<form action="<?php echo U('System/keywordsUpdate');?>" enctype="multipart/form-data" name="search_keywords" method="post">
			<table class="form_table">
				<colgroup>
					<col width="150px">
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th>关键词设置：</th>
						<td>
						<table class="border_table" id="keywords_box">
							<colgroup>
								<col width="120px">
								<col width="120px">
								<col width="250px">
								<col width="120px">
								<col width="120px">
								<col width="100px">
							</colgroup>
							<thead>
								<tr>
									<th>关键词</th>
									<th>Class</th>
									<th>样式</th>
									<th>颜色</th>
									<th>边框颜色</th>
									<th>操作</th>
								</tr>
							</thead>
							<tbody>
								<?php if(is_array($keywordsXml)): $i = 0; $__LIST__ = $keywordsXml;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr class="td_c"><td><input type="text" name="keyWordsName[]" class="small" pattern="required" alt="请填写关键词" value="<?php echo ($vo['value']); ?>" /></td><td><input type='text' name='keyWordsClass[]' class='small' value="<?php echo ($vo['attributes']['CLASS']); ?>" /></td><td><input type='text' name='keyWordsStyle[]' class='middle' value="<?php echo ($vo['attributes']['STYLE']); ?>" /></td><td><input type='text' name='keyWordsColor[]' class='small' value="<?php echo ($vo['attributes']['COLOR']); ?>" /></td><td><input type='text' name='keyWordsHiColor[]' class='small' value="<?php echo ($vo['attributes']['HICOLOR']); ?>" /></td><td><img class="operator" src="../Public/images/icon_asc.gif" alt="向上" title="向上" /><img class="operator" src="../Public/images/icon_desc.gif" alt="向下" title="向下" /><img class="operator" src="../Public/images/icon_del.gif" alt="删除" title="删除" /></td></tr><?php endforeach; endif; else: echo "" ;endif; ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="6">
										<button type="button" class="btn" onclick="add_keywords();"><span>添加关键词</span></button>
									</td>
								</tr>
							</tfoot>
						</table>
						<label>设置搜索关键词</label>
					</td>
				</tr>
				<tr>
					<th></th>
					<td><button type="submit" class="submit" id="linkSubmit"><span>保存关键词设置</span></button></td>
				</tr>
			</tbody>
			</table>
		</form>
		<form action="<?php echo U('System/linksUpdate');?>" enctype="multipart/form-data" name="links_conf" method="post">
			<table class="form_table">
				<colgroup>
					<col width="150px">
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th>友情链接设置：</th>
						<td>
						<table class="border_table" id="guide_box">
							<colgroup>
								<col width="150px">
								<col width="250px">
								<col width="120px">
							</colgroup>
							<thead>
								<tr>
									<th>名称</th>
									<th>链接地址</th>
									<th>操作</th>
								</tr>
							</thead>
							<tbody>
								<?php if(is_array($siteConfContent['links'])): $i = 0; $__LIST__ = $siteConfContent['links'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr class="td_c"><td><input type="text" name="linkName[]" class="small" pattern="required" alt="请填写友链名称" value="<?php echo ($vo['name']); ?>" /></td><td><input type="text" name="linkUrl[]" class="middle" pattern="url" alt="请填写URL，如：" value="<?php echo ($vo['url']); ?>" /></td><td><img class="operator" src="../Public/images/icon_asc.gif" alt="向上" title="向上" /><img class="operator" src="../Public/images/icon_desc.gif" alt="向下" title="向下" /><img class="operator" src="../Public/images/icon_del.gif" alt="删除" title="删除" /></td></tr><?php endforeach; endif; else: echo "" ;endif; ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3">
										<button type="button" class="btn" onclick="add_guide();"><span>添加友链</span></button>
									</td>
								</tr>
							</tfoot>
						</table>
						<label>设置友情链接</label>
					</td>
				</tr>
				<tr>
					<th></th>
					<td><button type="submit" class="submit" id="linkSubmit"><span>保存友链设置</span></button></td>
				</tr>
			</tbody>
			</table>
		</form>
	</div>
</div>

<script type="text/javascript">
//	$(function(){
//		$('#linkSubmit').click(function(){
//			var linkName = $('input:[name="link_name[]"]').val();
//			realAlert(linkName);
//			return false;
//		});
//	});
	//添加导航
	function add_guide()
	{
		var nodeValue =  "<tr class='td_c'>"
						+"<td><input type='text' name='linkName[]' class='small' pattern='required' alt='请填写友链名称' /></td>"
						+"<td><input type='text' name='linkUrl[]' class='middle' pattern='url' alt='请填写URL，如：' /></td>"
						+"<td>"
						+"<img class='operator' src='../Public/images/icon_asc.gif' alt='向上' title='向上' />"
						+"<img class='operator' src='../Public/images/icon_desc.gif' alt='向下' title='向下' />"
						+"<img class='operator' src='../Public/images/icon_del.gif' alt='删除' title='删除' />"
						+"</td>"
						+"</tr>";

		$('#guide_box tbody').append(nodeValue);
		var last_index = $('#guide_box tbody tr').size()-1;
		buttonInit(last_index);
	}
	function add_slide()
	{
		var nodeValue =  "<tr class='td_c'>"
						+"<td><input type='text' name='slide_name[]' class='small' pattern='required' /></td>"
						+"<td><input type='text' name='slide_url[]' class='middle' pattern='url' /></td>"
						+"<td><input type='file' name='slide_pic[]' />"
						+'<input type="hidden" value="" name="slide_img[]" /></td>'
						+"<td>"
						+"<img class='operator' src='../Public/images/icon_asc.gif' alt='向上' title='向上' />"
						+"<img class='operator' src='../Public/images/icon_desc.gif' alt='向下' title='向下' />"
						+"<img class='operator' src='../Public/images/icon_del.gif' alt='删除' title='删除' />"
						+"</td>"
						+"</tr>";

		$('#slide_box tbody').append(nodeValue);
		var last_index = $('#slide_box tbody tr').size()-1;
		buttonInit(last_index,'#slide_box');
	}
	function add_keywords()
	{
		var nodeValue =  "<tr class='td_c'>"
						+"<td><input type='text' name='keyWordsName[]' class='small' pattern='required' alt='请填写关键词' /></td>"
						+"<td><input type='text' name='keyWordsClass[]' class='small' /></td>"
						+"<td><input type='text' name='keyWordsStyle[]' class='middle' /></td>"
						+"<td><input type='text' name='keyWordsColor[]' class='small' /></td>"
						+"<td><input type='text' name='keyWordsHiColor[]' class='small' /></td>"
						+"<td>"
						+"<img class='operator' src='../Public/images/icon_asc.gif' alt='向上' title='向上' />"
						+"<img class='operator' src='../Public/images/icon_desc.gif' alt='向下' title='向下' />"
						+"<img class='operator' src='../Public/images/icon_del.gif' alt='删除' title='删除' />"
						+"</td>"
						+"</tr>";

		$('#keywords_box tbody').append(nodeValue);
		var last_index = $('#keywords_box tbody tr').size()-1;
		buttonInit(last_index);
	}
	//操作按钮绑定
	function buttonInit(indexValue,ele){
		ele = ele || "#keywords_box";
		if(indexValue == undefined || indexValue === ''){
			var button_times = $(ele+' tbody tr').length;

			for(var item=0;item < button_times;item++){
				buttonInit(item,ele);
			}
		} else {
			var obj = $(ele+' tbody tr:eq('+indexValue+') .operator')

			//功能操作按钮
			obj.each(function(i){
				switch(i){
					//向上排序
					case 0:{
						$(this).click(function(){
							var insertIndex = $(this).parent().parent().prev().index();
							if(insertIndex >= 0){
								$(ele+' tbody tr:eq('+insertIndex+')').before($(this).parent().parent());
							}
						});
					}
					break;

					//向上排序
					case 1:{
						$(this).click(function(){
							var insertIndex = $(this).parent().parent().next().index();
							$(ele+' tbody tr:eq('+insertIndex+')').after($(this).parent().parent());
						});
					}
					break;

					//删除排序
					case 2:{
						$(this).click(function(){
							var obj = $(this);
							art.dialog.confirm('确定要删除么？',function(){obj.parent().parent().remove()});
						});
					}
					break;
				}
			});
		}
	}
	//滑动门
	function select_tab(indexVal){
		//设置默认值
		if(indexVal == ''){
			indexVal = 'index_slides';
		}

		var formObj  = $('form[name="'+indexVal+'"]');
		var li_index = $('form').index(formObj);

		//切换form
		$('form').hide();
		$('form:eq('+li_index+')').show();

		//切换li
		$('ul[name="conf_menu"] li').attr('class','');
		$('ul[name="conf_menu"] li:eq('+li_index+')').attr('class','selected');
	}

	//默认系统定义
	select_tab("");
//	show_watermark(0);
//	show_mail(1);

	//当导航栏目为空时发生错误
	if($('#guide_box tbody tr').size() == 0){
		add_keywords();
	}

	buttonInit();
	buttonInit(undefined,'#guide_box');
	buttonInit(undefined,'#slide_box');

</script>
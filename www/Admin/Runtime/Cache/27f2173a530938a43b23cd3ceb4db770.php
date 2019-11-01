<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" src="__PUBLIC__/js/kindeditor/kindeditor-min.js"></script>
<script type="text/javascript">
	var editor;
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="app_desc"]', {
			allowFileManager : false,
			allowImageUpload : false,
			allowFlashUpload : false,
			allowMediaUpload : false,
			allowFileUpload : false,
			items : [
				'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste',
				'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
				'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
				'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
				'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
				'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image',
				'flash', 'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak',
				'anchor', 'link', 'unlink', '|', 'about'
			]
		});
	});
</script>
<div class="headbar clearfix">
	<div class="position"><span>应用</span><span>></span><span>应用管理</span><span>></span><span>修改应用</span></div>
	<!--<ul class="tab" name="menu1">
		<li id="li_1" class="selected"><a href="javascript:void(0)" hidefocus="true" onclick="select_tab('1')">商品信息</a></li>
		<li id="li_2"><a href="javascript:void(0)" hidefocus="true" onclick="select_tab('2')">描述</a></li>
		<li id="li_3"><a href="javascript:void(0)" hidefocus="true" onclick="select_tab('3')">营销选项</a></li>
	</ul>-->
</div>
<div class="content_box">
	<div class="content form_content">
		<form name="myform" id="myform" action="<?php echo U('AppStore://Apps/appEditAction');?>" method="post">
			{__NOTOKEN__}
			<input type="hidden" name="app_id" id="app_id" value="<?php echo ($appinfo['app_id']); ?>" />
			<input type="hidden" name="relationid" value="<?php echo ($appinfo['relationid']); ?>" />
			<table class="form_table">
				<col width="150px" />
				<col />
				<tr>
					<th>应用名称：</th>
					<td>
						<input class="normal" name="app_name"  id ="app_name" type="text" value="<?php echo ($appinfo['app_name']); ?>" pattern="required" alt="应用名称不能为空" onblur="validateApp()" />
						<label id="unique">*</label>
						<input name="goods_id" type="hidden" value="<?php echo ($goods_id); ?>" />
					</td>
				</tr>
				<tr>
					<th>应用版本：</th>
					<td>
						<input class="normal" name="app_version" type="text" value="<?php echo ($appinfo['app_version']); ?>" pattern="required" alt="应用版本不能为空" />
						<label>*</label>
					</td>
				</tr>
				<tr>
					<th>应用类型：</th>
					<td style="position:relative;">
						<select class="normal" name="apptype"  id ="apptype" onchange="updateCateSelect()">
							<?php if(is_array($apptypelist)): $i = 0; $__LIST__ = $apptypelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><option value="<?php echo ($vo['id']); ?>"  pattern="required" alt="应用类型不能为空" <?php if($appinfo['apptype'] == $vo['id']): ?>selected="selected"<?php endif; ?>><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
						<label>*</label>
					</td>
				</tr>
				<tr>
					<th>所属分类：</th>
					<td style="position:relative;">
						<select class="normal" name="appcate" id ="appcate" >
							<?php if(is_array($appcatelist)): $i = 0; $__LIST__ = $appcatelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><option value="<?php echo ($vo['id']); ?>"  pattern="required" alt="应用分类不能为空" <?php if($appinfo['appcate'] == $vo['id']): ?>selected="selected"<?php endif; ?>><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
						<label>*</label>
					</td>
				</tr>
				<tr>
					<th>作者：</th>
					<td style="position:relative;">
						<input class="normal" name="app_author" type="text" value="<?php echo ($appinfo['author_name']); ?>" />
						<label>*</label>
					</td>
				</tr>
				<tr>
					<th>应用价格：</th>
					<td>
						<input class="normal" name="app_price" type="text" value="<?php echo ($appinfo['app_price']); ?>" /><label></label>
					</td>
				</tr>
				<tr>
					<th>应用简述：</th>
					<td>
						<input class="normal" name="app_slogan" type="text" value="<?php echo ($appinfo['app_slogan']); ?>" /><label></label>
					</td>
				</tr>
				<tr>
					<th>最小SDK版本：</th>
					<td>
						<div id="sdk_td">
							<input class="normal" name="sdk_version"  id="sdk_version" type="text" value="<?php echo ($appinfo['sdk_version']); ?>"/>
							<img id="sdk_select_arrow" src="../Public/images/select.gif" /><label></label>
							<ul id="sdklist" style="display:none;">
								<?php if(is_array($sdklist)): $i = 0; $__LIST__ = $sdklist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><li> <?php echo ($vo['sdk_version']); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
							</ul>
						</div>
					<!--	<select class="normal" name="sdk_version_sel" id="sdklist" style="display:none;">
							<?php if(is_array($sdklist)): $i = 0; $__LIST__ = $sdklist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><option class="sdk_list_val" value="<?php echo ($vo['sdk_version']); ?>"><?php echo ($vo['sdk_version']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select> -->
					</td>
				</tr>
				<tr>
					<th>应用大小：</th>
					<td>
						<input type="text" name="app_size" class="normal" value="<?php echo ($apkimglist['app_size']); ?>" />
					</td>
				</tr>
				<tr>
					<th>是否推荐：</th>
					<td class="tLeft" >
					    <label>
						<?php if($appinfo['app_isrecommon'] == 1): ?><input type="radio" name="app_isrecommon" value="1" checked="checked" />是
						<?php else: ?>
						<input type="radio" name="app_isrecommon" value="1" />是<?php endif; ?>
					    </label>
					    <label>
						<?php if($appinfo['app_isrecommon'] == 0): ?><input type="radio" name="app_isrecommon" value="0" checked="checked" />否
						<?php else: ?>
						<input type="radio" name="app_isrecommon" value="0" />否<?php endif; ?>
					    </label>
					</td>					
				</tr>	
				<tr>
					<th>是否可见：</th>
					<td class="tLeft" >
					    <label>
						<?php if($appinfo['app_visible'] == 1): ?><input type="radio" name="app_visible" value="1" checked="true" />是
						<?php else: ?>
						<input type="radio" name="app_visible" value="1" />是<?php endif; ?>
					    </label>
					    <label>
						<?php if($appinfo['app_visible'] == 0): ?><input type="radio" name="app_visible" value="0" checked="true" />否
						<?php else: ?>
						<input type="radio" name="app_visible" value="0" />否<?php endif; ?>
					    </label>
					</td>						
				</tr>	
				<tr>
					<th>是否是英文版：</th>
					<td class="tLeft" >
					    <label>
						<?php if($appinfo['is_english'] == 1): ?><input type="radio" name="is_english" value="1" checked="true" />是
						<?php else: ?>
						<input type="radio" name="is_english" value="1" />是<?php endif; ?>
					    </label>
					    <label>
						<?php if($appinfo['is_english'] == 0): ?><input type="radio" name="is_english" value="0" checked="true" />否
						<?php else: ?>
						<input type="radio" name="is_english" value="0" />否<?php endif; ?>
					    </label>
					</td>	
				</tr>
				<tr>
					<th>icon：</th>
					<td>
						<button style="display:inline-block" onclick="return check_dir(0, 'icon');">上传icon</button>
						<div id="apk_icon">
							<?php if((isset($apkimglist['icon']))  ==  "true"): ?><img src="<?php echo __ROOT__.C('UPLOAD_APP_PATH').$apkimglist['icon'];?>" width="110px" />
								<span onclick="del_file('<?php echo ($apkimglist['icon']); ?>', this, 'icon')">删除<input type="hidden" name="apk_icon" value="<?php echo ($apkimglist['icon']); ?>" /></span><?php endif; ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>apk信息：</th>
					<td>
						<label>
							<input type="checkbox" name="screen_focus[]" value="1" onclick="show_upload_btn(1);"
								<?php if($screen[1] == 1): ?>checked="checked"<?php endif; ?> />自适应
							<button class="hide_button button_1" <?php if($screen[1] == 1): ?>style="display:inline-block"<?php endif; ?> onclick="return check_dir(1, 'apk');">上传apk</button>
						</label>
						<div class="apk_info">
							<div id="apk_file_1">
								<?php if((isset($apkimglist[1]['apk_path']))  ==  "true"): ?><span><?php echo ($apkimglist[1]['apk_name']); ?></span><span <?php if($screen[1] == 1): ?>style="display:block"<?php else: ?> style="display:none"<?php endif; ?> onclick="del_file('<?php echo ($apkimglist[1]['apk_path']); ?>', this, 'apk')">删除<input type="hidden" name="apk_1_file" value="<?php echo ($apkimglist[1]['apk_path']); ?>" /></span><?php endif; ?>
							</div>
						</div>
						<label>
							<input type="checkbox" name="screen_focus[]" value="2" onclick="show_upload_btn(2);"
								<?php if($screen[2] == 1): ?>checked="checked"<?php endif; ?> />240x320 
							<button class="hide_button button_2" <?php if($screen[2] == 1): ?>style="display:inline-block"<?php endif; ?> onclick="return check_dir(2, 'apk');">上传apk</button>
						</label>
						<div class="apk_info">
							<div id="apk_file_2">
								<?php if((isset($apkimglist[2]['apk_path']))  ==  "true"): ?><span><?php echo ($apkimglist[2]['apk_name']); ?></span><span <?php if($screen[2] == 1): ?>style="display:block"<?php else: ?> style="display:none"<?php endif; ?> onclick="del_file('<?php echo ($apkimglist[2]['apk_path']); ?>', this, 'apk')">删除<input type="hidden" name="apk_2_file" value="<?php echo ($apkimglist[2]['apk_path']); ?>" /></span><?php endif; ?>
							</div>
						</div>
						<label>
							<input type="checkbox" name="screen_focus[]" value="3" onclick="show_upload_btn(3);" 
								<?php if($screen[3] == 1): ?>checked="checked"<?php endif; ?> />320x480 
							<button class="hide_button button_3"  <?php if($screen[3] == 1): ?>style="display:inline-block"<?php endif; ?> onclick="return check_dir(3, 'apk');">上传apk</button>
						</label>
						<div class="apk_info">
							<div id="apk_file_3">
								<?php if((isset($apkimglist[3]['apk_path']))  ==  "true"): ?><span><?php echo ($apkimglist[3]['apk_name']); ?></span><span <?php if($screen[3] == 1): ?>style="display:block"<?php else: ?> style="display:none"<?php endif; ?> onclick="del_file('<?php echo ($apkimglist[3]['apk_path']); ?>', this, 'apk')">删除<input type="hidden" name="apk_3_file" value="<?php echo ($apkimglist[3]['apk_path']); ?>" /></span><?php endif; ?>
							</div>
						</div>
						<label>
							<input type="checkbox" name="screen_focus[]" value="4" onclick="show_upload_btn(4);" 
								<?php if($screen[4] == 1): ?>checked="checked"<?php endif; ?> />480x800 
							<button class="hide_button button_4" <?php if($screen[4] == 1): ?>style="display:inline-block"<?php endif; ?> onclick="return check_dir(4, 'apk');">上传apk</button>
						</label>
						<div class="apk_info">
							<div id="apk_file_4">
								<?php if((isset($apkimglist[4]['apk_path']))  ==  "true"): ?><span><?php echo ($apkimglist[4]['apk_name']); ?></span><span <?php if($screen[4] == 1): ?>style="display:block"<?php else: ?> style="display:none"<?php endif; ?> onclick="del_file('<?php echo ($apkimglist[4]['apk_path']); ?>', this, 'apk')">删除<input type="hidden" name="apk_4_file" value="<?php echo ($apkimglist[4]['apk_path']); ?>" /></span><?php endif; ?>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<th>截图信息：</th>
					<td>
						<button style="display:inline-block" onclick="return uploadApk(0, 'screenshot');">上传截图</button>
						<ul id="apk_image_view">
						<?php if(is_array($apkimglist['screenshots'])): $i = 0; $__LIST__ = $apkimglist['screenshots'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><li>
								<img src="<?php echo __ROOT__.C('UPLOAD_APP_PATH').$vo;?>" width="110px" />
								<span onclick="del_file('<?php echo ($vo); ?>', this, 'screenshots')">删除<input type="hidden" name="apk_image[]" value="<?php echo ($vo); ?>" /></span>
							</li><?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</td>
				</tr>
				<tr>
					<th>详细介绍：</th>
					<td>
						<textarea name="app_desc" style="width:800px;height:400px;visibility:hidden;"><?php echo ($appinfo['app_desc']); ?></textarea><label>大小写字母、数字、横线</label>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="submit" id="submit_app" value="确认" />
					</td>
				</tr>
				
			</table>
		</form>
		<script type="text/javascript">
			var appId = $("#app_id").val(); //edit id 已存在
			function show_upload_btn(type)
			{
				$('.button_'+type).toggle();
			}
			function check_dir(device_type, dir_type)
			{
				$.getJSON('<?php echo U("Admin://Ajax/checkDir");?>',{'appId' : appId, 'dir_type' : dir_type, 'type': device_type} , function(data){
					if(data.status == -1)
					{
						$.dialog({
							lock: true,
							content: data.msg,
							icon: 'error',
							ok: function () {
								clear_dir(device_type, dir_type);
								//uploadApk(device_type, dir_type); 
							},
							no:function(){
							}
						});
					}
					else
					{
						uploadApk(device_type, dir_type); 
					}
				});
				return false;
			}
			function clear_dir(device_type, dir_type)
			{
				$.getJSON('<?php echo U("Admin://Ajax/clear_dir");?>',{'appId' : appId, 'dir_type' : dir_type, 'type': device_type} , function(data){
					if(data.status == 1)
					{
						if(dir_type == 'apk')
						{
							$('#apk_file_'+ device_type + ' span').remove();
						}
						if(dir_type == 'screenshot')
						{
							$('#apk_image_view li').remove();
						}
						if(dir_type == 'icon')
						{
							$('#apk_icon span').remove();
						}
						$.dialog({
							lock: true,
							content: data.msg,
							icon: 'succeed',
							time: 3,
							ok: function () {
//								uploadApk(device_type, dir_type);
								//uploadApk(device_type, dir_type); 
							},
							close:function(){
								uploadApk(device_type, dir_type);
							}
						});
						
					}
					else
					{
						alert(data.msg);
					}
				});
				return false;
			}
			function uploadApk(device_type, dir_type)
			{
				var type = device_type+ '_' + appId + '_' + dir_type;
				var base_url = "<?php echo __ROOT__.C('UPLOAD_APP_PATH');?>";
				$.dialog.open('<?php echo U("Admin://Apps/appUpload?type=");?>'+type,{
					width:'700px',
					height:'395px',
					title: '上传APK',
					ok: function(iframeWin, topWin){
						//alert('123');return ;
						// iframeWin: 对话框iframe内容的window对象
						// topWin: 对话框所在位置的window对象
						var file_list = iframeWin.$('#filelist_'+device_type).val();
						if(file_list != '' && file_list != undefined)
						{
							var file_info = file_list.split(';');
							$('#apk_file_'+device_type).append('<span>'+file_info[1]+'</span><span style="display:block" onclick="del_file(\''+file_info[0]+'\', this, \'apk\')">删除<input type="hidden" name="apk_'+device_type+'_file" value="'+file_info[0]+'" /></span>');
						}
						var image_list = iframeWin.$('.imagelist');
						for(var i=0; i < image_list.length; i++)
						{
							var thisNode = $(image_list[i]);
							$('#apk_image_view').append('<li>'+'<img src="'+base_url+thisNode.val()+'" width="110px" /><span onclick="del_file(\''+thisNode.val()+'\', this, \'screenshots\')">删除<input type="hidden" name="apk_image[]" value="'+thisNode.val()+'" /></span></li>');
						}
						var icon_info = iframeWin.$('.icon_info').val();
						if(icon_info != '' && icon_info != undefined)
						{
							$('#apk_icon').append('<img src="'+base_url+icon_info+'" width="110px" /><span onclick="del_file(\''+icon_info+'\', this, \'icon\')">删除<input type="hidden" name="apk_icon" value="'+icon_info+'" /></span>');
						}
					}
				});
				return false;
			}
			function del_file(src, thisNode, type)
			{
				$.dialog({
					lock: true,
					content: "确认删除该文件吗？如果确认将立刻删除文件和数据库信息！",
					icon: 'error',
					ok: function () {
						$.getJSON("<?php echo U('Admin://Upload/delTempFile');?>",{'path' : src,'appId' : appId, 'type' : type}, function(data){
							alert(data.msg);
							if(data.status != -1)
							{
								if(type == 'apk')
									$(thisNode).parent().children('span').remove();
								else
									$(thisNode).parent().children('img').remove();
								$(thisNode).remove();
							}
						});
					}
				});
			}
			//判断APP名称是否重复
			function validateApp(){
				var nameVal = $("#app_name").val();
				$.post("<?php echo U('AppStore://Ajax/validAppName');?>", { "name": nameVal,"id":"<?php echo ($appinfo['app_id']); ?>"},
				function(data){
					if(data.name == 1 ){
						//alert("此分类已经使用！");
						$('#app_name').removeClass('valid-text');
						$('#unique').removeClass('valid-msg');
						$('#app_name').addClass('invalid-text');
						$('#unique').addClass('invalid-msg');
						$('#unique').html('此应用名称已经使用！');
						return false;
					}              
				}, "json");

				return true;
			}	
			function updateCateSelect(){
				var idVal = $("#apptype").val();
				$.post("<?php echo U('AppStore://Ajax/updateCateSelect');?>", { "id": idVal}, function(data){
					var obj=document.getElementById('appcate');
					obj.options.length=0;
					for(var i=0;i<data.list.length;i++ ){

						obj.options.add(new Option(data.list[i].name,data.list[i].id)); //这个兼容IE与firefox

					}

				}, "json");

				return true;
			}
			$(function(){
				var listNode = $('#sdklist');
				var inputNode = $('#sdk_version');
				var arrowNode = $('#sdk_select_arrow');
				inputNode.click(function(){
					listNode.show();
				});

				arrowNode.click(function(){
					listNode.toggle();

				});
				$('#sdklist li').each(function(){
					$(this).click(function(){
						listNode.hide();
						inputNode.val($(this).html());
					});
				});
				$(document).click(function (event) {
					var e = event || window.event; //兼容ie和非ie的event
					var aim = e.srcElement || e.target; //兼容ie和非ie的事件源
					if (aim.id != listNode.attr('id') && aim.id != inputNode.attr('id') && aim.id != arrowNode.attr('id'))
					{		
						listNode.hide();
					}
				});
			});
		</script>
	</div>
</div>
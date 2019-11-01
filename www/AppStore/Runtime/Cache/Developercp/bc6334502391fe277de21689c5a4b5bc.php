<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title></title>
		<link rel="stylesheet" type="text/css" href="../Public/css/admin.css" />
		<script type='text/javascript' src="__PUBLIC__/js/jquery-1.7.1.min.js"></script>
		<script type='text/javascript' src="__PUBLIC__/js/artdialog/jquery.artDialog.js?skin=aero"></script>
		<script type='text/javascript' src="__PUBLIC__/js/form.js"></script>
		<script type='text/javascript' src="__PUBLIC__/js/common.js"></script>
		<script type='text/javascript' src="../Public/js/admin.js"></script>
		<script type='text/javascript' src="../Public/js/common.js"></script>
		<script type='text/javascript' src="../Public/js/menu.js"></script>
		<link rel="stylesheet" type="text/css" href="__PUBLIC__/js/swfupload/swfupload.css" />
		<script type="text/javascript" src="__PUBLIC__/js/swfupload/swfupload.js"></script>
		<script type="text/javascript" src="__PUBLIC__/js/swfupload/swfupload.queue.js"></script>
		<script type="text/javascript" src="__PUBLIC__/js/swfupload/fileprogress.js"></script>
		<script type="text/javascript" src="__PUBLIC__/js/swfupload/handlers.js"></script>
	</head>
	<body>
		<script type="text/javascript">
			var swfu;
			var type = '<?php echo ($type); ?>';
			var appId = '<?php echo ($app_id); ?>';
			var dir_type = '';
//			('<?php echo ($dir_type); ?>' == 'apk') ? 'Apk' : 'Img';
			if('<?php echo ($dir_type); ?>' == 'apk')
			{
				dir_type = 'Apk';
			}
			else if('<?php echo ($dir_type); ?>' == 'icon')
			{
				dir_type = 'Icon';
			}
			else
			{
				dir_type = 'Img';
			}
			var button_font = ('<?php echo ($dir_type); ?>' == 'apk') ? 'Apk' : '图片';
			var screen_obj = {2:'240x320',3:'320x480',4:'480x800'};
			var file_limit = ('<?php echo ($dir_type); ?>' == 'apk' || '<?php echo ($dir_type); ?>' == 'icon') ? 1 : 0;
			var file_type = ('<?php echo ($dir_type); ?>' == 'apk') ? '*.apk; *.APK' : '*.*';
			$(function() {
				swfu = new SWFUpload({
					flash_url : "__PUBLIC__/js/swfupload/swfupload.swf",
					//			flash9_url : "../swfupload/swfupload_fp9.swf",
					upload_url: "<?php echo U('Admin://Upload/upload');?>" + dir_type,
					//upload_url: "http://192.168.0.104/SVN1/admin.php?m=Upload&a=uploadApk",
					post_params: {"PHPSESSID" : "<?php echo session_id(); ?>", 'type' : type , app_id : appId , ieFlag : "<?php echo $ieFlag;?>"},
					file_size_limit : "1000 MB",
					file_types : file_type,
					file_types_description : "All Files",
					file_upload_limit : 100,
					file_queue_limit : file_limit,
					custom_settings : {
						progressTarget : "fsUploadProgress",
						cancelButtonId : "btnCancel",
						upload_target : "show",
						upload_type : dir_type
						//upload_path : './TempFile/<?php echo session_id(); ?>/screenshot/'+screen_obj[type]
					},
					debug: false,
					// Button settings
					button_image_url: "images/TestImageNoText_65x29.png",
					button_width: "65",
					button_height: "29",
					button_placeholder_id: "spanButtonPlaceHolder",
					button_text: '<span class="theFont">上传'+button_font+'</span>',
					button_text_style: ".theFont { font-size: 12; }",
					button_text_left_padding: 12,
					button_text_top_padding: 3,
				
					// The event handler functions are defined in handlers.js
					swfupload_preload_handler : preLoad,
					swfupload_load_failed_handler : loadFailed,
					file_queued_handler : fileQueued,
					file_queue_error_handler : fileQueueError,
					file_dialog_complete_handler : fileDialogComplete,
					upload_start_handler : uploadStart,
					upload_progress_handler : uploadProgress,
					upload_error_handler : uploadError,
					upload_success_handler : uploadSuccess,
					upload_complete_handler : uploadComplete,
					queue_complete_handler : queueComplete	// Queue plugin event
				});
			});
			function del_file(src)
			{
				$.getJSON("<?php echo U('Admin://Upload/delTempFile');?>",{'path' : src}, function(data){
					alert(data.msg);
				});
			}
		</script>
		<div class="headbar clearfix">
			<div class="position"><span>应用</span><span>></span><span>应用管理</span><span>></span><span>应用上传</span></div>
			<!--<ul class="tab" name="menu1">
				<li id="li_1" class="selected"><a href="javascript:void(0)" hidefocus="true" onclick="select_tab('1')">商品信息</a></li>
				<li id="li_2"><a href="javascript:void(0)" hidefocus="true" onclick="select_tab('2')">描述</a></li>
				<li id="li_3"><a href="javascript:void(0)" hidefocus="true" onclick="select_tab('3')">营销选项</a></li>
			</ul>-->
		</div>
		<div class="content_box">
			<div class="content form_content">
				<form name="myform" id="myform" action="<?php echo U('AppStore://Apps/appAddAction');?>" method="post">
					<input type="hidden" name="app_type" value="<?php echo ($type); ?>" /> 
					<table class="form_table">
						<col width="150px" />
						<col />
						<tr>
							<th><?php if($dir_type == 'apk'): ?>APK<?php elseif($dir_type == 'icon'): ?>APK icon<?php else: ?>APK截图<?php endif; ?>上传：</th>
							<td>
								<!--<div id="status-message">Select some files to upload:</div>
								<div id="custom-queue"></div>
								<input id="custom_file_upload" type="file" name="Filedata" />
								<div id="filepath"></div>-->
								<div class="fieldset flash" id="fsUploadProgress"></div>
								<div>
									<span id="spanButtonPlaceHolder"></span>
									<input id="btnCancel" type="button" value="Cancel All Uploads" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
								</div>
								<div id="show2"></div>
								<div id="<?php if($dir_type == 'apk'): ?>file<?php else: ?><?php echo ($dir_type); ?><?php endif; ?>_list"></div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</body>
</html>
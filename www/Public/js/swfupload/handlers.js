/* Demo Note:  This demo uses a FileProgress class that handles the UI for displaying the file name and percent complete.
The FileProgress class is not part of SWFUpload.
*/


/* **********************
   Event Handlers
   These are my custom event handlers to make my
   web application behave the way I went when SWFUpload
   completes different tasks.  These aren't part of the SWFUpload
   package.  They are part of my application.  Without these none
   of the actions SWFUpload makes will show up in my application.
   ********************** */
function preLoad() {
	if (!this.support.loading) {
		alert("You need the Flash Player 9.028 or above to use SWFUpload.");
		return false;
	}
}
function loadFailed() {
	alert("Something went wrong while loading SWFUpload. If this were a real application we'd clean up and then give you an alternative");
}

function fileQueued(file) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("正在等待...");
		progress.toggleCancel(true, this);

	} catch (ex) {
		this.debug(ex);
	}

}

function fileQueueError(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
			return;
		}

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			progress.setStatus("File is too big.");
			this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			progress.setStatus("Cannot upload Zero Byte files.");
			this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			progress.setStatus("Invalid File Type.");
			this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		default:
			if (file !== null) {
				progress.setStatus("Unhandled Error");
			}
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
		
	} catch (ex) {
        this.debug(ex);
    }
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesSelected > 0) {
			document.getElementById(this.customSettings.cancelButtonId).disabled = false;
		}
		
		/* I want auto start the upload and I can do that here */
		this.startUpload();
	} catch (ex)  {
        this.debug(ex);
	}
}

function uploadStart(file) {
	try {
		/* I don't want to do any file validation or anything,  I'll just update the UI and
		return true to indicate that the upload should start.
		It's important to update the UI here because in Linux no uploadProgress events are called. The best
		we can do is say we are uploading.
		 */
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("正在上传...");
		progress.toggleCancel(true, this);
	}
	catch (ex) {
		this.debug(ex);
	}
	
	return true;
}

function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setProgress(percent);
		progress.setStatus("正在上传...");
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadSuccess(file, serverData) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		//progress.setComplete();
		//progress.setStatus("Complete.");
		//progress.toggleCancel(false);
		if(serverData == '-1')
		{
			progress.setStatus("您上传的文件类型不正确!");
			progress.toggleCancel(false);
		}
		else if(serverData == '-2')
		{
			progress.setStatus("您上传的文件大小超范围!");
			progress.toggleCancel(false);
		}else
		{
			progress.setComplete();
			if(this.customSettings.upload_type == 'Apk')
			{
				addFile(serverData,file);
			}
			if(this.customSettings.upload_type == 'Img')
			{
				addImage(serverData,file);
			}
			if(this.customSettings.upload_type == 'Icon')
			{
				addIcon(serverData,file);
			}
			progress.setStatus("文件上传成功.");
			progress.toggleCancel(false);
		}

	} catch (ex) {
		this.debug(ex);
	}
}

function uploadError(file, errorCode, message) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			progress.setStatus("Upload Error: " + message);
			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus("Upload Failed.");
			this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus("Server (IO) Error");
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus("Security Error");
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			progress.setStatus("Upload limit exceeded.");
			this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			progress.setStatus("Failed Validation.  Upload skipped.");
			this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			// If there aren't any files left (they were all cancelled) disable the cancel button
			if (this.getStats().files_queued === 0) {
				document.getElementById(this.customSettings.cancelButtonId).disabled = true;
			}
			progress.setStatus("Cancelled");
			progress.setCancelled();
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus("Stopped");
			break;
		default:
			progress.setStatus("Unhandled Error: " + errorCode);
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function uploadComplete(file) {
	if (this.getStats().files_queued === 0) {
		document.getElementById(this.customSettings.cancelButtonId).disabled = true;
	}
}

// This event comes from the Queue Plugin
function queueComplete(numFilesUploaded) {
	var status = document.getElementById("divStatus");
	status.innerHTML = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
}
function addFile(src, file) {
	$('#file_list').append(
		'<input type="hidden" id="filelist_'+type+'" value="' + src+ ';'+file.name+'" />'
	);
//	var arr = src.split('|');
//	var focus_photo = $('#focus_photo').val();
//	var photo_name = $('#photo_name').val();
//	//如果用户已经上传过该图片，则提示已经上传过
//	if(photo_name.indexOf(arr[3])==-1)
//	{
//		//最外层的span
//		var upSpan = $('<span class="pic" id=\'a'+arr[3]+'\'></span>');
//		//显示的图片
//		var claname = '';
//		if(focus_photo=='')
//		{
//			claname = 'current';
//		}
//		var newImg = $('<img name="img_name" class=\''+claname+'\' onclick="focus_img(\''+arr[2]+'\',this);" style="margin: 5px; opacity:1" src=\''+arr[0]+'\'/>').appendTo(upSpan);
//		var br = $('<br />').appendTo(upSpan);
//		var forea = $('<a href="#" style="padding-right:1em" onclick="show_link(\''+arr[2]+'\');">链接</a>').appendTo(upSpan);
//		var downa = $('<a href="javascript:;" id=\''+arr[3]+'\' onclick="del_img(this.id);">删除</a>').appendTo(upSpan);
//		upSpan.appendTo("#show_list");
//		//将photo名称存到页面中
//		photo_name += arr[2]+',';
//		$('#photo_name').val(photo_name);
//		$('#thumb').val(arr[5]);
//		//默认第一个照片为选中
//		if(focus_photo=='' || focus_photo==',')
//		{
//			$('#focus_photo').val(arr[2]);
//		}
//	}
}
function addImage(src,file) {
	var type = $('input:[name="app_type"]').val();
	$('#screenshot_list').append(
		'<input type="hidden" class="imagelist" value="' + src+ '" />'
	);
}
function addIcon(src,file) {
	var type = $('input:[name="app_type"]').val();
	$('#icon_list').append(
		'<input type="hidden" class="icon_info" value="' + src+ '" />'
	);
}
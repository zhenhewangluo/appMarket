function sendShort(url, dataObj)
{
	$.getJSON(url, dataObj, function(data){
		var icon = data.status == 1 ? 'succeed' : 'error';
		getDialog(data.msg, icon, false)
	});
}
function sendInfo(url, dataObj, reloadWin, time)
{
	$.post(url,dataObj, function(data){
		if(data.status == 1)
		{
			var list = $.dialog.list;
			for (var i in list) {
				list[i].close();
			}
			getDialog(data.msg, 'succeed', reloadWin);
//			if(reloadWin == 'reg')
//			{
//				$('.user_area').html(data.html);
//			}
			return false;
		}
		getDialog(data.msg, 'error', false, time);
		return false;
	}, 'json');
}
function getWin(url, winId, winTitle, resize, width, height, padding, init, close)
{
	$.get(url, function(data){
		$.dialog({
			id		: winId,
			title	: winTitle,
			width	: width,
			height	: height,
			resize	: resize,
			padding	: padding,
			init	: init == 1 ? function(){$('#act_invite textarea:[name="mobile"]').focus();} : function(){},
			close	: close == 1 ? function(){$('#act_invite textarea:[name="mobile"]').val('');} : function(){},
			content	: $(data).find('root').text()
		});
		validateInit();
	}, 'xml');
}
function getDialog(msg, icon, closeFn, time)
{
	$.dialog({
		icon	: icon,
		content	: msg,
		time	: (time > 1) ? time : 1,
		lock	: true,
		close	: (closeFn == true) ? function(){window.location.reload();} : function(){} //function(){window.location.reload();}//
	});
}
function checkInfo(obj, formId)
{
	if($('#' + formId + ' .invalid-msg').length > 0)
	{
		return false;
	}
	for(pro in obj)
	{
		var item = $('#' + formId + ' [name="'+pro+'"]');
		if($.trim(obj[pro]) == '')
		{
			//alert(pro);
			item.addClass('invalid-text');
			item.next('label').html(item.attr('alt'));
			item.next('label').addClass('invalid-msg');
			return false;
		}
	}
	return true;
}
var st = '';
function cdt()
{
	$('.verify_btn').hide();
	$('#countDown').show();
	st = setInterval(function(){
		var value = parseInt($('#cdtime').html());
		if(value <= 0)
		{
			clearInterval(st);
			$('#countDown').hide();
			$('.verify_btn').show();
			$('#cdtime').html(60);
			return false;
		}
		else
		{
			$('#cdtime').html(value - 1);
			return true;
		}
	},1000);
}
function clearcdt()
{
	clearInterval(st);
}
function selectAll(nameVal)
{
	//获取复选框的form对象
	var formObj = $("input:checkbox[name='"+nameVal+"']");
	//根据form缓存数据判断批量全选方式
	if(formObj.data('selectType')=='none' || formObj.data('selectType')==undefined)
	{
		$("input:checkbox[name='"+nameVal+"']:not(:checked)").attr('checked','checked');
		formObj.data('selectType','all');
	}
	else
	{
		$("input:checkbox[name='"+nameVal+"']").attr('checked',false);
		formObj.data('selectType','none');
	}
}
//倒计时
var countdown=function()
{
	var _self=this;
	this.handle={};
	this.parent={'second':'minute','minute':'hour','hour':'day', 'day':''};
	this.add=function(id){
		_self.handle.id=setInterval(function(){_self.work(id,'second');},1000);
	};
	this.work=function(id,type){
		if(type=="")
			return false;

		var e=document.getElementById("cd_"+type+"_"+id);

		var value=parseInt(e.innerHTML);
		if( value == 0 && _self.work( id,_self.parent[type] )==false )
		{
			clearInterval(_self.handle.id);
			return false;
		}
		else
		{
			e.innerHTML = (value==0?59:(value-1));
			return true;
		}
	};
};
$(function(){
	var tmpObj = $('#search_input');
	var defaultText = tmpObj.val();
	tmpObj.bind({
		focus:function(){checkInput($(this),defaultText);},
		blur :function(){checkInput($(this),defaultText);}
	});
	$('#comment_content').bind({
		focus:function(){$(this).addClass('comment_content_box_hover');},
		blur :function(){$(this).removeClass('comment_content_box_hover');}
	});
	$('.navbar_m li').hover(function(){
		var thisNode = $(this);
		if(thisNode.attr('class') == undefined || thisNode.attr('class').indexOf('onhover') == -1)
		{
			thisNode.addClass('onhover');
			thisNode.children('a').addClass('onhover');
		}
	},function(){
		var thisNode = $(this);
		if(thisNode.attr('class').indexOf('onhover') != -1 && thisNode.attr('class').indexOf('current_nav') == -1)
		{
			thisNode.removeClass('onhover');
			thisNode.children('a').removeClass('onhover');
		}
	});
});
function checkInput(para,textVal)
{
	var inputObj = (typeof(para) == 'object') ? para : $('#'+para+'');

	if(inputObj.val() == '')
	{
		inputObj.val(textVal);
	}
	else if(inputObj.val() == textVal)
	{
		inputObj.val('');
	}
}

function sendInfo(url, dataObj)
{
	$.post(url,dataObj, function(data){
		if(data.status == 1)
		{
			var list = $.dialog.list;
			for (var i in list) {
				list[i].close();
			}
			getDialog(data.msg, 'succeed', true);
			return false;
		}
		getDialog(data.msg, 'error', false);
		return false;
	}, 'json');
}
function getWin(url, winId, winTitle, resize, width, height)
{
	$.get(url, function(data){
//		realAlert($(data).find('root').text());
//		return false;
		$.dialog({
			id		: winId,
			title	: winTitle,
			width	: width,
			height	: height,
			resize	: resize,
			padding	: 0,
			content	: $(data).find('root').text()
		});
		validateInit();
	});
}
function getDialog(msg, icon, closeFn)
{
	$.dialog({
		icon	: icon,
		content	: msg,
		time	: 1,
		lock	: true,
		close	: closeFn == true ? function(){window.location.reload();} : function(){}
	});
}
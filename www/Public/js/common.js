window.realAlert = window.alert;
window.alert = function(mess)
{
	art.dialog.tips(mess);
}

window.realConfirm = window.confirm;
//对话框
window.confirm = function(mess,bnYes,bnNo)
{
	if(bnYes == undefined && bnNo == undefined)
	{
		return eval("window.realConfirm(mess)");
	}
	else
	{
		art.dialog.confirm(
			mess,
			function(){eval(bnYes)},
			function(){eval(bnNo)}
		);
	}
}
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
function switch_liTab_bg(aNodes, divNodes , currentStyle)
{
	aNodes.each(function(e){
		var thisNode = $(this);
		thisNode.click(function(){
			aNodes.removeClass(currentStyle);
			thisNode.addClass(currentStyle);
			divNodes.hide().eq(e).show();
		});
	});
}


$(function(){
	validateInit();
});
    function validateInit(formNodeName) {//初始化validate
		$('form').each(function(){//遍历页面表单个数
			var _formNode = $(this);
			var needsValidation = false;
			$(':input', _formNode).each(function(){//遍历页面当前表单内的元素
				var _inputNode = $(this);
				var pattern = typeof(_inputNode.attr('pattern')) != 'undefined';
				var required = typeof(_inputNode.attr('required')) != 'undefined';
				if(required && !pattern){//如果元素内有require属性但没有pattern属性，那么给当前元素添加一个pattern属性并赋值为'\\S';
					pattern = '\\S';
					_inputNode.attr('pattern', pattern);
				}
				if(pattern){//如果元素内有pattern属性，那么给当前元素的onchange事件绑定validateOnChange方法;
					_inputNode.bind('change', validateOnChange);
					needsValidation = true;
				}
				
				if (needsValidation){
					_formNode.bind('submit', validateOnSubmit).attr('novalidate', 'true');
				}
			});
		});
    }
    function validateOnChange(){
        var _inputNode = $(this);//要验证的表单元素
        var pattern = _inputNode.attr("pattern");//要验证的表单元素内容的匹配模式
		switch(pattern){
			case 'required':pattern = /\S+/i;break;
			case 'email':pattern = /^\w+([-+.]\w+)*@\w+([-.]\w+)+$/i;break;
			case 'qq':pattern = /^[1-9][0-9]{4,}$/i;break;
			case 'id':pattern = /^\d{15}(\d{2}[0-9x])?$/i;break;
			case 'ip':pattern = /^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/i;break;
			case 'zip':pattern = /^\d{6}$/i;break;
			case 'phone':pattern = /^((\d{3,4})|\d{3,4}-)?\d{7,8}(-\d+)*$/i;break;
			case 'mobi':pattern = /^1[3584]\d{9}$/i;break;
			case 'url':pattern = /^[a-zA-z]+:\/\/(\w+(-\w+)*)(\.(\w+(-\w+)*))+(\/?\S*)?$/i;break;
			case 'date':pattern = /^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)$/i;break;
			case 'datetime':pattern = /^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29) (?:(?:[0-1][0-9])|(?:2[0-3])):(?:[0-5][0-9]):(?:[0-5][0-9])$/i;break;
			case 'int':pattern = /^\d+$/i;break;
			case 'float':pattern = /^\d+\.?\d*$/i;break;
			case 'verify':pattern = /^\d{4}$/;break;
			case 'pass':pattern = /^\S{6,32}$/;break;
			case 'username' :pattern = /^[\w\u0391-\uFFE5]{2,15}$/;break;
		}
        var _inputNodeVal = _inputNode.val();//表单元素的值
		
        var alt = typeof( _inputNode.attr("alt")) == 'undefined';//表单元素的初始化信息
		
		var _inputNodeEmpty = typeof(_inputNode.attr("empty")) == 'undefined';//表单元素是否可以为空

		var _msgNode = null;//初始化提示信息所在的节点
		
		var _msgBoxNode = null;//新创建提示信息所在的节点

		var msgNodeValidClassName = 'valid-msg';//初始化验证通过提示信息的class
		
		var msgNodeInvalidClassName = 'invalid-msg';

		if ( (_inputNodeEmpty && _inputNodeVal == '') || (_inputNodeVal != '' && _inputNodeVal.search(pattern) == -1) ) { //1如果不能为空，并且值为空。2如果值不为空，并且没有通过验证
			
			_inputNode.removeClass("valid-text");//移除当前input元素验证通过的class

			if(!_inputNode.hasClass("invalid-ie6-text"))_inputNode.addClass("invalid-ie6-text");//如果不存在验证未通过的class,那么添加
			
			_msgNode = _inputNode.next();
			
			if(_msgNode[0] && (_msgNode[0].tagName == 'LABEL' || _msgNode[0].tagName == 'SPAN')){//存在初始化的提示信息节点
				
				_msgNode.removeClass(msgNodeValidClassName).addClass(msgNodeInvalidClassName).html('&nbsp;');//添加验证未通过样式，并附一个空格的值，兼容ie
				
				if(_inputNode.attr('initmsg') == '') _inputNode.attr('initmsg', _msgNode.html());//如果有初始化信息，那么直接添加初始化信息

				if(!alt)_msgNode.html(alt);
				
			} else {//不存在初始化的提示信息节点
				
				_msgBoxNode = $('<label/>').addClass(msgNodeInvalidClassName).html('&nbsp;').appendTo(_inputNode.parent());//创建提示信息节点并添加到节点后面
				
				if(_inputNode.attr('initmsg') == '') _inputNode.attr('initmsg', '');
				
				if(!alt)_msgBoxNode.html(alt);
			}
        } else {//通过验证
			_inputNode.removeClass("invalid-ie6-text");
			
        	if(!_inputNode.hasClass('valid-text'))_inputNode.addClass('valid-text');
			_msgNode=_inputNode.next();

        	if(_msgNode[0] && ( _msgNode[0].tagName == 'LABEL' || _msgNode[0].tagName == 'SPAN' )) {
				
				_msgNode.removeClass(msgNodeInvalidClassName).removeClass(msgNodeValidClassName).html('&nbsp;');
				if(!_inputNodeEmpty && _inputNodeVal == '') msgNodeValidClassName = '';
				
				_msgNode.addClass(msgNodeValidClassName).html('&nbsp;');
				
				if(_inputNode.attr('initmsg') == '') _inputNode.attr('initmsg', _msgNode.html());
				
				_msgNode.html(_inputNode.attr('initmsg'));
				
        	} else {
				 if(_inputNode.attr('initmsg') == '') _inputNode.attr('initmsg', '');
				 
				 if(!_inputNodeEmpty && _inputNodeVal == '') msgNodeValidClassName = '';
				 
				 _msgBoxNode = $('<label/>').addClass(msgNodeValidClassName).html('&nbsp;').appendTo(_inputNode.parent());
				 
				 _msgBoxNode.html(_inputNode.attr('initmsg'));
				 
                 _msgNode= _msgBoxNode;
        	}
			if(_inputNode.attr('type') == 'password')
	        {
	        	var bind = _inputNode.attr("bind");
		        var bind_flag = true;
		        var bind_arr = $('input:[name="' + bind + '"]');
				bind_arr.each(function(){
					
					var _bindNode = $(this);
					
					if(_bindNode.attr('name') == bind && _bindNode.val() != _inputNodeVal && _bindNode.val() != ''){
						bind_flag = false;
					}
					
				});

			    if(!bind_flag)
			    {
			    	_msgNode.removeClass(msgNodeValidClassName).addClass(msgNodeInvalidClassName).html('两次输入密码不一致');
					_inputNode.removeClass('valid-text');
			    	if(!_inputNode.hasClass('invalid-ie6-text'))_inputNode.addClass('invalid-ie6-text');
			    }
			    else
			    {
					_msgNode.addClass('valid-msg').html('&nbsp;');
					_inputNode.removeClass('invalid-ie6-text');
			    	if(!_inputNode.hasClass('valid-text'))_inputNode.addClass('valid-text');

					bind_arr.each(function(){
					
						var _reBindNode = $(this);
						if(_reBindNode.val() != ''){
							_reBindNode.next().addClass('valid-msg').html('&nbsp;');
							_reBindNode.removeClass('invalid-ie6-text');
							if(!_reBindNode.hasClass('valid-text')) _reBindNode.addClass('valid-text');
						}
					});
					
			    }
			}
        }
    }
	
    function validateOnSubmit() {
        var invalid = false;
		var _formNode = $(this);

		$(':input', _formNode).each(function(){//遍历页面当前表单内的元素
			var _inputNode = $(this);
			var _inputNodeType = _inputNode[0].type;
			var _inputNodePattern = _inputNode.attr('pattern');
			var _inputNodeIsShow = _inputNode.css('display');
			if ((_inputNodeType == "text" || _inputNodeType == "password" || _inputNodeType == "select-one" || _inputNodeType == "textarea") && _inputNodePattern && _inputNodeIsShow != 'none') {
				_inputNode.bind('change', validateOnChange);
				if(_inputNode.hasClass('invalid-ie6-text')){
					invalid = true;
					if(_inputNode[0].offsetHeight > 0 || _inputNode[0].client > 0){
						_inputNode.focus();
					}
					return false;
				} else {
					_inputNode.trigger('change');
					if (_inputNode.hasClass('invalid-ie6-text')){
						invalid = true;
						if(_inputNode[0].offsetHeight > 0 || _inputNode[0].client > 0){
							_inputNode.focus();
						}
						return false;
					}
				}
			}
		});

        var callback = _formNode.attr('callback');
        var result = true;
        if(callback != '') {result = eval(callback);}
        result = !(result==undefined?true:result);
        if (invalid || result) {
            return false;
        }
    }
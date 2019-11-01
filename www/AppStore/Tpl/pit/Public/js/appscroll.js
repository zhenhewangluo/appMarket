// JavaScript Document
$(function() {
	var sWidth = $("#focus").width(); //获取焦点图的宽度（显示面积）
	var len = $("#focus ul li").length; //获取焦点图个数
	var index = 0;
	var picTimer;
	
	//以下代码添加数字按钮和按钮后的半透明长条
	var btn = "<div class='btnBg'></div><div class='btn'>";
	for(var i=0; i < len; i++) {
		btn += "<span>" + (i+1) + "</span>";
	}
	btn += "</div>"
	$("#focus").append(btn);
	$("#focus .btnBg").css("opacity",0.5);
	
	//为数字按钮添加鼠标滑入事件，以显示相应的内容
	$("#focus .btn span").click(function() {
		index = $("#focus .btn span").index(this);
		showPics(index);
	}).eq(0).trigger("click");
	
	//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
	$("#focus ul").css("width",sWidth * (len + 1));
	
	//鼠标滑入某li中的某div里，调整其同辈div元素的透明度，由于li的背景为黑色，所以会有变暗的效果
	
	
	//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
	
		clearInterval(picTimer);
	
	//显示图片函数，根据接收的index值显示相应的内容
	function showPics(index) { //普通切换
		var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
		$("#focus ul").stop(true,false).animate({"left":nowLeft},500); //通过animate()调整ul元素滚动到计算出的position
		$("#focus .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
	}
	
	function showFirPic() { //最后一张图自动切换到第一张图时专用
		$("#focus ul").append($("#focus ul li:first").clone());
		var nowLeft = -len*sWidth; //通过li元素个数计算ul元素的left值，也就是最后一个li元素的右边
		$("#focus ul").stop(true,false).animate({"left":nowLeft},500,function() {
			//通过callback，在动画结束后把ul元素重新定位到起点，然后删除最后一个复制过去的元素
			$("#focus ul").css("left","0");
			$("#focus ul li:last").remove();
		}); 
		$("#focus .btn span").removeClass("on").eq(0).addClass("on"); //为第一个按钮添加选中的效果
	}
});

// JavaScript Document num2
$(function() {
	var sWidth = $("#focus2").width(); //获取焦点图的宽度（显示面积）
	var len = $("#focus2 ul li").length; //获取焦点图个数
	var index = 0;
	var picTimer;
	
	//以下代码添加数字按钮和按钮后的半透明长条
	var btn = "<div class='btnBg'></div><div class='btn'>";
	for(var i=0; i < len; i++) {
		btn += "<span>" + (i+1) + "</span>";
	}
	btn += "</div>"
	$("#focus2").append(btn);
	$("#focus2 .btnBg").css("opacity",0.5);
	
	//为数字按钮添加鼠标滑入事件，以显示相应的内容
	$("#focus2 .btn span").click(function() {
		index = $("#focus2 .btn span").index(this);
		showPics(index);
	}).eq(0).trigger("click");
	
	//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
	$("#focus2 ul").css("width",sWidth * (len + 1));
	
	//鼠标滑入某li中的某div里，调整其同辈div元素的透明度，由于li的背景为黑色，所以会有变暗的效果
	
	
	//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
	
		clearInterval(picTimer);
	
	//显示图片函数，根据接收的index值显示相应的内容
	function showPics(index) { //普通切换
		var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
		$("#focus2 ul").stop(true,false).animate({"left":nowLeft},500); //通过animate()调整ul元素滚动到计算出的position
		$("#focus2 .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
	}
	
	function showFirPic() { //最后一张图自动切换到第一张图时专用
		$("#focus2 ul").append($("#focus ul li:first").clone());
		var nowLeft = -len*sWidth; //通过li元素个数计算ul元素的left值，也就是最后一个li元素的右边
		$("#focus2 ul").stop(true,false).animate({"left":nowLeft},500,function() {
			//通过callback，在动画结束后把ul元素重新定位到起点，然后删除最后一个复制过去的元素
			$("#focus2 ul").css("left","0");
			$("#focus ul li:last").remove();
		}); 
		$("#focus2 .btn span").removeClass("on").eq(0).addClass("on"); //为第一个按钮添加选中的效果
	}
});

//appcenter tab
 $(function () {           
            $(".wrapper .bodyContainer .leftContainer .top > ul.header > li").click(function () {  
                $(this).parent().find("li.show").addClass("hide").removeClass("show");  
                $(this).addClass("show").removeClass("hide");  
                var parentsEl = $(this).parents(".wrapper .bodyContainer .leftContainer .top");  
                parentsEl.find(".wrapper .bodyContainer .leftContainer .top .content > ul.ulShow").addClass("ulHide").removeClass("ulShow");  
                /*页面静态内容*/  
                var ary = parentsEl.find(".wrapper .bodyContainer .leftContainer .top > ul.header > li");  
                parentsEl.find(".wrapper .bodyContainer .leftContainer .top .content > ul:eq(" + $.inArray(this, ary) + ")").addClass("ulShow").removeClass("ulHide");      
                /*用ajax动态加载内容  
                parentsEl.find("div > ul:eq(" + $.inArray(this, ary) + ")").addClass(function () {  
                    var el = this;  
                    $.post("TreeDataServlet", {param: "params"}, function (data) {  
                        $(el).html(data);  
                    });  
                    return "ulShow";  
                }).removeClass("ulHide");*/       
            })  
        });  

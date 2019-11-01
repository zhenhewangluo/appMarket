var infoLayerTimer;
var initTime = 500;
var infoLayerStatus = "closed";
var elementId;
var appId;
var viewType;
var rowIndexes = new Array();
function setOptions() {
	var A = $("#favSortList>li");
	var B = A.removeClass("first").removeClass("last").length - 2;
	$("#favSortList>li:eq(1)").addClass("first");
	$("#favSortList>li:eq(" + B + ")").addClass("last");
	$("#favSortList>li ul.options").remove();
	var C = "";
	if (B === 1) {
		$.each(A,
			function() {
				var E = $(this);
				var D = E.prop("id").substr(4);
				if (D !== "") {
					E.append('<ul class="options"><li><span class="sortUp">up</span></li><li><span class="sortDown">down</span></li><li><a href="javascript:void(0);" onclick="removeFromFavs(\'' + D + '\'); return false" class="sortRemove">remove</a></li></ul>');
					C += D
				}
			})
	} else {
		$.each(A,
			function() {
				var E = $(this);
				var D = E.prop("id").substr(4);
				if (D !== "") {
					if (E.hasClass("first")) {
						E.append('<ul class="options"><li><span class="sortUp">up</span></li><li><a href="javascript:void(0);" onclick="moveDown(\'' + D + '\'); return false;" class="sortDown">down</a></li><li><a href="javascript:void(0);" onclick="removeFromFavs(\'' + D + '\'); return false;" class="sortRemove">remove</a></li></ul>')
					} else {
						if (E.hasClass("last")) {
							E.append('<ul class="options"><li><a href="javascript:void(0);" onclick="moveUp(\'' + D + '\'); return false;" class="sortUp">up</a></li><li><span class="sortDown">down</span></li><li><a href="javascript:void(0);" onclick="removeFromFavs(\'' + D + '\'); return false;" class="sortRemove">remove</a></li></ul>')
						} else {
							E.append('<ul class="options"><li><a href="javascript:void(0);" onclick="moveUp(\'' + D + '\'); return false;" class="sortUp">up</a></li><li><a href="javascript:void(0);" onclick="moveDown(\'' + D + '\'); return false;" class="sortDown">down</a></li><li><a href="javascript:void(0);" onclick="removeFromFavs(\'' + D + '\'); return false;" class="sortRemove">remove</a></li></ul>')
						}
					}
					if (C.length > 0) {
						C += ","
					}
					C += D
				}
			})
	}
	$.ajax({
		url		: gl_localPrefix_saveAct,
		type	: 'POST',
		async	: true,
		dataType: "json",
		data	: {
			rids: C
		}
	})
	rowIndexes = [];
}
function closeAddWidget() {
	$(".addFavListOpen").hide();
	$(".addFavListClosed").show();
}
function openAddWidget() {
	$(".addFavListClosed").hide();
	$(".addFavListOpen").show();
}
function addToFavs(B) {
	var D = $("#unassignedFavs>li").length;
	var C = $("#fav_" + B + ">a").text();
	var E = '<li class="last" id="fav_' + B + '"><a href="#">' + C + '</a><ul class="options"><li><a href="javascript:void(0);" onclick="moveUp(\'' + B + '\'); return false" class="sortUp">up</a></li><li><span class="sortDown">down</span></li><li><a href="javascript:void(0);" onclick="removeFromFavs(\'' + B + '\'); return false" class="sortRemove">remove</a></li></ul></li>';
	$("#fav_" + B).remove();
	$("#favSortList li.addWidget").before(E);
	if (D - 1 === 0) {
		$("#favSortList li.addWidget").hide()
	}
	setOptions();
	var A = '';
	
	$.ajax({
		url			: gl_localPrefix_get3App,
		type		: 'POST',
		async		: false,
		dataType	: "json",
		data		: getDataForSlider(B, 0, true),
		success	: function(F) {
			A = '<div class="references" id="row_' + B + '"><h3>' + C + '<a href="' + F.url + '">' + text_recommendations_showAll + '</a></h3><div id="catSlider_' + B + '" class="sliderBox"><ul class="slider" style="left:-656px"><li class="prev"><ul></ul></li><li class="active"><ul></ul></li><li class="next"><ul></ul></li></ul></div><a href="javascript:void(0);" class="btnLeft">&lt;</a><a href="javascript:void(0);" class="btnRight">&gt;</a><div class="clear"></div></div>';
			$("#allAppsSliders").append(A);
			$("#catSlider_" + B + " ul.slider li.active ul").html(F.active);
			$("#catSlider_" + B + " ul.slider li.next ul").html(F.next);
			$("#catSlider_" + B + " ul.slider li.prev ul").html(F.prev);
			checkHideSlideButtons(B);
		}
	});
}
function getDataForSlider(B, C, A) {
	return {
		act		: B,
		cate	: ar_filter.appCategoryIds,
		rate	: ar_filter.minRating,
		day		: ar_filter.timeFilter,
		from	: ar_filter.installsMin,
		to		: ar_filter.installsMax,
		perpage	: 8,
		index	: C,
		full	: A
	}
}
function checkHideSlideButtons(A) {
	var B = $("#row_" + A + " a.btnLeft");
	var C = $("#row_" + A + " a.btnRight");
	var D = $("#catSlider_" + A + " ul.slider li.active ul li").length;
	if (D < 8) {
		B.hide();
		C.hide();
	} else {
		B.show();
		C.show();
	}
}
function removeFromFavs(A) {
	var B = $("#fav_" + A + ">a").text();
	var C = '<li id="fav_' + A + '"><a href="javascript:void(0);" onclick="addToFavs(\'' + A + "');  return false\">" + B + "</a></li>";
	$("#fav_" + A).remove();
	$("#unassignedFavs").append(C);
	$("#favSortList li.addWidget").show();
	setOptions();
	$("#row_" + A).remove();
}
function moveUp(B) {
	var A = $("#fav_" + B);
	var C = $("#favSortList>li").index(A);
	if (C - 1 > 0) {
		A.remove();
		$("#favSortList>li:eq(" + (C - 1) + ")").before(A);
	}
	setOptions();
	A = $("#row_" + B);
	C = $("#allAppsSliders>div").index(A);
	if (C - 1 > 0) {
		A.remove();
		$("#allAppsSliders>div:eq(" + (C - 1) + ")").before(A);
	}
}
function moveDown(A) {
	var C = $("#fav_" + A);
	var B = $("#favSortList>li").index(C);
	var D = $("#favSortList>li").length - 2;
	if (B + 1 <= D) {
		C.remove();
		$("#favSortList>li:eq(" + B + ")").after(C);
	}
	setOptions();
	C = $("#row_" + A);
	B = $("#allAppsSliders>div").index(C);
	D = $("#allAppsSliders>div").length - 1;
	if (B + 1 <= D) {
		C.remove();
		$("#allAppsSliders>div:eq(" + B + ")").after(C);
	}
}
function toggleCategories() {
	$("#appCategoriesOpen").toggle();
	$("#appCategoriesClosed").toggle()
}
function openInfoLayer(B, A, D) {
	if (viewType === "slider") {
		var C = $("#" + B).parent().parent().parent().parent().parent().parent();
	} else {
		if (viewType === "grid" || viewType === "list" || viewType === "listNormal") {
			var C = $("#" + B).parent().parent().parent();
		}
	}
	C.append('<div class="infoLayer" id="infoLayer">Loading...</div>');
	loadInfoLayerContent(B, A, D);
	infoLayerStatus = "opened";
	$("#infoLayer").mouseenter(function() {
		window.clearTimeout(infoLayerTimer);
	}).mouseleave(function() {
		infoLayerTimer = window.setTimeout("closeInfoLayer()", initTime);
	});
	C.mouseleave(function() {
		closeInfoLayer();
	})
}
function closeInfoLayer() {
	$("#infoLayer").remove();
	infoLayerStatus = "closed";
}
function loadInfoLayerContent(A, C, B) {
	var E = positionInfoLayer(A);
	var D = positionInfoLayerArrow(A);
	$("#infoLayer").load(gl_localPrefix_layer, {id : C}, function() {
		$("#infoLayerArrowTop, #infoLayerArrowBottom").css("left", D + "px");
		if (viewType === "slider") {
			if (E.boxPos === "above") {
				$("#infoLayerArrowBottom").show();
				$("#infoLayer").css("top", "-245px");
			} else {
				$("#infoLayerArrowTop").show();
				$("#infoLayer").css("top", "130px");
			}
		} else {
			if (viewType === "listNormal") {
				if (E.boxPos === "above") {
					$("#infoLayerArrowBottom").show();
					$("#infoLayer").css("top", (E.boxTop - 857) + "px");
				} else {
					$("#infoLayerArrowTop").show();
					$("#infoLayer").css("top", (E.boxTop - 470) + "px");
				}
			} else {
				if (viewType === "grid" || viewType === "list") {
					if (E.boxPos === "above") {
						$("#infoLayerArrowBottom").show();
						$("#infoLayer").css("top", (E.boxTop - 637) + "px");
					} else {
						$("#infoLayerArrowTop").show();
						$("#infoLayer").css("top", (E.boxTop - 265) + "px");
					}
				}
			}
		}
	});
}
function positionInfoLayer(G) {
	var C = $(window).scrollTop();
	var F = $(window).height();
	var E = $("#" + G).offset();
	var A = parseInt(E.top);
	var D = A - C;
	var B = "above";
	if (D > 375) {
		B = "above";
	} else {
		B = "below";
	}
	return {
		boxPos: B,
		boxTop: A
	}
}
function positionInfoLayerArrow(C) {
	var A = 0;
	var B = $("#" + C).position();
	if (viewType === "slider") {
		A = parseInt(B.left) + 57;
	} else {
		if (viewType === "grid") {
			A = parseInt(B.left) + 17;
		} else {
			if (viewType === "list" || viewType === "listNormal") {
				A = parseInt(B.left) + 10;
			}
		}
	}
	return A;
}
function prevScreen() {
	var C = parseInt($("#maxScreens").html());
	var B = parseInt($("#currentScreen").html());
	var A = B - 1;
	if (A > 0) {
		$("#currentScreen").html(A);
		$("#appScreens img").hide();
		$("#appScreens #screenshot_" + A).show();
	}
}
function nextScreen() {
	var C = parseInt($("#maxScreens").html());
	var B = parseInt($("#currentScreen").html());
	var A = B + 1;
	if (A <= C) {
		$("#currentScreen").html(A);
		$("#appScreens img").hide();
		$("#appScreens #screenshot_" + A).show();
	}
}
function initRecommendations() {
	if ($("#allAppsSliders").length > 0) {
		$("#allAppsSliders .references .sliderBox>ul").css("left", "-656px");
		$("#allAppsSliders").delegate(".references a.btnRight", "click",
			function(D) {
				D.preventDefault();
				var C = $(this).parent().attr("id");
				var B = C.substr(4);
				var A = rowIndexes[B];
				if (typeof A == "undefined") {
					A = 0;
				}
				var E = A + 1;
				rowIndexes[B] = E;
				$("#" + C + " ul.slider").animate({
					left: "-1312px"
				},
				500,
				function() {
					$.ajax({
						url			: gl_localPrefix_getApp,
						type		: 'POST',
						async		: false,
						dataType	: "json",
						data		: getDataForSlider(B, E + 1, false),
						success	: function(data) {
							$("#" + C + " ul.slider li.prev").remove();
							$("#" + C + " ul.slider li.active").attr("class", "prev");
							$("#" + C + " ul.slider li.next").attr("class", "active");
							$("#" + C + " ul.slider").append('<li class="next"><ul style="_width:656px;">' + data.list + "</ul></li>");
							$("#allAppsSliders .references .sliderBox>ul").css("left", "-656px");
							checkHideSlideButtons(B);
						}
					})
				})
			});
		$("#allAppsSliders").delegate(".references a.btnLeft", "click",
			function(D) {
				D.preventDefault();
				var C = $(this).parent().attr("id");
				var B = C.substr(4);
				var A = rowIndexes[B];
				if (typeof A == "undefined") {
					A = 0;
				}
				var E = A - 1;
				rowIndexes[B] = E;
				$("#" + C + " ul.slider").animate({
					left: "0px"
				},
				500,
				function() {
					$.ajax({
						url			: gl_localPrefix_getApp,
						type		: 'POST',
						async		: false,
						dataType	: "json",
						data		: getDataForSlider(B, E - 1, false),
						success	: function(F) {
							$("#" + C + " ul.slider li.next").remove();
							$("#" + C + " ul.slider li.active").attr("class", "next");
							$("#" + C + " ul.slider li.prev").attr("class", "active");
							$("#" + C + " ul.slider").prepend('<li class="prev"><ul style="_width:656px;">' + F.list + "</ul></li>");
							$("#" + C + " ul.slider ").css("left", "-656px");
							checkHideSlideButtons(B)
						}
					})
				})
			});
	}
	$("#allAppsSliders, #gridviewApps, #listviewApps, #listviewAppsNormal").delegate("li a.appIcon", "mouseenter",
		function() {
			elementId = $(this).attr("id");
			appId = elementId.split('-')[1];
			rowId = elementId.split('-')[0];
			if ($(this).parent().parent().parent().attr("id") === "gridviewApps") {
				viewType = "grid";
			} else {
				if ($(this).parent().parent().parent().attr("id") === "listviewApps") {
					viewType = "list";
				} else {
					if ($(this).parent().parent().parent().attr("id") === "listviewAppsNormal") {
						viewType = "listNormal";
					} else {
						viewType = "slider";
						$("#allAppsSliders .references").removeAttr("style");
						$(this).parent().parent().parent().parent().parent().parent().css("z-index", "6666");
					}
				}
			}
			if (infoLayerStatus === "closed") {
				infoLayerTimer = window.setTimeout('openInfoLayer("' + elementId + '", "' + appId + '", "' + rowId + '")', initTime);
			} else {
				window.clearTimeout(infoLayerTimer);
				loadInfoLayerContent(elementId, appId, rowId);
			}
		}).delegate("li a.appIcon", "mouseleave",
		function() {
			if (infoLayerStatus === "closed") {
				window.clearTimeout(infoLayerTimer);
			} else {
				infoLayerTimer = window.setTimeout("closeInfoLayer()", initTime);
			}
		});
	
	$("#filterSidebar select").change(function() {
		var A = $(this).attr("id") == "appInstalls_min";
		var C = $(this).attr("id") == "appInstalls_max";
		if (A || C) {
			var B = $("#appInstalls_min").prop("selectedIndex");
			var D = $("#appInstalls_max").prop("selectedIndex");
			if (D < B) {
				if (A) {
					$("#appInstalls_max").prop("selectedIndex", B);
				}
				if (C) {
					$("#appInstalls_min").prop("selectedIndex", D);
				}
			}
			if ($("#appInstalls_range").attr("checked") == "checked") {
				updateFilterVariable();
				applyFilter('MARKET_TOP_APP_POINTS');
			} else {
				$("#appInstalls_range").click();
			}
		}
	});
	$("#appsCategoryListOpen input").bind('click', function(e) {
		if ($(this).attr("checked") == "checked") {
			if ($(this).attr("id") === "appCategory_0") {
				$("#appsCategoryListOpen input").removeAttr("checked");
				$(this).attr("checked", "checked");
			} else {
				$("#appCategory_0").removeAttr("checked");
			}
		}
		fillAppCategoriesList();
		updateFilterVariable();
		applyFilter();
	});
	$("#gamesCategoryListOpen input").bind('click', function() {
		if ($(this).attr("checked") == "checked") {
			if ($(this).attr("id") === "gameCategory_0") {
				$("#gamesCategoryListOpen input").removeAttr("checked");
				$(this).attr("checked", "checked");
			} else {
				$("#gameCategory_0").removeAttr("checked");
			}
		}
		fillGameCategoriesList();
		updateFilterVariable();
		applyFilter();
	});
	$("#appRating input").change(function() {
		updateFilterVariable();
		applyFilter('RATING_WEIGHTS');
	});
	$("#appPeriod input").change(function() {
		updateFilterVariable();
		applyFilter('NEWCOMER_RATING');
	});
	$("#appInstalls input").change(function() {
		updateFilterVariable();
		applyFilter('MARKET_TOP_APP_POINTS');
	});
	fillAppCategoriesList();
	fillGameCategoriesList();
	updateFilterVariable();
	$("#allAppsSliders div.references").each(function(B) {
		var A = $(this).attr("id").substr(4);
		checkHideSlideButtons(A);
	})
}
function fillAppCategoriesList() {
	$("#appsCategoryList li").remove();
	$("#appsCategoryListOpen input").each(function(A) {
		if ($(this).attr("checked") == "checked") {
			var B = $('label[for="appCategory_' + A + '"]').html();
			$("#appsCategoryList").append("<li>" + B + "</li>");
		}
	});
	if ($("#appsCategoryList li").length === 0) {
		$("#appsCategoryList").append("<li>keine</li>");
	}
}
function fillGameCategoriesList() {
	$("#gamesCategoryList li").remove();
	$("#gamesCategoryListOpen input").each(function(A) {
		if ($(this).attr("checked") == "checked") {
			var B = $('label[for="gameCategory_' + A + '"]').html();
			$("#gamesCategoryList").append("<li>" + B + "</li>");
		}
	});
	if ($("#gamesCategoryList li").length === 0) {
		$("#gamesCategoryList").append("<li>keine</li>");
	}
}
var ar_filter = {
	appCategoryIds: "",
	minRating: 0,
	timeFilter: "",
	installsMin: -1,
	installsMax: -1,
	toString: function() {
		return "cate = " + this.appCategoryIds + "; minRating = " + this.minRating + "; timeFilter = " + this.timeFilter + "; installsMin = " + this.installsMin + "; installsMax = " + this.installsMax;
	},
	toParams: function() {
		var tmpCate = this.appCategoryIds != '' ? this.appCategoryIds : 0;
		return "/cate/" + tmpCate + "/rate/" + this.minRating + "/day/" + this.timeFilter + "/from/" + this.installsMin + "/to/" + this.installsMax;
	},
	toAjax : function() {
		return {
			cate : this.appCategoryIds,
			rate : this.minRating,
			day : this.timeFilter,
			from : this.installsMin,
			to : this.installsMax
		};
	}
};
function updateFilterVariable() {
	var C = "";
	if ($("#appCategory_0").attr("checked") == "checked") {} else {
		$("#appsCategoryListOpen input").each(function(D) {
			if ($(this).attr("checked") == "checked") {
				if (C.length > 0) {
					C += ",";
				}
				C += $(this).val();
			}
		});
		if (C.length == 0) {
			C = "0";
		}
	}
	var A = "";
	if ($("#gameCategory_0").attr("checked") == "checked") {} else {
		$("#gamesCategoryListOpen input").each(function(D) {
			if ($(this).attr("checked") == "checked") {
				if (A.length > 0) {
					A += ",";
				}
				A += $(this).val();
			}
		});
		if (A.length == 0) {
			A = "0";
		}
	}
	if(A.length > 0 && 0 != A){
		if(C != 0)A = C + ',' + A;
	}
	else{
		A = C;
	}
	ar_filter.appCategoryIds = A;
	ar_filter.minRating = $("input[name=minRating]:checked").val();
	ar_filter.timeFilter = $("input[name=timeFilter]:checked").val();
	if ($("#appInstalls_any").attr("checked") == "checked") {
		ar_filter.installsMin = -1;
		ar_filter.installsMax = -1;
	} else {
		ar_filter.installsMin = $("#appInstalls_min").val();
		ar_filter.installsMax = $("#appInstalls_max").val();
	}
}
function applyFilter(filterExceptName) {
	if(!filterExceptName)filterExceptName = '';
	
	$("#allAppsSliders div.references").each(function(B) {
		var A = $(this).attr("id").substr(4);
		//temp
		if(A != 'SPONSORED_APP_DATE' && A != 'TEST_REPORT_DATE' && A != filterExceptName){
			$.ajax({
				url			: gl_localPrefix_get3App,
				type		: 'POST',
				async		: false,
				dataType	: "json",
				data		: getDataForSlider(A, 0, true),
				success	: function(data) {
					$("#catSlider_" + A + " ul.slider li.active ul").html(data.active);
					$("#catSlider_" + A + " ul.slider li.next ul").html(data.next);
					$("#catSlider_" + A + " ul.slider li.prev ul").html(data.prev);
					checkHideSlideButtons(A);
					rowIndexes = [];
				}
			});
		}
		
	});
	if (document.getElementById("listviewApps") != null) {
		loadRecommendationsList(currentRowId, 0, maxResults);
	}
	if (document.getElementById("listviewAppsNormal") != null) {
		loadRecommendationsListNormal(currentRowId, 0, maxResults);
	}
	if (document.getElementById("gridviewApps") != null) {
		loadRecommendationsGrid(currentRowId, 0, maxResults);
	}
}
function loadRecommendationsList(A, B, C) {
	var obj = $.extend(ar_filter.toAjax(), {rid : A, si : B, max : C});
	$("#listviewApps").load(gl_localPrefix_recomm, obj);
}
function loadRecommendationsListNormal(A, B, C) {
	var obj = $.extend(ar_filter.toAjax(), {rid : A, si : B, max : C, htmlType : 'Normal'});
	$("#listviewAppsNormal").load(gl_localPrefix_recomm, obj);
}
function loadRecommendationsGrid(A, B, C) {
	$("#gridviewApps").load(gl_localPrefix + recommendations_url + "-grid?rid=" + A + ar_filter.toParams() + "&si=" + B + "&max=" + C + "&bare=1");
}
function excludeAppFromRecommendations(A, B) {
	if (!confirm(B)) {
		return
	}
	$.ajax({
		type: "POST",
		url: gl_localPrefix + "/community/user/update-apps-list",
		data: {
			appId: A,
			listName: "hideReco",
			add: true
		},
		dataType: "json",
		success: function(C) {
			if (C.status == "ok") {
				applyFilter();
			} else {
				if (C.sessionExpired) {
					alert(C.sessionExpiredError);
				}
			}
		},
		error: function() {
			alert(gl_fatalMsg);
		}
	})
};

(function($){
	$.fn.lavaLamp=function(o){
		o=$.extend({
			fx:"linear",
			speed:500,
			click:function(){}
		},o||{});
		return this.each(function(){
			var b=$(this),noop=function(){},$back=$('<li class="back"><div class="left"></div></li>').appendTo(b),$li=$("li",this),curr=$("li.current",this)[1]||$($li[0]).addClass("current")[1];
			$li.not(".back").hover(function(){
				move(this)
			},noop);
			$(this).hover(noop,function(){
				move(curr)
				});
			$li.click(function(e){
				setCurr(this);
				return o.click.apply(this,[e,this])
				});
			setCurr(curr);
			function setCurr(a){
				$back.css({
					"left":a.offsetLeft+"px",
					"width":a.offsetWidth+"px"
					});
				curr=a
				};
			function move(a){
				$back.each(function(){
					$(this).dequeue()
				}).animate({
					width:a.offsetWidth,
					left:a.offsetLeft
				},o.speed,o.fx)
			}
		})
	}
})(jQuery);
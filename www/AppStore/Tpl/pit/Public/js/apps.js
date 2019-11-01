function announceBadApp(A, B) {
    alert("Version incompatibility.  Please reload the page.")
}
function announceBadApp2(C, A, D) {
    var B = prompt(A);
    if (B == null || B == "") {
        return
    }
    $.ajax({
        url: gl_localPrefix + "/apps/announce-bad-app",
        data: {
            appId: C,
            reason: B
        },
        dataType: "json",
        success: function(E) {
            if (E.status == "ok") {
                alert(D)
            } else {
                if (E.sessionExpired) {
                    alert(E.sessionExpiredError)
                }
            }
        },
        error: function() {
            alert(gl_fatalMsg)
        }
    })
}
function selectCommentsLang2(B, A) {
    document.getElementById("commentsLangLink_" + B).className = "active";
    document.getElementById("commentsLangLink_" + A).className = "";
    $("#commentsLang_" + B).css("display", "");
    $("#commentsLang_" + A).css("display", "none")
}
function updateUserAppsFromApp(A, B) {
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/community/user/update-apps-list",
        data: {
            appId: A,
            listName: "profile",
            add: B
        },
        dataType: "json",
        success: function(C) {
            if (C.status == "ok") {
                $("#addAppToProfile").css("display", B ? "none": "");
                $("#removeAppFromProfile").css("display", B ? "": "none");
                $("#numUsersInProfile1").html(C.numUsersInProfile);
                $("#numUsersInProfile2").html(C.numUsersInProfile)
            } else {
                if (C.sessionExpired) {
                    alert(C.sessionExpiredError)
                }
            }
        },
        error: function() {
            alert(gl_fatalMsg)
        }
    })
}
function updateUserAppsOnWatchListFromApp(A, B) {
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/community/user/update-apps-list",
        data: {
            appId: A,
            listName: "watchlist",
            add: B
        },
        dataType: "json",
        success: function(C) {
            if (C.status == "ok") {
                $("#addAppToWatchList").css("display", B ? "none": "");
                $("#removeAppFromWatchList").css("display", B ? "": "none");
                $("#numUsersOnWatchList1").html(C.numUsersOnWatchList);
                $("#numUsersOnWatchList2").html(C.numUsersOnWatchList)
            } else {
                if (C.sessionExpired) {
                    alert(C.sessionExpiredError)
                }
            }
        },
        error: function() {
            alert(gl_fatalMsg)
        }
    })
}
function setAppCommentRating(B) {
    var A = document.getElementById("ratingStars");
    if (A) {
        A.className = "rating stars" + B
    }
    $("#rating").val(B == 0 ? "": B)
}
function subscribeToAppComments2(A, B) {
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/market/apps/app/" + (B ? "subscribe": "unsubscribe") + "-comments",
        data: {
            appId: A
        },
        dataType: "json",
        success: function(C) {
            if (C.status == "ok") {
                $("#subscriptionACActive").css("display", B ? "": "none");
                $("#subscriptionACInactive").css("display", B ? "none": "");
                $("#subscriptionACCheckboxActive").css("display", B ? "": "none");
                $("#subscriptionACCheckboxInactive").css("display", B ? "none": "")
            } else {
                if (C.sessionExpired) {
                    alert(C.sessionExpiredError)
                }
            }
        },
        error: function() {
            alert(gl_fatalMsg)
        }
    })
}
function addAppComment3(H, D, G, A, C, B, I, E, J, F) {
    H.submit.disabled = "disabled";
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/market/apps/app/postComment",
        data: $(H).serialize(),
        dataType: "json",
        success: function(M) {
            H.submit.disabled = null;
            if (M.status == "ok") {
                var N = document.getElementById("existingUserComments");
                var K = '<div class="comments" id="aucContainer' + M.commentId + '">';
                var O = "</div>";
                if (D) {
                    N.innerHTML = K + M.commentHtml + O + N.innerHTML
                } else {
                    N.innerHTML += K + M.commentHtml + O
                }
                H.comment.value = "";
                H.rating.value = "";
                setAppCommentRating(0);
                clearErrors(H, G, A);
                var L = document.getElementById(E);
                if (L) {
                    L.className = "inputboxError";
                    L.value = ""
                }
                $("#" + I).css("display", "none");
                if (H.subscribe != null && H.subscribe.checked) {
                    $("#subscriptionACActive").css("display", "");
                    $("#subscriptionACInactive").css("display", "none");
                    $("#subscriptionACCheckboxActive").css("display", "");
                    $("#subscriptionACCheckboxInactive").css("display", "none")
                }
            } else {
                if (M.sessionExpired) {
                    showPasswordParagraphComment(I, E, J, F);
                    alert(M.sessionExpired)
                } else {
                    if (M.passwordError) {
                        showPasswordParagraphComment(I, E, J, F)
                    }
                    showErrors(H, G, A, C, B, M.errors)
                }
            }
        },
        error: function() {
            H.submit.disabled = null;
            alert(gl_fatalMsg)
        }
    })
}
var oldAppCommentsHtml = new Array();
function editAppCommentForm2(A, B) {
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/market/apps/app/editCommentForm",
        data: {
            appId: A,
            commentId: B
        },
        dataType: "json",
        success: function(C) {
            if (C.status == "ok") {
                var E = document.getElementById("aucContainer" + B);
                var D = A + "/" + B;
                oldAppCommentsHtml[D] = E.innerHTML;
                E.innerHTML = C.editorHtml
            } else {
                if (C.sessionExpired) {
                    alert(C.sessionExpiredError)
                }
            }
        },
        error: function() {
            alert(gl_fatalMsg)
        }
    })
}
function editAppCommentSubmit2(J, H, E, I, B, D, C, L, F, A, G) {
    var K = document.getElementById("submitComment" + E);
    K.disabled = "disabled";
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/market/apps/app/editCommentPost",
        data: $(J).serialize(),
        dataType: "json",
        success: function(M) {
            K.disabled = null;
            if (M.status == "ok") {
                var N = document.getElementById("aucContainer" + E);
                N.innerHTML = M.commentHtml
            } else {
                if (M.sessionExpired) {
                    showPasswordParagraphComment(L, F, A, G);
                    alert(M.sessionExpired)
                } else {
                    if (M.passwordError) {
                        showPasswordParagraphComment(L, F, A, G)
                    }
                    showErrors(J, I, B, D, C, M.errors)
                }
            }
        },
        error: function() {
            K.disabled = null;
            alert(gl_fatalMsg)
        }
    })
}
function cancelEditAppComment(B, A) {
    var C = B + "/" + A;
    $("#aucContainer" + A).html(oldAppCommentsHtml[C]);
    oldAppCommentsHtml[C] = null
}
function showSimilarAppsForm(A) {
    $("#addSimilarApps").css("display", A ? "": "none")
}
function searchSimilarApps2(D) {
    var C = $("#addSimilarApps_search").val();
    var B = document.getElementById("addSimilarApps_list");
    while (B.firstChild) {
        B.removeChild(B.firstChild)
    }
    var A = $("#addSimilarApps_searchError");
    A.css("display", "none");
    $("#addSimilarApps_submitError").css("display", "none");
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/market/apps/searchSimilarApps",
        data: {
            thisAppId: D,
            searchText: C
        },
        dataType: "json",
        success: function(I) {
            var G = $("#addSimilarApps_resultEmpty");
            var F = $("#addSimilarApps_result");
            if (I.status == "ok") {
                var J = I.apps.length;
                if (J > 0) {
                    for (i = 0; i < J; i++) {
                        var H = I.apps[i];
                        var E = document.createElement("option");
                        E.appendChild(document.createTextNode(H.title + " (" + H.pname + ")"));
                        E.setAttribute("value", H.pname);
                        B.appendChild(E)
                    }
                    G.css("display", "none");
                    F.css("display", "")
                } else {
                    G.css("display", "");
                    F.css("display", "none")
                }
            } else {
                A.html(I.errors[0][1]);
                A.css("display", "");
                G.css("display", "none");
                F.css("display", "none")
            }
        },
        error: function() {
            alert(gl_fatalMsg)
        }
    })
}
function submitSimilarApps(B, C) {
    B.addSimilarApps_submit.disabled = "disabled";
    var A = $("#addSimilarApps_submitError");
    A.css("display", "none");
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/market/apps/submitSimilarApps",
        data: $(B).serialize(),
        dataType: "json",
        success: function(D) {
            B.addSimilarApps_submit.disabled = null;
            if (D.status == "ok") {
                alert(C);
                $("#addSimilarApps_resultEmpty").css("display", "none");
                $("#addSimilarApps_result").css("display", "none");
                showSimilarAppsForm(false);
                $.fancybox.close()
            } else {
                A.html(D.errors[0][1]);
                A.css("display", "")
            }
        },
        error: function() {
            B.addSimilarApps_submit.disabled = null;
            alert(gl_fatalMsg)
        }
    })
}
function loadAppComments(C, B, E, A, D) {
    parent.location = "#contentTabBody2";
    var F = $("#commentsContainer_" + B);
    F.html("");
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/market/apps/load-app-comments",
        data: {
            appId: C,
            type: B,
            language: E,
            sortAsc: A,
            si: D
        },
        dataType: "json",
        success: function(G) {
            F.html(G.innerHTML)
        },
        error: function() {
            F.html("");
            alert(gl_fatalMsg)
        }
    })
}
function loadMarketComments2(C, E, D, A) {
    parent.location = "#commentsLang_" + A + "_start";
    var B = $("#commentsLang_" + A);
    B.html("");
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/market/apps/load-market-comments",
        data: {
            appId: C,
            place: A,
            locale: E,
            si: D
        },
        dataType: "json",
        success: function(F) {
            B.html(F.innerHTML)
        },
        error: function() {
            B.heml("");
            alert(gl_fatalMsg)
        }
    })
}
function deleteAppComment(C, D, A) {
    var B = $("#aucText" + D).html().replace(/<br *\/*>/g, "\n");
    if (!confirm(A + "\n\n" + B)) {
        return
    }
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/market/apps/app/delete-comment",
        data: {
            appId: C,
            commentId: D
        },
        dataType: "json",
        success: function(H) {
            if (H.status == "ok") {
                var F = document.getElementById("aucContainer" + D);
                if (F != null) {
                    F.parentNode.removeChild(F)
                }
                var G = document.getElementById("aucContainer_all_" + D);
                if (G != null) {
                    G.parentNode.removeChild(G)
                }
                var E = document.getElementById("aucContainer_user_" + D);
                if (E != null) {
                    E.parentNode.removeChild(E)
                }
            } else {
                if (H.sessionExpired) {
                    alert(errorMsg + "\n\n" + H.sessionExpiredError);
                    window.location.reload()
                }
            }
        },
        error: function() {
            alert(gl_fatalMsg)
        }
    })
}
function showDescriptionEditor(C) {
    $("#description").css("display", C ? "none": "");
    $("#editDescription").css("display", C ? "": "none");
    if (!C) {
        var B = document.getElementById("defaultLanguage").value;
        var A = document.getElementById("languageSelect");
        for (i = 0; i < A.options.length; i++) {
            if (A.options[i].value == B) {
                A.selectedIndex = i
            }
        }
    }
}
function showAdvancedSettings(A) {
    $("#advancedSettings").css("display", A ? "": "none")
}
function updateDescriptionEditor(E) {
    var C = document.getElementById("advancedSettings").getElementsByTagName("input");
    for (var A in C) {
        C[A].checked = ""
    }
    var D = document.getElementById("languageSelect");
    var B = D.options[D.options.selectedIndex].value;
    document.getElementById(B).checked = "checked";
    var F = document.getElementById(E);
    if (!F) {
        console.error('Text area "' + E + '" undefined');
        return
    }
    F.focus();
    F.value = document.getElementById("desc_" + B).value
}
function saveAppDescription(H, D, G, A, E, B, I, C, J, F) {
    if (H.action.value == "delete" && !confirm(D)) {
        return
    }
    H.submitPost.disabled = "disabled";
    H.deletePost.disabled = "disabled";
    H.cancelPost.disabled = "disabled";
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/app-post-description",
        data: $(H).serialize(),
        dataType: "json",
        success: function(K) {
            H.submitPost.disabled = null;
            H.deletePost.disabled = null;
            H.cancelPost.disabled = null;
            if (K.status == "ok") {
                window.location.href = window.location.href
            } else {
                if (K.sessionExpired) {
                    showPasswordParagraph(I, C, J, F);
                    alert(K.sessionExpired)
                } else {
                    if (K.passwordError) {
                        showPasswordParagraph(I, C, J, F)
                    }
                    showErrors(H, G, A, E, B, K.errors)
                }
            }
        },
        error: function() {
            H.submitPost.disabled = null;
            H.deletePost.disabled = null;
            H.cancelPost.disabled = null;
            alert(gl_fatalMsg)
        }
    })
}
var appMoreBarClosedHeight = 200;
function initAppPage() {
	var item = null;
    var A = $("#resultTab1 div.screenImage");
    var D = 0;
    for (var C = 0; C < A.length; C++) {
        item = A[C];
        currentWidth = item.childNodes[0].childNodes[0].style.width;
        currentWidth = currentWidth.substring(0, currentWidth.length - 2);
        D += parseInt(currentWidth) + 10;
    }
    $("#resultTab1").css("width", D + "px");
    var F = Math.ceil(D / 600);
    var B = A.length / F;
    $("#paginationTab1").pagination(A.length, {
        items_per_page: B,
        next_text: "Next",
        num_display_entries: 10,
        num_edge_entries: 2,
        prev_text: "Prev",
        callback: function(H, G) {
            jQuery("#resultTab1").animate({
                left: -(H * 600)
            },
            400);
            return false;
        }
    });
    function E() {
        $(document).unbind("click");
        $(".popup.button").fadeOut();
    }
    $(".popup.button").click(function() {
        $(".popup.button").fadeOut();
    });
    $(".morebar span").click(function() {
        h = $("div.moreContainer > div.text").get(0).offsetHeight + 40;
        $("div.moreContainer").animate({
            height: h
        },
        500,
        function() {
            $("div.morebar").hide();
            $("div.lessbar").show();
        })
    });
    $(".lessbar span").click(function() {
        h = appMoreBarClosedHeight;
        $("div.moreContainer").animate({
            height: h
        },
        400,
        function() {
            $("div.lessbar").hide();
            $("div.morebar").show();
        })
    })
}
function showMoreData() {
    if ($("div#showMoreData1").css("display") == "none") {
        $("div.showMoreData").animate({
            height: "toggle"
        },
        500,
        function() {
            $("#btnShowMoreData").css("background-position", "0 -19px")
        })
    } else {
        $("div.showMoreData").animate({
            height: "toggle"
        },
        500,
        function() {
            $("div.showMoreData").css("display", "none");
            $("#btnShowMoreData").css("background-position", "0 0")
        })
    }
};
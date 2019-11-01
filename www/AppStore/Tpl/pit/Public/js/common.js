function openLogin() {
    $("#boxLoginTop").show()
}
function closeLogin() {
    $("#boxLoginTop").hide()
}
function requestNewPassword(E, A, F, C) {
    var B = document.getElementById(E);
    var D = B.emailAddress.value;
    var D = prompt(A, D != null ? D: "");
    if (D == null || D == "") {
        return
    }
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/session/requestNewPassword",
        data: {
            emailAddress: D
        },
        dataType: "json",
        success: function(G) {
            if (G.status == "ok") {
                alert(F)
            } else {
                showErrors(null, null, C, "Fehler:", null, G.errors);
            }
        },
        error: function() {
            alert(gl_fatalMsg);
        }
    })
}
function loginUser(D, E, B, A, F, C) {
    $.cookie("lf_e", D.emailAddress.value, {
        expires: 120,
        path: "/"
    });
    if (D.rememberMe) {
        $.cookie("lf_r", D.rememberMe.checked ? "1": "", {
            expires: 120,
            path: "/"
        })
    }
    $.cookie("lf_k", "", {
        expires: -1,
        path: "/"
    });
    D.loginSubmit.disabled = "disabled";
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/session/login",
        data: $(D).serialize(),
        dataType: "json",
        success: function(G) {
            D.loginSubmit.disabled = null;
            if (G.status == "ok") {
                if (D.rememberMe && D.rememberMe.checked) {
                    $.cookie("lf_k", G.autoLoginKey, {
                        expires: 30,
                        path: "/"
                    });
                }
                if (G.registering && B) {
                    window.location.href = B;
                } else {
                    if (E && G.pak) {
                        window.location.href = E + "&pak=" + G.pak;
                    } else {
                        if (E) {
                            window.location.href = E;
                        } else {
                            window.location.reload();
                        }
                    }
                }
            } else {
                showErrors(D, A, F, C, G.errors[0][1], G.errors);
            }
        },
        error: function() {
            D.loginSubmit.disabled = null;
            alert(gl_fatalMsg);
        }
    })
}
function logoutUser() {
    $.cookie("lf_e", "", {
        expires: -1,
        path: "/"
    });
    $.cookie("lf_r", "", {
        expires: -1,
        path: "/"
    });
    $.cookie("lf_k", "", {
        expires: -1,
        path: "/"
    });
    document.getElementById("exitLink").className = "exitDisabled";
    $.ajax({
        type: "GET",
        url: gl_localPrefix + "/session/logout",
        dataType: "json",
        success: function(A) {
            document.getElementById("exitLink").className = "exit";
            window.location.reload();
        },
        error: function() {
            document.getElementById("exitLink").className = "exit";
            alert(gl_fatalMsg);
        }
    })
}
function sendInvitation(D, B, A, F, C, E) {
    D.recommendSubmit.disabled = "disabled";
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/sendInvitation",
        data: $(D).serialize(),
        dataType: "json",
        success: function(G) {
            D.recommendSubmit.disabled = null;
            if (G.status == "ok") {
                D.reset();
                clearErrors(D, A, F);
                alert(B);
            } else {
                showErrors(D, A, F, C, E, G.errors);
            }
        },
        error: function() {
            D.recommendSubmit.disabled = null;
            alert(gl_fatalMsg);
        }
    })
}
function createBookmarkIcons(E) {
    var D = new Array("facebook", "twitter", "technorati", "stumble-upon", "mr-wong", "delicious", "digg", "google", "yahoo");
    var C = new Array("Facebook", "Twitter", "Technorati", "StumbleUpon", "MISTER WONG", "delicious", "digg", "Google", "Yahoo! Bookmarks");
    var A = '<ul id="bookmarks">';
    for (i = 0; i < C.length; i++) {
        A += '<li id="bookmark_' + D[i] + '" ><a href="' + gl_localPrefix + "/bookmark?s=" + D[i] + "&amp;u=" + escape(encodeUTF8(window.location.href)) + "&t=" + escape(encodeUTF8(document.title)) + '" title="' + C[i] + '" target="_blank" alt="' + C[i] + '"></li>'
    }
    A += "</ul>";
    var B = document.getElementById(E);
    B.innerHTML = A;
}
function encodeUTF8(C) {
    C = C.replace(/\r\n/g, "\n");
    var B = "";
    for (var A = 0; A < C.length; A++) {
        var D = C.charCodeAt(A);
        if (D < 128) {
            B += String.fromCharCode(D)
        } else {
            if ((D > 127) && (D < 2048)) {
                B += String.fromCharCode((D >> 6) | 192);
                B += String.fromCharCode((D & 63) | 128)
            } else {
                B += String.fromCharCode((D >> 12) | 224);
                B += String.fromCharCode(((D >> 6) & 63) | 128);
                B += String.fromCharCode((D & 63) | 128)
            }
        }
    }
    return B
}
function expandCollapseYear(A) {
    var B = document.getElementById("archive_" + A + "_a");
    var C = B.className == "expanded";
    C = !C;
    B.className = C ? "expanded": "collapsed";
    $("#archive_" + A + "_ul").css("display", C ? "": "none")
}
function expandCollapseMonth(B, D, A) {
    var C = document.getElementById("archive_" + B + "_" + D + "_a");
    var E = C.className == "expanded";
    E = !E;
    if (E) {
        loadMonth(B, D, A)
    }
    C.className = E ? "expanded": "collapsed";
    $("#archive_" + B + "_" + D + "_ul").css("display", E ? "": "none")
}
function loadMonth(B, D, A) {
    var C = $("#archive_" + B + "_" + D + "_ul");
    var F;
    if (A == "blog") {
        F = gl_localPrefix + "/blog/blogArchiveData"
    } else {
        if (A == "tests") {
            F = gl_localPrefix + "/tests/testsArchiveData"
        } else {
            return
        }
    }
    var E = B + "-" + D;
    if (typeof loadedMonths != undefined && $.inArray(E, loadedMonths) == -1) {
        $.ajax({
            type: "POST",
            cache: "false",
            url: F,
            data: {
                year: B,
                month: D
            },
            dataType: "json",
            success: function(G) {
                if (typeof(G.error) != "undefined") {
                    _handle_error(G.error)
                } else {
                    C.empty();
                    var H = "";
                    var I = G.articles;
                    $.each(I,
                    function(K, J) {
                        H += '<li><a href="' + gl_localPrefix + J.string2 + '" class="entry">' + J.string1 + "</a></li>";
                    });
                    C.html(H)
                }
            }
        });
        loadedMonths.push(E);
    }
}
function expandCollapseCategoryType(A) {
    var B = document.getElementById("tict_" + A + "_a");
    var C = B.className == "expanded";
    C = !C;
    B.className = C ? "expanded": "collapsed";
    $("#tict_" + A + "_ul").css("display", C ? "": "none")
}
function expandCollapseCategory(C, A) {
    var D = document.getElementById("tict_" + C + "_" + A + "_a");
    var B = D.className == "expanded";
    B = !B;
    if (B) {
        loadCategoryTests(C, A)
    }
    D.className = B ? "expanded": "collapsed";
    $("#tict_" + C + "_" + A + "_ul").css("display", B ? "": "none")
}
function loadCategoryTests(A, B) {
    var C = $("#tict_" + A + "_" + B + "_ul");
    var D = A + "-" + B;
    if (typeof loadedTests != "undefined" && $.inArray(D, loadedTests) == -1) {
        var E = gl_localPrefix + "/tests/testsListData";
        $.ajax({
            type: "POST",
            cache: "false",
            url: E,
            data: {
                type: A,
                category: B
            },
            dataType: "json",
            success: function(F) {
                if (typeof(F.error) != "undefined") {
                    _handle_error(F.error)
                } else {
                    C.empty();
                    var G = "";
                    var H = F.testsInCategory;
                    $.each(H,
                    function(J, I) {
                        G += '<li><a href="' + gl_localPrefix + "/" + I.string2 + '" class="entry">' + I.string1 + "</a></li>"
                    });
                    C.html(G)
                }
            }
        });
        loadedTests.push(D)
    }
}
function showErrors(J, I, K, D, A, F) {
    if (J) {
        for (i = 0; i < J.elements.length; i++) {
            var B = J.elements[i];
            var G = B.className ? B.className.indexOf("Error") : -1;
            if (G != -1) {
                B.className = B.className.substring(0, G)
            }
            if (B.id == "htmlTeaser") {
                setEditorError("htmlTeaser", false)
            }
            if (B.id == "htmlContent") {
                setEditorError("htmlContent", false)
            }
            if (B.id == "sectionTextFB") {
                setEditorError("sectionTextFB", false)
            }
            if (B.id == "sectionTextSO") {
                setEditorError("sectionTextSO", false)
            }
            if (B.id == "sectionTextSS") {
                setEditorError("sectionTextSS", false)
            }
            if (B.id == "sectionTextP") {
                setEditorError("sectionTextP", false)
            }
            $("#" + B.name + "Error").css("display", "none");
            $("#" + B.name + "Hint").css("display", "")
        }
        for (i = 0; i < F.length; i++) {
            var H = F[i][0].split("|");
            for (j = 0; j < H.length; j++) {
                var B = J.elements[H[j]];
                if (B) {
                    if (B.className && B.className.indexOf("Error") == -1) {
                        B.className = B.className.concat("Error")
                    }
                }
                if (H[j] == "htmlTeaser") {
                    setEditorError("htmlTeaser", true)
                }
                if (H[j] == "htmlContent") {
                    setEditorError("htmlContent", true)
                }
                if (H[j] == "sectionTextFB") {
                    setEditorError("sectionTextFB", true)
                }
                if (H[j] == "sectionTextSO") {
                    setEditorError("sectionTextSO", true)
                }
                if (H[j] == "sectionTextSS") {
                    setEditorError("sectionTextSS", true)
                }
                if (H[j] == "sectionTextP") {
                    setEditorError("sectionTextP", true)
                }
                var C = $("#" + H[j] + "Error");
                C.css("display", "");
                C.html(F[i][1]);
                $("#" + H[j] + "Hint").css("display", "none");
            }
        }
    }
    if (I) {
        $("#" + I).css("display", "");
        $("#" + K).html("<strong>" + D + "</strong> " + A);
    } else {
        if (D) {
            var E = "";
            for (i = 0; i < F.length; i++) {
                E = E + "\n" + F[i][1];
            }
            alert(D + "\n" + E);
        }
    }
}
function setEditorError(B, A) {
    var C = A ? "1px solid #cc0033": "1px solid #cccccc";
    $("#cke_" + B).css("border", C);
    $("#" + B + "___Frame").contents().find("#xEditingArea").css("border", C);
}
function clearErrors(C, A, F) {
    if (C) {
        for (i = 0; i < C.elements.length; i++) {
            var B = C.elements[i];
            var E = B.className.indexOf("Error");
            if (E != -1) {
                B.className = B.className.substring(0, E)
            }
            var D = $("#" + B.name + "Error");
            D.css("display", "none");
            D.html("");
            $("#" + B.name + "Hint").css("display", "")
        }
    }
    if (A) {
        $("#" + A).css("display", "none");
        $("#" + F).html("")
    }
}
function showPasswordParagraph(K, C, L, D) {
    var G = document.createElement("p");
    G.setAttribute("class", "label");
    var J = document.createElement("label");
    J.setAttribute("for", C);
    J.innerHTML = L;
    G.appendChild(J);
    var E = document.createElement("span");
    E.setAttribute("class", "optional");
    E.innerHTML = D;
    G.appendChild(E);
    var F = document.createElement("p");
    var B = document.createElement("input");
    B.setAttribute("type", "hidden");
    B.setAttribute("name", "pwFieldName");
    B.setAttribute("value", C);
    F.appendChild(B);
    var A = document.createElement("input");
    A.setAttribute("type", "password");
    A.setAttribute("class", "inputboxMediumError");
    A.setAttribute("id", C);
    A.setAttribute("name", C);
    A.setAttribute("maxlength", "200");
    F.appendChild(A);
    var I = document.createElement("span");
    I.setAttribute("id", C + "Error");
    I.setAttribute("class", "error");
    I.setAttribute("style", "display:none");
    F.appendChild(I);
    var H = document.getElementById(K);
    H.innerHTML = "";
    H.appendChild(G);
    H.appendChild(F);
    H.style.display = "";
}
function showPasswordParagraphMailForm(J, B, K, D) {
    var G = document.createElement("p");
    G.setAttribute("class", "first");
    G.innerHTML = D;
    var F = document.createElement("p");
    var C = document.createElement("label");
    C.setAttribute("for", B);
    C.innerHTML = K;
    F.appendChild(C);
    var A = document.createElement("input");
    A.setAttribute("type", "hidden");
    A.setAttribute("name", "pwFieldName");
    A.setAttribute("value", B);
    F.appendChild(A);
    var L = document.createElement("input");
    L.setAttribute("type", "password");
    L.setAttribute("class", "inputboxError");
    L.setAttribute("id", B);
    L.setAttribute("name", B);
    L.setAttribute("maxlength", "200");
    F.appendChild(L);
    var I = document.createElement("span");
    I.setAttribute("id", B + "Error");
    I.setAttribute("class", "error");
    I.setAttribute("style", "display:none");
    F.appendChild(I);
    var E = document.createElement("div");
    E.setAttribute("class", "hr");
    var H = document.getElementById(J);
    H.innerHTML = "";
    H.appendChild(G);
    H.appendChild(F);
    H.appendChild(E);
    H.style.display = ""
}
function showPasswordParagraphComment(G, A, H, C) {
    var D = document.createElement("label");
    D.setAttribute("for", A);
    D.innerHTML = H;
    var E = document.createElement("span");
    E.setAttribute("class", "date");
    E.innerHTML = C;
    var B = document.createElement("input");
    B.setAttribute("type", "hidden");
    B.setAttribute("name", "pwFieldName");
    B.setAttribute("value", A);
    var I = document.createElement("input");
    I.setAttribute("type", "password");
    I.setAttribute("class", "inputboxError");
    I.setAttribute("id", A);
    I.setAttribute("name", A);
    I.setAttribute("maxlength", "200");
    var J = document.createElement("span");
    J.setAttribute("id", A + "Error");
    J.setAttribute("class", "error");
    J.setAttribute("style", "display:none");
    var F = document.getElementById(G);
    F.innerHTML = "";
    F.appendChild(D);
    F.appendChild(document.createElement("br"));
    F.appendChild(E);
    F.appendChild(document.createElement("br"));
    F.appendChild(B);
    F.appendChild(I);
    F.appendChild(J);
    F.style.display = ""
}


function getNewCaptcha(B) {
    var A = new Date();
    document.getElementById(B).src = "/kaptcha/kaptcha.jpg?" + A.getTime()
}
var fileBrowserSuccessMessage;
function showFileBrowser(B, H) {
    fileBrowserSuccessMessage = B;
    var C = "/fckeditor/editor/filemanager/browser/default/browser.html?Type=" + H + "&Connector=http://" + window.location.hostname + "/fckeditor/editor/filemanager/connectors/php/connector.php";
    var E = screen.width * 0.7;
    var I = screen.height * 0.7;
    var G = (screen.width - E) / 2;
    var F = (screen.height - I) / 2;
    var A = "toolbar=no,status=no,resizable=yes,dependent=yes,width=" + E + ",height=" + I + ",left=" + G + ",top=" + F;
    var D = window.open(C, "BrowseWindow", A)
}
function SetUrl(B, C, E, D) {
    if (B.length > 7 && B.substr(0, 7) == "http://") {
        var A = B.indexOf("/", 7);
        if (A != -1) {
            B = B.substr(A)
        }
    }
    prompt(fileBrowserSuccessMessage, B)
}
function sendContributionLink(D, B, A, F, C, E) {
    D.mcSubmit.disabled = "disabled";
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/contact/sendContributionLink",
        data: $(D).serialize(),
        dataType: "json",
        success: function(G) {
            D.mcSubmit.disabled = null;
            if (G.status == "ok") {
                clearErrors(D, A, F);
                alert(B);
                D.reset();
                $("#boxMC").css("display", "none")
            } else {
                showErrors(D, A, F, C, E, G.errors)
            }
        },
        error: function() {
            D.mcSubmit.disabled = null;
            alert(gl_fatalMsg)
        }
    })
}
function enableReleaseDate(B) {
    var A = B ? "": "disabled";
    document.getElementById("releaseDate_date").disabled = A;
    document.getElementById("releaseDate_hour").disabled = A;
    document.getElementById("releaseDate_minute").disabled = A
}
function centerPopup(G) {
    var E = 0;
    var C = 0;
    if (typeof(window.innerWidth) == "number") {
        E = window.innerHeight;
        C = window.innerWidth
    } else {
        if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
            E = document.documentElement.clientHeight;
            C = document.documentElement.clientWidth
        } else {
            if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
                E = document.body.clientHeight;
                C = document.body.clientWidth
            }
        }
    }
    var B = document.getElementById(G);
    var A = B.offsetHeight;
    var H = B.offsetWidth;
    var F = (C - B.offsetWidth) / 2;
    var D = (E - B.offsetHeight) / 2;
    if (F < 0) {
        F = 0
    }
    if (D < 0) {
        D = 0
    }
    B.style.left = F + "px";
    B.style.top = D + "px"
}
function setTab(D, E, B) {
    for (d = 1; d <= B; d++) {
        var C = document.getElementById(D + "btn" + d);
        var A = document.getElementById(D + "Body" + d);
        if (C != null && A != null) {
            if (d == E) {
                C.className = "btn" + d + "A";
                A.style.display = "block"
            } else {
                C.className = "btn" + d;
                A.style.display = "none"
            }
        }
    }
}
function deletePaymentRequest(C, B, A) {
    if (!confirm(A)) {
        return
    }
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/billing/prr-delete",
        data: {
            uid: C,
            rid: B
        },
        dataType: "json",
        success: function(D) {
            var E = document.getElementById("containerPR");
            if (E) {
                E.parentNode.removeChild(E)
            }
        },
        error: function() {
            alert(gl_fatalMsg)
        }
    })
}
function showCharacterCountdown(D, A, C, B) {
    var F = D.val().length;
    var E = B.replace("{0}", F).replace("{1}", A);
    if (F > A) {
        E = '<span style="padding:0; margin:0; color:#cc0033">' + E + "</span>"
    }
    $("#" + C).html(E)
}
function radioButtonChecked(A) {
    if (typeof A.length == "undefined" && typeof A.checked == "boolean") {
        return A.checked
    }
    for (var B = 0; B < A.length; B++) {
        if (A[B].checked) {
            return true
        }
    }
    return false
}
function requestCancelInstallUninstall(B, C, A) {
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/market/apps/requestCancelInstallUninstall",
        data: {
            appId: B,
            request: C,
            install: A
        },
        dataType: "json",
        success: function(E) {
            if (E.status == "ok") {
                document.getElementById("shoppingDiv_" + B).innerHTML = E.innerHTML;
                var D = document.getElementById("shoppingDivPost_" + B);
                if (D != null) {
                    D.innerHTML = E.innerHTML
                }
            } else {
                if (E.sessionExpired) {
                    alert(E.sessionExpiredError)
                }
            }
        },
        error: function(D) {
            alert(fatalMsg + " " + D)
        }
    })
}
function requestUninstall(A, B) {
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/market/apps/requestUninstall",
        data: {
            appId: A
        },
        dataType: "json",
        success: function(C) {
            if (C.status == "ok") {
                $.fancybox(B, {
                    overlayColor: "#000"
                })
            } else {
                if (C.sessionExpired) {
                    alert(C.sessionExpiredError)
                }
            }
        },
        error: function(C) {
            alert(fatalMsg + " " + C)
        }
    })
}
function billingCountrySelected() {
    var A = $("#bc_countryId").val();
    var B = null;
    if (A == "GB") {
        B = "GBP"
    } else {
        if (A == "JP") {
            B = "JPY"
        } else {
            if (A == "US") {
                B = "USD"
            } else {
                if ("AT;AX;BE;BG;BL;CY;CZ;DE;DK;EE;ES;FI;FR;GR;HU;IE;IT;LT;LU;LV;MF;MT;NL;PL;PT;RO;SE;SI;SK".indexOf(A) != -1) {
                    B = "EUR"
                }
            }
        }
    }
    if (B != null) {
        $("#bc_currencyFix").css("display", "").html(B);
        $("#bc_currencySel").css("display", "none")
    } else {
        $("#bc_currencyFix").css("display", "none");
        $("#bc_currencySel").css("display", "")
    }
}
function contains(B, A) {
    return B.toLowerCase().indexOf(A.toLowerCase()) !== -1
}
function endsWith(A, B) {
    return A.toLowerCase().indexOf(B.toLowerCase(), A.length - B.length) !== -1
}
function trackOutbound(B, E, A) {
    try {
        var D = _gat._getTracker("UA-7489116-11");
        var F = "/outbound/" + B + "/" + E + "/" + A;
        D._trackPageview(F)
    } catch(C) {}
}
function setUserOrderCommentsDescending(A) {
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/community/user/set-order-comments-descending",
        data: {
            desc: A
        },
        dataType: "json",
        success: function(B) {
            window.location.reload()
        },
        error: function() {
            alert(gl_fatalMsg)
        }
    })
}
var unloadMsgTxt = null;
function setUnloadMessage(A) {
    unloadMsgTxt = A
}
function unloadMessage() {
    return unloadMsgTxt
}
function setBunload(A) {
    window.onbeforeunload = (A) ? unloadMessage: null
};
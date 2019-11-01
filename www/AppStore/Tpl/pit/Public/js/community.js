function setSocialButtons(A, B) {
    document.getElementById("socialButton_" + A).value = B
}
function sendMailToUser2(J, H, C, I, A, E, B, F, K, D, L, G) {
    J.submit.disabled = "disabled";
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/community/mail-to/post",
        data: $(J).serialize(),
        dataType: "json",
        success: function(M) {
            J.submit.disabled = null;
            if (M.status == "ok") {
                J.reset();
                clearErrors(J, I, A);
                $("#" + K).css("display", "none");
                alert(H);
                window.location.href = C
            } else {
                if (M.sessionExpired) {
                    showPasswordParagraphMailForm(K, D, L, G);
                    alert(M.sessionExpired)
                } else {
                    if (M.passwordError) {
                        showPasswordParagraphMailForm(K, D, L, G)
                    }
                    showErrors(J, I, A, E, B, M.errors)
                }
            }
        },
        error: function(M) {
            J.submit.disabled = null;
            alert(M.status == 403 ? F: fatalMsg)
        }
    })
}
function showEditor(A, B) {
    $("#entry_" + A).css("display", B ? "none": "");
    $("#entry_" + A + "_editor").css("display", B ? "": "none")
}
function updateUserName(C, D, E) {
    var B = C.name.value.trim();
    if (B.length < 3) {
        alert(E);
        return
    }
    var A = D + " " + B;
    if (!confirm(A)) {
        return
    }
    updateUser(C)
}
function updateUser(A) {
    var B = A.fieldName.value;
    var C = document.getElementById("submit_" + B);
    C.disabled = "disabled";
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/community/user/update",
        data: $(A).serialize(),
        dataType: "json",
        success: function(D) {
            C.disabled = null;
            if (D.status == "ok") {
                clearErrors(A, null, null);
                if (B == "email") {
                    if (D.emailChangeRequested) {
                        alert(D.emailChangeRequested)
                    }
                } else {
                    if (B == "password") {} else {
                        if (B == "name") {
                            window.location.reload()
                        } else {
                            if (B == "socialButtons") {} else {
                                $("#entry_" + B + "_value").html(D.newText);
                                if (B == "abbreviateCommunityName") {
                                    $("#headline").html(D.newText)
                                }
                            }
                        }
                    }
                }
                clearErrors(A, null, null);
                showEditor(B, false)
            } else {
                if (D.sessionExpired) {
                    alert(D.sessionExpiredError)
                } else {
                    showErrors(A, null, null, null, null, D.errors)
                }
            }
        },
        error: function() {
            C.disabled = null;
            alert(gl_fatalMsg)
        }
    })
}
var ruaConfMsgProfile;
var ruaConfMsgWatchList;
var ruaConfMsgHideReco;
function initRemoveUserAppMethod(B, A) {
    initRemoveUserAppMethod2(B, A, null)
}
function initRemoveUserAppMethod2(C, B, A) {
    ruaConfMsgProfile = C;
    ruaConfMsgWatchList = B;
    ruaConfMsgHideReco = A
}
function removeUserApp(B, C, A) {
    if (A) {
        removeUserApp2(B, C, "watchlist")
    } else {
        removeUserApp2(B, C, "profile")
    }
}
function removeUserApp2(D, E, C) {
    var B = C == "profile" ? ruaConfMsgProfile: C == "watchlist" ? ruaConfMsgWatchList: C == "hideReco" ? ruaConfMsgHideReco: null;
    if (B == null) {
        return
    }
    var A = B.replace("{0}", E);
    if (!confirm(A)) {
        return
    }
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/community/user/update-apps-list",
        data: {
            appId: D,
            listName: C,
            add: false
        },
        dataType: "json",
        success: function(F) {
            if (F.status == "ok") {
                var G = document.getElementById("app_" + C + "_" + D);
                if (G) {
                    document.getElementById("entry_apps_" + C).removeChild(G)
                }
            } else {
                if (F.sessionExpired) {
                    alert(F.sessionExpiredError)
                }
            }
        },
        error: function() {
            alert(gl_fatalMsg)
        }
    })
}
function showUploadImage(D, C, B, A) {
    $("#uploadFormImageId").val(D);
    $("#uploadImageTitle").html(C);
    $("#uploadImageHint").html(B);
    $("#submit_imageFile").val(A);
    $("#containerUploadImage").css("display", "");
    centerPopup("uploadImage")
}
function hideUploadImage() {
    $("#containerUploadImage").css("display", "none")
}
function uploadImage(A) {
    var B = A.imageId.value;
    A.submit_imageFile.disabled = "disabled";
    $(A).ajaxSubmit({
        iframe: true,
        dataType: "json",
        success: function(D) {
            A.submit_imageFile.disabled = null;
            if (D.status == "ok") {
                clearErrors(A, null, null);
                var C = document.getElementById("image_" + B);
                if (C) {
                    C.src = D.imageURI;
                    if (B == "developerLogo") {
                        C.style.display = ""
                    }
                }
                if (B == "user") {
                    document.getElementById("topUserImage").src = D.smallImageURI
                }
                $("#deleteImage_" + B).css("display", "");
                hideUploadImage()
            } else {
                if (D.sessionExpired) {
                    alert(D.sessionExpiredError)
                } else {
                    showErrors(A, null, null, null, null, D.errors)
                }
            }
        },
        error: function() {
            A.submit_imageFile.disabled = null;
            alert(gl_fatalMsg)
        }
    })
}
function deleteImage(C, B, A, D, E) {
    if (!confirm(E)) {
        return
    }
    $.ajax({
        type: "GET",
        url: gl_localPrefix + "/community/user/delete-image",
        data: {
            userId: C,
            imageId: B
        },
        dataType: "json",
        success: function(G) {
            if (G.status == "ok") {
                var F = document.getElementById("image_" + B);
                if (F) {
                    F.src = A;
                    if (B == "developerLogo") {
                        F.style.display = "none"
                    }
                }
                if (B == "user") {
                    document.getElementById("topUserImage").src = D
                }
                $("#deleteImage_" + B).css("display", "none")
            } else {
                if (G.sessionExpired) {
                    alert(G.sessionExpiredError)
                }
            }
        },
        error: function() {
            alert(gl_fatalMsg)
        }
    })
}
function phoneModelChanged(A) {
    var B = A.value == "*";
    $("#phoneModelText").css("display", B ? "": "none");
    $("#phoneModelTextLabel").css("display", B ? "": "none")
}
function phoneCarrierChanged(A) {
    var B = A.value == "*";
    $("#phoneCarrierText").css("display", B ? "": "none");
    $("#phoneCarrierTextLabel").css("display", B ? "": "none")
}
var mailOnOwnThreadPost;
var mailOnSubscribedThreadPost;
function toggleMailOnThreadPost(D, C, B) {
    var A;
    if (D == "own") {
        A = !mailOnOwnThreadPost
    } else {
        if (D == "subscribed") {
            A = !mailOnSubscribedThreadPost
        } else {
            return
        }
    }
    $.ajax({
        type: "GET",
        url: gl_localPrefix + "/community/user/set-mail-on-thread-post",
        data: {
            type: D,
            value: A
        },
        dataType: "json",
        success: function(E) {
            if (E.status == "ok") {
                $("#mailOnThreadPost_" + D).html(A ? C: B);
                if (D == "own") {
                    mailOnOwnThreadPost = A
                } else {
                    if (D == "subscribed") {
                        mailOnSubscribedThreadPost = A
                    }
                }
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
function loadUserContributions(C, B, D, E) {
    var A = $("#" + E);
    A.html("");
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/community/user-load-" + C,
        data: {
            userId: B,
            si: D
        },
        dataType: "json",
        success: function(F) {
            A.html(F.innerHTML)
        },
        error: function() {
            A.html("");
            alert(gl_fatalMsg)
        }
    })
};
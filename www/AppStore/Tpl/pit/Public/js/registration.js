function registerUser(C, A, E, B, D) {
    C.submit.disabled = "disabled";
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/registration/post",
        data: $(C).serialize(),
        dataType: "json",
        success: function(I) {
            C.submit.disabled = null;
            if (I.status == "ok") {
                document.location.href = I.targetURL;
            } else {
                showErrors(C, A, E, B, D, I.errors);
                var F = false;
                for (var H = 0; H < I.errors.length; H++) {
                    var G = I.errors[H][0];
                    if (G == "termsAccepted") {
                        F = true;
                    } else {
                        if (G == "captcha") {
                            getNewCaptcha("captchaImg");
                        }
                    }
                }
                document.getElementById("termsAccepted").className = F ? "accept checkboxError": "accept";
            }
        },
        error: function() {
            C.submit.disabled = null;
            alert(gl_fatalMsg);
        }
    })
}
function requestWelcomeMail(A, B) {
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/registration/requestWelcomeMail",
        data: {
            emailAddress: A
        },
        dataType: "json",
        success: function(C) {
            if (C.error) {
                alert(C.error);
            } else {
                alert(B);
            }
        },
        error: function() {
            alert(gl_fatalMsg);
        }
    })
}
function postChanges(C, E, A, F, B, D) {
    $.ajax({
        type: "POST",
        url: gl_localPrefix + "/registration/postChanges",
        data: $(C).serialize(),
        dataType: "json",
        success: function(G) {
            if (G.status == "ok") {
                document.location.href = E;
            } else {
                if (G.sessionExpired) {
                    alert(B + "\n\n" + G.sessionExpiredError);
                    window.location.reload();
                } else {
                    showErrors(C, A, F, B, D, G.errors);
                }
            }
        },
        error: function() {
            alert(gl_fatalMsg);
        }
    })
}
function showDeveloperPopup() {
    $("#containerDeveloperPopup").css("display", "");
    centerDeveloperPopup();
}
function centerDeveloperPopup() {
    centerPopup("developerPopup");
}
function closeDeveloperPopup(A) {
    $("#containerDeveloperPopup").css("display", "none");
    if (A == 0) {
        return
    }
    $("#regType").val(A == 2 ? "developer": "member");
    var B = A == 1 ? "": "none";
    $("#member_h").css("display", B);
    $("#member_intro").css("display", B);
    $("#member_popup").css("display", B);
    $("#member_info").css("display", B);
    var C = A == 2 ? "": "none";
    $("#developer_h").css("display", C);
    $("#developer_intro").css("display", C);
    $("#developer_popup").css("display", C);
    $("#developer_info").css("display", C);
};
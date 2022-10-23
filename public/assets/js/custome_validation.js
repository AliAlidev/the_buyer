function validationError(element, rules) {
    element.parent().find(".invalid-feedback").remove();

    var hasError = false;
    rules.forEach((rule) => {
        if (rule.type == "required") {
            if (element.val() == "") {
                hasError = true;
                var newItem = $(
                    '<div class= "invalid-feedback"></div>'
                ).insertAfter(element);
                newItem.addClass("d-flex justify-content-start");
                newItem.append(
                    "{{ __('labels.profile.you_should_enter_data') }}"
                );
            }
        }
        if (rule.type == "min") {
            if (element.val().length < rule.value) {
                hasError = true;
                var msg =
                    "{{ __('labels.profile.new_pass_at_least_char', ['charNum' => '#id']) }}";
                msg = msg.replace("#id", rule.value);
                var newItem = $(
                    '<div class= "invalid-feedback"></div>'
                ).insertAfter(element);
                newItem.addClass("d-flex justify-content-start");
                newItem.append(msg);
            }
        }
        if (rule.type == "max") {
            if (element.val().length > rule.value) {
                hasError = true;
                var msg =
                    "{{ __('labels.profile.new_pass_at_max_char', ['charNum' => '#id']) }}";
                msg = msg.replace("#id", rule.value);
                var newItem = $(
                    '<div class= "invalid-feedback"></div>'
                ).insertAfter(element);
                newItem.addClass("d-flex justify-content-start");
                newItem.append(msg);
            }
        }
        if (rule.type == "same") {
            if (rule.fValue != rule.sValue) {
                hasError = true;
                var newItem = $(
                    '<div class= "invalid-feedback"></div>'
                ).insertAfter(element);
                newItem.addClass("d-flex justify-content-start");
                newItem.append(
                    "{{ __('labels.profile.password_and_conf_mismatch') }}"
                );
            }
        }

        if (hasError) {
            element.css({
                "border-color": "red",
            });
        } else {
            element.css({
                "border-color": "#35a989",
            });
        }
    });
    return hasError;
}

function clearValidation(element) {
    element.css({
        "border-color": "rgba(50, 151, 211, .25)",
    });
    element.parent().find(".invalid-feedback").hide();
    element.parent().find(".invalid-feedback").empty();
    element.removeClass("is-valid");
}

function showMessage(result, form_id) {
    console.log(result);
    $('<div id="respone_message"></div>').insertBefore("#" + form_id);
    if (result.success) {
        $("#respone_message").empty();
        $("#respone_message").removeClass("alert alert-danger");
        $("#respone_message").addClass("alert alert-success");
        $("#respone_message").addClass("d-flex justify-content-start");
        var response =
            '<div class="row"><div class="col-md-12">' +
            result.message +
            '</div><div class="col-md-6"><a href="' +
            result.data.pdf_link +
            '" target="_blank">Invoice Link</a></div></div>';
        $("#respone_message").append(response);
        $("#respone_message").removeAttr("hidden");
        setTimeout(() => {
            if ($("#respone_message").hasClass("alert alert-success")) {
                $("#respone_message").empty();
                $("#respone_message").removeClass("alert alert-success");
                $("#respone_message").removeClass("alert alert-danger");
                $("#respone_message").attr("hidden", true);
            }
        }, 60000);
    } else {
        $("#respone_message").empty();
        $("#respone_message").removeClass("alert alert-success");
        $("#respone_message").addClass("alert alert-danger");
        $("#respone_message").addClass("d-flex justify-content-start");

        $("#respone_message").append("<ul>");
        result.data.forEach((element) => {
            $("#respone_message").append("<li>");
            $("#respone_message").append(element);
            $("#respone_message").append("</li>");
        });
        $("#respone_message").append("</ul>");
        $("#respone_message").removeAttr("hidden");
        setTimeout(() => {
            if ($("#respone_message").hasClass("alert alert-danger")) {
                $("#respone_message").empty();
                $("#respone_message").removeClass("alert alert-success");
                $("#respone_message").removeClass("alert alert-danger");
                $("#respone_message").attr("hidden", true);
            }
        }, 5000);
    }
}

function showShortMessage(type, message, element_id) {
    $('<div id="respone_message"></div>').insertBefore("#" + element_id);
    if (type == "success") {
        $("#respone_message").empty();
        $("#respone_message").removeClass("alert alert-danger");
        $("#respone_message").addClass("alert alert-success");
        $("#respone_message").addClass("d-flex justify-content-start");
        $("#respone_message").append(message);
        $("#respone_message").removeAttr("hidden");
    } else {
        $("#respone_message").empty();
        $("#respone_message").removeClass("alert alert-success");
        $("#respone_message").addClass("alert alert-danger");
        $("#respone_message").addClass("d-flex justify-content-start");

        $("#respone_message").append("<ul>");
        message.forEach((element) => {
            $("#respone_message").append("<li>");
            $("#respone_message").append(element);
            $("#respone_message").append("</li>");
        });
        $("#respone_message").append("</ul>");
        $("#respone_message").removeAttr("hidden");
    }

    setTimeout(() => {
        $("#respone_message").empty();
        $("#respone_message").removeClass("alert alert-success");
        $("#respone_message").removeClass("alert alert-danger");
        $("#respone_message").attr("hidden", true);
    }, 5000);
}

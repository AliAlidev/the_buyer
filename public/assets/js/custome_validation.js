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
                newItem.append(rule.message);
            }
        }
        if (rule.type == "min") {
            if (element.val().length < rule.value) {
                hasError = true;
                var newItem = $(
                    '<div class= "invalid-feedback"></div>'
                ).insertAfter(element);
                newItem.addClass("d-flex justify-content-start");
                newItem.append(rule.message);
            }
        }
        if (rule.type == "max") {
            if (element.val().length > rule.value) {
                hasError = true;
                var newItem = $(
                    '<div class= "invalid-feedback"></div>'
                ).insertAfter(element);
                newItem.addClass("d-flex justify-content-start");
                newItem.append(rule.message);
            }
        }
        if (rule.type == "same") {
            if (rule.fValue != rule.sValue) {
                hasError = true;
                var newItem = $(
                    '<div class= "invalid-feedback"></div>'
                ).insertAfter(element);
                newItem.addClass("d-flex justify-content-start");
                newItem.append(rule.message);
            }
        }
        if (rule.type == "match") {
            var regix = new RegExp(rule.string);
            if (!regix.test(element.val())) {
                hasError = true;
                var newItem = $(
                    '<div class= "invalid-feedback"></div>'
                ).insertAfter(element);
                newItem.addClass("d-flex justify-content-start");
                newItem.append(rule.message);
            }
        }
        if (rule.type == "phone_number") {
            var regix = new RegExp(
                /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im
            );
            if (!regix.test(element.val())) {
                hasError = true;
                var newItem = $(
                    '<div class= "invalid-feedback"></div>'
                ).insertAfter(element);
                newItem.addClass("d-flex justify-content-start");
                newItem.append(rule.message);
            }
        }
        if (rule.type == "email") {
            var regix = new RegExp(/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i);
            if (!regix.test(element.val())) {
                hasError = true;
                var newItem = $(
                    '<div class= "invalid-feedback"></div>'
                ).insertAfter(element);
                newItem.addClass("d-flex justify-content-start");
                newItem.append(rule.message);
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

function isValidPhone(phoneNumber) {
    var found = phoneNumber.search(
        /^(\+{1}\d{2,3}\s?[(]{1}\d{1,3}[)]{1}\s?\d+|\+\d{2,3}\s{1}\d+|\d+){1}[\s|-]?\d+([\s|-]?\d+){1,2}$/
    );
    if (found > -1) {
        return true;
    } else {
        return false;
    }
}

function validateSelect2(element_id, message) {
    var select2 = $("#" + element_id);
    // Reset
    select2.parents(".form-group").removeClass("is-invalid");
    select2.parents(".form-group").removeClass("is-valid");

    if (select2.val() == "" || select2.val() == null) {
        select2.parents(".form-group").addClass("is-invalid");
        select2
            .parents(".form-group")
            .parent()
            .find(".invalid-feedback")
            .remove();
        var newItem = $('<div class= "invalid-feedback"></div>').insertAfter(
            select2.parents(".form-group")
        );
        newItem.addClass("d-flex justify-content-start");
        newItem.append(message);
    } else {
        select2.parents(".form-group").addClass("is-valid");
    }
}

function clearSelect2Validation(element) {
    element.parents(".form-group").removeClass("is-invalid");
    element.parents(".form-group").removeClass("is-valid");
    element.parents(".form-group").parent().find(".invalid-feedback").remove();
    validateSelect2(element.attr("id"));
}

function clearValidation(element) {
    element.css({
        "border-color": "rgba(50, 151, 211, .25)",
    });
    element.parent().find(".invalid-feedback").hide();
    element.parent().find(".invalid-feedback").empty();
}

function showValidationMessage(result, element_id) {
    $('<div id="respone_message"></div>').insertBefore("#" + element_id);

    $("#respone_message").empty();
    $("#respone_message").removeClass("alert alert-success");
    $("#respone_message").addClass("alert alert-danger");
    if (sessionStorage.getItem("curr_language") == "AR") {
        $("#respone_message").css({
            "text-align": "right",
        });
    } else {
        $("#respone_message").css({
            "text-align": "left",
        });
    }

    $("#respone_message").append("<ul>");
    $.each(result.errors, function (key, val) {
        $("#respone_message").append("<li>" + val[0] + "</li>");
    });
    $("#respone_message").append("</ul>");
    $("#respone_message").removeAttr("hidden");

    setTimeout(() => {
        $("#respone_message").empty();
        $("#respone_message").removeClass("alert alert-success");
        $("#respone_message").removeClass("alert alert-danger");
        $("#respone_message").attr("hidden", true);
    }, 5000);
}

function showMessage(result, element_id) {
    $('<div id="respone_message"></div>').insertBefore("#" + element_id);
    if (result.success) {
        $("#respone_message").empty();
        $("#respone_message").removeClass("alert alert-danger");
        $("#respone_message").addClass("alert alert-success");
        $("#respone_message").addClass("d-flex justify-content-start");
        $("#respone_message").append(result.message);
        $("#respone_message").removeAttr("hidden");
    } else {
        $("#respone_message").empty();
        $("#respone_message").removeClass("alert alert-success");
        $("#respone_message").addClass("alert alert-danger");
        $("#respone_message").addClass("d-flex justify-content-start");

        $("#respone_message").append("<ul>");

        array = [];
        if (
            result.message == "Validation errors" ||
            result.message == "Validation error"
        ) {
            array = result.data;
        } else {
            array = result.message;
        }

        $.each(array, function (key, val) {
            $("#respone_message").append("<li>");
            if (Array.isArray(val)) {
                $("#respone_message").append(val[0]);
            } else {
                $("#respone_message").append(val);
            }
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

function showInvoiceMessage(result, form_id) {
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

        array = [];
        if (
            result.message == "Validation errors" ||
            result.message == "Validation error"
        ) {
            array = result.data;
        } else {
            array = result.message;
        }

        $.each(array, function (key, val) {
            $("#respone_message").append("<li>");
            if (Array.isArray(val)) {
                $("#respone_message").append(val[0]);
            } else {
                $("#respone_message").append(val);
            }
            $("#respone_message").append("</li>");
        });

        $("#respone_message").append("</ul>");
        $("#respone_message").removeAttr("hidden");

        setTimeout(() => {
            $("#respone_message").empty();
            $("#respone_message").removeClass("alert alert-success");
            $("#respone_message").removeClass("alert alert-danger");
            $("#respone_message").attr("hidden", true);
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

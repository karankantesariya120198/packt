$(document).ready(function() {
    
    var loginForm = $("form[name='loginForm']");
    
    $.validator.addMethod("emailRegex", function(value, element, regexpr) {          
        return regexpr.test(value);
    }, "Please enter valid email address.");

    $.validator.addMethod("passwordRegex", function(value, element, regexpr) {          
        return regexpr.test(value);
    }, "Password must contain at least 6 characters including uppercase, lowercase, special characters, and numbers.");

    loginForm.validate({
        ignore: [],
        errorClass: 'text-danger small',
        rules: {
            email: {
                required: true,
                email: true,
                emailRegex : /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i,
            },
            password: {
                required: true,
                minlength: 6,
                maxlength: 20,
                passwordRegex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{6,})/,
            }
        },
        messages: {
            email: {
                required: "Please enter email address.",
                email: "Please enter valid email address.",
            },
            password: {
                required: "Please enter password.",
                minlength: "Password must contain at least 6 characters including uppercase, lowercase, special characters, and numbers.",
                maxlength: "Password must contain at least 6 characters including uppercase, lowercase, special characters, and numbers.",
            }
        },
        highlight: function(element, errorClass) {
            $(element).parents("div.control-group").addClass(errorClass);
        },
        unhighlight: function(element, errorClass) {
            $(element).parents(".error").removeClass(errorClass);
        }
    });
});

$("body").on("click", "#btnLogin", function (e) {
    e.preventDefault();
    if ($(loginForm).valid()) {
        $(loginForm).submit();
    }
});
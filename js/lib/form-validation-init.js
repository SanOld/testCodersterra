
!function($) {
    "use strict";

    var FormValidator = function() {
        this.$commentForm = $("#commentForm"), //this could be any form, for example we are specifying the comment form
        this.$signupForm = $("#signupForm");
        this.$loginForm = $("#loginForm");
        this.$recoverForm = $("#recover-password");
        this.$resetForm = $("#resetForm");
    };

    //init
    FormValidator.prototype.init = function() {
        //validator plugin
        // $.validator.setDefaults({
        //     submitHandler: function() { swal("Good job!", "You clicked the button!", "success"); }
        // });

        // validate the comment form when it is submitted
        this.$commentForm.validate();


        // validate the recover password form when it is submitted
        this.$recoverForm.validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                email: "Bitte geben Sie eine Email Adresse ein, um das Passwort zurückzusetzen"
            },
            highlight: function(element) {
                $(element).parent('.wrap-line').addClass('error');
                $(element).parents('.wrap-line').removeClass('success');
                $('#recover-password .alert-danger').fadeIn();

            },
            unhighlight: function(element) {
                $(element).parent('.wrap-line').removeClass('error');
                $(element).parents('.wrap-line').addClass('success');
                $('#recover-password .alert-danger').fadeOut();
            }
        });

        // validate the login form when it is submitted
        this.$loginForm.validate({
            rules: {
                 username: {
                    required: true,
                    minlength: 2
                },
                password: {
                    required: true,
                    minlength: 5
                }
            },
            messages: {
                 username: {
                    required: "Bitte Benutzername eingeben",
                    minlength: "Benutzername muss aus mindestens 2 Zeichen bestehen"
                },
                password: {
                    required: "Bitte geben Sie ein Passwort",
                    minlength: "Das Passwort muss mindestens 5 Zeichen lang sein"
                }
            },
            highlight: function(element) {
                $(element).parent('.wrap-line').addClass('error');
                $('#loginForm .alert-danger').fadeIn();
                $(element).parent('.wrap-line').removeClass('success');
            },
            unhighlight: function(element) {
                $(element).parent('.wrap-line').removeClass('error');
                $('#loginForm .alert-danger').fadeOut();
                $(element).parent('.wrap-line').addClass('success');
            }
        });

         // validate the reset form when it is submitted
        this.$resetForm.validate({
            rules: {
                 password: {
                    required: true,
                    minlength: 5
                },
                password2: {
                    required: true,
                    minlength: 5,
                    equalTo: "#password"
                }
            },
            messages: {
                password: {
                    required: "Bitte geben Sie ein Passwort",
                    minlength: "Ihr Passwort muss aus mindestens 5 Zeichen bestehen"
                },
                password2: {
                    required: "Bitte geben Sie ein Passwort",
                    minlength: "Ihr Passwort muss aus mindestens 5 Zeichen bestehen",
                    equalTo: "Bitte geben Sie zweimal das gleiche Passwort ein"
                }
            },
            highlight: function(element) {
                $(element).parent('.wrap-line').addClass('error');
                $(element).parents('.wrap-line').removeClass('success');
                $('#resetForm .alert-danger').fadeIn();
            },
            unhighlight: function(element) {
                $(element).parent('.wrap-line').removeClass('error');
                $(element).parents('.wrap-line').addClass('success');
                $('#resetForm .alert-danger').fadeOut();
            }
        });



        // validate signup form on keyup and submit
        this.$signupForm.validate({
            rules: {
                firstname: "required",
                lastname: "required",
                username: {
                    required: true,
                    minlength: 2
                },
                password: {
                    required: true,
                    minlength: 5
                },
                confirm_password: {
                    required: true,
                    minlength: 5,
                    equalTo: "#password"
                },
                email: {
                    required: true,
                    email: true
                },
                topic: {
                    required: "#newsletter:checked",
                    minlength: 2
                },
                agree: "required"
            },
            messages: {
                firstname: "Please enter your firstname",
                lastname: "Please enter your lastname",
                username: {
                    required: "Please enter a username",
                    minlength: "Your username must consist of at least 2 characters"
                },
                password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 5 characters long"
                },
                confirm_password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 5 characters long",
                    equalTo: "Please enter the same password as above"
                },
                email: "Please enter a valid email address",
                agree: "Please accept our policy"
            }
        });

        // propose username by combining first- and lastname
        $("#username").focus(function() {
            var firstname = $("#firstname").val();
            var lastname = $("#lastname").val();
            if(firstname && lastname && !this.value) {
                this.value = firstname + "." + lastname;
            }
        });

        //code to hide topic selection, disable for demo
        var newsletter = $("#newsletter");
        // newsletter topics are optional, hide at first
        var inital = newsletter.is(":checked");
        var topics = $("#newsletter_topics")[inital ? "removeClass" : "addClass"]("gray");
        var topicInputs = topics.find("input").attr("disabled", !inital);
        // show when newsletter is checked
        newsletter.click(function() {
            topics[this.checked ? "removeClass" : "addClass"]("gray");
            topicInputs.attr("disabled", !this.checked);
        });

    },
    //init
    $.FormValidator = new FormValidator, $.FormValidator.Constructor = FormValidator
}(window.jQuery),


//initializing 
function($) {
    "use strict";
    $.FormValidator.init()
}(window.jQuery);
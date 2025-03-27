$(document).ready(function () {
    $('#show-signup-form').click(function() {
        $('#login-form').hide();
        $('#signup-form').show();
        $('#form-title').text('Signup');
    });
    
    $('#show-login-form').click(function() {
        $('#signup-form').hide();
        $('#login-form').show();
        $('#form-title').text('Login');
    });
    
    $('#show-password').change(function() {
        var passwordField = $('#password');
        if ($(this).prop('checked')) {
            passwordField.attr('type', 'text');
        } else {
            passwordField.attr('type', 'password');
        }
    });
    
    $('#show-signup-password').change(function() {
        var signupPasswordField = $('#signup-password');
        if ($(this).prop('checked')) {
            signupPasswordField.attr('type', 'text');
        } else {
            signupPasswordField.attr('type', 'password');
        }
    });

    $(document).on('submit', '#login-form', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        
        $.ajax({
            type: "POST",
            url: "actions/login.php?action=login",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);
                if (response.status === 200) {
                
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        button: "OK",
                    }).then(() => {
                        window.location.href = response.url;
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response.message,
                        icon: "error",
                        button: "OK",
                    });
                }                    
            }
        });
    });

    $(document).on("submit", "#signup-form", function (e) {
        e.preventDefault();

        if (!validateForm()) {
            return;
        }

        var formData = new FormData(this);

        $.ajax({
            type: "POST",
            url: "actions/login.php?action=signup",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json", 
            success: function (response) {
                if (response.status === 200) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        button: "OK",
                    }).then(() => {
                        window.location.href = response.url || "index.php"; 
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response.message,
                        icon: "error",
                        button: "OK",
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: "Error!",
                    text: "Something went wrong. Please try again!",
                    icon: "error",
                    button: "OK",
                });
            }
        });
    });

    function validateForm() {
        let username = $("#signup-username").val().trim();
        let password = $("#signup-password").val().trim();
        let confirmPassword = $("#signup-password-confirmation").val().trim();

        if (username === "" || password === "" || confirmPassword === "") {
            Swal.fire({
                title: "Error!",
                text: "All fields are required!",
                icon: "error",
                button: "OK",
            });
            return false;
        }

        if (password.length < 6) {
            Swal.fire({
                title: "Error!",
                text: "Password must be at least 6 characters long!",
                icon: "error",
                button: "OK",
            });
            return false;
        }

        if (password !== confirmPassword) {
            Swal.fire({
                title: "Error!",
                text: "Passwords do not match!",
                icon: "error",
                button: "OK",
            });
            return false;
        }

        return true;
    }
    
});
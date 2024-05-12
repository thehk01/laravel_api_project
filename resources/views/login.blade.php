
@include('header');

<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col-md-12">
            <h1 class="text-center text-danger">Login</h1>
            <form id="login_form">
                @csrf <!-- Add this line to include CSRF token -->

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp"
                                placeholder="Enter email">
                            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone
                                else.</small>
                                <span class="error email_err"></span> <!-- Add this line for error messages -->
                            </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Password">
                            <span class="error password_err"></span>
                        </div>
                    </div>
                </div>
                <a href="/forget-password">Reset Password</a>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

            <div id="alert-container"></div>

        </div>
    </div>
</div>


<script>
$(document).ready(function(){
    $("#login_form").submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();

        // Clear error messages when the user starts typing
        $(".form-control").on('input', function(){
            var fieldName = $(this).attr('name');
            $("."+fieldName+"_err").text('');
        });

        $.ajax({
            url: "http://127.0.0.1:8000/api/login",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(data){
                console.log(data);
                if(data.success){
                    handleSuccess(data);
                } else {
                    handleError(data);
                }
            },
            error: function(xhr, status, error) {
                handleError(xhr.responseJSON);
            }
        });
    });

    function handleSuccess(data){
        if (data.token_type && data.access_token) {
            // Store token in localStorage
            localStorage.setItem("user_token", data.token_type + " " + data.access_token);

            // Redirect to profile page
            window.location.href = "/profile";
        } else {
            // Display a success alert
            var alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            '<strong>Success!</strong> ' + data.msg +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span></button></div>';

            // Append the alert to the container
            $("#alert-container").html(alertHtml);


        }
    }

    function handleError(errors) {
    if (errors && errors.msg) {
        // Display the general error message in Bootstrap alert box
        var errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<strong>Error!</strong> ' + errors.msg +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span></button></div>';

        // Append the alert to the container
        $("#alert-container").html(errorHtml);
    } else if (errors && errors.errors) {
        // Display field-specific error messages in Bootstrap alert boxes
        Object.keys(errors.errors).forEach(function(fieldName) {
            var errorMessages = errors.errors[fieldName];
            var errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<strong>Error!</strong> ' + errorMessages.join('<br>') +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span></button></div>';

            // Append the alert to the container
            $("."+fieldName+"_err").html(errorHtml);
        });
    }
}



});

 </script>

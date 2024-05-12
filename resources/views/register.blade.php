@include('header');

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1 class="text-center text-danger">Registration</h1>
                <form id="register_form">
                    @csrf <!-- Add this line to include CSRF token -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter Full Name">
                                <span class="error name_err"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp"
                                    placeholder="Enter email">
                                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone
                                    else.</small>
                                <span class="error email_err"></span>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password"
                                    name="password_confirmation" placeholder="Enter Password">
                                <span class="error password_confirmation_err"></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <div id="alert-container"></div>

            </div>
        </div>
    </div>

   <script>
       $(document).ready(function(){
    $("#register_form").submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();

        // Clear error messages when user starts typing
        $(".form-control").on('input', function(){
            var fieldName = $(this).attr('name');
            $("."+fieldName+"_err").text('');
        });

        $.ajax({
            url: "http://127.0.0.1:8000/api/register",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(data){
                if(data.message){
                    // Handle success, if needed
                     // Create a success alert
                    var alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                    '<strong>Success!</strong> ' + data.message +
                                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                    '<span aria-hidden="true">&times;</span></button></div>';

                    // Append the alert to the container
                    $("#alert-container").html(alertHtml);
                    $("#register_form")[0].reset();

                    console.log(data.message);
                }
            },
            error: function(xhr, status, error) {
                if(xhr.status === 422) {
                    // Validation error, display error messages
                    var errors = xhr.responseJSON.errors;
                    printErrorMsg(errors);
                } else {
                    console.error(xhr.responseText);
                    // Handle other types of errors
                }
            }
        });
    });

    function printErrorMsg(errors){
        $.each(errors, function(key, value){
            if (key === 'password' && value[0] === 'The password confirmation does not match.') {
                // Handle password confirmation error with a custom message
                $("."+key+"_err").text('Password confirmation does not match.');
            } else {
                // Display the original error message
                $("."+key+"_err").text(value[0]);
            }
        });
    }
});

    </script>

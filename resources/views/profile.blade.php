@include('header')
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-md-12">
                <h1 class="text-center text-danger">Hello,<span class="name"></span></h1>

                <div class="email_verify">
                    <p><b>Email:- <span class="email"></span>&nbsp; <span class="verify"></span></b></p>
                </div>
                <form id="profile_form">
                    @csrf <!-- Add this line to include CSRF token -->
                    <input type="hidden" name="id" id="userid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter FullName">
                                <span class="error name_err"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp"
                                    placeholder="Enter email">
                                <span class="error email_err"></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
                <div id="alert-container"></div>

            </div>
        </div>
    </div>
<script>
$(document).ready(function () {
    // var token = localStorage.getItem('user_token');
    $.ajax({
        url: "http://127.0.0.1:8000/api/profile",
        type: "GET",
        headers: {'Authorization': localStorage.getItem('user_token')},
        success: function (response) {
            if (response.success === true) {
                if (response.data !== null) {  // Add this check
                    $("#userid").val(response.data.id);
                    $(".name").text(response.data.name);
                    $(".email").text(response.data.email);
                    $("#email").val(response.data.email);
                    $("#name").val(response.data.name);

                    if (response.data.is_verified == 0) {
                        $(".verify").html("<button type='submit' class='btn btn-success verify_mail' data-id='"+response.data.email+"'>Verify</button>");
                    } else {
                        $(".verify").text("Verified");
                    }
                } else {
                    localStorage.removeItem('user_token');
                    window.open('/login', '_self');
                    console.error("Profile data is null or undefined");
                }
            } else {
                alert(response.msg);
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            // Handle other types of errors
        }
    });

$(document).on('click', '.verify_mail',function () {
    var email = $(this).attr('data-id');
    $.ajax({
        url: "http://127.0.0.1:8000/api/send-verify-mail/" + email,
        type: "GET",
        headers: { 'Authorization': localStorage.getItem('user_token') },
        success: function (response) {
            // console.log(response);
            $('.alert-container').text(response.msg);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            // Handle other types of errors
        }
    });
});


});

$("#profile_form").submit(function (e) {
    e.preventDefault();

    // Clear error messages when the user starts typing
    $(".form-control").on('input', function () {
        var fieldName = $(this).attr('name');
        $("." + fieldName + "_err").text('');
    });

    formData = $(this).serialize();
    $.ajax({
        url: "http://127.0.0.1:8000/api/update-profile",
        type: "POST",
        data: formData,
        headers: { 'Authorization': localStorage.getItem('user_token') },
        success: function (response) {
            if (response.success) {
                var alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                    '<strong>Success!</strong> ' + response.msg +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span></button></div>';
                    $(".email").text(response.data.email);
                    if (response.data.is_verified == 0) {
                        $(".verify").html("<button type='submit' class='btn btn-success verify_mail' data-id='"+response.data.email+"'>Verify</button>");
                    } else {
                        $(".verify").text("Verified");
                    }
                // Append the success alert to the container
                $("#alert-container").html(alertHtml);
            } else {
                handleError(response.errors);
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            // Handle other types of errors
        }
    });
});

function handleError(errors) {
    if (errors) {
        if (errors.errors) {
            // Display field-specific error messages in Bootstrap alert boxes
            Object.keys(errors.errors).forEach(function (fieldName) {
                var errorMessages = errors.errors[fieldName];
                var errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    '<strong>Error!</strong> ' + errorMessages.join('<br>') +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span></button></div>';

                // Append the alert to the container
                $("#" + fieldName + "_err").html(errorHtml);
            });
        } else if (errors.msg) {
            // Display the general error message in Bootstrap alert box
            var errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                '<strong>Error!</strong> ' + errors.msg.join('<br>') +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span></button></div>';

            // Append the alert to the container
            $("#alert-container").html(errorHtml);
        }
    }
}



</script>

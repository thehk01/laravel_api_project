
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

   <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-md-12">
                <h1 class="text-center text-danger">Forgot Password</h1>
                <form method="POST" id="forgetForm">
                    @csrf <!-- Add this line to include CSRF token -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Password">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Forgot Password</button>
                    <div class="result"></div>
                </form>
                <div id="alert-container"></div>

            </div>
        </div>
    </div>

<script>
$(document).ready(function(){
    $("#forgetForm").submit(function(e){
        e.preventDefault();
        var formData = $(this).serialize();  // Corrected spelling here
        $.ajax({
            url: "http://127.0.0.1:8000/api/forget-password",
            type: "POST",
            data: formData,
            success: function(data){
                // console.log(data);
                if(data.success = true){
                    $(".result").text(data.msg);
                }else{
                    // Append the alert to the container
                    $(".result").text(data.msg);
                    setTimeout(() => {
                        $(".result").text("");
                    }, 2000);

                }
            }
        });
    });
});

</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rest Api</title>
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

    <button class="btn btn-danger logout" style="display: none;">Logout</button>
    <button class="btn btn-danger refresh" style="display: none;">Refresh User</button>
    <script>
        var token = localStorage.getItem('user_token');
        var currentPath = window.location.pathname;

        if ((currentPath === '/login' || currentPath === '/register') && token !== null) {
            // Redirect to profile page if on login or register page and token exists
            window.location.href = '/profile';
        } else if (currentPath !== '/login' && currentPath !== '/register' && token === null) {
            // Redirect to login page if on any other page and token is missing
            window.location.href = '/login';
        }

        // Hide or show the logout button
        $(document).ready(function () {
            if (token !== null) {
                $(".logout").show();  // Show the logout button
                $(".refresh").show();  // Show the logout button
            } else {
                $(".logout").hide();  // Hide the logout button
                $(".refresh").hide();  // Hide the logout button
            }

            $(".logout").click(function () {
                $.ajax({
                    url: "http://127.0.0.1:8000/api/logout",
                    type: "POST",
                    headers: {'Authorization': localStorage.getItem('user_token')},
                    success: function (data) {
                        if (data.success) {
                            localStorage.removeItem('user_token');
                            window.open('/login', '_self');
                        } else {
                            alert(data.msg);
                            window.open('/login', '_self');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        // Handle other types of errors
                    }
                });
            });

            $(".refresh").click(function () {
                $.ajax({
                    url: "http://127.0.0.1:8000/api/refresh-token",
                    type: "GET",
                    headers: {'Authorization': localStorage.getItem('user_token')},
                    success: function (data) {
                        if (data.success==true) {
                            localStorage.setItem('user_token',data.token_type+" "+data.access_token);
                            alert("user is refresh");
                        } else {
                            alert(data.msg);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        // Handle other types of errors
                    }
                });
            });
        });
    </script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $mailData['title'] }}</title>
</head>
<body>
    <p>{{ $mailData['body'] }}</p>
    <a href="{{ $mailData['url'] }}">Click here to verify mail</a>
</body>
</html>

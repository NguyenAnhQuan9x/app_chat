<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Qnichat</title>
    @vite('resources/js/app.js')
    <script src="https://kit.fontawesome.com/6da5ba8330.js" crossorigin="anonymous"></script>
</head>
<body>
    <div id="app"></div>
</body>
</html>
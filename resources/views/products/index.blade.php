<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Index Products Controller</h1>
    @foreach ($myPhone as $item)
        <h3>{{ $item }}</h3>
    @endforeach
</body>
</html>
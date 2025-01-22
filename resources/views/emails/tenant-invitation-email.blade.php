<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invitation Email</title>
</head>

<body>
    <div>
        @if ($password)
        Esta es tu contraseña temporal: <strong> {{ $password }} </strong>
        @endif
    </div>

    <div>
        <p>
            Fue añadido a un equipo en tygor bots, haga click en el siguiente <a rel="stylesheet"
                href="https://tygor.bbf.com.ec/">Aqui para entrar a tu cuenta!</a>
        </p>
    </div>
</body>

</html>
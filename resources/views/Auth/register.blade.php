<!DOCTYPE html>
<html>

<head>

    <title>Register</title>

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

</head>

<body>

<div class="login-box">

<h2>Register</h2>

<form action="/register" method="POST">

    @csrf

    <input type="text" name="name" placeholder="Nom"><br><br>

    <input type="email" name="email" placeholder="Email"><br><br>

    <input type="password" name="password" placeholder="Password"><br><br>

    <input type="password" name="password_confirmation" placeholder="Confirmer Password"><br><br>

    <button>S'inscrire</button>

</form>

</div>

</body>

</html>
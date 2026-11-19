@extends('layouts.app')

@section('title', 'Modifier')

@section('content')

<h2>Modifier un utilisateur</h2>

<form action="{{ route('users.update', $user->id) }}" method="POST">

    @csrf
    @method('PUT')

    <label>Nom</label>

    <input type="text"
           name="name"
           value="{{ $user->name }}">

    <br><br>

    <label>Email</label>

    <input type="email"
           name="email"
           value="{{ $user->email }}">

    <br><br>

    <label>Mot de passe</label>
<input type="password" name="password">

<br><br>

<label>Confirmation du mot de passe</label>
<input type="password" name="password_confirmation">

    <button type="submit">

        Modifier

    </button>

</form>

@endsection
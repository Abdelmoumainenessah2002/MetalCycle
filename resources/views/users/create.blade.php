@extends('layouts.app')

@section('title', 'Ajouter un utilisateur')

@section('content')

<h2>Ajouter un utilisateur</h2>

<form action="{{ route('users.store') }}" method="POST">

    @csrf

    <div>
        <label>Nom</label>
        <input type="text" name="name">
    </div>

    <br>

    <div>
        <label>Email</label>
        <input type="email" name="email">
    </div>

    <br>

    <div>
        <label>Mot de passe</label>
        <input type="password" name="password">
    </div>

    <br>

    <div>
        <label>Confirmation</label>
        <input type="password" name="password_confirmation">
    </div>

    <br>

    <button type="submit">

        Enregistrer

    </button>

</form>

@endsection
@extends('layouts.app')

@section('title', 'Users')

@section('content')

@if(session('success'))

    <div style="color:green; margin-bottom:15px;">
        {{ session('success') }}
    </div>

@endif

<h1>Liste des utilisateurs</h1>

<a href="{{ route('users.create') }}">Ajouter un utilisateur</a>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>

    @foreach($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>
            <a href="{{ route('users.edit', $user->id) }}">Modifier</a>
        </td>
        <td>

            <a href="{{ route('users.edit', $user->id) }}">
                Modifier
            </a>
        
            <form action="{{ route('users.destroy', $user->id) }}"
                  method="POST"
                  style="display:inline;">
        
                @csrf
        
                @method('DELETE')
        
                <button type="submit"
                        onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">
        
                    Supprimer
        
                </button>
        
            </form>
        
        </td>
    </tr>
    @endforeach
</table>

@endsection
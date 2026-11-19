@extends('layouts.auth')

@section('title','Connexion')

@section('content')

<div class="login-page">

    <!-- LEFT SIDE -->

    <div class="login-left">

        <div class="overlay"></div>

        <div class="left-content">

            <div class="logo-container">
                <i class="fa-solid fa-recycle logo"></i>
            </div>
            <h1>RECYCLAGE</h1>

            <h3>SYSTÈME DE GESTION</h3>

            <div class="red-line"></div>

            <p class="description">

                Solution complète pour la gestion des collectes,
                des réceptions et du stock des matières métalliques.

            </p>

            <!-- FEATURES -->

            <div class="features">

                <div class="feature">

                    <i class="fa-solid fa-truck"></i>

                    <h4>Collecte</h4>

                    <span>Optimisée</span>

                </div>

                <div class="feature">

                    <i class="fa-solid fa-clipboard-check"></i>

                    <h4>Réception</h4>

                    <span>Contrôlée</span>

                </div>

                <div class="feature">

                    <i class="fa-solid fa-box"></i>

                    <h4>Stock</h4>

                    <span>Maîtrisé</span>

                </div>

                <div class="feature">

                    <i class="fa-solid fa-chart-column"></i>

                    <h4>Pilotage</h4>

                    <span>Performant</span>

                </div>

            </div>

            <!-- STATS -->

            <div class="stats">

                <div>

                    <i class="fa-solid fa-users"></i>

                    <strong>500+</strong>

                    <span>Fournisseurs</span>

                </div>

                <div>

                    <i class="fa-solid fa-truck"></i>

                    <strong>120+</strong>

                    <span>Missions</span>

                </div>

                <div>

                    <i class="fa-solid fa-layer-group"></i>

                    <strong>2500+</strong>

                    <span>Tonnes</span>

                </div>

                <div>

                    <i class="fa-solid fa-shield-halved"></i>

                    <strong>100%</strong>

                    <span>Sécurisé</span>

                </div>

            </div>

        </div>

    </div>

    <!-- RIGHT SIDE -->

    <div class="login-right">

        <div class="login-card">

            <div class="login-icon">

                <i class="fa-solid fa-user-lock"></i>

            </div>

            <h2>Bienvenue</h2>

            <p class="subtitle">

                Connectez-vous à votre espace

            </p>

            <div class="title-line"></div>

            @if(session('error'))

                <div class="alert alert-danger">

                    {{ session('error') }}

                </div>

            @endif

            <form action="{{ route('login.submit') }}" method="POST">

                @csrf

                <div class="form-group">

                    <label>Nom d'utilisateur</label>

                    <div class="input-box">

                        <i class="fa-solid fa-user"></i>

                        <input
                            type="text"
                            name="username"
                            placeholder="Entrez votre nom d'utilisateur"
                            required>

                    </div>

                </div>

                <div class="form-group">

                    <label>Mot de passe</label>

                    <div class="input-box">

                        <i class="fa-solid fa-lock"></i>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Entrez votre mot de passe"
                            required>

                        {{-- <i class="fa-solid fa-eye toggle-password"></i> --}}

                    </div>

                </div>

                <div class="login-options">

                    {{-- <label>

                        <input type="checkbox">

                        Se souvenir de moi

                    </label>

                    <a href="#">

                        Mot de passe oublié ?

                    </a> --}}

                </div>

                <button class="login-btn">

                    Se connecter

                    <i class="fa-solid fa-arrow-right"></i>

                </button>

            </form>

        </div>

    </div>

</div>

@endsection
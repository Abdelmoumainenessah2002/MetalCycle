<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title> 

    <link rel="preconnect" href="https://fonts.googleapis.com"> 
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> 
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> 

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"> 

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"> 

    <link rel="stylesheet" href="{{ asset('css/variables.css') }}"> 
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}"> 
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"> 
</head> 
<body> 

<div class="wrapper"> 
    @include('partials.sidebar') 

    <div class="main"> 
        @include('partials.navbar') 

        <div class="content"> 
            @yield('content') 
        </div> 

        @include('partials.footer') 
    </div> 
</div> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script> 
</body> 
</html>
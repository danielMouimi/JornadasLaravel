<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jornadas de Videojuegos')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header>
    <nav>
        <a href="{{ route('home') }}">Inicio</a>
        <a href="{{ route('eventos.index') }}">Eventos</a>
        <a href="{{ route('ponentes.index') }}">Ponentes</a>
{{--        <a href="{{ route('pagos.index') }}">Pagos</a>--}}
        @guest
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}">Register</a>
        @endguest

        @auth
            <a href="{{ route('dashboard') }}">Panel de control</a>
            <a href="{{ route('logout') }}">Cerrar sesión</a>
           @if(auth()->user()->administrador == true)  <!-- Suponiendo que tienes un campo 'role' -->
            <a href="{{ route('admin.ponentes.index') }}">GestionPonentes</a>
            <a href="{{route('admin.eventos.index')}}">GestionEventos</a>
            <a href="{{route('admin.asistencias.index')}}">Usuarios Registrados</a>
            @endif

        @endauth

{{--        <form method="post" action="{{ route('admin.ponentes.index') }}">--}}
{{--            <button type="submit">Gestion Ponentes post</button>--}}
{{--        </form>--}}

    </nav>
</header>

<main>
    @yield('content')
</main>

<footer>
    <p>&copy; 2025 Jornadas de Videojuegos</p>
</footer>
</body>
</html>

@extends('layouts.app')

@section('title', 'Jornadas de Videojuegos')

@section('content')
    {{-- Sección Hero --}}
    <section class="hero bg-gray-900 text-white text-center py-16">
        <h1 class="text-5xl font-bold">🎮 Jornadas de Videojuegos</h1>
        <p class="text-xl mt-4">Descubre conferencias y talleres con expertos del sector.</p>

        <div class="mt-6 flex justify-center space-x-4">
            <a href="{{ route('register') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg shadow-lg transition">
                Regístrate Ahora
            </a>
            <a href="{{ route('login') }}" class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-3 px-6 rounded-lg text-lg shadow-lg transition">
                Iniciar Sesión
            </a>
        </div>
    </section>

    {{-- Sección Ponentes Destacados --}}
    <section class="bg-white py-16 text-center">
        <h2 class="text-3xl font-bold">🎤 Ponentes Destacados</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 px-8">
            {{-- Aquí se insertarán dinámicamente los ponentes --}}
        </div>
        <a href="{{ route('ponentes.index') }}" class="text-blue-500 mt-6 inline-block font-semibold">
            Ver todos los ponentes →
        </a>
    </section>

    {{-- Sección Eventos Destacados --}}
    <section class="bg-gray-200 py-16 text-center">
        <h2 class="text-3xl font-bold">📅 Eventos Destacados</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8 px-8">
            {{-- Aquí se insertarán dinámicamente los eventos --}}
        </div>
        <a href="{{ route('eventos.index') }}" class="text-blue-500 mt-6 inline-block font-semibold">
            Ver todos los eventos →
        </a>
    </section>

    {{-- Sección Cómo Participar --}}
    <section class="bg-white py-16 text-center">
        <h2 class="text-3xl font-bold">🚀 ¿Cómo Participar?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 px-8">
            <div class="bg-gray-100 p-6 rounded-lg shadow-lg transition hover:scale-105">
                <h3 class="text-xl font-semibold">🎟️ Compra tu Entrada</h3>
                <p class="text-gray-600">Accede a todas las conferencias y talleres.</p>
            </div>
            <div class="bg-gray-100 p-6 rounded-lg shadow-lg transition hover:scale-105">
                <h3 class="text-xl font-semibold">📅 Selecciona tus Eventos</h3>
                <p class="text-gray-600">Elige hasta 5 conferencias y 4 talleres.</p>
            </div>
            <div class="bg-gray-100 p-6 rounded-lg shadow-lg transition hover:scale-105">
                <h3 class="text-xl font-semibold">📩 Recibe tu Ticket</h3>
                <p class="text-gray-600">Valida tu acceso con tu ticket digital.</p>
            </div>
        </div>

        <div class="mt-8 flex justify-center space-x-4">
            <a href="{{ route('register') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg shadow-lg transition">
                Registrarme
            </a>
            <a href="{{ route('pago.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-lg shadow-lg transition">
                Comprar Entrada
            </a>
        </div>
    </section>
@endsection


@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <h1>Bienvenido a las Jornadas de Videojuegos</h1>
    <p>Descubre conferencias y talleres con expertos del sector.</p>

    <h2>Eventos destacados</h2>
    <ul id="eventos-list">
        <li>Cargando eventos...</li> <!-- Placeholder mientras se carga la info -->
    </ul>

    <a href="{{ route('eventos.index') }}">Ver todos los eventos</a>
    <a href="{{ route('pago.index') }}">Proceder al pago</a>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('/api/home')
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Error al obtener los eventos");
                    }
                    return response.json();
                })
                .then(data => {
                    const eventosList = document.getElementById('eventos-list');
                    eventosList.innerHTML = ""; // Limpiar el placeholder

                    data.forEach(evento => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                            <strong>${evento.titulo}</strong> - ${evento.tipo} - ${evento.fecha}
                            <a href="/eventos/${evento.id}">Ver más</a>
                        `;
                        eventosList.appendChild(li);
                    });
                })
                .catch(error => {
                    console.error('Error al cargar los eventos:', error);
                    document.getElementById('eventos-list').innerHTML = "<li>Error al cargar los eventos</li>";
                });
        });
    </script>
@endsection


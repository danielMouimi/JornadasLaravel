@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <h1>Bienvenido a las Jornadas de Videojuegos</h1>
    <p>Descubre conferencias y talleres con expertos del sector.</p>

    <h2>Eventos destacados</h2>
    <ul id="eventos-list">
        <li>Cargando eventos...</li> <!-- Placeholder mientras se carga la info -->
    </ul>

    <h2>Ponentes destacados</h2>
    <ul id="ponentes-list">
        <li>Cargando ponentes...</li> <!-- Placeholder mientras se carga la info -->
    </ul>

    <a href="{{ route('eventos.index') }}">Ver todos los eventos</a>
    <a href="{{ route('pago.index') }}">Proceder al pago</a>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('/api/home')
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Error al obtener los datos");
                    }
                    return response.json();
                })
                .then(data => {
                    // Cargar eventos
                    const eventosList = document.getElementById('eventos-list');
                    eventosList.innerHTML = ""; // Limpiar el placeholder

                    if (data.eventos.length === 0) {
                        eventosList.innerHTML = "<li>No hay eventos disponibles.</li>";
                    } else {
                        data.eventos.forEach(evento => {
                            const li = document.createElement('li');
                            li.innerHTML = `
                                <strong>${evento.titulo}</strong> - ${evento.tipo} - ${evento.fecha}
                                <a href="/eventos/${evento.id}">Ver más</a>
                            `;
                            eventosList.appendChild(li);
                        });
                    }

                    // Cargar ponentes
                    const ponentesList = document.getElementById('ponentes-list');
                    ponentesList.innerHTML = ""; // Limpiar el placeholder

                    if (data.ponentes.length === 0) {
                        ponentesList.innerHTML = "<li>No hay ponentes disponibles.</li>";
                    } else {
                        data.ponentes.forEach(ponente => {
                            const li = document.createElement('li');
                            li.innerHTML = `
                                <strong>${ponente.nombre}</strong> - ${ponente.experiencia}
                            `;
                            ponentesList.appendChild(li);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error al cargar los datos:', error);
                    document.getElementById('eventos-list').innerHTML = "<li>Error al cargar los eventos</li>";
                    document.getElementById('ponentes-list').innerHTML = "<li>Error al cargar los ponentes</li>";
                });
        });
    </script>
@endsection


@extends('layouts.app')

@section('content')
    @if (Auth::user()->email_verified_at == null)
        <div class="alert alert-warning">
            ¡Por favor verifica tu correo! Te hemos enviado un enlace de verificación.
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit">Reenviar correo de verificación</button>
            </form>
        </div>
    @else
        <div class="container">
            <h2 id="user-name">Bienvenido 👋</h2>

            <h3>Mis Eventos</h3>
            <ul id="eventos-list">
                <li>Cargando eventos...</li>
            </ul>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const userId = "{{ Auth::id() }}"; // Obtener el ID del usuario autenticado

                fetch(`/api/asistencias/${userId}`, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    },
                    credentials: "same-origin"
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(asistencias => {
                        let eventosList = document.getElementById("eventos-list");
                        eventosList.innerHTML = "";

                        if (asistencias.length === 0) {
                            eventosList.innerHTML = "<p>No estás registrado en ningún evento.</p>";
                            return;
                        }

                        // Hacer fetch de cada evento basado en su ID
                        asistencias.forEach(asistencia => {
                            fetch(`/api/eventos/${asistencia.evento_id}`, {
                                headers: {
                                    "X-Requested-With": "XMLHttpRequest",
                                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                                    'Accept': 'application/json'
                                },
                                credentials: "same-origin"
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error in evento! Status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(evento => {
                                    let li = document.createElement("li");
                                    li.innerHTML = `
                                ${evento.tipo}: ${evento.titulo} ${evento.fecha} (${evento.hora_inicio} - ${evento.hora_fin})
                                <form action="/eventos/${evento.id}/desapuntarse" method="POST" style="display: inline;" onsubmit="return confirm('¿Seguro que quieres desapuntarte de este evento?');">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger">Desapuntarme</button>
                                </form>
                            `;
                                    eventosList.appendChild(li);
                                })
                                .catch(error => {
                                    console.error("Error cargando el evento:", error);
                                });
                        });
                    })
                    .catch(error => {
                        console.error("Error cargando asistencias:", error);
                        document.getElementById("eventos-list").innerHTML = "<p>Error cargando eventos. Intenta de nuevo.</p>";
                    });
            });
        </script>
    @endif
@endsection


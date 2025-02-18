@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Usuarios Confirmados y sus Asistencias</h1>

        <div id="usuarios-container">
            <p>Cargando usuarios...</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const usuariosContainer = document.getElementById("usuarios-container");
            try {
                // Llamada a la API para obtener los usuarios y sus asistencias
                const response = await fetch('/api/asistencias');
                const data = await response.json();

                if (data.length === 0) {
                    usuariosContainer.innerHTML = "<p>No hay usuarios confirmados con asistencias registradas.</p>";
                    return;
                }

                // Limpiar el contenedor
                usuariosContainer.innerHTML = '';

                data.forEach(async usuario => {
                    // Crear una fila para cada usuario
                    const usuarioRow = document.createElement('div');
                    usuarioRow.classList.add('usuario-row');

                    // Mostrar nombre y email del usuario
                    usuarioRow.innerHTML = `
                        <h3>${usuario.name} - ${usuario.email}</h3>
                        <p><strong>Asistencias:</strong></p>
                        <ul id="asistencias-${usuario.id}"></ul>
                    `;

                    usuariosContainer.appendChild(usuarioRow);

                    // Ahora hacer llamadas para obtener los detalles de cada evento al que está apuntado
                    const eventosContainer = document.getElementById(`asistencias-${usuario.id}`);

                    if (usuario.asistencias.length === 0) {
                        eventosContainer.innerHTML = "<li>No tiene asistencias registradas.</li>";
                    } else {
                        for (let asistencia of usuario.asistencias) {
                            const eventoResponse = await fetch(`/api/eventos/${asistencia.evento_id}`);
                            const evento = await eventoResponse.json();

                            eventosContainer.innerHTML += `
                                <li>
                                    ${evento.titulo} - ${new Date(evento.fecha).toLocaleDateString()}
                                    (${(evento.hora_inicio)} -
                                    ${(evento.hora_fin)})
                                </li>
                            `;
                        }
                    }
                });
            } catch (error) {
                usuariosContainer.innerHTML = `<p>Error al cargar los usuarios: ${error.message}</p>`;
            }
        });
    </script>
@endsection


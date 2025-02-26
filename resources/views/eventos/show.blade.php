@extends('layouts.app')

@section('title', 'Detalles del Evento')

@section('content')
    <div class="container mt-5">
        <div id="evento-container">
            <p>Cargando evento...</p>
        </div>
    </div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', async function() {
        const eventoId = window.location.pathname.split('/').pop(); // Obtener el ID del evento desde la URL
        const eventoContainer = document.getElementById('evento-container');

        try {
// Obtener detalles del evento
            const eventoResponse = await fetch(`/api/eventos/${eventoId}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
            const evento = await eventoResponse.json();

            if (!evento) {
                eventoContainer.innerHTML = '<p>Evento no encontrado.</p>';
                return;
            }

            const token = localStorage.getItem('token');
            if (!token) {
                console.error("Token no encontrado");
                return;
            }

// Obtener inscripciones del usuario
            const inscripcionesResponse = await fetch('/api/user/inscripciones', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            let userInscripciones = await inscripcionesResponse.json();
            console.log(userInscripciones);


// Asegurar que userInscripciones es un array
            if (!Array.isArray(userInscripciones)) {
                userInscripciones = [];
            }
            const usuarioActualId = @json(auth()->id());
            userInscripciones = userInscripciones.filter(inscripcion => inscripcion.usuario_id === usuarioActualId);


// Contadores para talleres y conferencias
            let talleresCount = 0;
            let conferenciasCount = 0;

                userInscripciones.forEach( async inscripcion => {
                const eventoResponse1 = await fetch(`/api/eventos/${inscripcion.evento_id}`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                });
                const event = await eventoResponse1.json();
                if (event.tipo === 'taller') {
                    talleresCount++;
                } else if (event.tipo === 'conferencia') {
                    conferenciasCount++;
                }
                    if (evento.tipo === 'taller' && talleresCount >= 4) {
                        formHtml = `<p><strong>⚠️ No puedes inscribirte a más de 4 talleres.</strong></p>`;
                    } else if (evento.tipo === 'conferencia' && conferenciasCount >= 5) {
                        formHtml = `<p><strong>⚠️ No puedes inscribirte a más de 5 conferencias.</strong></p>`;
                    }
                    eventoContainer.innerHTML = `
                <h1>${evento.titulo}</h1>
                <p><strong>Tipo:</strong> ${capitalizeFirstLetter(evento.tipo)}</p>
                <p><strong>Fecha:</strong> ${new Date(evento.fecha).toLocaleDateString()}</p>
                <p><strong>Hora:</strong> ${evento.hora_inicio} - ${evento.hora_fin}</p>
                <p><strong>Ponente:</strong> ${evento.ponente ? evento.ponente.nombre : 'No asignado'}</p>
                <p><strong>Capacidad Máxima:</strong> ${evento.capacidad_maxima}</p>


                <h3>Inscribirse</h3>
                ${formHtml}
                <a href="/eventos" class="btn btn-secondary mt-3">Volver a la lista</a>
            `;


                    document.getElementById('inscribirse-form')?.addEventListener('click', async function(event) {
                        event.preventDefault();
                        const response = await fetch(`/api/eventos/${evento.id}/inscribirse`, {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                                'Accept': 'application/json'
                            }
                        });
                        const result = await response.json();
                        if (result.success) {
                            alert('Te has inscrito al evento correctamente.');
                            location.reload();
                        }
                    });

                });

// Verificar si el usuario ya está inscrito en el evento
            const eventoInscrito = userInscripciones.some(inscripcion => inscripcion.evento_id === evento.id);

            let formHtml = '';

// Reglas para la inscripción
            if (eventoInscrito) {
                formHtml = `
                <p class="text-success"><strong>✅ Ya estás inscrito en este evento.</strong></p>
                `;

            } else if (evento.capacidad_maxima > userInscripciones.length) {
                formHtml = `

                        <button id="inscribirse-form" class="btn btn-primary">Inscribirme</button>

                `;
            } else {
                formHtml = `<p><strong>⚠️ Evento completo</strong></p>`;
            }


            // Mostrar detalles del evento
            eventoContainer.innerHTML = `
                <h1>${evento.titulo}</h1>
                <p><strong>Tipo:</strong> ${capitalizeFirstLetter(evento.tipo)}</p>
                <p><strong>Fecha:</strong> ${new Date(evento.fecha).toLocaleDateString()}</p>
                <p><strong>Hora:</strong> ${evento.hora_inicio} - ${evento.hora_fin}</p>
                <p><strong>Ponente:</strong> ${evento.ponente ? evento.ponente.nombre : 'No asignado'}</p>
                <p><strong>Capacidad Máxima:</strong> ${evento.capacidad_maxima}</p>


                <h3>Inscribirse</h3>
                ${formHtml}
                <a href="/eventos" class="btn btn-secondary mt-3">Volver a la lista</a>
            `;

            // Manejador de evento para desapuntarse
            document.getElementById('desapuntarse')?.addEventListener('click', async function() {
                const token = localStorage.getItem('token');
                if (!token) {
                    console.error("Token no encontrado");
                    return;
                }
                const response = await fetch(`/api/eventos/${evento.id}/desapuntarse`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'

                    }
                });
                const result = await response.json();
                location.reload();
                if (result.success) {
                    alert('Te has desapuntado del evento correctamente.');
                    location.reload();
                }
            });

            // Manejador de evento para inscribirse
            document.getElementById('inscribirse-form')?.addEventListener('click', async function(event) {
                event.preventDefault();
                const response = await fetch(`/api/eventos/${evento.id}/inscribirse`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (result.success) {
                    alert('Te has inscrito al evento correctamente.');
                    location.reload();
                }
            });

        } catch (error) {
            eventoContainer.innerHTML = `<p>Error al cargar el evento: ${error.message}</p>`;
        }
    });

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
</script>




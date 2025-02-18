@extends('layouts.app')

@section('title', 'Lista de Eventos')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Eventos Disponibles</h1>

        <table class="table table-striped">
            <thead>
            <tr>
                <th>Título</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>Ponente</th>
                <th>Capacidad Máxima</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody id="eventos-tbody">
            <tr>
                <td colspan="8" class="text-center">Cargando eventos...</td>
            </tr>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetchEventos();
        });

        async function fetchEventos() {
            try {
                const response = await fetch("/api/eventos");
                if (!response.ok) {
                    throw new Error("Error al obtener los eventos");
                }
                const data = await response.json();
                const tbody = document.getElementById("eventos-tbody");
                tbody.innerHTML = ""; // Limpiar el contenido anterior

                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8" class="text-center">No hay eventos disponibles</td></tr>`;
                    return;
                }

                for (let evento of data) {
                    const nombre = await nombrePonente(evento.ponente_id);
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${evento.titulo}</td>
                        <td>${evento.tipo.charAt(0).toUpperCase() + evento.tipo.slice(1)}</td>
                        <td>${formatearFecha(evento.fecha)}</td>
                        <td>${formatearHora(evento.hora_inicio)}</td>
                        <td>${formatearHora(evento.hora_fin)}</td>
                        <td>${nombre}</td>
                        <td>${evento.capacidad_maxima}</td>
                        <td>
                            <a href="/eventos/${evento.id}" class="btn btn-info btn-sm">Ver</a>
                        </td>
                    `;
                    tbody.appendChild(row);
                }
            } catch (error) {
                console.error("Error:", error);
                document.getElementById("eventos-tbody").innerHTML =
                    `<tr><td colspan="8" class="text-center text-danger">Error al cargar los eventos</td></tr>`;
            }
        }

        function formatearFecha(fecha) {
            const opciones = { day: "2-digit", month: "2-digit", year: "numeric" };
            return new Date(fecha).toLocaleDateString("es-ES", opciones);
        }

        function formatearHora(hora) {
            return hora.substring(0, 5); // Solo HH:mm
        }

        async function nombrePonente(id) {
            try {
                const response = await fetch("/api/ponentes/" + id);
                if (!response.ok) {
                    throw new Error("Error al obtener los ponentes");
                }
                const data = await response.json();
                return data.nombre;
            } catch (error) {
                console.error(error);
                return "Desconocido";
            }
        }
    </script>
@endsection



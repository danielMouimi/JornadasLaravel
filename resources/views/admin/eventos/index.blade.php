@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Gestión de Eventos</h1>

        <!-- Botón para crear un nuevo evento -->
        <a href="{{ route('admin.eventos.create') }}" class="btn btn-primary mb-3">Crear Nuevo Evento</a>

        <!-- Tabla de eventos -->
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
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
                <td colspan="9" class="text-center">Cargando eventos...</td>
            </tr>
            </tbody>
        </table>

        <!-- Paginación -->
        <nav id="pagination" aria-label="Page navigation example">
            <ul class="pagination">
                <!-- Se llenará dinámicamente con las páginas disponibles -->
            </ul>
        </nav>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetchEventos();
        });

        async function fetchEventos(page = 1) {
            try {
                const response = await fetch(`/api/eventos?page=${page}`);
                if (!response.ok) {
                    throw new Error("Error al obtener los eventos");
                }
                const data = await response.json();
                const tbody = document.getElementById("eventos-tbody");
                const pagination = document.getElementById("pagination");

                tbody.innerHTML = ""; // Limpiar el contenido anterior
                pagination.innerHTML = ""; // Limpiar la paginación

                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="9" class="text-center">No hay eventos disponibles</td></tr>`;
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
                            <a href="/admin/eventos/edit/${evento.id}" class="btn btn-warning btn-sm">Editar</a>
                            <button class="btn btn-danger btn-sm" onclick="eliminarEvento(${evento.id})">Eliminar</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                };

                // Agregar paginación
                if (data.meta && data.meta.last_page > 1) {
                    for (let i = 1; i <= data.meta.last_page; i++) {
                        const pageItem = document.createElement("li");
                        pageItem.classList.add("page-item");
                        pageItem.innerHTML = `
                            <a class="page-link" href="#" onclick="fetchEventos(${i})">${i}</a>
                        `;
                        pagination.appendChild(pageItem);
                    }
                }
            } catch (error) {
                console.error("Error:", error);
                document.getElementById("eventos-tbody").innerHTML =
                    `<tr><td colspan="9" class="text-center text-danger">Error al cargar los eventos</td></tr>`;
            }
        }

        async function eliminarEvento(id) {
            if (confirm("¿Seguro que quieres eliminar este evento?")) {
                try {
                    const token = localStorage.getItem('token');
                    if (!token) {
                        console.error("Token no encontrado");
                        return;
                    }

                    const response = await fetch(`/api/eventos/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        alert("Evento eliminado exitosamente");
                        fetchEventos(); // Refrescar la lista después de eliminar
                    } else {
                        throw new Error("Error al eliminar el evento");
                    }
                } catch (error) {
                    console.error("Error al eliminar el evento:", error);
                    alert("Hubo un error al intentar eliminar el evento");
                }
            }
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

        function formatearFecha(fecha) {
            const opciones = { day: "2-digit", month: "2-digit", year: "numeric" };
            return new Date(fecha).toLocaleDateString("es-ES", opciones);
        }

        function formatearHora(hora) {
            return hora.substring(0, 5); // Solo HH:mm
        }
    </script>
@endsection

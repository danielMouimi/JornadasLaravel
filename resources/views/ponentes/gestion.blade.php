@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Gestión de Ponentes</h1>
        <a href="{{ route('admin.ponentes.create') }}" class="btn btn-primary mb-3">Añadir Ponente</a>

        <div id="success-message" class="alert alert-success d-none"></div>

        <table class="table">
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Experiencia</th>
                <th>Redes Sociales</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody id="ponentes-table">
            <tr><td colspan="5">Cargando ponentes...</td></tr> <!-- Placeholder mientras carga -->
            </tbody>
        </table>

        <nav>
            <ul class="pagination" id="pagination"></ul>
        </nav>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            cargarPonentes();

            async function cargarPonentes(page = 1) {
                try {
                    const response = await fetch(`/api/ponentes?page=${page}`);
                    if (!response.ok) throw new Error("Error al obtener los ponentes");

                    const data = await response.json();
                    const ponentesTable = document.getElementById("ponentes-table");
                    const pagination = document.getElementById("pagination");

                    ponentesTable.innerHTML = ""; // Limpiar la tabla

                    data.forEach(ponente => {
                        const row = document.createElement("tr");

                        row.innerHTML = `
                        <td>${ponente.nombre}</td>
                        <td>${ponente.experiencia}</td>
                        <td>${ponente.redes_sociales ? `${ponente.redes_sociales}` : 'No disponible'}</td>
                        <td>
                            ${ponente.foto ? `<img src="http://localhost${ponente.foto}" width="50" height="50">` : 'No disponible'}
                        </td>
                        <td>
                            <a href="/ponentes/${ponente.id}" class="btn btn-warning">Editar</a>
                            <button class="btn btn-danger deleteButton">Eliminar</button>
                        </td>
                    `;
                        ponentesTable.appendChild(row);

                        // Usar una función de cierre para mantener el valor correcto de ponente.id
                        row.querySelector(".deleteButton").addEventListener("click", (function(id) {
                            return function() {
                                eliminarPonente(id); // Aquí el ID correcto de cada ponente
                            };
                        })(ponente.id)); // Pasamos el ID actual de ponente
                    });


                    // Generar la paginación
                    generarPaginacion(data);
                } catch (error) {
                    console.error(error);
                    document.getElementById("ponentes-table").innerHTML = "<tr><td colspan='5'>Error al cargar los ponentes</td></tr>";
                }
            }

            function generarPaginacion(data) {
                const pagination = document.getElementById("pagination");
                pagination.innerHTML = "";

                if (data.prev_page_url) {
                    pagination.innerHTML += `<li class="page-item"><a class="page-link" href="#" onclick="cargarPonentes(${data.current_page - 1})">Anterior</a></li>`;
                }

                for (let i = 1; i <= data.last_page; i++) {
                    pagination.innerHTML += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="cargarPonentes(${i})">${i}</a>
                    </li>`;
                }

                if (data.next_page_url) {
                    pagination.innerHTML += `<li class="page-item"><a class="page-link" href="#" onclick="cargarPonentes(${data.current_page + 1})">Siguiente</a></li>`;
                }
            }

            async function eliminarPonente(id) {
                if (!confirm("¿Seguro que deseas eliminar este ponente?")) return;

                try {
                    const response = await fetch(`/api/ponentes/${id}`, {
                        method: "DELETE",
                        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                    });

                    if (!response.ok) throw new Error("Error al eliminar el ponente");

                    document.getElementById("success-message").classList.remove("d-none");
                    document.getElementById("success-message").textContent = "Ponente eliminado correctamente";

                    cargarPonentes();
                } catch (error) {
                    console.error("Error al eliminar:", error);
                    alert("No se pudo eliminar el ponente.");
                }
            }


        });
    </script>
@endsection


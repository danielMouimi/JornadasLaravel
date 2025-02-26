@extends('layouts.app')

@section('title', 'Ponentes')

@section('content')
    <h1>Ponentes de las Jornadas</h1>

    <table id="ponentes-table">
        <thead>
        <tr>
            <th>Foto</th>
            <th>Nombre</th>
            <th>Experiencia</th>
            <th>Redes Sociales</th>
        </tr>
        </thead>
        <tbody>
        <!-- Los ponentes se agregarán aquí dinámicamente -->
        </tbody>
    </table>

    <div id="pagination"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Llamada a la API para obtener los ponentes
            fetch('/api/ponentes')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#ponentes-table tbody');
                    tbody.innerHTML = ''; // Limpiar tabla antes de agregar nuevos datos

                    data.forEach(ponente => {
                        const row = document.createElement('tr');

                        // Foto
                        const fotoCell = document.createElement('td');
                        const img = document.createElement('img');
                        img.src =`${ponente.foto}`;
                        img.alt = ponente.nombre;
                        img.width = 80;
                        fotoCell.appendChild(img);
                        row.appendChild(fotoCell);

                        // Nombre
                        const nombreCell = document.createElement('td');
                        nombreCell.textContent = ponente.nombre;
                        row.appendChild(nombreCell);

                        // Experiencia
                        const experienciaCell = document.createElement('td');
                        experienciaCell.textContent = ponente.experiencia;
                        row.appendChild(experienciaCell);

                        // Redes Sociales
                        const redesCell = document.createElement('td');

                            redesCell.textContent = ponente.redes_sociales ? ponente.redes_sociales : "no disponible";

                        row.appendChild(redesCell);

                        // Añadir la fila a la tabla
                        tbody.appendChild(row);
                    })
                })
                .catch(error => {
                    console.error('Error al cargar los ponentes:', error);
                });
        });
    </script>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Editar Ponente</h1>

        <div id="success-message" class="alert alert-success d-none"></div>
        <div id="error-message" class="alert alert-danger d-none"></div>

        <form id="edit-ponente-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="experiencia" class="form-label">Experiencia</label>
                <textarea name="experiencia" id="experiencia" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="redes_sociales" class="form-label">Redes sociales</label>
                <textarea name="redes_sociales" id="redes_sociales" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="foto" class="form-label">Imagen</label>
                <input type="file" name="foto" id="foto" class="form-control">
                <div id="current-photo"></div> <!-- Muestra la imagen actual -->
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>

    <script>
        // Función para cargar los datos del ponente
        async function cargarPonente(id) {
            try {
                const response = await fetch(`/api/ponentes/${id}`);

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al obtener los datos del ponente');
                }

                // Rellenar el formulario con los datos del ponente
                document.getElementById('nombre').value = data.nombre;
                document.getElementById('experiencia').value = data.experiencia;
                document.getElementById('redes_sociales').value = data.redes_sociales;

                // Mostrar la foto actual si existe
                if (data.foto) {
                    const imgElement = document.createElement('img');
                    imgElement.src = `{{ asset('storage') }}/${data.foto}`;
                    imgElement.width = 50;
                    imgElement.height = 50;
                    document.getElementById('current-photo').appendChild(imgElement);
                }

            } catch (error) {
                document.getElementById('error-message').classList.remove('d-none');
                document.getElementById('error-message').textContent = error.message;
            }
        }

        // Cargar los datos del ponente cuando la página se cargue
        document.addEventListener('DOMContentLoaded', () => {
            const ponenteId = "{{ $ponente->id }}"; // ID del ponente
            cargarPonente(ponenteId);
        });

        // Evento para enviar el formulario con fetch
        document.getElementById('edit-ponente-form').addEventListener('submit', async function (event) {
            event.preventDefault(); // Prevenir el envío tradicional del formulario

            const formData = new FormData(this); // Recoger todos los datos del formulario, incluidos los archivos

            const ponenteId = "{{ $ponente->id }}"; // ID del ponente a actualizar

            try {
                const response = await fetch(`/api/ponentes/${ponenteId}`, {
                    method: 'PUT',
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}" // Asegurarse de pasar el CSRF token
                    }
                });

                console.log(response.status);  // Para verificar el código de respuesta
                console.log(response.url);     // Para ver a dónde está redirigiendo
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || "Error al actualizar el ponente");
                }

                // Mostrar mensaje de éxito
                document.getElementById('success-message').classList.remove('d-none');
                document.getElementById('success-message').textContent = "Ponente actualizado correctamente";

            } catch (error) {
                // Mostrar mensaje de error
                document.getElementById('error-message').classList.remove('d-none');
                document.getElementById('error-message').textContent = error.message;
            }
        });
    </script>
@endsection



@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Añadir Ponente</h1>

        <div id="success-message" class="alert alert-success d-none"></div>
        <div id="error-message" class="alert alert-danger d-none"></div>

        <form id="ponente-form">
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
            </div>

            <button type="submit" class="btn btn-success">Guardar</button>
        </form>
    </div>

    <script>
        document.getElementById('ponente-form').addEventListener('submit', async function (event) {
            event.preventDefault(); // Evita el envío tradicional del formulario

            const formData = new FormData(this); // Recoge los datos del formulario

            try {
                const response = await fetch('/api/ponentes', {
                    method: 'POST',
                    body: formData,
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || "Error al añadir ponente");
                }

                document.getElementById('success-message').classList.remove('d-none');
                document.getElementById('success-message').textContent = "Ponente añadido correctamente";

                this.reset(); // Limpia el formulario tras éxito
            } catch (error) {
                document.getElementById('error-message').classList.remove('d-none');
                document.getElementById('error-message').textContent = error.message;
            }
        });
    </script>
@endsection

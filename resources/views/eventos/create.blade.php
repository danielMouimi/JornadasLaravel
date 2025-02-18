@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Crear Nuevo Evento</h1>

        <div id="error-messages" class="alert alert-danger" style="display: none;">
            <ul id="error-list"></ul>
        </div>

        <form id="create-event-form">
            @csrf

            <!-- Título -->
            <div class="mb-3">
                <label for="titulo" class="form-label">Título del evento</label>
                <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo') }}" required>
            </div>

            <!-- Tipo -->
            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo de evento</label>
                <select name="tipo" id="tipo" class="form-control" required>
                    <option value="conferencia" {{ old('tipo') == 'conferencia' ? 'selected' : '' }}>Conferencia</option>
                    <option value="taller" {{ old('tipo') == 'taller' ? 'selected' : '' }}>Taller</option>
                </select>
            </div>

            <!-- Fecha -->
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="{{ old('fecha') }}" required min="{{ now()->toDateString() }}">
            </div>

            <!-- Hora de inicio -->
            <div class="mb-3">
                <label for="hora_inicio" class="form-label">Hora de inicio</label>
                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control" value="{{ old('hora_inicio') }}" required>
            </div>

            <!-- Hora de fin (Se rellena automáticamente) -->
            <div class="mb-3">
                <label for="hora_fin" class="form-label">Hora de fin</label>
                <input type="time" name="hora_fin" id="hora_fin" class="form-control" readonly required>
            </div>

            <!-- Ponente (Desplegable) -->
            <div class="mb-3">
                <label for="ponente_id" class="form-label">Ponente</label>
                <select name="ponente_id" id="ponente_id" class="form-control" required>
                    <option value="">Selecciona un ponente</option>
                    @foreach ($ponentes as $ponente)
                        <option value="{{ $ponente->id }}" {{ old('ponente_id') == $ponente->id ? 'selected' : '' }}>
                            {{ $ponente->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Capacidad máxima -->
            <div class="mb-3">
                <label for="capacidad_maxima" class="form-label">Capacidad máxima</label>
                <input type="number" name="capacidad_maxima" id="capacidad_maxima" class="form-control" value="{{ old('capacidad_maxima') }}" required min="1">
            </div>

            <button type="submit" class="btn btn-success">Crear Evento</button>
            <a href="{{ route('admin.eventos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script>
        document.getElementById('hora_inicio').addEventListener('input', function() {
            let horaInicio = this.value;
            if (horaInicio) {
                let [horas, minutos] = horaInicio.split(':').map(Number);
                let fecha = new Date();
                fecha.setHours(horas, minutos);
                fecha.setMinutes(fecha.getMinutes() + 55); // Agregar 55 minutos

                let horaFin = fecha.toTimeString().split(':').slice(0, 2).join(':');
                document.getElementById('hora_fin').value = horaFin;
            }
        });

        document.getElementById('create-event-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Evita el envío del formulario

            const token = localStorage.getItem('token');

            if (!token) {
                console.error("Token no encontrado");
                return;
            }

            const formData = new FormData(this);
            const data = {
                titulo: formData.get('titulo'),
                tipo: formData.get('tipo'),
                fecha: formData.get('fecha'),
                hora_inicio: formData.get('hora_inicio'),
                hora_fin: formData.get('hora_fin'),
                ponente_id: formData.get('ponente_id'),
                capacidad_maxima: formData.get('capacidad_maxima')
            };

            fetch('/api/eventos', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(responseData => {
                    if (responseData.errors) {
                        // Muestra los errores si los hay
                        const errorMessages = document.getElementById('error-messages');
                        const errorList = document.getElementById('error-list');
                        errorList.innerHTML = '';
                        errorMessages.style.display = 'block';
                        responseData.errors.forEach(error => {
                            const errorItem = document.createElement('li');
                            errorItem.textContent = error;
                            errorList.appendChild(errorItem);
                        });
                    } else {
                        // Redirige o muestra un mensaje de éxito
                        window.location.href = '/admin/GestionEventos';
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Hubo un error al crear el evento.");
                });
        });
    </script>
@endsection

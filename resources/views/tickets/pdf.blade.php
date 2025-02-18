<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Inscripción</title>
    <style>
body { font-family: Arial, sans-serif; text-align: center; }
        .ticket { border: 2px dashed black; padding: 20px; width: 50%; margin: auto; }
        .codigo { font-size: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="ticket">
        <h1>Jornadas de Videojuegos</h1>
        <p><strong>Nombre:</strong> {{ $user->name }}</p>
<p><strong>Email:</strong> {{ $user->email }}</p>
<p><strong>Tipo de inscripción:</strong> {{ ucfirst($user->tipo_inscripcion) }}</p>
<p><strong>Código de ticket:</strong> <span class="codigo">{{ $ticket->codigo }}</span></p>
<p>Este ticket es intransferible y deberá presentarse en la entrada.</p>
</div>
</body>
</html>

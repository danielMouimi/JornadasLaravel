<!DOCTYPE html>
<html>
<head>
    <title>Tu Ticket</title>
</head>
<body>
<h1>¡Gracias por tu pago, {{ $user->name }}!</h1>
<p>Has completado tu inscripción como {{ $user->tipo_inscripcion }}.</p>
<p>Muestra este ticket en la entrada del evento</p>
</body>
</html>

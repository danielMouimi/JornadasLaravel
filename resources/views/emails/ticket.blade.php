<!DOCTYPE html>
<html>
<head>
    <title>Tu Ticket</title>
</head>
<body>
<h1>¡Gracias por tu pago, {{ $user->name }}!</h1>
<p>Has completado tu inscripción como {{ $user->tipo_inscripcion }}.</p>
<p>Total pagado: €{{ number_format($user->total_pagado, 2) }}</p>
<p>Tu ticket: <strong>#{{ $user->id }}-{{ $user->paypal_transaction_id }}</strong></p>
</body>
</html>

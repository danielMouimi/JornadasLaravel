<script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_CLIENT_ID') }}&currency=EUR"></script>
@if (Auth::user()->email_verified_at == null)
    <div class="alert alert-warning">
        ¡Por favor verifica tu correo! Te hemos enviado un enlace de verificación.
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit">Reenviar correo de verificación</button>
        </form>
    </div>
@else
<!-- Selector para elegir tipo de inscripción -->
<label for="tipo-inscripcion">Selecciona tu tipo de inscripción:</label>
<select id="tipo-inscripcion">
    <option value="15">Presencial (€15)</option>
    <option value="10">Virtual (€10)</option>
    <option value="0">Alumno (Gratis)</option>
</select>

<div id="paypal-button-container"></div>

<script>
    // Obtener el elemento del selector
    const tipoInscripcion = document.getElementById("tipo-inscripcion");

    // Variable para almacenar el precio seleccionado
    let precio = tipoInscripcion.value;

    // Actualizar el precio cuando el usuario cambie el tipo de inscripción
    tipoInscripcion.addEventListener("change", function () {
        precio = this.value;
        renderPayPalButtons();
    });

    function renderPayPalButtons() {
        // Limpiar el contenedor antes de renderizar los botones nuevamente
        document.getElementById("paypal-button-container").innerHTML = "";

        paypal.Buttons({
            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: precio
                        }
                    }]
                });
            },

            onApprove: function (data, actions) {
                return actions.order.capture().then(function (details) {
                    alert('Transacción completada por: ' + details.payer.name.given_name);
                    window.location.href = '/pagado';
                });
            }

        }).render("#paypal-button-container");
    }

    // Renderizar los botones de PayPal por primera vez
    renderPayPalButtons();
</script>
@endif

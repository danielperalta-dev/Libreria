<?php
require 'config.php';
requiere_login();
$items = carrito_detalle($pdo);
if (!$items) { msg('err', 'Tu carrito está vacío.'); header('Location: carrito.php'); exit; }
$total = array_sum(array_column($items, 'subtotal'));
$titulo = 'Pagar | El Rincón del Lector';
require 'includes/header.php';
?>
<main class="contenedor sombra seccion">
    <h2>Resumen y pago</h2>
    <div class="tabla-scroll">
    <table class="tabla">
        <tr><th>Libro</th><th>Cant.</th><th class="num">Subtotal</th></tr>
        <?php foreach ($items as $i): ?>
        <tr><td><?= e($i['titulo']) ?></td><td><?= (int)$i['cantidad'] ?></td><td class="num">$<?= number_format($i['subtotal'], 2) ?></td></tr>
        <?php endforeach; ?>
    </table>
    </div>
    <p class="total">Total: $<?= number_format($total, 2) ?> <?= MONEDA ?></p>

    <form class="formulario" id="datos-envio" onsubmit="return false">
        <fieldset>
            <legend>Datos de envío</legend>
            <div class="campo"><label for="telefono">Teléfono</label><input class="input-text" type="tel" id="telefono" required></div>
            <div class="campo"><label for="direccion">Dirección de envío</label>
                <textarea class="input-text" id="direccion" placeholder="Calle, número, colonia, ciudad y código postal" required></textarea></div>
            <div id="paypal-button"></div>
            <p id="error-pago" class="alerta alerta-err" style="display:none;margin-top:2rem"></p>
        </fieldset>
    </form>
</main>

<script src="https://www.paypal.com/sdk/js?client-id=<?= e(PAYPAL_CLIENT_ID) ?>&currency=<?= MONEDA ?>"></script>
<script>
const form = document.getElementById('datos-envio');
const err = document.getElementById('error-pago');
function mostrarError(t) { err.textContent = t; err.style.display = 'block'; }

paypal.Buttons({
    createOrder: async () => {
        if (!form.reportValidity()) throw new Error('Completa los datos de envío');
        const r = await fetch('api/crear_orden.php', {
            method: 'POST', headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({telefono: telefono.value, direccion: direccion.value})
        });
        const d = await r.json();
        if (!d.id) { mostrarError(d.error || 'No se pudo crear la orden'); throw new Error(d.error); }
        return d.id;
    },
    onApprove: async (data) => {
        const r = await fetch('api/capturar_orden.php', {
            method: 'POST', headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({orderID: data.orderID})
        });
        const d = await r.json();
        if (d.ok) location.href = 'gracias.php?id=' + d.venta_id;
        else mostrarError(d.error || 'No se pudo confirmar el pago');
    },
    onError: () => mostrarError('Ocurrió un error con PayPal. Intenta de nuevo.')
}).render('#paypal-button');
</script>
<?php require 'includes/footer.php'; ?>

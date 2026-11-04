<?php
require 'config.php';
requiere_login();
$id = (int)($_GET['id'] ?? 0);
$titulo = 'Gracias por tu compra';
require 'includes/header.php';
?>
<main class="contenedor sombra seccion" style="text-align:center">
    <h2>¡Pago realizado con éxito!</h2>
    <p>Tu pedido #<?= $id ?> quedó registrado. Descarga tu comprobante:</p>
    <p><a class="boton" href="recibo.php?id=<?= $id ?>" target="_blank">Ver recibo en PDF</a></p>
    <p><a href="index.php#catalogo">Seguir comprando</a></p>
</main>
<?php require 'includes/footer.php'; ?>

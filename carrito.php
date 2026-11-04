<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    if ($accion === 'add') {
        $st = $pdo->prepare('SELECT stock FROM productos WHERE id=?'); $st->execute([$id]);
        $p = $st->fetch();
        if (!$p || $p['stock'] < 1) msg('err', 'Producto no disponible.');
        else {
            $_SESSION['carrito'][$id] = min($p['stock'], ($_SESSION['carrito'][$id] ?? 0) + 1);
            msg('ok', 'Libro agregado al carrito.');
        }
        header('Location: '.(($_POST['volver'] ?? '') === 'catalogo' ? 'index.php#catalogo' : 'carrito.php')); exit;
    }
    if ($accion === 'actualizar') {
        $st = $pdo->prepare('SELECT stock FROM productos WHERE id=?'); $st->execute([$id]);
        $p = $st->fetch(); $c = (int)($_POST['cantidad'] ?? 1);
        if ($p && $c > 0) $_SESSION['carrito'][$id] = min($p['stock'], $c); else unset($_SESSION['carrito'][$id]);
    }
    if ($accion === 'quitar') unset($_SESSION['carrito'][$id]);
    if ($accion === 'vaciar') unset($_SESSION['carrito']);
    header('Location: carrito.php'); exit;
}
$titulo = 'Carrito | El Rincón del Lector';
require 'includes/header.php';
$items = carrito_detalle($pdo);
$total = array_sum(array_column($items, 'subtotal'));
?>
<main class="contenedor sombra seccion">
    <h2>Tu carrito</h2>
    <?php mostrar_msg(); ?>
    <?php if (!$items): ?>
        <p style="text-align:center">Tu carrito está vacío.</p>
        <p style="text-align:center"><a class="boton" href="index.php#catalogo">Ver catálogo</a></p>
    <?php else: ?>
    <div class="tabla-scroll">
    <table class="tabla">
        <tr><th></th><th>Libro</th><th>Precio</th><th>Cantidad</th><th class="num">Subtotal</th><th></th></tr>
        <?php foreach ($items as $i): ?>
        <tr>
            <td><?php if ($i['imagen']): ?><img src="Img/<?= e($i['imagen']) ?>" alt=""><?php endif; ?></td>
            <td><?= e($i['titulo']) ?></td>
            <td>$<?= number_format($i['precio'], 2) ?></td>
            <td>
                <form method="post" class="flex" style="gap:.5rem">
                    <input type="hidden" name="accion" value="actualizar">
                    <input type="hidden" name="id" value="<?= (int)$i['id'] ?>">
                    <input type="number" name="cantidad" value="<?= (int)$i['cantidad'] ?>" min="0" max="<?= (int)$i['stock'] ?>" style="width:7rem;padding:.6rem">
                    <button class="boton boton-chico" type="submit">Actualizar</button>
                </form>
            </td>
            <td class="num">$<?= number_format($i['subtotal'], 2) ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="accion" value="quitar">
                    <input type="hidden" name="id" value="<?= (int)$i['id'] ?>">
                    <button class="boton boton-chico boton-peligro" type="submit">Quitar</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <p class="total">Total: $<?= number_format($total, 2) ?> <?= MONEDA ?></p>
    <div class="flex alinear-derecha" style="gap:1rem;flex-wrap:wrap">
        <form method="post"><input type="hidden" name="accion" value="vaciar"><button class="boton boton-peligro" type="submit">Vaciar</button></form>
        <a class="boton" href="checkout.php">Pagar con PayPal</a>
    </div>
    <?php endif; ?>
</main>
<?php require 'includes/footer.php'; ?>

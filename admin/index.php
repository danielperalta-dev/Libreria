<?php
$ruta = '../';
require '../config.php';
requiere_admin();
$productos = $pdo->query('SELECT * FROM productos ORDER BY id')->fetchAll();
$ventas = $pdo->query('SELECT v.*, u.nombre, u.correo FROM ventas v JOIN usuarios u ON u.id=v.usuario_id ORDER BY v.id DESC')->fetchAll();
$totalIngresos = array_sum(array_column($ventas, 'total'));
$librosVendidos = array_sum(array_column($productos, 'vendidos'));
$clientes = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol='cliente'")->fetchColumn();
$titulo = 'Panel de administración';
require '../includes/header.php';
?>
<main class="contenedor sombra seccion">
    <h2>Panel de administración</h2>
    <?php mostrar_msg(); ?>
    <div class="stats">
        <div class="stat"><span><?= count($ventas) ?></span>Ventas</div>
        <div class="stat"><span>$<?= number_format($totalIngresos, 2) ?></span>Ingresos</div>
        <div class="stat"><span><?= (int)$librosVendidos ?></span>Libros vendidos</div>
        <div class="stat"><span><?= (int)$clientes ?></span>Clientes</div>
    </div>

    <h3 style="margin-bottom:1.5rem">Productos</h3>
    <p style="text-align:right"><a class="boton boton-chico" href="producto.php">+ Agregar producto</a></p>
    <div class="tabla-scroll">
    <table class="tabla">
        <tr><th>ID</th><th>Imagen</th><th>Título</th><th>Autor</th><th>Precio</th><th>Etiqueta</th><th>Stock</th><th>Vendidos</th><th></th></tr>
        <?php foreach ($productos as $p): ?>
        <tr>
            <td><?= (int)$p['id'] ?></td>
            <td><?php if ($p['imagen']): ?><img src="../Img/<?= e($p['imagen']) ?>" alt=""><?php endif; ?></td>
            <td><?= e($p['titulo']) ?></td>
            <td><?= e($p['autor']) ?></td>
            <td>$<?= number_format($p['precio'], 2) ?><?= $p['precio_anterior'] ? ' <del>$'.number_format($p['precio_anterior'], 2).'</del>' : '' ?></td>
            <td><?= e($p['etiqueta']) ?></td>
            <td class="<?= $p['stock'] <= 5 ? 'bajo' : '' ?>"><?= (int)$p['stock'] ?></td>
            <td><?= (int)$p['vendidos'] ?></td>
            <td><a class="boton boton-chico" href="producto.php?id=<?= (int)$p['id'] ?>">Editar</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>

    <h3 style="margin-bottom:1.5rem">Ventas</h3>
    <div class="tabla-scroll">
    <table class="tabla">
        <tr><th>Folio</th><th>Cliente</th><th>Correo</th><th>Total</th><th>Fecha</th><th>ID PayPal</th><th>Recibo</th></tr>
        <?php foreach ($ventas as $v): ?>
        <tr>
            <td><?= (int)$v['id'] ?></td><td><?= e($v['nombre']) ?></td><td><?= e($v['correo']) ?></td>
            <td>$<?= number_format($v['total'], 2) ?></td><td><?= e($v['fecha']) ?></td><td><?= e($v['paypal_order_id']) ?></td>
            <td><a href="../recibo.php?id=<?= (int)$v['id'] ?>" target="_blank">PDF</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$ventas): ?><tr><td colspan="7">Aún no hay ventas.</td></tr><?php endif; ?>
    </table>
    </div>
</main>
<?php require '../includes/footer.php'; ?>

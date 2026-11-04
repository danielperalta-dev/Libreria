<?php
require_once __DIR__.'/../config.php';
$ruta = $ruta ?? '';
$u = usuario();
$nCarrito = array_sum($_SESSION['carrito'] ?? []);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Librería en línea: clásicos, novelas y más con envíos a todo México.">
    <title><?= e($titulo ?? 'El Rincón del Lector | Librería') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Krub:wght@400;700&display=swap" rel="stylesheet">
    <link href="<?= $ruta ?>styles.css" rel="stylesheet">
</head>
<body>
    <header class="header" id="inicio">
        <div class="contenedor contenido-header">
            <h1 class="titulo">El Rincón del Lector <span>Librería</span></h1>
        </div>
    </header>

    <div class="nav-bg">
        <nav class="navegacion-principal contenedor">
            <a href="<?= $ruta ?>index.php">Inicio</a>
            <a href="<?= $ruta ?>index.php#beneficios">Beneficios</a>
            <a href="<?= $ruta ?>index.php#catalogo">Catálogo</a>
            <a href="<?= $ruta ?>carrito.php">Carrito (<?= $nCarrito ?>)</a>
            <?php if ($u): ?>
                <?php if (es_admin()): ?><a href="<?= $ruta ?>admin/index.php">Panel admin</a><?php endif; ?>
                <a href="<?= $ruta ?>logout.php">Salir (<?= e(explode(' ', $u['nombre'])[0]) ?>)</a>
            <?php else: ?>
                <a href="<?= $ruta ?>login.php">Ingresar</a>
            <?php endif; ?>
        </nav>
    </div>

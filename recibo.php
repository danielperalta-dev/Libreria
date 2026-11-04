<?php
require 'config.php';
require 'includes/pdf.php';
requiere_login();
$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare('SELECT v.*, u.nombre, u.correo FROM ventas v JOIN usuarios u ON u.id=v.usuario_id WHERE v.id=?');
$st->execute([$id]);
$v = $st->fetch();
if (!$v || (!es_admin() && $v['usuario_id'] != usuario()['id'])) { http_response_code(404); die('Recibo no encontrado'); }
$d = $pdo->prepare('SELECT * FROM venta_detalle WHERE venta_id=?'); $d->execute([$id]);
$det = $d->fetchAll();

$p = new MiniPDF();
$y = 790;
$p->text(50, $y, 'El Rincón del Lector - Librería', 18, true); $y -= 22;
$p->text(50, $y, 'RECIBO DE PAGO', 13, true); $y -= 25;
$p->line(50, $y, 545, $y); $y -= 20;
$p->text(50, $y, 'Folio: '.str_pad($v['id'], 6, '0', STR_PAD_LEFT)); $p->text(300, $y, 'Fecha: '.$v['fecha']); $y -= 16;
$p->text(50, $y, 'Cliente: '.$v['nombre']); $y -= 16;
$p->text(50, $y, 'Correo: '.$v['correo']); $y -= 16;
$p->text(50, $y, 'Teléfono: '.$v['telefono']); $y -= 16;
$p->text(50, $y, 'ID de pago PayPal: '.$v['paypal_order_id']); $y -= 16;
$p->text(50, $y, 'Dirección de envío:'); $y -= 14;
foreach (explode("\n", wordwrap(str_replace(["\r", "\n"], ' ', $v['direccion']), 85, "\n", true)) as $l) { $p->text(60, $y, $l); $y -= 14; }
$y -= 10; $p->line(50, $y, 545, $y); $y -= 16;
$p->text(50, $y, 'Libro', 11, true); $p->text(320, $y, 'Cant.', 11, true); $p->text(380, $y, 'Precio', 11, true); $p->text(470, $y, 'Subtotal', 11, true);
$y -= 8; $p->line(50, $y, 545, $y); $y -= 16;
foreach ($det as $i) {
    $p->text(50, $y, mb_strimwidth($i['titulo'], 0, 45, '...'));
    $p->text(325, $y, (string)$i['cantidad']);
    $p->text(380, $y, '$'.number_format($i['precio_unitario'], 2));
    $p->text(470, $y, '$'.number_format($i['cantidad'] * $i['precio_unitario'], 2));
    $y -= 16;
}
$y -= 4; $p->line(50, $y, 545, $y); $y -= 22;
$p->text(380, $y, 'TOTAL: $'.number_format($v['total'], 2).' '.MONEDA, 13, true); $y -= 40;
$p->text(50, $y, 'Pago procesado con PayPal. Gracias por tu compra.', 10);
$p->output('Recibo_'.str_pad($v['id'], 6, '0', STR_PAD_LEFT).'.pdf');

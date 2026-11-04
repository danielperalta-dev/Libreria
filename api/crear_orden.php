<?php
require '../config.php';
require '../includes/paypal.php';
header('Content-Type: application/json; charset=utf-8');
function fin($a, $c = 200) { http_response_code($c); echo json_encode($a); exit; }

if (!usuario()) fin(['error' => 'Sesión expirada'], 401);
$in = json_decode(file_get_contents('php://input'), true);
$tel = trim($in['telefono'] ?? ''); $dir = trim($in['direccion'] ?? '');
if ($tel === '' || $dir === '') fin(['error' => 'Completa teléfono y dirección'], 400);

$items = carrito_detalle($pdo);
if (!$items) fin(['error' => 'Carrito vacío'], 400);
$total = 0;
foreach ($items as $i) {
    if ($i['cantidad'] > $i['stock']) fin(['error' => 'Sin existencias suficientes de: '.$i['titulo']], 409);
    $total += $i['subtotal'];       // el total se calcula en el servidor, nunca con datos del navegador
}
$_SESSION['envio'] = ['telefono' => $tel, 'direccion' => $dir];

$r = paypal_request('POST', '/v2/checkout/orders', [
    'intent' => 'CAPTURE',
    'purchase_units' => [['amount' => ['currency_code' => MONEDA, 'value' => number_format($total, 2, '.', '')]]],
]);
if (empty($r['id'])) fin(['error' => $r['error'] ?? ($r['message'] ?? 'PayPal rechazó la orden')], 502);
fin(['id' => $r['id']]);

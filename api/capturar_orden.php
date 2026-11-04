<?php
require '../config.php';
require '../includes/paypal.php';
header('Content-Type: application/json; charset=utf-8');
function fin($a, $c = 200) { http_response_code($c); echo json_encode($a); exit; }

$u = usuario();
if (!$u) fin(['ok' => false, 'error' => 'Sesión expirada'], 401);
$in = json_decode(file_get_contents('php://input'), true);
$orderId = preg_replace('/[^A-Za-z0-9]/', '', $in['orderID'] ?? '');
if (!$orderId) fin(['ok' => false, 'error' => 'Orden inválida'], 400);

// Evita registrar dos veces la misma orden
$ya = $pdo->prepare('SELECT id FROM ventas WHERE paypal_order_id=?'); $ya->execute([$orderId]);
if ($v = $ya->fetch()) fin(['ok' => true, 'venta_id' => $v['id']]);

$items = carrito_detalle($pdo);
if (!$items) fin(['ok' => false, 'error' => 'Carrito vacío'], 400);

$cap = paypal_request('POST', "/v2/checkout/orders/$orderId/capture");
if (($cap['status'] ?? '') !== 'COMPLETED') fin(['ok' => false, 'error' => 'El pago no se completó'], 402);
$pagado = (float)($cap['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? 0);

$envio = $_SESSION['envio'] ?? ['telefono' => '', 'direccion' => ''];
try {
    $pdo->beginTransaction();
    $pdo->prepare('INSERT INTO ventas (usuario_id,total,paypal_order_id,telefono,direccion) VALUES (?,?,?,?,?)')
        ->execute([$u['id'], $pagado, $orderId, $envio['telefono'], $envio['direccion']]);
    $vid = $pdo->lastInsertId();
    $upd = $pdo->prepare('UPDATE productos SET stock = stock - ?, vendidos = vendidos + ? WHERE id=? AND stock >= ?');
    $det = $pdo->prepare('INSERT INTO venta_detalle (venta_id,producto_id,titulo,cantidad,precio_unitario) VALUES (?,?,?,?,?)');
    foreach ($items as $i) {
        $upd->execute([$i['cantidad'], $i['cantidad'], $i['id'], $i['cantidad']]);   // disminuye inventario y suma ventas
        if ($upd->rowCount() === 0) throw new Exception('Sin existencias de '.$i['titulo']);
        $det->execute([$vid, $i['id'], $i['titulo'], $i['cantidad'], $i['precio']]);
    }
    $pdo->commit();
    unset($_SESSION['carrito'], $_SESSION['envio']);
    fin(['ok' => true, 'venta_id' => $vid]);
} catch (Exception $ex) {
    $pdo->rollBack();
    fin(['ok' => false, 'error' => $ex->getMessage().' (referencia PayPal: '.$orderId.')'], 409);
}

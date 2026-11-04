<?php
session_start();

const DB_HOST = 'localhost';   
const DB_PORT = 3308;         
const DB_NAME = 'libreria';
const DB_USER = 'root';
const DB_PASS = '';

const PAYPAL_CLIENT_ID = 'Pega el tuyo aqui';
const PAYPAL_SECRET    = 'Pega el tuyo aqui';
const PAYPAL_API       = 'https://api-m.sandbox.paypal.com'; 
const MONEDA           = 'MXN';
const VERIFICAR_SSL    = true; 

try {
    $pdo = new PDO('mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('No se pudo conectar a la base de datos. Revisa config.php');
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function usuario() { return $_SESSION['usuario'] ?? null; }
function es_admin() { return (usuario()['rol'] ?? '') === 'admin'; }
function msg($tipo, $texto) { $_SESSION['msg'] = [$tipo, $texto]; }
function mostrar_msg() {
    if (!empty($_SESSION['msg'])) {
        [$t, $x] = $_SESSION['msg']; unset($_SESSION['msg']);
        echo '<div class="alerta alerta-'.($t === 'ok' ? 'ok' : 'err').'">'.e($x).'</div>';
    }
}
function requiere_login($ruta = '') {
    if (!usuario()) { msg('err', 'Inicia sesión para continuar.'); header('Location: '.$ruta.'login.php'); exit; }
}
function requiere_admin($ruta = '../') {
    if (!es_admin()) { msg('err', 'Acceso solo para administradores.'); header('Location: '.$ruta.'login.php'); exit; }
}
function carrito_detalle(PDO $pdo) {
    $c = $_SESSION['carrito'] ?? [];
    if (!$c) return [];
    $ids = array_keys($c);
    $in = implode(',', array_fill(0, count($ids), '?'));
    $st = $pdo->prepare("SELECT * FROM productos WHERE id IN ($in)");
    $st->execute($ids);
    $r = [];
    foreach ($st->fetchAll() as $p) {
        $p['cantidad'] = $c[$p['id']];
        $p['subtotal'] = $p['cantidad'] * $p['precio'];
        $r[] = $p;
    }
    return $r;
}

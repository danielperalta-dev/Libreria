<?php
require 'config.php';
$correo = 'admin@libreria.com';
$pass = 'Admin123';
$st = $pdo->prepare('SELECT id FROM usuarios WHERE correo=?');
$st->execute([$correo]);
if ($st->fetch()) { echo 'El administrador ya existe.'; exit; }
$pdo->prepare('INSERT INTO usuarios (nombre,correo,password,rol) VALUES (?,?,?,"admin")')
    ->execute(['Administrador', $correo, password_hash($pass, PASSWORD_DEFAULT)]);
echo "Administrador creado. Correo: $correo | Contraseña: $pass <br><b>Borra este archivo ahora.</b>";

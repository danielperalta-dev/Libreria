<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = trim($_POST['nombre'] ?? ''); $c = trim($_POST['correo'] ?? ''); $p = $_POST['password'] ?? '';
    if ($n === '' || !filter_var($c, FILTER_VALIDATE_EMAIL) || strlen($p) < 6) {
        msg('err', 'Completa los campos (contraseña de al menos 6 caracteres).');
    } else {
        $st = $pdo->prepare('SELECT id FROM usuarios WHERE correo=?'); $st->execute([$c]);
        if ($st->fetch()) msg('err', 'Ese correo ya está registrado.');
        else {
            $pdo->prepare('INSERT INTO usuarios (nombre,correo,password) VALUES (?,?,?)')->execute([$n, $c, password_hash($p, PASSWORD_DEFAULT)]);
            msg('ok', 'Cuenta creada. Ya puedes iniciar sesión.');
            header('Location: login.php'); exit;
        }
    }
    header('Location: registro.php'); exit;
}
$titulo = 'Registro | El Rincón del Lector';
require 'includes/header.php';
?>
<main class="contenedor sombra seccion">
    <h2>Crear cuenta</h2>
    <?php mostrar_msg(); ?>
    <form class="formulario" method="post">
        <fieldset>
            <legend>Regístrate para comprar</legend>
            <div class="campo"><label for="nombre">Nombre</label><input class="input-text" type="text" id="nombre" name="nombre" required></div>
            <div class="campo"><label for="correo">Correo</label><input class="input-text" type="email" id="correo" name="correo" required></div>
            <div class="campo"><label for="password">Contraseña</label><input class="input-text" type="password" id="password" name="password" minlength="6" required></div>
            <div class="flex alinear-derecha"><input class="boton w-sm-100" type="submit" value="Registrarme"></div>
        </fieldset>
    </form>
</main>
<?php require 'includes/footer.php'; ?>

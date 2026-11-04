<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = $pdo->prepare('SELECT * FROM usuarios WHERE correo=?');
    $st->execute([trim($_POST['correo'] ?? '')]);
    $u = $st->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['usuario'] = ['id' => $u['id'], 'nombre' => $u['nombre'], 'correo' => $u['correo'], 'rol' => $u['rol']];
        header('Location: '.($u['rol'] === 'admin' ? 'admin/index.php' : 'index.php')); exit;
    }
    msg('err', 'Correo o contraseña incorrectos.');
    header('Location: login.php'); exit;
}
$titulo = 'Ingresar | El Rincón del Lector';
require 'includes/header.php';
?>
<main class="contenedor sombra seccion">
    <h2>Iniciar sesión</h2>
    <?php mostrar_msg(); ?>
    <form class="formulario" method="post">
        <fieldset>
            <legend>Ingresa a tu cuenta</legend>
            <div class="campo"><label for="correo">Correo</label><input class="input-text" type="email" id="correo" name="correo" required></div>
            <div class="campo"><label for="password">Contraseña</label><input class="input-text" type="password" id="password" name="password" required></div>
            <div class="flex alinear-derecha"><input class="boton w-sm-100" type="submit" value="Entrar"></div>
            <p style="color:#fff;text-align:center;margin-top:2rem">¿No tienes cuenta? <a href="registro.php" style="color:var(--primario)">Regístrate</a></p>
        </fieldset>
    </form>
</main>
<?php require 'includes/footer.php'; ?>

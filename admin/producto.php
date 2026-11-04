<?php
$ruta = '../';
require '../config.php';
requiere_admin();
$id = (int)($_GET['id'] ?? 0);
$p = ['titulo' => '', 'autor' => '', 'descripcion' => '', 'precio' => '', 'precio_anterior' => '', 'etiqueta' => '', 'imagen' => '', 'stock' => 10];
if ($id) {
    $st = $pdo->prepare('SELECT * FROM productos WHERE id=?'); $st->execute([$id]);
    $p = $st->fetch();
    if (!$p) { header('Location: index.php'); exit; }
}
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? ''); $autor = trim($_POST['autor'] ?? '');
    $desc = trim($_POST['descripcion'] ?? ''); $precio = (float)($_POST['precio'] ?? 0);
    $ant = (float)($_POST['precio_anterior'] ?? 0) ?: null;
    $etq = in_array($_POST['etiqueta'] ?? '', ['Más vendido', 'Oferta']) ? $_POST['etiqueta'] : null;
    $stock = max(0, (int)($_POST['stock'] ?? 0));
    $img = $p['imagen'];
    if (!empty($_FILES['imagen']['name']) && $_FILES['imagen']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $base = preg_replace('/[^A-Za-z0-9_-]/', '', pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME)) ?: 'libro';
            $img = $base.'_'.time().'.'.$ext;
            move_uploaded_file($_FILES['imagen']['tmp_name'], '../Img/'.$img);
        } else $err = 'La imagen debe ser JPG, PNG o WEBP.';
    }
    if (!$err && ($titulo === '' || $autor === '' || $precio <= 0)) $err = 'Título, autor y precio son obligatorios.';
    if (!$err) {
        if ($id) $pdo->prepare('UPDATE productos SET titulo=?,autor=?,descripcion=?,precio=?,precio_anterior=?,etiqueta=?,imagen=?,stock=? WHERE id=?')
                     ->execute([$titulo, $autor, $desc, $precio, $ant, $etq, $img, $stock, $id]);
        else $pdo->prepare('INSERT INTO productos (titulo,autor,descripcion,precio,precio_anterior,etiqueta,imagen,stock) VALUES (?,?,?,?,?,?,?,?)')
                 ->execute([$titulo, $autor, $desc, $precio, $ant, $etq, $img, $stock]);
        msg('ok', 'Producto guardado.');
        header('Location: index.php'); exit;
    }
    $p = array_merge($p, ['titulo' => $titulo, 'autor' => $autor, 'descripcion' => $desc, 'precio' => $precio, 'stock' => $stock]);
}
$titulo = $id ? 'Editar producto' : 'Nuevo producto';
require '../includes/header.php';
?>
<main class="contenedor sombra seccion">
    <h2><?= e($titulo) ?></h2>
    <?php if ($err): ?><div class="alerta alerta-err"><?= e($err) ?></div><?php endif; ?>
    <form class="formulario" method="post" enctype="multipart/form-data">
        <fieldset>
            <legend>Datos del producto</legend>
            <div class="contenedor-campos">
                <div class="campo"><label>Título</label><input class="input-text" name="titulo" value="<?= e($p['titulo']) ?>" required></div>
                <div class="campo"><label>Autor</label><input class="input-text" name="autor" value="<?= e($p['autor']) ?>" required></div>
                <div class="campo"><label>Precio</label><input class="input-text" type="number" step="0.01" min="0" name="precio" value="<?= e($p['precio']) ?>" required></div>
                <div class="campo"><label>Precio anterior (opcional)</label><input class="input-text" type="number" step="0.01" min="0" name="precio_anterior" value="<?= e($p['precio_anterior']) ?>"></div>
                <div class="campo"><label>Etiqueta</label>
                    <select class="input-text" name="etiqueta">
                        <?php foreach (['' => 'Ninguna', 'Más vendido' => 'Más vendido', 'Oferta' => 'Oferta'] as $k => $t): ?>
                            <option value="<?= e($k) ?>" <?= ($p['etiqueta'] ?? '') === $k ? 'selected' : '' ?>><?= e($t) ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="campo"><label>Stock</label><input class="input-text" type="number" min="0" name="stock" value="<?= (int)$p['stock'] ?>" required></div>
                <div class="campo campo-completo"><label>Descripción</label><textarea class="input-text" name="descripcion"><?= e($p['descripcion']) ?></textarea></div>
                <div class="campo campo-completo"><label>Imagen (JPG, PNG o WEBP)<?= $p['imagen'] ? ' — actual: '.e($p['imagen']) : '' ?></label>
                    <input class="input-text" type="file" name="imagen" accept=".jpg,.jpeg,.png,.webp"></div>
            </div>
            <div class="flex alinear-derecha"><input class="boton w-sm-100" type="submit" value="Guardar"></div>
        </fieldset>
    </form>
</main>
<?php require '../includes/footer.php'; ?>

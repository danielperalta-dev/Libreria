<?php
$titulo = 'El Rincón del Lector | Librería';
require 'includes/header.php';
$libros = $pdo->query('SELECT * FROM productos ORDER BY id')->fetchAll();
?>

    <section class="hero">
        <div class="contenido-hero">
            <h2>Encuentra tu próximo libro favorito <span>Envíos a todo México</span></h2>
            <div class="ubicacion">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFC107" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z"/>
                    <circle cx="12" cy="11" r="3" />
                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1 -2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z" />
                </svg>
                <p>Guadalajara, Jalisco</p>
            </div>
        </div> <!-- .contenido-hero -->
    </section>

    <main class="contenedor sombra">
        <?php mostrar_msg(); ?>

        <section class="seccion" id="beneficios">
            <h2>¿Por qué comprar con nosotros?</h2>

            <div class="beneficios">
                <div class="beneficio">
                    <div class="iconos">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck-delivery" width="56" height="56" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"/>
                            <circle cx="7" cy="17" r="2" />
                            <circle cx="17" cy="17" r="2" />
                            <path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                            <line x1="3" y1="9" x2="7" y2="9" />
                        </svg>
                    </div>
                    <h3>Envíos a todo México</h3>
                    <p>Envío gratis en compras mayores a $499. Recibe tu pedido en 3 a 5 días hábiles.</p>
                </div>

                <div class="beneficio">
                    <div class="iconos">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-tag" width="56" height="56" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"/>
                            <circle cx="8.5" cy="8.5" r="1" fill="currentColor" />
                            <path d="M4 7v3.859c0 .537 .213 1.052 .593 1.432l8.116 8.116a2.025 2.025 0 0 0 2.864 0l4.834 -4.834a2.025 2.025 0 0 0 0 -2.864l-8.117 -8.116a2.025 2.025 0 0 0 -1.431 -.593h-3.859a3 3 0 0 0 -3 3z" />
                        </svg>
                    </div>
                    <h3>Ofertas cada semana</h3>
                    <p>Descuentos en clásicos, novedades y libros de texto. Revisa el catálogo seguido.</p>
                </div>

                <div class="beneficio">
                    <div class="iconos">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shopping-cart" width="56" height="56" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"/>
                            <circle cx="6" cy="19" r="2" />
                            <circle cx="17" cy="19" r="2" />
                            <path d="M17 17h-11v-14h-2" />
                            <path d="M6 5l14 1l-1 7h-13" />
                        </svg>
                    </div>
                    <h3>Compra fácil</h3>
                    <p>Agrega tus libros al carrito, inicia sesión y paga de forma segura con PayPal.</p>
                </div>
            </div> <!-- .beneficios -->
        </section>

        <section class="seccion" id="catalogo">
            <h2>Libros Destacados</h2>
            <div class="libros">
            <?php foreach ($libros as $l): ?>
                <article class="libro">
                    <?php if ($l['etiqueta']): ?>
                        <span class="etiqueta <?= $l['etiqueta'] === 'Oferta' ? 'etiqueta-oferta' : '' ?>"><?= e($l['etiqueta']) ?></span>
                    <?php endif; ?>
                    <div class="libro-imagen">
                        <?php if ($l['imagen']): ?>
                            <img src="Img/<?= e($l['imagen']) ?>" alt="Portada de <?= e($l['titulo']) ?>" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <div class="libro-info">
                        <h3><?= e($l['titulo']) ?></h3>
                        <p class="libro-autor"><?= e($l['autor']) ?></p>
                        <p class="libro-descripcion"><?= e($l['descripcion']) ?></p>
                        <p class="stock <?= $l['stock'] <= 5 ? 'bajo' : '' ?>"><?= $l['stock'] > 0 ? 'Disponibles: '.(int)$l['stock'] : 'Sin existencias' ?></p>
                        <div class="libro-compra">
                            <p class="precio">$<?= number_format($l['precio'], 0) ?>
                                <?php if ($l['precio_anterior']): ?><del>$<?= number_format($l['precio_anterior'], 0) ?></del><?php endif; ?></p>
                            <?php if ($l['stock'] > 0): ?>
                            <form method="post" action="carrito.php">
                                <input type="hidden" name="accion" value="add">
                                <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
                                <input type="hidden" name="volver" value="catalogo">
                                <button class="boton" type="submit">Comprar</button>
                            </form>
                            <?php else: ?><span class="agotado">Agotado</span><?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
            </div>
        </section>
    </main>

<?php require 'includes/footer.php'; ?>

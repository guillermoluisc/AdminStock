<!-- app/views/productos/index.php -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Listado de Productos</h2>
    <a href="index.php?c=producto&a=crear" class="btn btn-primary">+ Nuevo Producto</a>
</div>

<?php if (empty($productos)): ?>
    <p>No hay productos registrados.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Producto</th>
                <th>Stock</th>
                <th>P. Compra</th>
                <th>P. Venta</th>
                <th>Promo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $prod): ?>
                <?php 
                    $producto_model = new Producto();
                    $nombre = $producto_model->getNombreCompleto($prod);
                ?>
                <tr>
                    <td>
                        <span class="badge badge-<?= $prod['tipo'] ?>">
                            <?= $prod['tipo'] == 'dulce' ? '🍯 Dulce' : '🧀 Salado' ?>
                        </span>
                    </td>
                    <td><strong><?= htmlspecialchars($nombre) ?></strong></td>
                    <td>
                        <span style="<?= $prod['stock'] < 10 ? 'color: red; font-weight: bold;' : '' ?>">
                            <?= $prod['stock'] ?>
                        </span>
                    </td>
                    <td>$<?= number_format($prod['precio_compra'], 2) ?></td>
                    <td><strong>$<?= number_format($prod['precio_venta'], 2) ?></strong></td>
                    <td>
                        <?php if ($prod['precio_promo']): ?>
                            <span style="color: #27ae60; font-weight: bold;">
                                <?= $prod['cantidad_promo'] ?> x $<?= number_format($prod['precio_promo'], 2) ?>
                            </span>
                        <?php else: ?>
                            <span style="color: #95a5a6;">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="index.php?c=producto&a=editar&id=<?= $prod['id'] ?>" class="btn btn-success" style="padding: 5px 10px;">Editar</a>
                        <a href="index.php?c=producto&a=eliminar&id=<?= $prod['id'] ?>" 
                           class="btn btn-danger" 
                           style="padding: 5px 10px;"
                           onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 20px; color: #666;">
        Total de productos: <?= count($productos) ?>
    </div>
<?php endif; ?>
<!-- app/views/productos_padre/index.php -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Productos Padre</h2>
    <a href="index.php?c=productoPadre&a=crear" class="btn btn-primary">+ Nuevo Producto Padre</a>
</div>

<p style="color: #666; margin-bottom: 20px;">
    Los productos padre son las categorías generales que agrupan variedades. Aquí se configuran los descuentos que se aplicarán a todas sus variedades.
</p>

<?php if (empty($productos)): ?>
    <p>No hay productos padre registrados.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Pack x3 Tarjeta</th>
                <th>Pack x3 Efectivo</th>
                <th>Por 1 Tarjeta</th>
                <th>Por 1 Efectivo</th>
                <th>Variedades</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $productoPadreModel = new ProductoPadre();
            foreach ($productos as $prod): 
                $cantidad_variedades = $productoPadreModel->cantidadVariedades($prod['id']);
            ?>
                <tr>
                    <td><strong><?= htmlspecialchars($prod['nombre']) ?></strong></td>
                    <td><?= $prod['porcentaje_pack3_tarjeta'] ?>%</td>
                    <td><?= $prod['porcentaje_pack3_efectivo'] ?>%</td>
                    <td><?= $prod['porcentaje_unidad_tarjeta'] ?>%</td>
                    <td><?= $prod['porcentaje_unidad_efectivo'] ?>%</td>
                    <td>
                        <span class="badge" style="background: #3498db;"><?= $cantidad_variedades ?> variedades</span>
                    </td>
                    <td>
                        <span class="badge badge-activo">Activo</span>
                    </td>
                    <td>
                        <a href="index.php?c=productoPadre&a=editar&id=<?= $prod['id'] ?>" class="btn btn-success" style="padding: 5px 10px;">Editar</a>
                        <a href="index.php?c=productoPadre&a=eliminar&id=<?= $prod['id'] ?>" 
                           class="btn btn-danger" 
                           style="padding: 5px 10px;"
                           onclick="return confirm('¿Eliminar este producto padre?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 20px; color: #666;">
        Total de productos padre: <?= count($productos) ?>
    </div>
<?php endif; ?>
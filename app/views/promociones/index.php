<!-- app/views/promociones/index.php -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Promociones</h2>
    <a href="index.php?c=promocion&a=crear" class="btn btn-primary">+ Nueva Promoción</a>
</div>

<?php if (empty($promociones)): ?>
    <p>No hay promociones registradas.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Variedad</th>
                <th>Cantidad</th>
                <th>Precio Promocional</th>
                <th>Ahorro</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $variedadModel = new Variedad();
            foreach ($promociones as $promo): 
                $variedad = $variedadModel->getById($promo['variedad_id']);
                $precio_normal = $variedad['precio_venta_unitario'] * $promo['cantidad'];
                $ahorro = $precio_normal - $promo['precio_promocional'];
                $ahorro_porcentaje = ($precio_normal > 0) ? (($ahorro / $precio_normal) * 100) : 0;
            ?>
                <tr>
                    <td>
                        <span style="color: #7f8c8d; font-size: 12px;"><?= htmlspecialchars($promo['producto_padre_nombre']) ?></span>
                    </td>
                    <td><strong><?= htmlspecialchars($promo['variedad_nombre']) ?></strong></td>
                    <td>
                        <span class="badge" style="background: #3498db;"><?= $promo['cantidad'] ?> unidades</span>
                    </td>
                    <td>
                        <strong style="color: #27ae60;">$<?= number_format($promo['precio_promocional'], 2) ?></strong>
                        <br>
                        <small style="color: #95a5a6;">Normal: $<?= number_format($precio_normal, 2) ?></small>
                    </td>
                    <td>
                        <span style="color: #27ae60; font-weight: bold;">
                            $<?= number_format($ahorro, 2) ?>
                            <br>
                            <small>(<?= number_format($ahorro_porcentaje, 1) ?>%)</small>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-activo">Activa</span>
                    </td>
                    <td>
                        <a href="index.php?c=promocion&a=editar&id=<?= $promo['id'] ?>" class="btn btn-success" style="padding: 5px 10px;">Editar</a>
                        <a href="index.php?c=promocion&a=eliminar&id=<?= $promo['id'] ?>" 
                           class="btn btn-danger" 
                           style="padding: 5px 10px;"
                           onclick="return confirm('¿Eliminar esta promoción?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 20px; color: #666;">
        Total de promociones: <?= count($promociones) ?>
    </div>
<?php endif; ?>
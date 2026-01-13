<!-- app/views/variedades/index.php -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Listado de Variedades</h2>
    <a href="index.php?c=variedad&a=crear" class="btn btn-primary">+ Nueva Variedad</a>
</div>

<!-- Filtros -->
<div class="filtros">
    <form method="GET" action="index.php">
        <input type="hidden" name="c" value="variedad">
        <input type="hidden" name="a" value="index">
        
        <div class="form-group">
            <label for="stock_estado">Estado de Stock</label>
            <select id="stock_estado" name="stock_estado">
                <option value="">Todos</option>
                <option value="sin_stock" <?= ($filtros['stock_estado'] ?? '') == 'sin_stock' ? 'selected' : '' ?>>Sin Stock</option>
                <option value="bajo" <?= ($filtros['stock_estado'] ?? '') == 'bajo' ? 'selected' : '' ?>>Stock Bajo</option>
                <option value="normal" <?= ($filtros['stock_estado'] ?? '') == 'normal' ? 'selected' : '' ?>>Stock Normal</option>
                <option value="alto" <?= ($filtros['stock_estado'] ?? '') == 'alto' ? 'selected' : '' ?>>Stock Alto</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="producto_padre_id">Producto Padre</label>
            <select id="producto_padre_id" name="producto_padre_id">
                <option value="">Todos</option>
                <?php foreach ($productos_padre as $pp): ?>
                    <option value="<?= $pp['id'] ?>" <?= ($filtros['producto_padre_id'] ?? '') == $pp['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pp['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="index.php?c=variedad&a=index" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>
</div>

<?php if (empty($variedades)): ?>
    <p>No hay variedades registradas.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Producto Padre</th>
                <th>Variedad</th>
                <th>Stock</th>
                <th>P. Costo Unit.</th>
                <th>P. Venta Unit.</th>
                <th>Margen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($variedades as $var): 
                $margen = $var['precio_venta_unitario'] - $var['precio_costo_unitario'];
                $margen_porcentaje = ($var['precio_costo_unitario'] > 0) 
                    ? (($margen / $var['precio_costo_unitario']) * 100) 
                    : 0;
                
                $stock_bajo = $var['stock'] <= $var['stock_minimo'];
            ?>
                <tr>
                    <td>
                        <span style="color: #7f8c8d; font-size: 12px;"><?= htmlspecialchars($var['producto_padre_nombre']) ?></span>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($var['nombre']) ?></strong>
                        <?php if ($var['descripcion']): ?>
                            <br><small style="color: #95a5a6;"><?= htmlspecialchars($var['descripcion']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($stock_bajo): ?>
                            <span class="badge badge-stock-bajo"><?= $var['stock'] ?></span>
                            <small style="display: block; color: #e74c3c;">Mín: <?= $var['stock_minimo'] ?></small>
                        <?php else: ?>
                            <span class="badge badge-stock-ok"><?= $var['stock'] ?></span>
                            <small style="display: block; color: #95a5a6;">Mín: <?= $var['stock_minimo'] ?></small>
                        <?php endif; ?>
                    </td>
                    <td>$<?= number_format($var['precio_costo_unitario'], 2) ?></td>
                    <td><strong>$<?= number_format($var['precio_venta_unitario'], 2) ?></strong></td>
                    <td>
                        <span style="color: <?= $margen > 0 ? '#27ae60' : '#e74c3c' ?>;">
                            $<?= number_format($margen, 2) ?>
                            (<?= number_format($margen_porcentaje, 1) ?>%)
                        </span>
                    </td>
                    <td>
                        <a href="index.php?c=variedad&a=editar&id=<?= $var['id'] ?>" class="btn btn-success" style="padding: 5px 10px;">Editar</a>
                        <a href="index.php?c=variedad&a=eliminar&id=<?= $var['id'] ?>" 
                           class="btn btn-danger" 
                           style="padding: 5px 10px;"
                           onclick="return confirm('¿Eliminar esta variedad?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 20px; color: #666;">
        Total de variedades: <?= count($variedades) ?>
    </div>
<?php endif; ?>
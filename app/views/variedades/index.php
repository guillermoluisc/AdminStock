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
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Producto Padre</th>
                    <th>Variedad</th>
                    <th>Stock</th>
                    <th>Costo Unit.</th>
                    <th>Costo Tot. Compra</th>
                    <th>Pack x3 💳</th>
                    <th>Pack x3 💵</th>
                    <th>x1 💳</th>
                    <th>x1 💵</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <?php
                $total_costo_compra = 0;
                foreach ($variedades as $var) {
                    $total_costo_compra += $var['precio_compra_total'];
                }
            ?>
            <!-- Estadísticas -->
            <div class="grid-3" style="margin-bottom: 20px;">
                <div class="card" style="background: #e8f5e9;">
                    <h4 style="margin-bottom: 10px; color: #27ae60;">💲 Costo Total de las compras</h4>
                    <div style="font-size: 28px; font-weight: bold; color: #27ae60;">
                        <?= FormatHelper::precio($total_costo_compra) ?>
                    </div>
                    <small style="color: #666;"><?= count($variedades) ?? 0 ?> compras realizadas</small>
                </div>
            </div>
            <tbody>
                <?php foreach ($variedades as $var): 
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
                        <td>
                            <small style="color: #95a5a6;">Costo:</small><br>
                            <strong><?= FormatHelper::precio($var['precio_costo_unitario']) ?></strong>
                        </td>
                        <td>
                            <small style="color: #95a5a6;">Costo Tot. Compra:</small><br>
                            <strong><?= FormatHelper::precio($var['precio_compra_total']) ?></strong>
                        </td>
                        <td>
                            <?php if ($var['precio_pack3_tarjeta'] > 0): ?>
                                <strong style="color: #3498db;"><?= FormatHelper::precio($var['precio_pack3_tarjeta']) ?></strong>
                                <br><small style="color: #95a5a6;">c/u</small>
                            <?php else: ?>
                                <span style="color: #95a5a6;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($var['precio_pack3_efectivo'] > 0): ?>
                                <strong style="color: #27ae60;"><?= FormatHelper::precio($var['precio_pack3_efectivo']) ?></strong>
                                <br><small style="color: #95a5a6;">c/u</small>
                            <?php else: ?>
                                <span style="color: #95a5a6;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($var['precio_unidad_tarjeta'] > 0): ?>
                                <strong style="color: #3498db;"><?= FormatHelper::precio($var['precio_unidad_tarjeta']) ?></strong>
                            <?php else: ?>
                                <span style="color: #95a5a6;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($var['precio_unidad_efectivo'] > 0): ?>
                                <strong style="color: #27ae60;"><?= FormatHelper::precio($var['precio_unidad_efectivo']) ?></strong>
                            <?php else: ?>
                                <span style="color: #95a5a6;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?c=variedad&a=editar&id=<?= $var['id'] ?>" class="btn btn-success" style="padding: 5px 10px;">✏️</a>
                            <a href="index.php?c=variedad&a=eliminar&id=<?= $var['id'] ?>" 
                               class="btn btn-danger" 
                               style="padding: 5px 10px;"
                               onclick="return confirm('¿Eliminar esta variedad?')">🗑️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<!-- app/views/ventas/index.php -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Historial de Ventas</h2>
    <a href="index.php?c=venta&a=nueva" class="btn btn-primary">+ Nueva Venta</a>
</div>

<!-- Filtros -->
<div class="filtros">
    <form method="GET" action="index.php">
        <input type="hidden" name="c" value="venta">
        <input type="hidden" name="a" value="index">
        
        <div class="form-group">
            <label for="fecha_desde">Desde</label>
            <input type="date" id="fecha_desde" name="fecha_desde" value="<?= $filtros['fecha_desde'] ?? '' ?>">
        </div>
        
        <div class="form-group">
            <label for="fecha_hasta">Hasta</label>
            <input type="date" id="fecha_hasta" name="fecha_hasta" value="<?= $filtros['fecha_hasta'] ?? '' ?>">
        </div>
        
        <div class="form-group">
            <label for="metodo_pago">Método de Pago</label>
            <select id="metodo_pago" name="metodo_pago">
                <option value="">Todos</option>
                <option value="efectivo" <?= ($filtros['metodo_pago'] ?? '') == 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                <option value="transferencia" <?= ($filtros['metodo_pago'] ?? '') == 'transferencia' ? 'selected' : '' ?>>Transferencia</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="index.php?c=venta&a=index" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>
</div>

<!-- Estadísticas -->
<?php if (!empty($estadisticas)): ?>
<div class="card" style="margin-bottom: 20px;">
    <h3 style="margin-bottom: 15px;">📊 Estadísticas del Período</h3>
    <div class="grid-3">
        <div>
            <strong>Total Ventas:</strong><br>
            <span style="font-size: 24px; color: #27ae60;">$<?= number_format($estadisticas['total_vendido'] ?? 0, 2) ?></span>
        </div>
        <div>
            <strong>Cantidad de Ventas:</strong><br>
            <span style="font-size: 24px;"><?= $estadisticas['total_ventas'] ?? 0 ?></span>
        </div>
        <div>
            <strong>Promedio por Venta:</strong><br>
            <span style="font-size: 24px;">$<?= number_format($estadisticas['promedio_venta'] ?? 0, 2) ?></span>
        </div>
    </div>
    <div class="grid-2" style="margin-top: 15px;">
        <div>
            <strong>💵 Efectivo:</strong>
            <span style="font-size: 20px; color: #27ae60;">$<?= number_format($estadisticas['total_efectivo'] ?? 0, 2) ?></span>
        </div>
        <div>
            <strong>💳 Transferencia:</strong>
            <span style="font-size: 20px; color: #3498db;">$<?= number_format($estadisticas['total_transferencia'] ?? 0, 2) ?></span>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (empty($ventas)): ?>
    <p>No hay ventas registradas con los filtros aplicados.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Descuento</th>
                <th>Método de Pago</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ventas as $venta): ?>
                <tr>
                    <td><strong>#<?= $venta['id'] ?></strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                    <td><strong style="color: #27ae60;">$<?= number_format($venta['total'], 2) ?></strong></td>
                    <td>
                        <?php if ($venta['descuento_aplicado'] > 0): ?>
                            <span style="color: #e74c3c;">-$<?= number_format($venta['descuento_aplicado'], 2) ?></span>
                        <?php else: ?>
                            <span style="color: #95a5a6;">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($venta['metodo_pago'] == 'efectivo'): ?>
                            <span class="badge" style="background: #27ae60;">💵 Efectivo</span>
                        <?php else: ?>
                            <span class="badge" style="background: #3498db;">💳 Transferencia</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="index.php?c=venta&a=detalle&id=<?= $venta['id'] ?>" class="btn btn-primary" style="padding: 5px 10px;">Ver Detalle</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
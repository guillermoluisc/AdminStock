<!-- app/views/ventas/index.php -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Historial de Ventas</h2>
    <a href="index.php?c=venta&a=nueva" class="btn btn-primary">+ Nueva Venta</a>
</div>
<!-- <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Historial de Ventas</h2>
    <div style="display: flex; gap: 10px;">
        <?php
        // Construir URL con filtros actuales
        // $exportUrl = 'index.php?c=venta&a=exportarExcel';
        // if (!empty($filtros['fecha_desde'])) $exportUrl .= '&fecha_desde=' . $filtros['fecha_desde'];
        // if (!empty($filtros['fecha_hasta'])) $exportUrl .= '&fecha_hasta=' . $filtros['fecha_hasta'];
        // if (!empty($filtros['metodo_pago'])) $exportUrl .= '&metodo_pago=' . $filtros['metodo_pago'];
        ?>
        <a href="<?= $exportUrl ?>" class="btn btn-success">📊 Exportar a Excel</a>
        <a href="index.php?c=venta&a=nueva" class="btn btn-primary">+ Nueva Venta</a>
    </div>
</div> -->

<!-- Estadísticas -->
<div class="grid-3" style="margin-bottom: 20px;">
    <div class="card" style="background: #e8f5e9;">
        <h4 style="margin-bottom: 10px; color: #27ae60;">💰 Total Vendido</h4>
        <div style="font-size: 28px; font-weight: bold; color: #27ae60;">
            $<?= number_format($estadisticas['total_vendido'] ?? 0, 2) ?>
        </div>
        <small style="color: #666;"><?= $estadisticas['total_ventas'] ?? 0 ?> ventas realizadas</small>
    </div>
    
    <div class="card" style="background: #fff3cd;">
        <h4 style="margin-bottom: 10px; color: #f39c12;">💵 Efectivo</h4>
        <div style="font-size: 28px; font-weight: bold; color: #f39c12;">
            $<?= number_format($estadisticas['total_efectivo'] ?? 0, 2) ?>
        </div>
        <small style="color: #666;">
            <?php 
            $porcentaje_efectivo = ($estadisticas['total_vendido'] > 0) 
                ? (($estadisticas['total_efectivo'] / $estadisticas['total_vendido']) * 100) 
                : 0;
            echo number_format($porcentaje_efectivo, 1) . '% del total';
            ?>
        </small>
    </div>
    
    <div class="card" style="background: #e3f2fd;">
        <h4 style="margin-bottom: 10px; color: #3498db;">💳 Tarjeta</h4>
        <div style="font-size: 28px; font-weight: bold; color: #3498db;">
            $<?= number_format($estadisticas['total_tarjeta_combinado'] ?? 0, 2) ?>
        </div>
        <small style="color: #666;">
            <?php 
            $porcentaje_tarjeta = ($estadisticas['total_vendido'] > 0) 
                ? (($estadisticas['total_tarjeta_combinado'] / $estadisticas['total_vendido']) * 100) 
                : 0;
            echo number_format($porcentaje_tarjeta, 1) . '% del total';
            ?>
        </small>
    </div>
</div>

<!-- Filtros -->
<div class="filtros">
    <form method="GET" action="index.php">
        <input type="hidden" name="c" value="venta">
        <input type="hidden" name="a" value="index">
        
        <div class="form-group">
            <label for="fecha_desde">Fecha Desde</label>
            <input type="date" id="fecha_desde" name="fecha_desde" value="<?= $filtros['fecha_desde'] ?? '' ?>">
        </div>
        
        <div class="form-group">
            <label for="fecha_hasta">Fecha Hasta</label>
            <input type="date" id="fecha_hasta" name="fecha_hasta" value="<?= $filtros['fecha_hasta'] ?? '' ?>">
        </div>
        
        <div class="form-group">
            <label for="metodo_pago">Método de Pago</label>
            <select id="metodo_pago" name="metodo_pago">
                <option value="">Todos</option>
                <option value="efectivo" <?= ($filtros['metodo_pago'] ?? '') == 'efectivo' ? 'selected' : '' ?>>💵 Efectivo</option>
                <option value="tarjeta" <?= ($filtros['metodo_pago'] ?? '') == 'tarjeta' ? 'selected' : '' ?>>💳 Tarjeta</option>
                <!-- <option value="transferencia" <?= ($filtros['metodo_pago'] ?? '') == 'transferencia' ? 'selected' : '' ?>>🔄 Transferencia</option> -->
            </select>
        </div>
        
        <div class="form-group">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="index.php?c=venta&a=index" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>
</div>

<?php if (empty($ventas)): ?>
    <div class="card">
        <p style="text-align: center; color: #95a5a6; padding: 20px;">No hay ventas registradas.</p>
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Método de Pago</th>
                <th>Total</th>
                <th>Descuento</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ventas as $venta): ?>
                <tr>
                    <td><strong>#<?= $venta['id'] ?></strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                    <td>
                        <?php if ($venta['metodo_pago'] == 'efectivo'): ?>
                            <span class="badge" style="background: #27ae60;">💵 Efectivo</span>
                        <?php elseif ($venta['metodo_pago'] == 'tarjeta'): ?>
                            <span class="badge" style="background: #3498db;">💳 Tarjeta</span>
                        <?php else: ?>
                            <span class="badge" style="background: #9b59b6;">🔄 Transferencia</span>
                        <?php endif; ?>
                    </td>
                    <td><strong style="color: #27ae60; font-size: 16px;">$<?= number_format($venta['total'], 2) ?></strong></td>
                    <td>
                        <?php if ($venta['descuento_aplicado'] > 0): ?>
                            <span style="color: #e74c3c;">-$<?= number_format($venta['descuento_aplicado'], 2) ?></span>
                        <?php else: ?>
                            <span style="color: #95a5a6;">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="index.php?c=venta&a=detalle&id=<?= $venta['id'] ?>" class="btn btn-primary" style="padding: 5px 10px;">Ver Detalle</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background: #ecf0f1; font-weight: bold;">
                <td colspan="3" style="text-align: right;">TOTAL (Filtrado):</td>
                <td style="color: #27ae60; font-size: 18px;">$<?= number_format($total_ventas, 2) ?></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    
    <div style="margin-top: 20px; color: #666;">
        Total de ventas: <?= count($ventas) ?>
        <?php if (!empty($filtros['fecha_desde']) || !empty($filtros['fecha_hasta']) || !empty($filtros['metodo_pago'])): ?>
            (filtrado)
        <?php endif; ?>
    </div>
<?php endif; ?>
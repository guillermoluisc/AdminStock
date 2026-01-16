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
            <?= FormatHelper::precio($estadisticas['total_vendido'] ?? 0) ?>
        </div>
        <small style="color: #666;"><?= $estadisticas['total_ventas'] ?? 0 ?> ventas realizadas</small>
    </div>
    
    <div class="card" style="background: #fff3cd;">
        <h4 style="margin-bottom: 10px; color: #f39c12;">💵 Efectivo</h4>
        <div style="font-size: 28px; font-weight: bold; color: #f39c12;">
            <?= FormatHelper::precio($estadisticas['total_efectivo'] ?? 0) ?>
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
            <?= FormatHelper::precio($estadisticas['total_tarjeta_combinado'] ?? 0) ?>
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
    
    <!-- NUEVO: Total Stock Disponible -->
    <div class="card" style="background: #f3e5f5;">
        <h4 style="margin-bottom: 10px; color: #9c27b0;">📦 Total Stock Disponible</h4>
        <div style="font-size: 28px; font-weight: bold; color: #9c27b0;">
            <?= FormatHelper::precio($total_stock_disponible ?? 0) ?>
        </div>
        <small style="color: #666;">Valor total del inventario</small>
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
            <th>Cliente</th>
            <th>Estado</th>
            <th>Método de Pago</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ventas as $venta): ?>
            <tr style="<?= $venta['estado'] == 'cancelada' ? 'opacity: 0.6;' : '' ?>">
                <td><strong>#<?= $venta['id'] ?></strong></td>
                <td><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                <td>
                    <?php if ($venta['nombre_cliente']): ?>
                        <strong>👤 <?= htmlspecialchars($venta['nombre_cliente']) ?></strong>
                    <?php else: ?>
                        <span style="color: #95a5a6;">Anónimo</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($venta['estado'] == 'completada'): ?>
                        <span class="badge" style="background: #27ae60;">✅ Completada</span>
                    <?php elseif ($venta['estado'] == 'preventa'): ?>
                        <span class="badge" style="background: #f39c12;">⏳ Pre-venta</span>
                    <?php else: ?>
                        <span class="badge" style="background: #e74c3c;">❌ Cancelada</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($venta['metodo_pago'] == 'efectivo'): ?>
                        <span class="badge" style="background: #27ae60;">💵 Efectivo</span>
                    <?php elseif ($venta['metodo_pago'] == 'tarjeta'): ?>
                        <span class="badge" style="background: #3498db;">💳 Tarjeta</span>
                    <?php else: ?>
                        <span class="badge" style="background: #9b59b6;">🔄 Transferencia</span>
                    <?php endif; ?>
                </td>
                <td><strong style="color: #27ae60; font-size: 16px;"><?= FormatHelper::precio($venta['total']) ?></strong></td>
                <td>
                    <a href="index.php?c=venta&a=detalle&id=<?= $venta['id'] ?>" class="btn btn-primary" style="padding: 5px 10px;">Ver Detalle</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
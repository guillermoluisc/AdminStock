<!-- app/views/ventas/index.php -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Historial de Ventas</h2>
    <a href="index.php?c=venta&a=nueva" class="btn btn-primary">+ Nueva Venta</a>
</div>

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
    
    <div class="card" style="background: #f3e5f5;">
        <h4 style="margin-bottom: 10px; color: #9c27b0;">💼 Total en Stock</h4>
        <div style="font-size: 28px; font-weight: bold; color: #9c27b0;">
            <?= FormatHelper::precio($total_stock_disponible ?? 0) ?>
        </div>
        <small style="color: #666;">A precio de costo</small>
    </div>
    

    
    <div class="card" style="background: #ffebee;">
        <h4 style="margin-bottom: 10px; color: #c62828;">💸 Egresos</h4>
        <div style="font-size: 24px; font-weight: bold; color: #c62828;">
            <?= FormatHelper::precio($total_egresos ?? 0) ?>
        </div>
        <small style="color: #666;">
            <a href="index.php?c=egreso&a=index" style="color: #c62828; text-decoration: underline;">Ver detalle</a>
        </small>
    </div>
</div>

<div class="card" style="background: <?= $estadisticas['total_vendido'] - $total_egresos >= 0 ? '#e8f5e9' : '#ffebee' ?>; margin-bottom: 20px; border: 2px solid <?= $estadisticas['total_vendido'] - $total_egresos >= 0 ? '#27ae60' : '#c62828' ?>;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin-bottom: 5px; color: <?= $estadisticas['total_vendido'] - $total_egresos >= 0 ? '#27ae60' : '#c62828' ?>;">
                <?= $estadisticas['total_vendido'] - $total_egresos >= 0 ? '📈' : '📉' ?> Caja total del Período
            </h3>
            <small style="color: #666;">Total Vendido - Egresos</small>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 32px; font-weight: bold; color: <?= $estadisticas['total_vendido'] - $total_egresos >= 0 ? '#27ae60' : '#c62828' ?>;">
                <?= FormatHelper::precio($estadisticas['total_vendido'] - $total_egresos) ?>
            </div>
            <small style="color: #666;">
                <?php if ($estadisticas['total_vendido'] - $total_egresos >= 0): ?>
                    ✅ Resultado positivo
                <?php else: ?>
                    ⚠️ Resultado negativo
                <?php endif; ?>
            </small>
        </div>
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
            <label for="estado">Estado</label>
            <select id="estado" name="estado">
                <option value="">Todos</option>
                <option value="completada" <?= ($filtros['estado'] ?? '') == 'completada' ? 'selected' : '' ?>>
                    ✅ Completada
                </option>
                <option value="preventa" <?= ($filtros['estado'] ?? '') == 'preventa' ? 'selected' : '' ?>>
                    ⏳ Pre-venta
                </option>
                <option value="cancelada" <?= ($filtros['estado'] ?? '') == 'cancelada' ? 'selected' : '' ?>>
                    ❌ Cancelada
                </option>
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

    <!-- Paginación -->
    <?php if (isset($paginacion)): ?>
    <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #ecf0f1;">
        
        <?php if ($paginacion['total_paginas'] > 1): ?>
            <div style="display: flex; justify-content: center; align-items: center; gap: 8px; margin-bottom: 15px; flex-wrap: wrap;">
                <?php
                $pagina_actual = $paginacion['pagina_actual'];
                $total_paginas = $paginacion['total_paginas'];
                
                $url_base = 'index.php?c=venta&a=index';
                if (!empty($filtros['fecha_desde'])) $url_base .= '&fecha_desde=' . $filtros['fecha_desde'];
                if (!empty($filtros['fecha_hasta'])) $url_base .= '&fecha_hasta=' . $filtros['fecha_hasta'];
                if (!empty($filtros['metodo_pago'])) $url_base .= '&metodo_pago=' . $filtros['metodo_pago'];
                if (!empty($filtros['estado'])) $url_base .= '&estado=' . $filtros['estado'];
                if (!empty($_GET['por_pagina'])) $url_base .= '&por_pagina=' . $_GET['por_pagina'];
                ?>
                
                <?php if ($pagina_actual > 1): ?>
                    <a href="<?= $url_base ?>&pagina=1" 
                       style="padding: 10px 15px; background: #9c27b0; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500;">
                        ⮪️ Primera
                    </a>
                    <a href="<?= $url_base ?>&pagina=<?= $pagina_actual - 1 ?>" 
                       style="padding: 10px 15px; background: #7b1fa2; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500;">
                        ◀️ Anterior
                    </a>
                <?php endif; ?>
                
                <span style="padding: 10px 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 6px; font-size: 14px; font-weight: bold;">
                    Página <?= $pagina_actual ?> de <?= $total_paginas ?>
                </span>
                
                <?php if ($pagina_actual < $total_paginas): ?>
                    <a href="<?= $url_base ?>&pagina=<?= $pagina_actual + 1 ?>" 
                       style="padding: 10px 15px; background: #7b1fa2; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500;">
                        Siguiente ▶️
                    </a>
                    <a href="<?= $url_base ?>&pagina=<?= $total_paginas ?>" 
                       style="padding: 10px 15px; background: #9c27b0; color: white; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500;">
                        Última ⭐️
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div style="display: flex; justify-content: center; align-items: center; gap: 15px; flex-wrap: wrap; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 15px; border-radius: 8px;">
            <div style="font-size: 14px; color: #333; font-weight: 500;">
                Mostrando <strong style="color: #9c27b0;"><?= count($ventas) ?></strong> de <strong style="color: #9c27b0;"><?= $paginacion['total'] ?></strong> ventas
            </div>
            
            <form method="GET" action="index.php" style="display: inline-flex; align-items: center; gap: 10px;">
                <input type="hidden" name="c" value="venta">
                <input type="hidden" name="a" value="index">
                <?php if (!empty($filtros['fecha_desde'])): ?>
                    <input type="hidden" name="fecha_desde" value="<?= $filtros['fecha_desde'] ?>">
                <?php endif; ?>
                <?php if (!empty($filtros['fecha_hasta'])): ?>
                    <input type="hidden" name="fecha_hasta" value="<?= $filtros['fecha_hasta'] ?>">
                <?php endif; ?>
                <?php if (!empty($filtros['metodo_pago'])): ?>
                    <input type="hidden" name="metodo_pago" value="<?= $filtros['metodo_pago'] ?>">
                <?php endif; ?>
                <?php if (!empty($filtros['estado'])): ?>
                    <input type="hidden" name="estado" value="<?= $filtros['estado'] ?>">
                <?php endif; ?>
                
                <label for="por_pagina" style="font-size: 14px; color: #333; font-weight: 600;">Mostrar:</label>
                <select name="por_pagina" id="por_pagina" onchange="this.form.submit()" 
                        style="padding: 8px 12px; border: 2px solid #9c27b0; border-radius: 6px; font-size: 14px; cursor: pointer;">
                    <option value="10" <?= ($paginacion['por_pagina'] ?? 10) == 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= ($paginacion['por_pagina'] ?? 10) == 20 ? 'selected' : '' ?>>20</option>
                    <option value="50" <?= ($paginacion['por_pagina'] ?? 10) == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= ($paginacion['por_pagina'] ?? 10) == 100 ? 'selected' : '' ?>>100</option>
                </select>
                <span style="font-size: 14px; color: #333; font-weight: 500;">por página</span>
            </form>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>
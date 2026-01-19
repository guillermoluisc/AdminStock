<!-- app/views/pedidos/index.php -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Gestión de Pedidos</h2>
    <a href="index.php?c=pedido&a=nuevo" class="btn btn-primary">+ Nuevo Pedido</a>
</div>
<!-- Filtros -->
<div class="filtros">
    <form method="GET" action="index.php">
        <input type="hidden" name="c" value="pedido">
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
            <label for="estado">Estado</label>
            <select id="estado" name="estado">
                <option value="">Todos</option>
                <option value="pendiente" <?= ($filtros['estado'] ?? '') == 'pendiente' ? 'selected' : '' ?>>⏳ Pendiente</option>
                <option value="realizado" <?= ($filtros['estado'] ?? '') == 'realizado' ? 'selected' : '' ?>>✅ Realizado</option>
                <option value="faltante" <?= ($filtros['estado'] ?? '') == 'faltante' ? 'selected' : '' ?>>⚠️ Faltante</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="index.php?c=pedido&a=index" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>
</div>
<?php if (empty($pedidos)): ?>
    <p>No hay pedidos registrados.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha Creación</th>
                <th>Cantidad Items</th>
                <th>Total Unidades</th>
                <th>Estado</th>
                <th>Fecha Realización</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><strong>#<?= $pedido['id'] ?></strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($pedido['fecha_creacion'])) ?></td>
                    <td>
                        <span class="badge" style="background: #3498db;"><?= $pedido['cantidad_items'] ?> productos</span>
                    </td>
                    <td>
                        <strong><?= $pedido['total_unidades'] ?></strong> unidades
                    </td>
                    <td>
                        <?php if ($pedido['estado'] == 'pendiente'): ?>
                            <span class="badge badge-pendiente">⏳ Pendiente</span>
                        <?php elseif ($pedido['estado'] == 'realizado'): ?>
                            <span class="badge badge-realizado">✓ Realizado</span>
                        <?php else: ?>
                            <span class="badge badge-faltante">⚠ Faltante</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($pedido['fecha_realizacion']): ?>
                            <?= date('d/m/Y H:i', strtotime($pedido['fecha_realizacion'])) ?>
                        <?php else: ?>
                            <span style="color: #95a5a6;">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="index.php?c=pedido&a=detalle&id=<?= $pedido['id'] ?>" class="btn btn-primary" style="padding: 5px 10px;">Ver Detalle</a>
                        <?php if ($pedido['estado'] == 'pendiente'): ?>
                            <a href="index.php?c=pedido&a=eliminar&id=<?= $pedido['id'] ?>" 
                               class="btn btn-danger" 
                               style="padding: 5px 10px;"
                               onclick="return confirm('¿Eliminar este pedido?')">Eliminar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 20px; color: #666;">
        Total de pedidos: <?= count($pedidos) ?>
    </div>
<?php endif; ?>
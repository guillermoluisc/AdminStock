<!-- app/views/egresos/index.php -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Registro de Egresos</h2>
    <a href="index.php?c=egreso&a=crear" class="btn btn-primary">+ Nuevo Egreso</a>
</div>

<!-- Estadísticas -->
<div class="card" style="background: #ffebee; margin-bottom: 20px;">
    <h4 style="margin-bottom: 10px; color: #c62828;">💸 Total de Egresos</h4>
    <div style="font-size: 28px; font-weight: bold; color: #c62828;">
        <?= FormatHelper::precio($total_egresos) ?>
    </div>
    <small style="color: #666;"><?= count($egresos) ?> egresos registrados</small>
</div>

<!-- Filtros -->
<div class="filtros">
    <form method="GET" action="index.php">
        <input type="hidden" name="c" value="egreso">
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
            <label for="categoria">Categoría</label>
            <select id="categoria" name="categoria">
                <option value="">Todas</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= ($filtros['categoria'] ?? '') == $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="index.php?c=egreso&a=index" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>
</div>

<?php if (empty($egresos)): ?>
    <div class="card">
        <p style="text-align: center; color: #95a5a6; padding: 20px;">No hay egresos registrados.</p>
    </div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Monto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($egresos as $egreso): ?>
                <tr>
                    <td><strong><?= date('d/m/Y', strtotime($egreso['fecha'])) ?></strong></td>
                    <td>
                        <?php if ($egreso['categoria']): ?>
                            <span class="badge" style="background: #9b59b6;">
                                <?= htmlspecialchars($egreso['categoria']) ?>
                            </span>
                        <?php else: ?>
                            <span style="color: #95a5a6;">Sin categoría</span>
                        <?php endif; ?>
                    </td>
                    <td><?= nl2br(htmlspecialchars($egreso['descripcion'])) ?></td>
                    <td><strong style="color: #c62828; font-size: 16px;"><?= FormatHelper::precio($egreso['monto']) ?></strong></td>
                    <td>
                        <a href="index.php?c=egreso&a=editar&id=<?= $egreso['id'] ?>" class="btn btn-success" style="padding: 5px 10px;">✏️ Editar</a>
                        <a href="index.php?c=egreso&a=eliminar&id=<?= $egreso['id'] ?>" 
                           class="btn btn-danger" 
                           style="padding: 5px 10px;"
                           onclick="return confirm('¿Eliminar este egreso?')">🗑️ Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 20px; color: #666;">
        Total de egresos mostrados: <?= count($egresos) ?>
    </div>
<?php endif; ?>
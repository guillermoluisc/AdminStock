<!-- app/views/promociones/editar.php -->
<h2>Editar Promoción</h2>

<div class="card" style="margin-bottom: 20px;">
    <h3><?= htmlspecialchars($promocion['producto_padre_nombre']) ?> - <?= htmlspecialchars($promocion['variedad_nombre']) ?></h3>
</div>

<form method="POST" action="index.php?c=promocion&a=editar&id=<?= $promocion['id'] ?>">
    <div class="grid-2">
        <div class="form-group">
            <label for="cantidad">Cantidad de Unidades *</label>
            <input type="number" id="cantidad" name="cantidad" min="2" required value="<?= $promocion['cantidad'] ?>">
        </div>
        
        <div class="form-group">
            <label for="precio_promocional">Precio Promocional *</label>
            <input type="number" id="precio_promocional" name="precio_promocional" step="0.01" min="0" required value="<?= $promocion['precio_promocional'] ?>">
        </div>
    </div>
    
    <div class="grid-2">
        <div class="form-group">
            <label for="fecha_inicio">Fecha Inicio</label>
            <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" 
                   value="<?= $promocion['fecha_inicio'] ? date('Y-m-d\TH:i', strtotime($promocion['fecha_inicio'])) : '' ?>">
        </div>
        
        <div class="form-group">
            <label for="fecha_fin">Fecha Fin</label>
            <input type="datetime-local" id="fecha_fin" name="fecha_fin"
                   value="<?= $promocion['fecha_fin'] ? date('Y-m-d\TH:i', strtotime($promocion['fecha_fin'])) : '' ?>">
        </div>
    </div>
    
    <div class="form-group checkbox-group">
        <input type="checkbox" id="activo" name="activo" <?= $promocion['activo'] ? 'checked' : '' ?>>
        <label for="activo">Promoción Activa</label>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="index.php?c=promocion&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
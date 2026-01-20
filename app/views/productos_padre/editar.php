<!-- app/views/productos_padre/editar.php -->
<h2>Editar Producto</h2>

<form method="POST" action="index.php?c=productoPadre&a=editar&id=<?= $producto['id'] ?>">
    <div class="form-group">
        <label for="nombre">Nombre del Producto *</label>
        <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($producto['nombre']) ?>">
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 15px;">📦 Precios Pack x3</h3>
        <div class="grid-2">
            <div class="form-group">
                <label for="porcentaje_pack3_tarjeta">💳 Porcentaje Tarjeta (%)</label>
                <input type="number" id="porcentaje_pack3_tarjeta" name="porcentaje_pack3_tarjeta" step="0.01" min="0" value="<?= $producto['porcentaje_pack3_tarjeta'] ?>">
            </div>
            <div class="form-group">
                <label for="porcentaje_pack3_efectivo">💵 Porcentaje Efectivo (%)</label>
                <input type="number" id="porcentaje_pack3_efectivo" name="porcentaje_pack3_efectivo" step="0.01" min="0" value="<?= $producto['porcentaje_pack3_efectivo'] ?>">
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 15px;">🏷️ Precios Por Unidad</h3>
        <div class="grid-2">
            <div class="form-group">
                <label for="porcentaje_unidad_tarjeta">💳 Porcentaje Tarjeta (%)</label>
                <input type="number" id="porcentaje_unidad_tarjeta" name="porcentaje_unidad_tarjeta" step="0.01" min="0" value="<?= $producto['porcentaje_unidad_tarjeta'] ?>">
            </div>
            <div class="form-group">
                <label for="porcentaje_unidad_efectivo">💵 Porcentaje Efectivo (%)</label>
                <input type="number" id="porcentaje_unidad_efectivo" name="porcentaje_unidad_efectivo" step="0.01" min="0" value="<?= $producto['porcentaje_unidad_efectivo'] ?>">
            </div>
        </div>
    </div>
    
    <div class="form-group checkbox-group">
        <input type="checkbox" id="activo" name="activo" <?= $producto['activo'] ? 'checked' : '' ?>>
        <label for="activo">Producto Activo</label>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="index.php?c=productoPadre&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
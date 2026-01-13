<!-- app/views/variedades/editar.php -->
<h2>Editar Variedad</h2>

<div class="card" style="margin-bottom: 20px;">
    <h3><?= htmlspecialchars($variedad['producto_padre_nombre']) ?> - <?= htmlspecialchars($variedad['nombre']) ?></h3>
</div>

<form method="POST" action="index.php?c=variedad&a=editar&id=<?= $variedad['id'] ?>">
    <div class="grid-2">
        <div class="form-group">
            <label for="nombre">Nombre de la Variedad *</label>
            <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($variedad['nombre']) ?>">
        </div>
        
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" id="descripcion" name="descripcion" value="<?= htmlspecialchars($variedad['descripcion']) ?>">
        </div>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 15px;">💰 Datos de Compra</h3>
        <div class="grid-3">
            <div class="form-group">
                <label for="precio_compra_total">Precio Total de Compra *</label>
                <input type="number" id="precio_compra_total" name="precio_compra_total" step="0.01" min="0" required value="<?= $variedad['precio_compra_total'] ?>" onchange="calcularCostoUnitario()">
            </div>
            
            <div class="form-group">
                <label for="cantidad_comprada">Cantidad Comprada *</label>
                <input type="number" id="cantidad_comprada" name="cantidad_comprada" min="1" required value="<?= $variedad['cantidad_comprada'] ?>" onchange="calcularCostoUnitario()">
            </div>
            
            <div class="form-group">
                <label>Costo Unitario (Calculado)</label>
                <input type="text" id="costo_unitario_display" readonly style="background: #ecf0f1; font-weight: bold;" value="$<?= number_format($variedad['precio_costo_unitario'], 2) ?>">
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 15px;">💵 Precio de Venta y Stock</h3>
        <div class="grid-3">
            <div class="form-group">
                <label for="precio_venta_unitario">Precio Venta Unitario *</label>
                <input type="number" id="precio_venta_unitario" name="precio_venta_unitario" step="0.01" min="0" required value="<?= $variedad['precio_venta_unitario'] ?>">
            </div>
            
            <div class="form-group">
                <label for="stock">Stock Actual *</label>
                <input type="number" id="stock" name="stock" min="0" required value="<?= $variedad['stock'] ?>">
            </div>
            
            <div class="form-group">
                <label for="stock_minimo">Stock Mínimo</label>
                <input type="number" id="stock_minimo" name="stock_minimo" min="0" value="<?= $variedad['stock_minimo'] ?>">
            </div>
        </div>
    </div>
    
    <?php if (!empty($promociones)): ?>
    <div class="card">
        <h3 style="margin-bottom: 15px;">🎁 Promociones Activas</h3>
        <?php foreach ($promociones as $promo): ?>
            <div style="padding: 10px; background: white; border-radius: 4px; margin-bottom: 10px;">
                <strong><?= $promo['cantidad'] ?> unidades por $<?= number_format($promo['precio_promocional'], 2) ?></strong>
                <?php if ($promo['activo']): ?>
                    <span class="badge badge-activo">Activa</span>
                <?php else: ?>
                    <span class="badge badge-inactivo">Inactiva</span>
                <?php endif; ?>
                <a href="index.php?c=promocion&a=editar&id=<?= $promo['id'] ?>" class="btn btn-warning" style="padding: 3px 8px; font-size: 12px; float: right;">Editar</a>
            </div>
        <?php endforeach; ?>
        <a href="index.php?c=promocion&a=crear" class="btn btn-success" style="padding: 5px 10px; margin-top: 10px;">+ Agregar Promoción</a>
    </div>
    <?php endif; ?>
    
    <div class="form-group checkbox-group">
        <input type="checkbox" id="activo" name="activo" <?= $variedad['activo'] ? 'checked' : '' ?>>
        <label for="activo">Variedad Activa</label>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="index.php?c=variedad&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<script>
function calcularCostoUnitario() {
    const precioTotal = parseFloat(document.getElementById('precio_compra_total').value) || 0;
    const cantidad = parseInt(document.getElementById('cantidad_comprada').value) || 0;
    
    if (cantidad > 0) {
        const costoUnitario = precioTotal / cantidad;
        document.getElementById('costo_unitario_display').value = '$' + costoUnitario.toFixed(2);
    }
}
</script>
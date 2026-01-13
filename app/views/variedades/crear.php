<!-- app/views/variedades/crear.php -->
<h2>Crear Nueva Variedad</h2>

<form method="POST" action="index.php?c=variedad&a=crear">
    <div class="form-group">
        <label for="producto_padre_id">Producto Padre *</label>
        <select id="producto_padre_id" name="producto_padre_id" required>
            <option value="">Seleccione...</option>
            <?php foreach ($productos_padre as $pp): ?>
                <option value="<?= $pp['id'] ?>"><?= htmlspecialchars($pp['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <small style="color: #666;">Los descuentos se heredan del producto padre</small>
    </div>
    
    <div class="grid-2">
        <div class="form-group">
            <label for="nombre">Nombre de la Variedad *</label>
            <input type="text" id="nombre" name="nombre" required placeholder="Ej: Remera Negra M">
        </div>
        
        <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" id="descripcion" name="descripcion" placeholder="Opcional">
        </div>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 15px;">💰 Datos de Compra</h3>
        <div class="grid-3">
            <div class="form-group">
                <label for="precio_compra_total">Precio Total de Compra *</label>
                <input type="number" id="precio_compra_total" name="precio_compra_total" step="0.01" min="0" required onchange="calcularCostoUnitario()">
                <small style="color: #666;">Precio total pagado al proveedor</small>
            </div>
            
            <div class="form-group">
                <label for="cantidad_comprada">Cantidad Comprada *</label>
                <input type="number" id="cantidad_comprada" name="cantidad_comprada" min="1" required onchange="calcularCostoUnitario()">
                <small style="color: #666;">Unidades que compraste</small>
            </div>
            
            <div class="form-group">
                <label>Costo Unitario (Calculado)</label>
                <input type="text" id="costo_unitario_display" readonly style="background: #ecf0f1; font-weight: bold;" value="$0.00">
                <small style="color: #666;">Se calcula automáticamente</small>
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 15px;">💵 Precio de Venta y Stock</h3>
        <div class="grid-3">
            <div class="form-group">
                <label for="precio_venta_unitario">Precio Venta Unitario *</label>
                <input type="number" id="precio_venta_unitario" name="precio_venta_unitario" step="0.01" min="0" required>
                <small style="color: #666;">Precio al que venderás cada unidad</small>
            </div>
            
            <div class="form-group">
                <label for="stock">Stock Inicial *</label>
                <input type="number" id="stock" name="stock" min="0" required>
                <small style="color: #666;">Por defecto = cantidad comprada</small>
            </div>
            
            <div class="form-group">
                <label for="stock_minimo">Stock Mínimo</label>
                <input type="number" id="stock_minimo" name="stock_minimo" min="0" value="10">
                <small style="color: #666;">Alerta cuando llegue a este nivel</small>
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 15px;">🎁 Configurar Promoción (Opcional)</h3>
        <div class="grid-2">
            <div class="form-group">
                <label for="promo_cantidad">Cantidad en Promo</label>
                <input type="number" id="promo_cantidad" name="promo_cantidad" min="2" placeholder="Ej: 3">
            </div>
            
            <div class="form-group">
                <label for="promo_precio">Precio Promocional</label>
                <input type="number" id="promo_precio" name="promo_precio" step="0.01" min="0" placeholder="Ej: 30000">
            </div>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar Variedad</button>
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
        
        // Auto-completar el stock con la cantidad comprada
        if (!document.getElementById('stock').value) {
            document.getElementById('stock').value = cantidad;
        }
    }
}
</script>
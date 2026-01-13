<!-- app/views/productos/crear.php -->
<h2>Crear Nuevo Producto</h2>

<form method="POST" action="index.php?c=producto&a=crear" id="formProducto">
    <div class="form-group">
        <label for="tipo">Tipo de Producto *</label>
        <select id="tipo" name="tipo" required onchange="cambiarTipo()">
            <option value="">Seleccione...</option>
            <option value="dulce">🍯 Dulce</option>
            <option value="salado">🧀 Salado</option>
        </select>
    </div>
    
    <!-- Campos para DULCES -->
    <div id="campos_dulce" style="display: none;">
        <div class="form-group">
            <label for="tamano_id">Tamaño *</label>
            <select id="tamano_id" name="tamano_id">
                <option value="">Seleccione...</option>
                <?php foreach ($tamanos as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="sabor_id">Sabor *</label>
            <select id="sabor_id" name="sabor_id">
                <option value="">Seleccione...</option>
                <?php foreach ($sabores as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <!-- Campos para SALADOS -->
    <div id="campos_salado" style="display: none;">
        <div class="form-group">
            <label for="variedad_salado_id">Variedad *</label>
            <select id="variedad_salado_id" name="variedad_salado_id">
                <option value="">Seleccione...</option>
                <?php foreach ($variedades_salados as $v): ?>
                    <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['tipo_nombre']) ?> - <?= htmlspecialchars($v['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <label for="stock">Stock Inicial *</label>
        <input type="number" id="stock" name="stock" min="0" value="0" required>
    </div>
    
    <div class="form-group">
        <label for="precio_compra">Precio de Compra</label>
        <input type="number" id="precio_compra" name="precio_compra" step="0.01" min="0" value="0">
    </div>
    
    <div class="form-group">
        <label for="precio_venta">Precio de Venta *</label>
        <input type="number" id="precio_venta" name="precio_venta" step="0.01" min="0" required>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 10px;">Configurar Promoción (Opcional)</h3>
        <div class="grid-2">
            <div class="form-group">
                <label for="cantidad_promo">Cantidad en Promo (ej: 3)</label>
                <input type="number" id="cantidad_promo" name="cantidad_promo" min="1">
            </div>
            
            <div class="form-group">
                <label for="precio_promo">Precio Promo (ej: 10000)</label>
                <input type="number" id="precio_promo" name="precio_promo" step="0.01" min="0">
            </div>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar Producto</button>
        <a href="index.php?c=producto&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<script>
function cambiarTipo() {
    const tipo = document.getElementById('tipo').value;
    const camposDulce = document.getElementById('campos_dulce');
    const camposSalado = document.getElementById('campos_salado');
    
    if (tipo === 'dulce') {
        camposDulce.style.display = 'block';
        camposSalado.style.display = 'none';
        document.getElementById('tamano_id').required = true;
        document.getElementById('sabor_id').required = true;
        document.getElementById('variedad_salado_id').required = false;
    } else if (tipo === 'salado') {
        camposDulce.style.display = 'none';
        camposSalado.style.display = 'block';
        document.getElementById('tamano_id').required = false;
        document.getElementById('sabor_id').required = false;
        document.getElementById('variedad_salado_id').required = true;
    } else {
        camposDulce.style.display = 'none';
        camposSalado.style.display = 'none';
    }
}
</script>
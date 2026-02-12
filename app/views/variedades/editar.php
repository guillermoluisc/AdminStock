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
                <input type="number" id="precio_compra_total" name="precio_compra_total" step="0.01" min="0" required value="<?= $variedad['precio_compra_total'] ?>" onchange="calcularCostoYPrecios()">
            </div>
            
            <div class="form-group">
                <label for="cantidad_comprada">Cantidad Comprada *</label>
                <input type="number" id="cantidad_comprada" name="cantidad_comprada" min="1" required value="<?= $variedad['cantidad_comprada'] ?>" onchange="calcularCostoYPrecios()">
            </div>
            
            <div class="form-group">
                <label>Costo Unitario (Calculado)</label>
                <input type="text" id="costo_unitario_display" readonly style="background: #ecf0f1; font-weight: bold;" value="$<?= number_format($variedad['precio_costo_unitario'], 2) ?>">
            </div>
        </div>
        <div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-radius: 4px;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <label for="unidades_por_pack" style="margin: 0; font-weight: bold;">📦 Unidades por Pack:</label>
        <select id="unidades_por_pack" name="unidades_por_pack" style="padding: 8px;">
            <option value="2" <?= $variedad['unidades_por_pack'] == 2 ? 'selected' : '' ?>>x2</option>
            <option value="3" <?= $variedad['unidades_por_pack'] == 3 ? 'selected' : '' ?>>x3</option>
        </select>
    </div>
</div>
    </div>
    <div class="card" style="background: #e8f5e9;">
        <h3 style="margin-bottom: 15px;">📊 Precios de Venta</h3>
        
        <div class="grid-2">
            <div class="form-group">
                <label for="precio_pack3_tarjeta">💳 Pack x3 Tarjeta (por unidad)</label>
                <input type="number" id="precio_pack3_tarjeta" name="precio_pack3_tarjeta" step="0.01" min="0" value="<?= $variedad['precio_pack3_tarjeta'] ?>" style="font-weight: bold;">
                <small style="color: #666;">Precio por unidad en pack de 3</small>
            </div>
            
            <div class="form-group">
                <label for="precio_pack3_efectivo">💵 Pack x3 Efectivo (por unidad)</label>
                <input type="number" id="precio_pack3_efectivo" name="precio_pack3_efectivo" step="0.01" min="0" value="<?= $variedad['precio_pack3_efectivo'] ?>" style="font-weight: bold;">
                <small style="color: #666;">Precio por unidad en pack de 3</small>
            </div>
        </div>
        
        <div class="grid-2">
            <div class="form-group">
                <label for="precio_unidad_tarjeta">💳 Por 1 Unidad Tarjeta</label>
                <input type="number" id="precio_unidad_tarjeta" name="precio_unidad_tarjeta" step="0.01" min="0" value="<?= $variedad['precio_unidad_tarjeta'] ?>" style="font-weight: bold;">
                <small style="color: #666;">Precio unitario individual</small>
            </div>
            
            <div class="form-group">
                <label for="precio_unidad_efectivo">💵 Por 1 Unidad Efectivo</label>
                <input type="number" id="precio_unidad_efectivo" name="precio_unidad_efectivo" step="0.01" min="0" value="<?= $variedad['precio_unidad_efectivo'] ?>" style="font-weight: bold;">
                <small style="color: #666;">Precio unitario individual</small>
            </div>
        </div>
        
        <button type="button" class="btn btn-info" onclick="recalcularPrecios()" style="margin-top: 10px;">
            🔄 Recalcular Precios Automáticamente
        </button>

        <!-- NUEVO: Botón para igualar precios -->
        <button type="button" class="btn btn-warning" onclick="copiarPreciosPack3AUnidad()" style="margin-top: 10px; margin-left: 10px;">
            📋 Copiar Precios Pack x3 a Por Unidad
        </button>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 15px;">🏷️ Stock</h3>
        <div class="grid-2">
            <!-- <div class="form-group">
                <label for="precio_venta_unitario">Precio Base (Referencia)</label>
                <input type="number" id="precio_venta_unitario" name="precio_venta_unitario" step="0.01" min="0" required value="<?= $variedad['precio_venta_unitario'] ?>">
                <small style="color: #666;">Solo para referencia interna</small>
            </div> -->
            
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
<!-- SECCIÓN REGALO -->
<div class="card" style="background: #fdecea; border: 1px solid #e74c3c; margin-top: 20px;">
    <h3 style="margin-bottom: 15px; color: #c0392b;">🎁 Registrar Regalo</h3>
    <form method="POST" action="index.php?c=variedad&a=regalar&id=<?= $variedad['id'] ?>"
          onsubmit="return confirm('¿Confirmar regalo de ' + document.getElementById('cantidad_regalo').value + ' unidades? Esto descontará stock y registrará un egreso en caja.')">
        <div class="form-group" style="margin-bottom: 15px;">
            <label for="cantidad_regalo">Cantidad a regalar</label>
            <input type="number" id="cantidad_regalo" name="cantidad_regalo"
                   min="1" max="<?= $variedad['stock'] ?>" value="1" required>
            <small style="color: #666;">Stock disponible: <strong><?= $variedad['stock'] ?></strong> unidades</small>
        </div>
        <div style="text-align: center;">
            <button type="submit" class="btn btn-danger">🎁 Registrar Regalo</button>
        </div>
    </form>
</div>
<script>
// Obtener el producto_padre_id desde PHP
const productoPadreId = <?= $variedad['producto_padre_id'] ?>;

function calcularCostoYPrecios() {
    const precioTotal = parseFloat(document.getElementById('precio_compra_total').value) || 0;
    const cantidad = parseInt(document.getElementById('cantidad_comprada').value) || 0;
    
    if (cantidad > 0) {
        const costoUnitario = precioTotal / cantidad;
        document.getElementById('costo_unitario_display').value = '$' + costoUnitario.toFixed(2);
    }
}

function recalcularPrecios() {
    const precioTotal = parseFloat(document.getElementById('precio_compra_total').value) || 0;
    const cantidad = parseInt(document.getElementById('cantidad_comprada').value) || 0;
    
    if (cantidad <= 0 || precioTotal <= 0) {
        alert('Primero ingrese el precio total y la cantidad comprada');
        return;
    }
    
    const costoUnitario = precioTotal / cantidad;
    
    // Hacer petición AJAX para obtener los porcentajes del producto padre
    fetch('index.php?c=variedad&a=calcularPreciosVenta', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'costo_unitario=' + costoUnitario + '&producto_padre_id=' + productoPadreId
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('precio_pack3_tarjeta').value = data.precio_pack3_tarjeta;
        document.getElementById('precio_pack3_efectivo').value = data.precio_pack3_efectivo;
        document.getElementById('precio_unidad_tarjeta').value = data.precio_unidad_tarjeta;
        document.getElementById('precio_unidad_efectivo').value = data.precio_unidad_efectivo;
        
        alert('✅ Precios recalculados correctamente');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al calcular los precios');
    });
}
/**
 * Copia los precios del Pack x3 a los precios Por Unidad
 */
function copiarPreciosPack3AUnidad() {
    const pack3Tarjeta = document.getElementById('precio_pack3_tarjeta').value;
    const pack3Efectivo = document.getElementById('precio_pack3_efectivo').value;
    
    if (!pack3Tarjeta || !pack3Efectivo) {
        alert('Los precios del Pack x3 están vacíos.');
        return;
    }
    
    if (confirm('¿Está seguro de copiar los precios del Pack x3 a Por Unidad?\n\nEsto sobrescribirá los valores actuales de Por 1 Unidad.')) {
        document.getElementById('precio_unidad_tarjeta').value = pack3Tarjeta;
        document.getElementById('precio_unidad_efectivo').value = pack3Efectivo;
        
        alert('✅ Precios copiados correctamente.');
    }
}
</script>
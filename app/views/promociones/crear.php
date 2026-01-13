<!-- app/views/promociones/crear.php -->
<h2>Crear Nueva Promoción</h2>

<form method="POST" action="index.php?c=promocion&a=crear">
    <div class="form-group">
        <label for="variedad_id">Variedad *</label>
        <select id="variedad_id" name="variedad_id" required onchange="mostrarInfoVariedad()">
            <option value="">Seleccione una variedad...</option>
            <?php foreach ($variedades as $var): ?>
                <option value="<?= $var['id'] ?>" 
                        data-precio="<?= $var['precio_venta_unitario'] ?>"
                        data-nombre="<?= htmlspecialchars($var['nombre']) ?>"
                        data-padre="<?= htmlspecialchars($var['producto_padre_nombre']) ?>">
                    <?= htmlspecialchars($var['producto_padre_nombre']) ?> - <?= htmlspecialchars($var['nombre']) ?> 
                    (Precio: $<?= number_format($var['precio_venta_unitario'], 2) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div id="info_variedad" style="display: none;" class="card">
        <h4 style="margin-bottom: 10px;">Información del Producto</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
            <div>
                <strong>Producto Padre:</strong><br>
                <span id="info_padre">-</span>
            </div>
            <div>
                <strong>Variedad:</strong><br>
                <span id="info_nombre">-</span>
            </div>
            <div>
                <strong>Precio Unitario:</strong><br>
                <span id="info_precio" style="color: #27ae60; font-weight: bold;">-</span>
            </div>
        </div>
    </div>
    
    <div class="grid-2">
        <div class="form-group">
            <label for="cantidad">Cantidad de Unidades *</label>
            <input type="number" id="cantidad" name="cantidad" min="2" required onchange="calcularPrecioSugerido()">
            <small style="color: #666;">Cantidad mínima para aplicar la promoción</small>
        </div>
        
        <div class="form-group">
            <label for="precio_promocional">Precio Promocional *</label>
            <input type="number" id="precio_promocional" name="precio_promocional" step="0.01" min="0" required onchange="calcularAhorro()">
            <small style="color: #666;">Precio total de la promoción</small>
        </div>
    </div>
    
    <div id="calculo_promo" style="display: none;" class="card">
        <h4 style="margin-bottom: 10px;">Cálculo de Promoción</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
            <div>
                <strong>Precio Normal:</strong><br>
                <span id="precio_normal" style="font-size: 18px;">$0.00</span>
            </div>
            <div>
                <strong>Precio Promo:</strong><br>
                <span id="precio_promo" style="font-size: 18px; color: #27ae60;">$0.00</span>
            </div>
            <div>
                <strong>Ahorro:</strong><br>
                <span id="ahorro" style="font-size: 18px; color: #e74c3c;">$0.00</span>
                <br>
                <span id="ahorro_porcentaje" style="color: #e74c3c; font-weight: bold;">0%</span>
            </div>
        </div>
    </div>
    
    <div class="grid-2">
        <div class="form-group">
            <label for="fecha_inicio">Fecha Inicio (Opcional)</label>
            <input type="datetime-local" id="fecha_inicio" name="fecha_inicio">
        </div>
        
        <div class="form-group">
            <label for="fecha_fin">Fecha Fin (Opcional)</label>
            <input type="datetime-local" id="fecha_fin" name="fecha_fin">
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Crear Promoción</button>
        <a href="index.php?c=promocion&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<script>
let precioUnitario = 0;

function mostrarInfoVariedad() {
    const select = document.getElementById('variedad_id');
    const option = select.options[select.selectedIndex];
    
    if (option.value) {
        precioUnitario = parseFloat(option.dataset.precio);
        
        document.getElementById('info_padre').textContent = option.dataset.padre;
        document.getElementById('info_nombre').textContent = option.dataset.nombre;
        document.getElementById('info_precio').textContent = '$' + precioUnitario.toFixed(2);
        document.getElementById('info_variedad').style.display = 'block';
        
        calcularPrecioSugerido();
    } else {
        document.getElementById('info_variedad').style.display = 'none';
        document.getElementById('calculo_promo').style.display = 'none';
    }
}

function calcularPrecioSugerido() {
    const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
    
    if (cantidad > 0 && precioUnitario > 0) {
        // Sugerir un 10% de descuento
        const precioNormal = precioUnitario * cantidad;
        const precioSugerido = precioNormal * 0.9;
        
        document.getElementById('precio_promocional').value = precioSugerido.toFixed(2);
        calcularAhorro();
    }
}

function calcularAhorro() {
    const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
    const precioPromo = parseFloat(document.getElementById('precio_promocional').value) || 0;
    
    if (cantidad > 0 && precioPromo > 0 && precioUnitario > 0) {
        const precioNormal = precioUnitario * cantidad;
        const ahorro = precioNormal - precioPromo;
        const ahorroPorcentaje = (ahorro / precioNormal) * 100;
        
        document.getElementById('precio_normal').textContent = '$' + precioNormal.toFixed(2);
        document.getElementById('precio_promo').textContent = '$' + precioPromo.toFixed(2);
        document.getElementById('ahorro').textContent = '$' + ahorro.toFixed(2);
        document.getElementById('ahorro_porcentaje').textContent = ahorroPorcentaje.toFixed(1) + '%';
        
        document.getElementById('calculo_promo').style.display = 'block';
    }
}
</script>
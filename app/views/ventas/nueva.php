<!-- app/views/ventas/nueva.php -->
<h2>Nueva Venta</h2>

<div class="card" style="margin-bottom: 20px;">
    <h3 style="margin-bottom: 15px;">Agregar Productos a la Venta</h3>
    
    <div class="form-group">
        <label for="variedad_select">Seleccionar Producto</label>
        <select id="variedad_select" style="width: 100%; padding: 10px;">
            <option value="">Seleccione un producto...</option>
            <?php foreach ($variedades as $var): ?>
                <option value="<?= $var['id'] ?>" 
                        data-nombre="<?= htmlspecialchars($var['producto_padre_nombre'] . ' - ' . $var['nombre']) ?>"
                        data-stock="<?= $var['stock'] ?>"
                        data-pack3-tarjeta="<?= $var['precio_pack3_tarjeta'] ?>"
                        data-pack3-efectivo="<?= $var['precio_pack3_efectivo'] ?>"
                        data-unidad-tarjeta="<?= $var['precio_unidad_tarjeta'] ?>"
                        data-unidad-efectivo="<?= $var['precio_unidad_efectivo'] ?>">
                    <?= htmlspecialchars($var['producto_padre_nombre']) ?> - <?= htmlspecialchars($var['nombre']) ?> 
                    (Stock: <?= $var['stock'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="grid-3" style="margin-top: 15px;">
        <div class="form-group">
            <label for="cantidad_venta">Cantidad</label>
            <input type="number" id="cantidad_venta" min="1" value="1" style="width: 100%; padding: 8px;">
        </div>
        
        <div class="form-group">
            <label for="tipo_venta">Tipo de Venta</label>
            <select id="tipo_venta" style="width: 100%; padding: 8px;" onchange="actualizarPrecioVenta()">
                <option value="pack3">Pack x3</option>
                <option value="unidad">Por Unidad</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="metodo_pago_item">Método de Pago</label>
            <select id="metodo_pago_item" style="width: 100%; padding: 8px;" onchange="actualizarPrecioVenta()">
                <option value="efectivo">💵 Efectivo</option>
                <option value="tarjeta">💳 Tarjeta</option>
            </select>
        </div>
    </div>
    
    <div id="info_precio" style="display: none; margin-top: 15px; padding: 15px; background: #e8f5e9; border-radius: 4px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
            <div>
                <strong>Precio Unitario:</strong><br>
                <span id="precio_unitario_display" style="color: #27ae60; font-size: 20px; font-weight: bold;">$0.00</span>
            </div>
            <div>
                <strong>Cantidad:</strong><br>
                <span id="cantidad_display" style="font-size: 20px; font-weight: bold;">1</span>
            </div>
            <div>
                <strong>Subtotal:</strong><br>
                <span id="subtotal_display" style="color: #2980b9; font-size: 20px; font-weight: bold;">$0.00</span>
            </div>
        </div>
    </div>
    
    <button type="button" onclick="agregarProducto()" class="btn btn-success" style="margin-top: 15px;">➕ Agregar a la Venta</button>
</div>

<div class="card">
    <h3 style="margin-bottom: 15px;">Productos en la Venta</h3>
    
    <table id="tabla_venta">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Método Pago</th>
                <th>Precio Unit.</th>
                <th>Subtotal</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="items_venta">
            <tr id="sin_productos">
                <td colspan="7" style="text-align: center; color: #95a5a6;">No hay productos agregados a la venta</td>
            </tr>
        </tbody>
        <tfoot id="total_venta" style="display: none;">
            <tr style="background: #ecf0f1; font-weight: bold;">
                <td colspan="5" style="text-align: right;">TOTAL:</td>
                <td id="total_general" style="color: #27ae60; font-size: 18px;">$0.00</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<form method="POST" action="index.php?c=venta&a=nueva" id="form_venta" style="margin-top: 20px;">
    <input type="hidden" name="productos_json" id="productos_json">
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary" onclick="return finalizarVenta()">✅ Finalizar Venta</button>
        <a href="index.php?c=venta&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<script>
let productosVenta = [];

function actualizarPrecioVenta() {
    const select = document.getElementById('variedad_select');
    const option = select.options[select.selectedIndex];
    
    if (!option.value) {
        document.getElementById('info_precio').style.display = 'none';
        return;
    }
    
    const cantidad = parseInt(document.getElementById('cantidad_venta').value) || 1;
    const tipoVenta = document.getElementById('tipo_venta').value;
    const metodoPago = document.getElementById('metodo_pago_item').value;
    
    let precioUnitario = 0;
    
    // Seleccionar el precio según tipo de venta y método de pago
    if (tipoVenta === 'pack3') {
        if (metodoPago === 'efectivo') {
            precioUnitario = parseFloat(option.dataset.pack3Efectivo) || 0;
        } else {
            precioUnitario = parseFloat(option.dataset.pack3Tarjeta) || 0;
        }
        // Forzar cantidad a 3 para pack
        document.getElementById('cantidad_venta').value = 3;
    } else {
        if (metodoPago === 'efectivo') {
            precioUnitario = parseFloat(option.dataset.unidadEfectivo) || 0;
        } else {
            precioUnitario = parseFloat(option.dataset.unidadTarjeta) || 0;
        }
    }
    
    const subtotal = precioUnitario * cantidad;
    
    // Mostrar información
    document.getElementById('precio_unitario_display').textContent = '$' + precioUnitario.toFixed(2);
    document.getElementById('cantidad_display').textContent = cantidad;
    document.getElementById('subtotal_display').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('info_precio').style.display = 'block';
}

// Actualizar precio cuando cambia la selección
document.getElementById('variedad_select').addEventListener('change', actualizarPrecioVenta);
document.getElementById('cantidad_venta').addEventListener('change', actualizarPrecioVenta);

function agregarProducto() {
    const select = document.getElementById('variedad_select');
    const option = select.options[select.selectedIndex];
    
    if (!option.value) {
        alert('Seleccione un producto');
        return;
    }
    
    const variedadId = option.value;
    const nombre = option.dataset.nombre;
    const stock = parseInt(option.dataset.stock);
    const tipoVenta = document.getElementById('tipo_venta').value;
    const metodoPago = document.getElementById('metodo_pago_item').value;
    
    let cantidad = parseInt(document.getElementById('cantidad_venta').value) || 1;
    let precioUnitario = 0;
    
    // Determinar precio según configuración
    if (tipoVenta === 'pack3') {
        cantidad = 3; // Forzar cantidad a 3
        precioUnitario = metodoPago === 'efectivo' 
            ? parseFloat(option.dataset.pack3Efectivo) 
            : parseFloat(option.dataset.pack3Tarjeta);
    } else {
        precioUnitario = metodoPago === 'efectivo' 
            ? parseFloat(option.dataset.unidadEfectivo) 
            : parseFloat(option.dataset.unidadTarjeta);
    }
    
    // Validar stock
    if (cantidad > stock) {
        alert(`Stock insuficiente. Solo hay ${stock} unidades disponibles.`);
        return;
    }
    
    // Validar que tenga precio
    if (precioUnitario <= 0) {
        alert('Este producto no tiene precio configurado para la opción seleccionada.');
        return;
    }
    
    const subtotal = precioUnitario;
    
    // Agregar producto
    productosVenta.push({
        variedad_id: variedadId,
        nombre: nombre,
        cantidad: cantidad,
        tipo_venta: tipoVenta === 'pack3' ? 'Pack x3' : 'Por Unidad',
        metodo_pago: metodoPago,
        precio_unitario: precioUnitario,
        subtotal: subtotal
    });
    
    actualizarTablaVenta();
    
    // Limpiar selección
    select.selectedIndex = 0;
    document.getElementById('cantidad_venta').value = 1;
    document.getElementById('info_precio').style.display = 'none';
}

function eliminarProducto(index) {
    productosVenta.splice(index, 1);
    actualizarTablaVenta();
}

function actualizarTablaVenta() {
    const tbody = document.getElementById('items_venta');
    const sinProductos = document.getElementById('sin_productos');
    const totalFooter = document.getElementById('total_venta');
    
    if (productosVenta.length === 0) {
        sinProductos.style.display = 'table-row';
        totalFooter.style.display = 'none';
        return;
    }
    
    sinProductos.style.display = 'none';
    totalFooter.style.display = 'table-footer-group';
    tbody.innerHTML = '';
    
    let totalGeneral = 0;
    
    productosVenta.forEach((item, index) => {
        totalGeneral += item.subtotal;
        
        const metodoPagoIcon = item.metodo_pago === 'efectivo' ? '💵' : '💳';
        const metodoPagoText = item.metodo_pago === 'efectivo' ? 'Efectivo' : 'Tarjeta';
        
        const row = `
            <tr>
                <td><strong>${item.nombre}</strong></td>
                <td>
                    <span class="badge" style="background: ${item.tipo_venta === 'Pack x3' ? '#3498db' : '#9b59b6'};">
                        ${item.tipo_venta}
                    </span>
                </td>
                <td>${item.cantidad}</td>
                <td>${metodoPagoIcon} ${metodoPagoText}</td>
                <td>$${item.precio_unitario.toFixed(2)}</td>
                <td><strong style="color: #27ae60;">$${item.subtotal.toFixed(2)}</strong></td>
                <td>
                    <button type="button" class="btn btn-danger" style="padding: 5px 10px;" 
                            onclick="eliminarProducto(${index})">Quitar</button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
    
    tbody.innerHTML += sinProductos.outerHTML;
    document.getElementById('total_general').textContent = '$' + totalGeneral.toFixed(2);
}

function finalizarVenta() {
    if (productosVenta.length === 0) {
        alert('Debe agregar al menos un producto a la venta');
        return false;
    }
    
    document.getElementById('productos_json').value = JSON.stringify(productosVenta);
    return true;
}
</script>
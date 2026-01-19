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
                        data-unidades-pack="<?= $var['unidades_por_pack'] ?? 3 ?>"
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
                <option value="pack">Pack (x<span id="pack_cantidad">2</span>)(x<span id="pack_cantidad">3</span>)</option>
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
    <input type="hidden" name="es_preventa" id="es_preventa" value="0">
    
    <!-- NUEVO: Campo opcional para nombre del cliente -->
    <div class="card" style="background: #e3f2fd; margin-bottom: 20px;">
        <h3 style="margin-bottom: 15px;">👤 Información del Cliente (Opcional)</h3>
        <div class="form-group">
            <label for="nombre_cliente">Nombre del Cliente</label>
            <input type="text" id="nombre_cliente" name="nombre_cliente" placeholder="Dejar vacío para venta anónima" style="width: 100%; padding: 10px;">
            <small style="color: #666;">Útil para pre-ventas o ventas a crédito</small>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="button" class="btn btn-primary" onclick="finalizarVenta(false)">
            ✅ Completar Venta
        </button>
        <button type="button" class="btn btn-warning" onclick="finalizarVenta(true)" style="margin-left: 10px;">
            ⏳ Guardar como Pre-venta
        </button>
        <a href="index.php?c=venta&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
    
    <!-- <div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-radius: 4px; border-left: 4px solid #f39c12;">
        <strong>💡 Diferencia entre Venta y Pre-venta:</strong>
        <ul style="margin: 10px 0 0 20px; line-height: 1.8;">
            <li><strong>Venta Completa:</strong> Se registra inmediatamente el ingreso y descuenta stock definitivamente.</li>
            <li><strong>Pre-venta:</strong> Reserva el stock pero no registra el ingreso hasta que se formalice. Útil para apartados o ventas a crédito.</li>
        </ul>
    </div> -->
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
    
    const unidadesPorPack = parseInt(option.dataset.unidadesPack) || 3;
    let cantidad = parseInt(document.getElementById('cantidad_venta').value) || 1;
    const tipoVenta = document.getElementById('tipo_venta').value;
    const metodoPago = document.getElementById('metodo_pago_item').value;
    
    let precioUnitario = 0;
    
    if (tipoVenta === 'pack') {
        // Usar el precio del pack guardado (que ya es el precio total)
        precioUnitario = metodoPago === 'efectivo' 
            ? parseFloat(option.dataset.pack3Efectivo) 
            : parseFloat(option.dataset.pack3Tarjeta);
        
        // Forzar cantidad según las unidades del pack
        cantidad = unidadesPorPack;
        document.getElementById('cantidad_venta').value = cantidad;
    } else {
        precioUnitario = metodoPago === 'efectivo' 
            ? parseFloat(option.dataset.unidadEfectivo) 
            : parseFloat(option.dataset.unidadTarjeta);
    }
    
    const subtotal = precioUnitario * (tipoVenta === 'pack' ? 1 : cantidad);
    
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
    const unidadesPorPack = parseInt(option.dataset.unidadesPack) || 3;
    const tipoVenta = document.getElementById('tipo_venta').value;
    const metodoPago = document.getElementById('metodo_pago_item').value;
    
    let cantidad = parseInt(document.getElementById('cantidad_venta').value) || 1;
    let precioUnitario = 0;
    let tipoVentaTexto = '';
    
    if (tipoVenta === 'pack') {
        cantidad = unidadesPorPack;
        tipoVentaTexto = `Pack x${unidadesPorPack}`;
        precioUnitario = metodoPago === 'efectivo' 
            ? parseFloat(option.dataset.pack3Efectivo) 
            : parseFloat(option.dataset.pack3Tarjeta);
    } else {
        tipoVentaTexto = 'Por Unidad';
        precioUnitario = metodoPago === 'efectivo' 
            ? parseFloat(option.dataset.unidadEfectivo) 
            : parseFloat(option.dataset.unidadTarjeta);
    }
    
    // Validar stock
    if (cantidad > stock) {
        alert(`Stock insuficiente. Solo hay ${stock} unidades disponibles.`);
        return;
    }
    
    if (precioUnitario <= 0) {
        alert('Este producto no tiene precio configurado para la opción seleccionada.');
        return;
    }
    
    // El subtotal es diferente según el tipo
    const subtotal = tipoVenta === 'pack' ? precioUnitario : (precioUnitario * cantidad);
    
    productosVenta.push({
        variedad_id: variedadId,
        nombre: nombre,
        cantidad: cantidad,
        tipo_venta: tipoVentaTexto,
        metodo_pago: metodoPago,
        precio_unitario: precioUnitario,
        subtotal: subtotal
    });
    
    actualizarTablaVenta();
    
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
    const totalFooter = document.getElementById('total_venta');
    
    // Limpiar completamente el tbody
    tbody.innerHTML = '';
    
    if (productosVenta.length === 0) {
        // Si no hay productos, mostrar mensaje
        tbody.innerHTML = '<tr id="sin_productos"><td colspan="7" style="text-align: center; color: #95a5a6;">No hay productos agregados a la venta</td></tr>';
        totalFooter.style.display = 'none';
        return;
    }
    
    // Si hay productos, construir la tabla
    totalFooter.style.display = 'table-footer-group';
    
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
    
    document.getElementById('total_general').textContent = '$' + totalGeneral.toFixed(2);
}

function finalizarVenta(esPreventa) {
    if (productosVenta.length === 0) {
        alert('Debe agregar al menos un producto a la venta');
        return false;
    }
    
    // Si es pre-venta, recomendar agregar nombre del cliente
    if (esPreventa) {
        const nombreCliente = document.getElementById('nombre_cliente').value.trim();
        if (!nombreCliente) {
            if (!confirm('⚠️ No ha ingresado el nombre del cliente.\n\n¿Desea continuar con la pre-venta anónima?')) {
                return false;
            }
        }
        
        if (!confirm('¿Confirma crear esta PRE-VENTA?\n\nSe reservará el stock pero no se registrará el ingreso hasta que se formalice.')) {
            return false;
        }
    }
    
    document.getElementById('productos_json').value = JSON.stringify(productosVenta);
    document.getElementById('es_preventa').value = esPreventa ? '1' : '0';
    document.getElementById('form_venta').submit();
    return true;
}
</script>
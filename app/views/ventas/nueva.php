<!-- app/views/ventas/nueva.php -->
<h2>Nueva Venta</h2>

<div class="card" style="margin-bottom: 20px;">
    <h3 style="margin-bottom: 15px;">Agregar Productos</h3>
    
    <div class="form-group">
        <label for="variedad_select">Seleccionar Producto</label>
        <select id="variedad_select" style="width: 100%; padding: 10px;">
            <option value="">Seleccione un producto...</option>
            <?php foreach ($variedades as $var): 
                if ($var['stock'] > 0):
            ?>
                <option value="<?= $var['id'] ?>" 
                        data-precio="<?= $var['precio_venta_unitario'] ?>"
                        data-stock="<?= $var['stock'] ?>"
                        data-nombre="<?= htmlspecialchars($var['nombre']) ?>"
                        data-padre="<?= htmlspecialchars($var['producto_padre_nombre']) ?>"
                        data-producto-padre-id="<?= $var['producto_padre_id'] ?>">
                    <?= htmlspecialchars($var['producto_padre_nombre']) ?> - <?= htmlspecialchars($var['nombre']) ?> 
                    - Stock: <?= $var['stock'] ?> - $<?= number_format($var['precio_venta_unitario'], 2) ?>
                </option>
            <?php 
                endif;
            endforeach; 
            ?>
        </select>
    </div>
    
    <button type="button" onclick="agregarProducto()" class="btn btn-success">➕ Agregar a la Venta</button>
</div>

<div class="card">
    <h3 style="margin-bottom: 15px;">Productos en la Venta</h3>
    
    <table id="tabla_venta">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Descuento</th>
                <th>Subtotal</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="items_venta">
            <tr id="sin_productos">
                <td colspan="6" style="text-align: center; color: #95a5a6;">No hay productos agregados</td>
            </tr>
        </tbody>
        <tfoot>
            <tr style="background: #ecf0f1; font-weight: bold;">
                <td colspan="4" style="text-align: right;">TOTAL:</td>
                <td colspan="2" style="color: #27ae60; font-size: 18px;">$<span id="total_venta">0.00</span></td>
            </tr>
        </tfoot>
    </table>
</div>

<form method="POST" action="index.php?c=venta&a=nueva" id="form_venta" style="margin-top: 20px;">
    <input type="hidden" name="productos_json" id="productos_json">
    
    <div class="form-group">
        <label for="metodo_pago">Método de Pago *</label>
        <select id="metodo_pago" name="metodo_pago" required style="width: 100%; padding: 10px;" onchange="recalcularDescuentos()">
            <option value="">Seleccione...</option>
            <option value="efectivo">💵 Efectivo</option>
            <option value="transferencia">💳 Transferencia</option>
        </select>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary" onclick="return validarVenta()">✓ Registrar Venta</button>
        <a href="index.php?c=venta&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<script>
let productosVenta = [];

function agregarProducto() {
    const select = document.getElementById('variedad_select');
    const option = select.options[select.selectedIndex];
    
    if (!option.value) {
        alert('Seleccione un producto');
        return;
    }
    
    const variedadId = option.value;
    const nombre = option.dataset.padre + ' - ' + option.dataset.nombre;
    const precio = parseFloat(option.dataset.precio);
    const stock = parseInt(option.dataset.stock);
    const productoPadreId = option.dataset.productoPadreId;
    
    // Verificar si ya está agregado
    const existe = productosVenta.find(p => p.variedad_id == variedadId);
    if (existe) {
        if (existe.cantidad < stock) {
            existe.cantidad++;
            actualizarTabla();
        } else {
            alert('No hay más stock disponible');
        }
        return;
    }
    
    // Agregar nuevo producto
    productosVenta.push({
        variedad_id: variedadId,
        nombre: nombre,
        cantidad: 1,
        precio_unitario: precio,
        stock: stock,
        producto_padre_id: productoPadreId
    });
    
    actualizarTabla();
    select.selectedIndex = 0;
}

function eliminarProducto(index) {
    productosVenta.splice(index, 1);
    actualizarTabla();
}

function cambiarCantidad(index, cantidad) {
    const producto = productosVenta[index];
    const nuevaCantidad = parseInt(cantidad);
    
    if (nuevaCantidad < 1) {
        alert('La cantidad debe ser al menos 1');
        return;
    }
    
    if (nuevaCantidad > producto.stock) {
        alert('No hay suficiente stock. Disponible: ' + producto.stock);
        return;
    }
    
    producto.cantidad = nuevaCantidad;
    actualizarTabla();
}

function recalcularDescuentos() {
    actualizarTabla();
}

function actualizarTabla() {
    const tbody = document.getElementById('items_venta');
    const sinProductos = document.getElementById('sin_productos');
    const metodoPago = document.getElementById('metodo_pago').value;
    
    if (productosVenta.length === 0) {
        sinProductos.style.display = 'table-row';
        document.getElementById('total_venta').textContent = '0.00';
        return;
    }
    
    sinProductos.style.display = 'none';
    tbody.innerHTML = '';
    let total = 0;
    
    productosVenta.forEach((item, index) => {
        // Calcular descuento basado en cantidad y método de pago
        let descuentoUnitario = 0;
        
        if (metodoPago) {
            // Aquí podrías hacer una llamada AJAX al servidor para obtener los descuentos
            // Por simplicidad, lo calculamos en el cliente
            descuentoUnitario = 0; // Se calculará en el servidor al guardar
        }
        
        const subtotal = (item.precio_unitario * item.cantidad) - (descuentoUnitario * item.cantidad);
        total += subtotal;
        
        const row = `
            <tr>
                <td>${item.nombre}</td>
                <td>
                    <input type="number" value="${item.cantidad}" min="1" max="${item.stock}" 
                           style="width: 80px; padding: 5px;" 
                           onchange="cambiarCantidad(${index}, this.value)">
                    <small style="display: block; color: #95a5a6;">Stock: ${item.stock}</small>
                </td>
                <td>$${item.precio_unitario.toFixed(2)}</td>
                <td>
                    ${descuentoUnitario > 0 ? '-$' + (descuentoUnitario * item.cantidad).toFixed(2) : '-'}
                </td>
                <td><strong>$${subtotal.toFixed(2)}</strong></td>
                <td>
                    <button type="button" class="btn btn-danger" style="padding: 5px 10px;" 
                            onclick="eliminarProducto(${index})">Quitar</button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
    
    tbody.innerHTML += sinProductos.outerHTML;
    document.getElementById('total_venta').textContent = total.toFixed(2);
}

function validarVenta() {
    if (productosVenta.length === 0) {
        alert('Debe agregar al menos un producto a la venta');
        return false;
    }
    
    if (!document.getElementById('metodo_pago').value) {
        alert('Debe seleccionar un método de pago');
        return false;
    }
    
    document.getElementById('productos_json').value = JSON.stringify(productosVenta);
    return true;
}
</script>
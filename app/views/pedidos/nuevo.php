<!-- app/views/pedidos/nuevo.php -->
<h2>Nuevo Pedido</h2>

<?php if (!empty($variedades_stock_bajo)): ?>
<div class="card" style="margin-bottom: 20px; background: #fff3cd; border: 1px solid #ffc107;">
    <h3 style="margin-bottom: 15px; color: #856404;">⚠️ Productos con Stock Bajo</h3>
    <p style="margin-bottom: 10px;">Los siguientes productos tienen stock por debajo del mínimo:</p>
    <div style="display: grid; gap: 10px;">
        <?php foreach ($variedades_stock_bajo as $var): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: white; border-radius: 4px;">
                <div>
                    <strong><?= htmlspecialchars($var['producto_padre_nombre']) ?> - <?= htmlspecialchars($var['nombre']) ?></strong><br>
                    <small style="color: #666;">Stock actual: <span style="color: #e74c3c; font-weight: bold;"><?= $var['stock'] ?></span> | Mínimo: <?= $var['stock_minimo'] ?></small>
                </div>
                <button type="button" class="btn btn-warning" style="padding: 5px 10px;" onclick="agregarProductoRapido(<?= $var['id'] ?>, '<?= htmlspecialchars($var['producto_padre_nombre'] . ' - ' . $var['nombre']) ?>', <?= $var['stock_minimo'] - $var['stock'] + 10 ?>)">
                    + Agregar al Pedido
                </button>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="card" style="margin-bottom: 20px;">
    <h3 style="margin-bottom: 15px;">Agregar Productos al Pedido</h3>
    
    <div class="form-group">
        <label for="variedad_select">Seleccionar Producto</label>
        <select id="variedad_select" style="width: 100%; padding: 10px;">
            <option value="">Seleccione un producto...</option>
            <?php foreach ($variedades as $var): 
                $stock_bajo = $var['stock'] <= $var['stock_minimo'];
            ?>
                <option value="<?= $var['id'] ?>" 
                        data-nombre="<?= htmlspecialchars($var['producto_padre_nombre'] . ' - ' . $var['nombre']) ?>"
                        data-stock="<?= $var['stock'] ?>"
                        data-minimo="<?= $var['stock_minimo'] ?>"
                        data-stock-bajo="<?= $stock_bajo ? 'true' : 'false' ?>">
                    <?= $stock_bajo ? '⚠️ ' : '' ?>
                    <?= htmlspecialchars($var['producto_padre_nombre']) ?> - <?= htmlspecialchars($var['nombre']) ?> 
                    (Stock: <?= $var['stock'] ?> / Mín: <?= $var['stock_minimo'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <button type="button" onclick="agregarProducto()" class="btn btn-success">➕ Agregar al Pedido</button>
</div>

<div class="card">
    <h3 style="margin-bottom: 15px;">Productos en el Pedido</h3>
    
    <table id="tabla_pedido">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Stock Actual</th>
                <th>Cantidad a Pedir</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="items_pedido">
            <tr id="sin_productos">
                <td colspan="5" style="text-align: center; color: #95a5a6;">No hay productos agregados al pedido</td>
            </tr>
        </tbody>
    </table>
</div>

<form method="POST" action="index.php?c=pedido&a=nuevo" id="form_pedido" style="margin-top: 20px;">
    <input type="hidden" name="productos_json" id="productos_json">
    
    <div class="form-group">
        <label for="observaciones">Observaciones del Pedido</label>
        <textarea id="observaciones" name="observaciones" rows="4" placeholder="Notas adicionales sobre el pedido..."></textarea>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary" onclick="return validarPedido()">✓ Crear Pedido</button>
        <a href="index.php?c=pedido&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<script>
let productosPedido = [];

function agregarProductoRapido(variedadId, nombre, cantidadSugerida) {
    // Verificar si ya está agregado
    const existe = productosPedido.find(p => p.variedad_id == variedadId);
    if (existe) {
        existe.cantidad = cantidadSugerida;
    } else {
        productosPedido.push({
            variedad_id: variedadId,
            nombre: nombre,
            cantidad: cantidadSugerida,
            observaciones: 'Stock bajo - Pedido automático'
        });
    }
    
    actualizarTabla();
}

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
    const minimo = parseInt(option.dataset.minimo);
    const stockBajo = option.dataset.stockBajo === 'true';
    
    // Verificar si ya está agregado
    const existe = productosPedido.find(p => p.variedad_id == variedadId);
    if (existe) {
        alert('Este producto ya está en el pedido');
        return;
    }
    
    // Calcular cantidad sugerida
    let cantidadSugerida = 10;
    if (stockBajo) {
        cantidadSugerida = minimo - stock + 10;
    }
    
    // Agregar nuevo producto
    productosPedido.push({
        variedad_id: variedadId,
        nombre: nombre,
        cantidad: cantidadSugerida,
        stock: stock,
        minimo: minimo,
        observaciones: ''
    });
    
    actualizarTabla();
    select.selectedIndex = 0;
}

function eliminarProducto(index) {
    productosPedido.splice(index, 1);
    actualizarTabla();
}

function cambiarCantidad(index, cantidad) {
    const nuevaCantidad = parseInt(cantidad);
    
    if (nuevaCantidad < 1) {
        alert('La cantidad debe ser al menos 1');
        return;
    }
    
    productosPedido[index].cantidad = nuevaCantidad;
    actualizarTabla();
}

function cambiarObservaciones(index, observaciones) {
    productosPedido[index].observaciones = observaciones;
}

function actualizarTabla() {
    const tbody = document.getElementById('items_pedido');
    
    // Limpiar completamente el tbody
    tbody.innerHTML = '';
    
    if (productosPedido.length === 0) {
        tbody.innerHTML = '<tr id="sin_productos"><td colspan="5" style="text-align: center; color: #95a5a6;">No hay productos agregados al pedido</td></tr>';
        return;
    }
    
    productosPedido.forEach((item, index) => {
        const stockBajo = item.stock <= item.minimo;
        const row = `<tr>
            <td>
                ${stockBajo ? '<span style="color: #e74c3c;">⚠️</span> ' : ''}
                <strong>${item.nombre}</strong>
            </td>
            <td>
                <span style="color: ${stockBajo ? '#e74c3c' : '#27ae60'}; font-weight: bold;">
                    ${item.stock || 0}
                </span>
                <small style="display: block; color: #95a5a6;">Mínimo: ${item.minimo || 10}</small>
            </td>
            <td>
                <input type="number" value="${item.cantidad}" min="1" 
                       style="width: 100px; padding: 5px;" 
                       onchange="cambiarCantidad(${index}, this.value)">
            </td>
            <td>
                <input type="text" value="${item.observaciones || ''}" 
                       style="width: 200px; padding: 5px;" 
                       placeholder="Notas..." 
                       onchange="cambiarObservaciones(${index}, this.value)">
            </td>
            <td>
                <button type="button" class="btn btn-danger" 
                        style="padding: 5px 10px;" 
                        onclick="eliminarProducto(${index})">Quitar</button>
            </td>
        </tr>`;
        tbody.innerHTML += row;
    });
}

function validarPedido() {
    if (productosPedido.length === 0) {
        alert('Debe agregar al menos un producto al pedido');
        return false;
    }
    
    document.getElementById('productos_json').value = JSON.stringify(productosPedido);
    return true;
}
</script>
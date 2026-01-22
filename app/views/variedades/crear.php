<!-- app/views/variedades/crear.php -->
<h2>Crear Nueva Variedad</h2>

<form method="POST" action="index.php?c=variedad&a=crear">
    <div class="form-group">
        <label for="producto_padre_id">Producto *</label>
        <select id="producto_padre_id" name="producto_padre_id" required onchange="cargarPorcentajes()">
            <option value="">Seleccione...</option>
            <?php foreach ($productos_padre as $pp): ?>
                <option value="<?= $pp['id'] ?>"
                        data-pack3-tarjeta="<?= $pp['porcentaje_pack3_tarjeta'] ?>"
                        data-pack3-efectivo="<?= $pp['porcentaje_pack3_efectivo'] ?>"
                        data-unidad-tarjeta="<?= $pp['porcentaje_unidad_tarjeta'] ?>"
                        data-unidad-efectivo="<?= $pp['porcentaje_unidad_efectivo'] ?>">
                    <?= htmlspecialchars($pp['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small style="color: #666;">Los precios se calcularán según los porcentajes del producto padre</small>
    </div>
    
    <div class="grid-2">
        <div class="form-group">
            <label for="nombre">Nombre de la Variedad *</label>
            <input type="text" id="nombre" name="nombre" required placeholder="Ej: Colaless VINTAGE Negro M">
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
                <input type="number" id="precio_compra_total" name="precio_compra_total" step="0.01" min="0" required onchange="calcularCostoYPrecios()">
                <small style="color: #666;">Precio total pagado al proveedor</small>
            </div>
            
            <div class="form-group">
                <label for="cantidad_comprada">Cantidad Comprada *</label>
                <input type="number" id="cantidad_comprada" name="cantidad_comprada" min="1" required onchange="calcularCostoYPrecios()">
                <small style="color: #666;">Unidades que compraste</small>
            </div>
            
            <div class="form-group">
                <label>Costo Unitario (Calculado)</label>
                <input type="text" id="costo_unitario_display" readonly style="background: #ecf0f1; font-weight: bold;" value="$ 0.00">
                <small style="color: #666;">Se calcula automáticamente</small>
            </div>
        </div>
        
        <!-- SELECTOR DE UNIDADES POR PACK -->
        <div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-radius: 4px; border-left: 4px solid #f39c12;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <label for="unidades_por_pack" style="margin: 0; font-weight: bold;">📦 Unidades por Pack:</label>
                <select id="unidades_por_pack" name="unidades_por_pack" onchange="calcularCostoYPrecios()" style="padding: 8px; font-size: 16px; border-radius: 4px; border: 2px solid #f39c12;">
                    <option value="1">x1 (Pack de 1 unidades)</option>
                    <option value="2">x2 (Pack de 2 unidades)</option>
                    <option value="3" selected>x3 (Pack de 3 unidades)</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="card" style="background: #e8f5e9;">
        <h3 style="margin-bottom: 15px;">📊 Precios de Venta Calculados Automáticamente</h3>
        <p style="color: #666; margin-bottom: 15px;">
            Los precios se calculan según los porcentajes configurados en el Producto Base
        </p>
        
        <div class="grid-2">
            <div class="form-group">
                <label for="precio_pack3_tarjeta">💳 <span id="label_pack_tarjeta">Pack x3</span> Tarjeta (precio total)</label>
                <input type="text" id="precio_pack3_tarjeta_display" readonly style="background: #f0f0f0; font-weight: bold; font-size: 18px; color: #27ae60;">
                <input type="hidden" id="precio_pack3_tarjeta" name="precio_pack3_tarjeta">
                <small id="info_pack3_tarjeta" style="color: #666;">-</small>
            </div>
            
            <div class="form-group">
                <label for="precio_pack3_efectivo">💵 <span id="label_pack_efectivo">Pack x3</span> Efectivo (precio total)</label>
                <input type="text" id="precio_pack3_efectivo_display" readonly style="background: #f0f0f0; font-weight: bold; font-size: 18px; color: #27ae60;">
                <input type="hidden" id="precio_pack3_efectivo" name="precio_pack3_efectivo">
                <small id="info_pack3_efectivo" style="color: #666;">-</small>
            </div>
        </div>
        
        <div class="grid-2" style="margin-top: 15px;">
            <div class="form-group">
                <label for="precio_unidad_tarjeta">💳 Por 1 Unidad Tarjeta</label>
                <input type="text" id="precio_unidad_tarjeta_display" readonly style="background: #f0f0f0; font-weight: bold; font-size: 18px; color: #3498db;">
                <input type="hidden" id="precio_unidad_tarjeta" name="precio_unidad_tarjeta">
                <small id="info_unidad_tarjeta" style="color: #666;">-</small>
            </div>
            
            <div class="form-group">
                <label for="precio_unidad_efectivo">💵 Por 1 Unidad Efectivo</label>
                <input type="text" id="precio_unidad_efectivo_display" readonly style="background: #f0f0f0; font-weight: bold; font-size: 18px; color: #3498db;">
                <input type="hidden" id="precio_unidad_efectivo" name="precio_unidad_efectivo">
                <small id="info_unidad_efectivo" style="color: #666;">-</small>
            </div>
        </div>
        
        <!-- BOTÓN para igualar precios -->
        <div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-radius: 4px; border-left: 4px solid #f39c12;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">
                        Con este Botón Igualas los precios automaticamente para productos que se venden por unidad
                    </p>
                </div>
                <button type="button" class="btn btn-warning" onclick="igualarPrecios()">
                    📋 Igualar Precios
                </button>
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 15px;">🏷️ Stock</h3>
        <div class="grid-2">
            <div class="form-group">
                <label for="stock">Stock Inicial *</label>
                <input type="number" id="stock" name="stock" min="0" required>
                <small id="stock_help" style="color: #666;">Por defecto = cantidad comprada × 3</small>
            </div>
            
            <div class="form-group">
                <label for="stock_minimo">Stock Mínimo</label>
                <input type="number" id="stock_minimo" name="stock_minimo" min="0" value="3">
                <small style="color: #666;">Alerta cuando llegue a este nivel</small>
            </div>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar Variedad</button>
        <a href="index.php?c=variedad&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<script>
let porcentajes = {
    pack3_tarjeta: 0,
    pack3_efectivo: 0,
    unidad_tarjeta: 0,
    unidad_efectivo: 0
};

function formatearPrecio(numero) {
    return '$ ' + numero.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function getUnidadesPorPack() {
    return parseInt(document.getElementById('unidades_por_pack').value) || 3;
}

function actualizarLabels() {
    const unidades = getUnidadesPorPack();
    document.getElementById('label_pack_tarjeta').textContent = `Pack x${unidades}`;
    document.getElementById('label_pack_efectivo').textContent = `Pack x${unidades}`;
    document.getElementById('stock_help').textContent = `Por defecto = cantidad comprada × ${unidades}`;
}

function cargarPorcentajes() {
    const select = document.getElementById('producto_padre_id');
    const option = select.options[select.selectedIndex];
    
    if (option.value) {
        porcentajes.pack3_tarjeta = parseFloat(option.dataset.pack3Tarjeta) || 0;
        porcentajes.pack3_efectivo = parseFloat(option.dataset.pack3Efectivo) || 0;
        porcentajes.unidad_tarjeta = parseFloat(option.dataset.unidadTarjeta) || 0;
        porcentajes.unidad_efectivo = parseFloat(option.dataset.unidadEfectivo) || 0;
        
        calcularCostoYPrecios();
    }
}

function calcularCostoYPrecios() {
    const precioTotal = parseFloat(document.getElementById('precio_compra_total').value) || 0;
    const cantidad = parseInt(document.getElementById('cantidad_comprada').value) || 0;
    const unidadesPorPack = getUnidadesPorPack();
    
    // Actualizar labels
    actualizarLabels();
    
    if (cantidad <= 0 || precioTotal <= 0) {
        return;
    }

    // 1. COSTO UNITARIO
    const costoUnitario = precioTotal / cantidad;
    document.getElementById('costo_unitario_display').value = formatearPrecio(costoUnitario);

    // Auto-completar stock (cantidad × unidades por pack)
    if (!document.getElementById('stock').value) {
        document.getElementById('stock').value = cantidad * unidadesPorPack;
    }

    // 2. CALCULAR PRECIOS
    calcularPreciosVenta(precioTotal, costoUnitario, unidadesPorPack);
}

function calcularPreciosVenta(precioCompraTotal, costoUnitario, unidadesPorPack) {
    // PRECIOS PACK (sobre el costo TOTAL de la caja/pack)
    const pack3Tarjeta = costoUnitario + (costoUnitario * porcentajes.pack3_tarjeta / 100);
    const pack3Efectivo = costoUnitario + (costoUnitario * porcentajes.pack3_efectivo / 100);

    // PRECIOS POR UNIDAD (sobre el costo UNITARIO, dividido por unidades del pack)
    const unidadTarjeta = (costoUnitario) + (costoUnitario * porcentajes.unidad_tarjeta / 100);
    const unidadEfectivo = costoUnitario + (costoUnitario * porcentajes.unidad_efectivo / 100);

    // Actualizar campos OCULTOS (valores reales para enviar)
    document.getElementById('precio_pack3_tarjeta').value = pack3Tarjeta.toFixed(2);
    document.getElementById('precio_pack3_efectivo').value = pack3Efectivo.toFixed(2);
    document.getElementById('precio_unidad_tarjeta').value = (unidadTarjeta / unidadesPorPack).toFixed(2);
    document.getElementById('precio_unidad_efectivo').value = (unidadEfectivo / unidadesPorPack).toFixed(2);

    // Actualizar campos VISIBLES (con formato)
    document.getElementById('precio_pack3_tarjeta_display').value = formatearPrecio(pack3Tarjeta);
    document.getElementById('precio_pack3_efectivo_display').value = formatearPrecio(pack3Efectivo);
    document.getElementById('precio_unidad_tarjeta_display').value = formatearPrecio(unidadTarjeta / unidadesPorPack);
    document.getElementById('precio_unidad_efectivo_display').value = formatearPrecio(unidadEfectivo / unidadesPorPack);

    // Actualizar textos informativos
    document.getElementById('info_pack3_tarjeta').textContent = 
        `${precioCompraTotal.toFixed(2)} + (${precioCompraTotal.toFixed(2)} × ${porcentajes.pack3_tarjeta}%) | Por unidad: ${formatearPrecio(pack3Tarjeta/unidadesPorPack)}`;
    
    document.getElementById('info_pack3_efectivo').textContent = 
        `${precioCompraTotal.toFixed(2)} + (${precioCompraTotal.toFixed(2)} × ${porcentajes.pack3_efectivo}%) | Por unidad: ${formatearPrecio(pack3Efectivo/unidadesPorPack)}`;
    
    document.getElementById('info_unidad_tarjeta').textContent = 
        `${costoUnitario.toFixed(2)} + (${costoUnitario.toFixed(2)} × ${porcentajes.unidad_tarjeta}%)`;
    
    document.getElementById('info_unidad_efectivo').textContent = 
        `${costoUnitario.toFixed(2)} + (${costoUnitario.toFixed(2)} × ${porcentajes.unidad_efectivo}%)`;
}

function igualarPrecios() {
    const packTarjeta = parseFloat(document.getElementById('precio_pack3_tarjeta').value) || 0;
    const packEfectivo = parseFloat(document.getElementById('precio_pack3_efectivo').value) || 0;
    
    if (packTarjeta === 0 || packEfectivo === 0) {
        alert('⚠️ Primero debe calcular los precios del pack.');
        return;
    }
    
    if (!confirm(
        '¿Confirma que este producto se vende SOLO por unidad?\n\n' +
        'El precio del PACK se tomará como precio por UNIDAD.'
    )) {
        return;
    }

    // Actualizar valores ocultos
    document.getElementById('precio_unidad_tarjeta').value = packTarjeta.toFixed(2);
    document.getElementById('precio_unidad_efectivo').value = packEfectivo.toFixed(2);

    // Actualizar valores visibles con formato
    document.getElementById('precio_unidad_tarjeta_display').value = formatearPrecio(packTarjeta);
    document.getElementById('precio_unidad_efectivo_display').value = formatearPrecio(packEfectivo);

    // Estilo visual
    document.getElementById('precio_unidad_tarjeta_display').style.background = '#fff3cd';
    document.getElementById('precio_unidad_efectivo_display').style.background = '#fff3cd';

    // Info
    document.getElementById('info_unidad_tarjeta').textContent = '⚠️ Igualado al precio del pack';
    document.getElementById('info_unidad_efectivo').textContent = '⚠️ Igualado al precio del pack';

    alert(
        '✅ Precios ajustados correctamente.\n\n' +
        'El precio del pack ahora se usa como precio por unidad.'
    );
}
</script>
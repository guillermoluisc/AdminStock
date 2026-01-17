<!-- app/views/variedades/crear.php -->
<h2>Crear Nueva Variedad</h2>

<div class="alert alert-info" style="background: #d1ecf1; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
    <strong>📌 Instrucciones:</strong>
    <ul style="margin: 10px 0 0 20px; line-height: 1.6;">
        <li><strong>Caso 1:</strong> Si compras 1 CAJA que contiene 3 unidades → Ingresa cantidad = 1</li>
        <li><strong>Caso 2:</strong> Si compras 1 producto que se vende SOLO por unidad → Usa el botón "Igualar Precios"</li>
    </ul>
</div>

<form method="POST" action="index.php?c=variedad&a=crear">
    <div class="form-group">
        <label for="producto_padre_id">Producto Padre *</label>
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
                <input type="text" id="costo_unitario_display" readonly style="background: #ecf0f1; font-weight: bold;" value="$0.00">
                <small style="color: #666;">Se calcula automáticamente</small>
            </div>
        </div>
    </div>
    
    <div class="card" style="background: #e8f5e9;">
        <h3 style="margin-bottom: 15px;">📊 Precios de Venta Calculados Automáticamente</h3>
        <p style="color: #666; margin-bottom: 15px;">
            Los precios se calculan según los porcentajes configurados en el Producto Padre
        </p>
        
        <div class="grid-2">
            <div class="form-group">
                <label for="precio_pack3_tarjeta">💳 Pack x3 Tarjeta (precio total)</label>
                <input type="number" id="precio_pack3_tarjeta" name="precio_pack3_tarjeta" step="0.01" min="0" readonly style="background: #f0f0f0; font-weight: bold; font-size: 18px; color: #27ae60;">
                <small id="info_pack3_tarjeta" style="color: #666;">-</small>
            </div>
            
            <div class="form-group">
                <label for="precio_pack3_efectivo">💵 Pack x3 Efectivo (precio total)</label>
                <input type="number" id="precio_pack3_efectivo" name="precio_pack3_efectivo" step="0.01" min="0" readonly style="background: #f0f0f0; font-weight: bold; font-size: 18px; color: #27ae60;">
                <small id="info_pack3_efectivo" style="color: #666;">-</small>
            </div>
        </div>
        
        <div class="grid-2" style="margin-top: 15px;">
            <div class="form-group">
                <label for="precio_unidad_tarjeta">💳 Por 1 Unidad Tarjeta</label>
                <input type="number" id="precio_unidad_tarjeta" name="precio_unidad_tarjeta" step="0.01" min="0" readonly style="background: #f0f0f0; font-weight: bold; font-size: 18px; color: #3498db;">
                <small id="info_unidad_tarjeta" style="color: #666;">-</small>
            </div>
            
            <div class="form-group">
                <label for="precio_unidad_efectivo">💵 Por 1 Unidad Efectivo</label>
                <input type="number" id="precio_unidad_efectivo" name="precio_unidad_efectivo" step="0.01" min="0" readonly style="background: #f0f0f0; font-weight: bold; font-size: 18px; color: #3498db;">
                <small id="info_unidad_efectivo" style="color: #666;">-</small>
            </div>
        </div>
        
        <!-- BOTÓN para igualar precios -->
        <div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-radius: 4px; border-left: 4px solid #f39c12;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>💡 ¿Vender solo por unidad al mismo precio?</strong>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">
                        Si NO vendes por pack de 3, usa este botón para igualar los precios
                    </p>
                </div>
                <button type="button" class="btn btn-warning" onclick="igualarPrecios()">
                    📋 Igualar Precios
                </button>
            </div>
        </div>
        
        <div id="ejemplo_calculo" style="display: none; margin-top: 15px; padding: 15px; background: white; border-radius: 4px;">
            <h4 style="margin-bottom: 10px;">🔍 Detalle del Cálculo:</h4>
            <div id="detalle_calculo" style="font-size: 14px; line-height: 1.8;"></div>
        </div>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 15px;">🏷️ Stock</h3>
        <div class="grid-3">
            <div class="form-group">
                <label for="precio_venta_unitario">Precio Base (Referencia)</label>
                <input type="number" id="precio_venta_unitario" name="precio_venta_unitario" step="0.01" min="0" required value="0">
                <small style="color: #666;">Solo para referencia interna</small>
            </div>
            
            <div class="form-group">
                <label for="stock">Stock Inicial *</label>
                <input type="number" id="stock" name="stock" min="0" required>
                <small style="color: #666;">Por defecto = cantidad comprada × 3</small>
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
    
    if (cantidad <= 0 || precioTotal <= 0) {
        return;
    }

    // 1. COSTO UNITARIO
    const costoUnitario = precioTotal / cantidad;
    document.getElementById('costo_unitario_display').value = '$' + costoUnitario.toFixed(2);

    // Auto-completar stock (cantidad × 3 para cajas)
    if (!document.getElementById('stock').value) {
        document.getElementById('stock').value = cantidad * 3;
    }

    // 2. CALCULAR PRECIOS
    calcularPreciosVenta(precioTotal, costoUnitario);
}

function calcularPreciosVenta(precioCompraTotal, costoUnitario) {
    // PRECIOS PACK x3 (sobre el costo TOTAL de la caja/pack)
    // Fórmula: PrecioCompraTotal + (PrecioCompraTotal × Porcentaje/100)
    const pack3Tarjeta = precioCompraTotal + (precioCompraTotal * porcentajes.pack3_tarjeta / 100);
    const pack3Efectivo = precioCompraTotal + (precioCompraTotal * porcentajes.pack3_efectivo / 100);

    // PRECIOS POR UNIDAD (sobre el costo UNITARIO)
    // Fórmula: CostoUnitario + (CostoUnitario × Porcentaje/100)
    const unidadTarjeta = (costoUnitario) + (costoUnitario * porcentajes.unidad_tarjeta / 100);
    const unidadEfectivo = costoUnitario + (costoUnitario * porcentajes.unidad_efectivo / 100);

    // Actualizar campos
    document.getElementById('precio_pack3_tarjeta').value = pack3Tarjeta.toFixed(2);
    document.getElementById('precio_pack3_efectivo').value = pack3Efectivo.toFixed(2);
    document.getElementById('precio_unidad_tarjeta').value = (unidadTarjeta / 3).toFixed(2);
    document.getElementById('precio_unidad_efectivo').value = (unidadEfectivo / 3).toFixed(2);

    // Actualizar textos informativos
    document.getElementById('info_pack3_tarjeta').textContent = 
        `${precioCompraTotal.toFixed(2)} + (${precioCompraTotal.toFixed(2)} × ${porcentajes.pack3_tarjeta}%) | Por unidad: $${(pack3Tarjeta/3).toFixed(2)}`;
    
    document.getElementById('info_pack3_efectivo').textContent = 
        `${precioCompraTotal.toFixed(2)} + (${precioCompraTotal.toFixed(2)} × ${porcentajes.pack3_efectivo}%) | Por unidad: $${(pack3Efectivo/3).toFixed(2)}`;
    
    document.getElementById('info_unidad_tarjeta').textContent = 
        `${costoUnitario.toFixed(2)} + (${costoUnitario.toFixed(2)} × ${porcentajes.unidad_tarjeta}%)`;
    
    document.getElementById('info_unidad_efectivo').textContent = 
        `${costoUnitario.toFixed(2)} + (${costoUnitario.toFixed(2)} × ${porcentajes.unidad_efectivo}%)`;

    // Mostrar ejemplo
    mostrarEjemploCalculo(precioCompraTotal, costoUnitario, pack3Tarjeta, pack3Efectivo, unidadTarjeta, unidadEfectivo);
}

function mostrarEjemploCalculo(costoTotal, costoUnit, p3t, p3e, u1t, u1e) {
    document.getElementById('ejemplo_calculo').style.display = 'block';
    
    const html = `
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
            <div>
                <strong>📦 DATOS DE COMPRA:</strong><br>
                • Costo total: <strong>$${costoTotal.toFixed(2)}</strong><br>
                • Costo unitario: <strong>$${costoUnit.toFixed(2)}</strong>
            </div>
            <div>
                <strong>⚙️ PORCENTAJES:</strong><br>
                • Pack x3 Tarjeta: <strong>${porcentajes.pack3_tarjeta}%</strong><br>
                • Pack x3 Efectivo: <strong>${porcentajes.pack3_efectivo}%</strong><br>
                • Por 1 Tarjeta: <strong>${porcentajes.unidad_tarjeta}%</strong><br>
                • Por 1 Efectivo: <strong>${porcentajes.unidad_efectivo}%</strong>
            </div>
        </div>
        <hr style="margin: 15px 0; border: none; border-top: 1px solid #ddd;">
        <div>
            <strong>💳 PACK x3 TARJETA:</strong><br>
            $${costoTotal.toFixed(2)} + ($${costoTotal.toFixed(2)} × ${porcentajes.pack3_tarjeta}%) = 
            <span style="color: #27ae60; font-weight: bold;">$${p3t.toFixed(2)}</span>
            <small style="display: block; color: #666;">Por unidad en el pack: $${(p3t/3).toFixed(2)}</small>
        </div>
        <div style="margin-top: 10px;">
            <strong>💵 PACK x3 EFECTIVO:</strong><br>
            $${costoTotal.toFixed(2)} + ($${costoTotal.toFixed(2)} × ${porcentajes.pack3_efectivo}%) = 
            <span style="color: #27ae60; font-weight: bold;">$${p3e.toFixed(2)}</span>
            <small style="display: block; color: #666;">Por unidad en el pack: $${(p3e/3).toFixed(2)}</small>
        </div>
        <div style="margin-top: 10px;">
            <strong>💳 POR 1 UNIDAD TARJETA:</strong><br>
            $${costoUnit.toFixed(2)} + ($${costoUnit.toFixed(2)} × ${porcentajes.unidad_tarjeta}%) = 
            <span style="color: #3498db; font-weight: bold;">$${u1t.toFixed(2)}</span>
        </div>
        <div style="margin-top: 10px;">
            <strong>💵 POR 1 UNIDAD EFECTIVO:</strong><br>
            $${costoUnit.toFixed(2)} + ($${costoUnit.toFixed(2)} × ${porcentajes.unidad_efectivo}%) = 
            <span style="color: #3498db; font-weight: bold;">$${u1e.toFixed(2)}</span>
        </div>
    `;
    
    document.getElementById('detalle_calculo').innerHTML = html;
}

/**
 * Igualar precios: Copia los precios unitarios a los precios de pack
 * Usado cuando el producto se vende SOLO por unidad (no por pack de 3)
 */
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

    // Hacer editables los unitarios
    document.getElementById('precio_unidad_tarjeta').readOnly = false;
    document.getElementById('precio_unidad_efectivo').readOnly = false;

    // 🔥 CLAVE: unidad = pack
    document.getElementById('precio_unidad_tarjeta').value = packTarjeta.toFixed(2);
    document.getElementById('precio_unidad_efectivo').value = packEfectivo.toFixed(2);

    // Estilo visual
    document.getElementById('precio_unidad_tarjeta').style.background = '#fff3cd';
    document.getElementById('precio_unidad_efectivo').style.background = '#fff3cd';

    // Info
    document.getElementById('info_unidad_tarjeta').textContent = '⚠️ Igualado al precio del pack';
    document.getElementById('info_unidad_efectivo').textContent = '⚠️ Igualado al precio del pack';

    alert(
        '✅ Precios ajustados correctamente.\n\n' +
        'El precio del pack ahora se usa como precio por unidad.'
    );
}
</script>
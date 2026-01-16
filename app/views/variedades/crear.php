<!-- app/views/variedades/crear.php -->
<h2>Crear Nueva Variedad</h2>

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
                <label for="precio_pack3_tarjeta">💳 Pack x3 Tarjeta (por unidad)</label>
                <input type="number" id="precio_pack3_tarjeta" name="precio_pack3_tarjeta" step="0.01" min="0" readonly style="background: #f0f0f0; font-weight: bold;">
                <small style="color: #666;">Precio por unidad en pack de 3</small>
            </div>
            
            <div class="form-group">
                <label for="precio_pack3_efectivo">💵 Pack x3 Efectivo (por unidad)</label>
                <input type="number" id="precio_pack3_efectivo" name="precio_pack3_efectivo" step="0.01" min="0" readonly style="background: #f0f0f0; font-weight: bold;">
                <small style="color: #666;">Precio por unidad en pack de 3</small>
            </div>
        </div>
        
<div class="card" style="background: #e8f5e9;">
    <h3 style="margin-bottom: 15px;">📊 Precios de Venta Calculados Automáticamente</h3>
    <p style="color: #666; margin-bottom: 15px;">
        Los precios se calculan según los porcentajes configurados en el Producto Padre
    </p>
    
    <div class="grid-2">
        <div class="form-group">
            <label for="precio_pack3_tarjeta">💳 Pack x3 Tarjeta (por unidad)</label>
            <input type="number" id="precio_pack3_tarjeta" name="precio_pack3_tarjeta" step="0.01" min="0" readonly style="background: #f0f0f0; font-weight: bold;">
            <small style="color: #666;">Precio por unidad en pack de 3</small>
        </div>
        
        <div class="form-group">
            <label for="precio_pack3_efectivo">💵 Pack x3 Efectivo (por unidad)</label>
            <input type="number" id="precio_pack3_efectivo" name="precio_pack3_efectivo" step="0.01" min="0" readonly style="background: #f0f0f0; font-weight: bold;">
            <small style="color: #666;">Precio por unidad en pack de 3</small>
        </div>
    </div>
    
    <div class="grid-2" style="margin-top: 15px;">
        <div class="form-group">
            <label for="precio_unidad_tarjeta">💳 Por 1 Unidad Tarjeta</label>
            <input type="number" id="precio_unidad_tarjeta" name="precio_unidad_tarjeta" step="0.01" min="0" readonly style="background: #f0f0f0; font-weight: bold;">
            <small style="color: #666;">Precio unitario individual</small>
        </div>
        
        <div class="form-group">
            <label for="precio_unidad_efectivo">💵 Por 1 Unidad Efectivo</label>
            <input type="number" id="precio_unidad_efectivo" name="precio_unidad_efectivo" step="0.01" min="0" readonly style="background: #f0f0f0; font-weight: bold;">
            <small style="color: #666;">Precio unitario individual</small>
        </div>
    </div>
    
    <!-- NUEVO: Botón para igualar precios -->
    <div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-radius: 4px; border-left: 4px solid #f39c12;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong>💡 ¿Vender solo por unidad al mismo precio del pack?</strong>
                <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">
                    Esto copiará los precios del Pack x3 a los precios Por 1 Unidad
                </p>
            </div>
            <button type="button" class="btn btn-warning" onclick="copiarPreciosPack3AUnidad()">
                📋 Igualar Precios
            </button>
        </div>
    </div>
    
    <div id="ejemplo_calculo" style="display: none; margin-top: 15px; padding: 15px; background: white; border-radius: 4px;">
        <h4 style="margin-bottom: 10px;">🔍 Ejemplo de Cálculo:</h4>
        <div style="font-size: 14px; line-height: 1.8;">
            <div><strong>Costo unitario:</strong> <span id="ej_costo">$0</span></div>
            <div style="margin-top: 5px;">
                <strong>Pack x3 Tarjeta:</strong> 
                <span id="ej_pack3_tarjeta_formula"></span> = 
                <span id="ej_pack3_tarjeta_total" style="color: #27ae60; font-weight: bold;">$0</span>
                (por unidad: <span id="ej_pack3_tarjeta_unit" style="color: #27ae60;">$0</span>)
            </div>
            <div>
                <strong>Pack x3 Efectivo:</strong> 
                <span id="ej_pack3_efectivo_formula"></span> = 
                <span id="ej_pack3_efectivo_total" style="color: #27ae60; font-weight: bold;">$0</span>
                (por unidad: <span id="ej_pack3_efectivo_unit" style="color: #27ae60;">$0</span>)
            </div>
            <div>
                <strong>Por 1 Tarjeta:</strong> 
                <span id="ej_unidad_tarjeta_formula"></span> = 
                <span id="ej_unidad_tarjeta" style="color: #3498db; font-weight: bold;">$0</span>
            </div>
            <div>
                <strong>Por 1 Efectivo:</strong> 
                <span id="ej_unidad_efectivo_formula"></span> = 
                <span id="ej_unidad_efectivo" style="color: #3498db; font-weight: bold;">$0</span>
            </div>
        </div>
    </div>
</div>
        
        <div id="ejemplo_calculo" style="display: none; margin-top: 15px; padding: 15px; background: white; border-radius: 4px;">
            <h4 style="margin-bottom: 10px;">📝 Ejemplo de Cálculo:</h4>
            <div style="font-size: 14px; line-height: 1.8;">
                <div><strong>Costo unitario:</strong> <span id="ej_costo">$0</span></div>
                <div style="margin-top: 5px;">
                    <strong>Pack x3 Tarjeta:</strong> 
                    <span id="ej_pack3_tarjeta_formula"></span> = 
                    <span id="ej_pack3_tarjeta_total" style="color: #27ae60; font-weight: bold;">$0</span>
                    (por unidad: <span id="ej_pack3_tarjeta_unit" style="color: #27ae60;">$0</span>)
                </div>
                <div>
                    <strong>Pack x3 Efectivo:</strong> 
                    <span id="ej_pack3_efectivo_formula"></span> = 
                    <span id="ej_pack3_efectivo_total" style="color: #27ae60; font-weight: bold;">$0</span>
                    (por unidad: <span id="ej_pack3_efectivo_unit" style="color: #27ae60;">$0</span>)
                </div>
                <div>
                    <strong>Por 1 Tarjeta:</strong> 
                    <span id="ej_unidad_tarjeta_formula"></span> = 
                    <span id="ej_unidad_tarjeta" style="color: #3498db; font-weight: bold;">$0</span>
                </div>
                <div>
                    <strong>Por 1 Efectivo:</strong> 
                    <span id="ej_unidad_efectivo_formula"></span> = 
                    <span id="ej_unidad_efectivo" style="color: #3498db; font-weight: bold;">$0</span>
                </div>
            </div>
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
                <small style="color: #666;">Por defecto = cantidad comprada</small>
            </div>
            
            <div class="form-group">
                <label for="stock_minimo">Stock Mínimo</label>
                <input type="number" id="stock_minimo" name="stock_minimo" min="0" value="10">
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
    pack3_tarjeta: 100,
    pack3_efectivo: 80,
    unidad_tarjeta: 100,
    unidad_efectivo: 80
};

function cargarPorcentajes() {
    const select = document.getElementById('producto_padre_id');
    const option = select.options[select.selectedIndex];
    
    if (option.value) {
        porcentajes.pack3_tarjeta = parseFloat(option.dataset.pack3Tarjeta) || 100;
        porcentajes.pack3_efectivo = parseFloat(option.dataset.pack3Efectivo) || 80;
        porcentajes.unidad_tarjeta = parseFloat(option.dataset.unidadTarjeta) || 100;
        porcentajes.unidad_efectivo = parseFloat(option.dataset.unidadEfectivo) || 80;
        
        calcularCostoYPrecios();
    }
}

function calcularCostoYPrecios() {
    const precioTotal = parseFloat(document.getElementById('precio_compra_total').value) || 0;
    const cantidad = parseInt(document.getElementById('cantidad_comprada').value) || 0;
    
    if (cantidad > 0 && precioTotal > 0) {
        const costoUnitario = precioTotal / cantidad;
        document.getElementById('costo_unitario_display').value = '$' + costoUnitario.toFixed(2);
        
        // Auto-completar el stock
        if (!document.getElementById('stock').value) {
            document.getElementById('stock').value = cantidad;
        }
        
        // Calcular los 4 precios
        calcularPreciosVenta(costoUnitario);
    }
}

function calcularPreciosVenta(costoUnitario) {
    // Pack x3 Tarjeta: (Costo × 3) + ((Costo × 3) × porcentaje/100)
    const pack3TarjetaTotal = (costoUnitario * 3) + ((costoUnitario * 3) * porcentajes.pack3_tarjeta / 100);
    const pack3TarjetaUnit = pack3TarjetaTotal / 3;
    
    // Pack x3 Efectivo: (Costo × 3) + ((Costo × 3) × porcentaje/100)
    const pack3EfectivoTotal = (costoUnitario * 3) + ((costoUnitario * 3) * porcentajes.pack3_efectivo / 100);
    const pack3EfectivoUnit = pack3EfectivoTotal / 3;
    
    // Por 1 Tarjeta: Costo + (Costo × porcentaje/100)
    const unidadTarjeta = costoUnitario + (costoUnitario * porcentajes.unidad_tarjeta / 100);
    
    // Por 1 Efectivo: Costo + (Costo × porcentaje/100)
    const unidadEfectivo = costoUnitario + (costoUnitario * porcentajes.unidad_efectivo / 100);
    
    // Actualizar campos
    document.getElementById('precio_pack3_tarjeta').value = pack3TarjetaUnit.toFixed(2);
    document.getElementById('precio_pack3_efectivo').value = pack3EfectivoUnit.toFixed(2);
    document.getElementById('precio_unidad_tarjeta').value = unidadTarjeta.toFixed(2);
    document.getElementById('precio_unidad_efectivo').value = unidadEfectivo.toFixed(2);
    
    // Los campos por unidad siguen siendo readonly hasta que se copien manualmente
    document.getElementById('precio_unidad_tarjeta').style.background = '#f0f0f0';
    document.getElementById('precio_unidad_efectivo').style.background = '#f0f0f0';
    
    // Mostrar ejemplo de cálculo
    mostrarEjemploCalculo(costoUnitario, pack3TarjetaTotal, pack3EfectivoTotal, pack3TarjetaUnit, pack3EfectivoUnit, unidadTarjeta, unidadEfectivo);
}

function mostrarEjemploCalculo(costo, pack3T, pack3E, pack3TU, pack3EU, unit1T, unit1E) {
    document.getElementById('ejemplo_calculo').style.display = 'block';
    
    document.getElementById('ej_costo').textContent = '$' + costo.toFixed(2);
    
    document.getElementById('ej_pack3_tarjeta_formula').textContent = 
        `($${costo.toFixed(2)} × 3) + (($${costo.toFixed(2)} × 3) × ${porcentajes.pack3_tarjeta}%)`;
    document.getElementById('ej_pack3_tarjeta_total').textContent = '$' + pack3T.toFixed(2);
    document.getElementById('ej_pack3_tarjeta_unit').textContent = '$' + pack3TU.toFixed(2);
    
    document.getElementById('ej_pack3_efectivo_formula').textContent = 
        `($${costo.toFixed(2)} × 3) + (($${costo.toFixed(2)} × 3) × ${porcentajes.pack3_efectivo}%)`;
    document.getElementById('ej_pack3_efectivo_total').textContent = '$' + pack3E.toFixed(2);
    document.getElementById('ej_pack3_efectivo_unit').textContent = '$' + pack3EU.toFixed(2);
    
    document.getElementById('ej_unidad_tarjeta_formula').textContent = 
        `$${costo.toFixed(2)} + ($${costo.toFixed(2)} × ${porcentajes.unidad_tarjeta}%)`;
    document.getElementById('ej_unidad_tarjeta').textContent = '$' + unit1T.toFixed(2);
    
    document.getElementById('ej_unidad_efectivo_formula').textContent = 
        `$${costo.toFixed(2)} + ($${costo.toFixed(2)} × ${porcentajes.unidad_efectivo}%)`;
    document.getElementById('ej_unidad_efectivo').textContent = '$' + unit1E.toFixed(2);
}

/**
 * Copia los precios del Pack x3 a los precios Por Unidad
 */
function copiarPreciosPack3AUnidad() {
    const pack3Tarjeta = document.getElementById('precio_pack3_tarjeta').value;
    const pack3Efectivo = document.getElementById('precio_pack3_efectivo').value;
    
    if (!pack3Tarjeta || !pack3Efectivo) {
        alert('Primero debe calcular los precios del Pack x3.\nIngrese el precio de compra y la cantidad.');
        return;
    }
    
    // Hacer los campos editables temporalmente
    document.getElementById('precio_unidad_tarjeta').readOnly = false;
    document.getElementById('precio_unidad_efectivo').readOnly = false;
    
    // Copiar valores
    document.getElementById('precio_unidad_tarjeta').value = pack3Tarjeta;
    document.getElementById('precio_unidad_efectivo').value = pack3Efectivo;
    
    // Cambiar estilo para indicar que fueron modificados manualmente
    document.getElementById('precio_unidad_tarjeta').style.background = '#fff3cd';
    document.getElementById('precio_unidad_efectivo').style.background = '#fff3cd';
    
    alert('✅ Precios copiados correctamente.\n\nAhora los precios por unidad son iguales a los del Pack x3.');
}
</script>
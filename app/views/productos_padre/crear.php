<!-- app/views/productos_padre/crear.php -->
<h2>Crear Producto Padre</h2>

<form method="POST" action="index.php?c=productoPadre&a=crear">
    <div class="form-group">
        <label for="nombre">Nombre del Producto *</label>
        <input type="text" id="nombre" name="nombre" required placeholder="Ej: Colaless VINTAGE">
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 15px;">📦 Precios Pack x3</h3>
        <!-- <p style="color: #666; margin-bottom: 15px;">
            El precio final será: <strong>Costo + (Costo × Porcentaje / 100)</strong><br>
            Ejemplo: Si costo = $7,904 y porcentaje = 100%, precio final = $7,904 + ($7,904 × 100/100) = $15,808
        </p> -->
        <div class="grid-2">
            <div class="form-group">
                <label for="porcentaje_pack3_tarjeta">💳 Porcentaje Tarjeta (%)</label>
                <input type="number" id="porcentaje_pack3_tarjeta" name="porcentaje_pack3_tarjeta" step="0.01" min="0" value="100">
            </div>
            <div class="form-group">
                <label for="porcentaje_pack3_efectivo">💵 Porcentaje Efectivo (%)</label>
                <input type="number" id="porcentaje_pack3_efectivo" name="porcentaje_pack3_efectivo" step="0.01" min="0" value="80">
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 15px;">🏷️ Precios Por Unidad</h3>
        <!-- <p style="color: #666; margin-bottom: 15px;">
            El precio por unidad se calcula dividiendo el precio del pack x3 entre 3
        </p> -->
        <div class="grid-2">
            <div class="form-group">
                <label for="porcentaje_unidad_tarjeta">💳 Porcentaje Tarjeta (%)</label>
                <input type="number" id="porcentaje_unidad_tarjeta" name="porcentaje_unidad_tarjeta" step="0.01" min="0" value="100">
            </div>
            <div class="form-group">
                <label for="porcentaje_unidad_efectivo">💵 Porcentaje Efectivo (%)</label>
                <input type="number" id="porcentaje_unidad_efectivo" name="porcentaje_unidad_efectivo" step="0.01" min="0" value="80">
            </div>
        </div>
    </div>
    
    <!-- <div class="card" style="background: #e8f5e9;">
        <h4 style="margin-bottom: 10px;">💡 Ejemplo de Cálculo</h4>
        <p style="margin-bottom: 5px;"><strong>Si el costo es $7,904:</strong></p>
        <ul style="margin-left: 20px;">
            <li>Pack x3 Tarjeta (100%): $7,904 + $7,904 = <strong>$15,808</strong></li>
            <li>Pack x3 Efectivo (80%): $7,904 + $6,323.20 = <strong>$14,227.20</strong></li>
            <li>Por 1 Tarjeta: $15,808 / 3 = <strong>$5,269.33</strong></li>
            <li>Por 1 Efectivo: $14,227.20 / 3 = <strong>$4,742.40</strong></li>
        </ul>
    </div> -->
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar Producto Padre</button>
        <a href="index.php?c=productoPadre&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
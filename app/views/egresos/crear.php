<!-- app/views/egresos/crear.php -->
<h2>Registrar Nuevo Egreso</h2>

<form method="POST" action="index.php?c=egreso&a=crear">
    <div class="grid-2">
        <div class="form-group">
            <label for="fecha">Fecha del Egreso *</label>
            <input type="date" id="fecha" name="fecha" required value="<?= date('Y-m-d') ?>">
        </div>
        
        <div class="form-group">
            <label for="monto">Monto *</label>
            <input type="number" id="monto" name="monto" step="0.01" min="0" required placeholder="0.00">
        </div>
    </div>
    
    <div class="form-group">
        <label for="categoria">Categoría (Opcional)</label>
        <input type="text" id="categoria" name="categoria" list="categorias_list" placeholder="Ej: Compras, Servicios, Impuestos...">
        <datalist id="categorias_list">
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>">
            <?php endforeach; ?>
        </datalist>
        <small style="color: #666;">Escriba una categoría existente o cree una nueva</small>
    </div>
    
    <div class="form-group">
        <label for="descripcion">Descripción del Gasto *</label>
        <textarea id="descripcion" name="descripcion" rows="4" required placeholder="Ej: Compra de artículos para stock - 4 productos (detalle de los productos)"></textarea>
        <!-- <small style="color: #666;">Sea específico: qué se compró, dónde, cantidad, etc.</small> -->
    </div>
    
    <!-- <div class="card" style="background: #fff3cd; margin-top: 20px;">
        <h4 style="margin-bottom: 10px;">💡 Ejemplo de Descripción Detallada:</h4>
        <p style="margin: 0; font-style: italic; color: #666;">
            "Compra en MercadoLibre - 4 artículos:<br>
            - 10 unidades de Producto A ($5.000 c/u)<br>
            - 5 unidades de Producto B ($8.000 c/u)<br>
            - Gastos de envío: $2.500"
        </p>
    </div> -->
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Guardar Egreso</button>
        <a href="index.php?c=egreso&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
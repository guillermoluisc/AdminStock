<!-- app/views/egresos/editar.php -->
<h2>Editar Egreso</h2>

<form method="POST" action="index.php?c=egreso&a=editar&id=<?= $egreso['id'] ?>">
    <div class="grid-2">
        <div class="form-group">
            <label for="fecha">Fecha del Egreso *</label>
            <input type="date" id="fecha" name="fecha" required value="<?= $egreso['fecha'] ?>">
        </div>
        
        <div class="form-group">
            <label for="monto">Monto *</label>
            <input type="number" id="monto" name="monto" step="0.01" min="0" required value="<?= $egreso['monto'] ?>">
        </div>
    </div>
    
    <div class="form-group">
        <label for="categoria">Categoría (Opcional)</label>
        <input type="text" id="categoria" name="categoria" list="categorias_list" value="<?= htmlspecialchars($egreso['categoria'] ?? '') ?>" placeholder="Ej: Compras, Servicios, Impuestos...">
        <datalist id="categorias_list">
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>">
            <?php endforeach; ?>
        </datalist>
        <small style="color: #666;">Escriba una categoría existente o cree una nueva</small>
    </div>
    
    <div class="form-group">
        <label for="descripcion">Descripción del Gasto *</label>
        <textarea id="descripcion" name="descripcion" rows="4" required><?= htmlspecialchars($egreso['descripcion']) ?></textarea>
        <small style="color: #666;">Sea específico: qué se compró, dónde, cantidad, etc.</small>
    </div>
    
    <div class="card" style="background: #e3f2fd; margin-top: 10px;">
        <small style="color: #666;">
            <strong>Registrado el:</strong> <?= date('d/m/Y H:i', strtotime($egreso['fecha_creacion'])) ?>
        </small>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Actualizar Egreso</button>
        <a href="index.php?c=egreso&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
<!-- app/views/productos/editar.php -->
<h2>Editar Producto</h2>

<?php 
    $producto_model = new Producto();
    $nombre = $producto_model->getNombreCompleto($producto);
?>

<div class="card" style="margin-bottom: 20px;">
    <h3>
        <span class="badge badge-<?= $producto['tipo'] ?>">
            <?= $producto['tipo'] == 'dulce' ? '🍯 Dulce' : '🧀 Salado' ?>
        </span>
        <?= htmlspecialchars($nombre) ?>
    </h3>
</div>

<form method="POST" action="index.php?c=producto&a=editar&id=<?= $producto['id'] ?>">
    <div class="form-group">
        <label for="stock">Stock *</label>
        <input type="number" id="stock" name="stock" min="0" value="<?= $producto['stock'] ?>" required>
    </div>
    
    <div class="form-group">
        <label for="precio_compra">Precio de Compra</label>
        <input type="number" id="precio_compra" name="precio_compra" step="0.01" min="0" value="<?= $producto['precio_compra'] ?>">
    </div>
    
    <div class="form-group">
        <label for="precio_venta">Precio de Venta *</label>
        <input type="number" id="precio_venta" name="precio_venta" step="0.01" min="0" value="<?= $producto['precio_venta'] ?>" required>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom: 10px;">Configurar Promoción</h3>
        <div class="grid-2">
            <div class="form-group">
                <label for="cantidad_promo">Cantidad en Promo</label>
                <input type="number" id="cantidad_promo" name="cantidad_promo" min="1" value="<?= $producto['cantidad_promo'] ?>">
            </div>
            
            <div class="form-group">
                <label for="precio_promo">Precio Promo</label>
                <input type="number" id="precio_promo" name="precio_promo" step="0.01" min="0" value="<?= $producto['precio_promo'] ?>">
            </div>
        </div>
    </div>
    
    <div class="form-group checkbox-group">
        <input type="checkbox" id="activo" name="activo" <?= $producto['activo'] ? 'checked' : '' ?>>
        <label for="activo">Producto Activo</label>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="index.php?c=producto&a=index" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
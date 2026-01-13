<!-- app/views/configuracion/index.php -->
<h2>Configuración del Sistema</h2>

<div class="grid-2" style="margin-top: 20px;">
    <!-- TAMAÑOS -->
    <div class="card">
        <h3 style="margin-bottom: 15px;">🍯 Tamaños (Dulces)</h3>
        
        <form method="POST" action="index.php?c=configuracion&a=agregarTamano" style="margin-bottom: 15px;">
            <div style="display: flex; gap: 10px;">
                <input type="text" name="nombre" placeholder="Ej: 500g" required style="flex: 1; padding: 8px;">
                <button type="submit" class="btn btn-success">Agregar</button>
            </div>
        </form>
        
        <div style="max-height: 300px; overflow-y: auto;">
            <?php foreach ($tamanos as $t): ?>
                <div style="display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ddd; <?= !$t['activo'] ? 'opacity: 0.5;' : '' ?>">
                    <span><?= htmlspecialchars($t['nombre']) ?></span>
                    <?php if ($t['activo']): ?>
                        <a href="index.php?c=configuracion&a=eliminarTamano&id=<?= $t['id'] ?>" 
                           class="btn btn-danger" style="padding: 3px 8px; font-size: 12px;"
                           onclick="return confirm('¿Eliminar?')">✕</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- SABORES -->
    <div class="card">
        <h3 style="margin-bottom: 15px;">🍫 Sabores (Dulces)</h3>
        
        <form method="POST" action="index.php?c=configuracion&a=agregarSabor" style="margin-bottom: 15px;">
            <div style="display: flex; gap: 10px;">
                <input type="text" name="nombre" placeholder="Ej: Frutilla" required style="flex: 1; padding: 8px;">
                <button type="submit" class="btn btn-success">Agregar</button>
            </div>
        </form>
        
        <div style="max-height: 300px; overflow-y: auto;">
            <?php foreach ($sabores as $s): ?>
                <div style="display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ddd; <?= !$s['activo'] ? 'opacity: 0.5;' : '' ?>">
                    <span><?= htmlspecialchars($s['nombre']) ?></span>
                    <?php if ($s['activo']): ?>
                        <a href="index.php?c=configuracion&a=eliminarSabor&id=<?= $s['id'] ?>" 
                           class="btn btn-danger" style="padding: 3px 8px; font-size: 12px;"
                           onclick="return confirm('¿Eliminar?')">✕</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- VARIEDADES DE SALADOS -->
<div class="card" style="margin-top: 20px;">
    <h3 style="margin-bottom: 15px;">🧀 Variedades de Salados</h3>
    
    <form method="POST" action="index.php?c=configuracion&a=agregarVariedad" style="margin-bottom: 15px;">
        <div style="display: flex; gap: 10px;">
            <select name="tipo_salado_id" required style="padding: 8px;">
                <?php foreach ($tipos_salados as $ts): ?>
                    <option value="<?= $ts['id'] ?>"><?= htmlspecialchars($ts['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="nombre" placeholder="Ej: Queso con Ajo" required style="flex: 1; padding: 8px;">
            <button type="submit" class="btn btn-success">Agregar</button>
        </div>
    </form>
    
    <div style="max-height: 400px; overflow-y: auto;">
        <?php foreach ($variedades as $v): ?>
            <div style="display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ddd; <?= !$v['activo'] ? 'opacity: 0.5;' : '' ?>">
                <span>
                    <span class="badge" style="background: #95a5a6;"><?= htmlspecialchars($v['tipo_nombre']) ?></span>
                    <?= htmlspecialchars($v['nombre']) ?>
                </span>
                <?php if ($v['activo']): ?>
                    <a href="index.php?c=configuracion&a=eliminarVariedad&id=<?= $v['id'] ?>" 
                       class="btn btn-danger" style="padding: 3px 8px; font-size: 12px;"
                       onclick="return confirm('¿Eliminar?')">✕</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
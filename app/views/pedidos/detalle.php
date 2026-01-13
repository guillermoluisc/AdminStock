<!-- app/views/pedidos/detalle.php -->
<div style="margin-bottom: 20px;">
    <a href="index.php?c=pedido&a=index" class="btn btn-secondary">← Volver a Pedidos</a>
</div>

<div class="card" style="margin-bottom: 20px;">
    <h2>Detalle del Pedido #<?= $pedido['id'] ?></h2>
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 15px;">
        <div>
            <strong>Fecha Creación:</strong><br>
            <?= date('d/m/Y H:i', strtotime($pedido['fecha_creacion'])) ?>
        </div>
        <div>
            <strong>Estado:</strong><br>
            <?php if ($pedido['estado'] == 'pendiente'): ?>
                <span class="badge badge-pendiente">⏳ Pendiente</span>
            <?php elseif ($pedido['estado'] == 'realizado'): ?>
                <span class="badge badge-realizado">✓ Realizado</span>
            <?php else: ?>
                <span class="badge badge-faltante">⚠ Faltante</span>
            <?php endif; ?>
        </div>
        <div>
            <strong>Fecha Realización:</strong><br>
            <?php if ($pedido['fecha_realizacion']): ?>
                <?= date('d/m/Y H:i', strtotime($pedido['fecha_realizacion'])) ?>
            <?php else: ?>
                <span style="color: #95a5a6;">-</span>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($pedido['observaciones']): ?>
    <div style="margin-top: 15px; padding: 10px; background: #ecf0f1; border-radius: 4px;">
        <strong>Observaciones:</strong><br>
        <?= nl2br(htmlspecialchars($pedido['observaciones'])) ?>
    </div>
    <?php endif; ?>
</div>

<?php if ($pedido['estado'] == 'pendiente'): ?>
<div class="card" style="margin-bottom: 20px; background: #fff3cd;">
    <h3 style="margin-bottom: 15px;">Acciones del Pedido</h3>
    <div style="display: flex; gap: 10px;">
        <a href="index.php?c=pedido&a=marcarRealizado&id=<?= $pedido['id'] ?>" 
           class="btn btn-success"
           onclick="return confirm('¿Marcar este pedido como realizado? Esto actualizará el stock de todos los productos.')">
            ✓ Marcar como Realizado
        </a>
        
        <button type="button" class="btn btn-warning" onclick="mostrarFormFaltante()">
            ⚠ Marcar con Faltantes
        </button>
    </div>
    
    <div id="form_faltante" style="display: none; margin-top: 15px;">
        <form method="POST" action="index.php?c=pedido&a=marcarFaltante&id=<?= $pedido['id'] ?>">
            <div class="form-group">
                <label for="observaciones">Detalles de los faltantes:</label>
                <textarea id="observaciones" name="observaciones" rows="3" required placeholder="Especifique qué productos faltaron y otros detalles..."></textarea>
            </div>
            <button type="submit" class="btn btn-warning">Guardar</button>
            <button type="button" class="btn btn-secondary" onclick="ocultarFormFaltante()">Cancelar</button>
        </form>
    </div>
</div>
<?php endif; ?>

<h3 style="margin-bottom: 15px;">Productos del Pedido</h3>

<table>
    <thead>
        <tr>
            <th>Producto Padre</th>
            <th>Variedad</th>
            <th>Stock Actual</th>
            <th>Cant. Solicitada</th>
            <th>Cant. Recibida</th>
            <th>Observaciones</th>
            <?php if ($pedido['estado'] == 'pendiente'): ?>
            <th>Acciones</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($detalles as $detalle): 
            $stockBajo = $detalle['stock'] <= $detalle['stock_minimo'];
        ?>
            <tr>
                <td>
                    <span style="color: #7f8c8d; font-size: 12px;"><?= htmlspecialchars($detalle['producto_padre_nombre']) ?></span>
                </td>
                <td>
                    <?= $stockBajo ? '<span style="color: #e74c3c;">⚠️</span> ' : '' ?>
                    <strong><?= htmlspecialchars($detalle['variedad_nombre']) ?></strong>
                </td>
                <td>
                    <span style="color: <?= $stockBajo ? '#e74c3c' : '#27ae60' ?>; font-weight: bold;">
                        <?= $detalle['stock'] ?>
                    </span>
                    <br>
                    <small style="color: #95a5a6;">Mín: <?= $detalle['stock_minimo'] ?></small>
                </td>
                <td><strong><?= $detalle['cantidad_solicitada'] ?></strong></td>
                <td>
                    <?php if ($detalle['cantidad_recibida'] > 0): ?>
                        <span style="color: #27ae60; font-weight: bold;"><?= $detalle['cantidad_recibida'] ?></span>
                    <?php else: ?>
                        <span style="color: #95a5a6;">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($detalle['observaciones']): ?>
                        <small><?= nl2br(htmlspecialchars($detalle['observaciones'])) ?></small>
                    <?php else: ?>
                        <span style="color: #95a5a6;">-</span>
                    <?php endif; ?>
                </td>
                <?php if ($pedido['estado'] == 'pendiente'): ?>
                <td>
                    <button type="button" class="btn btn-info" style="padding: 5px 10px;" 
                            onclick="mostrarFormRecepcion(<?= $detalle['id'] ?>, '<?= htmlspecialchars($detalle['variedad_nombre']) ?>', <?= $detalle['cantidad_solicitada'] ?>)">
                        Recibir
                    </button>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal para recepción de mercadería -->
<div id="modal_recepcion" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; min-width: 400px;">
        <h3 id="modal_titulo" style="margin-bottom: 15px;">Recibir Mercadería</h3>
        <form method="POST" action="index.php?c=pedido&a=actualizarDetalle&id=<?= $pedido['id'] ?>">
            <input type="hidden" id="detalle_id" name="detalle_id">
            
            <div class="form-group">
                <label for="cantidad_recibida">Cantidad Recibida *</label>
                <input type="number" id="cantidad_recibida" name="cantidad_recibida" min="0" required style="width: 100%; padding: 8px;">
                <small style="color: #666;">El stock se actualizará automáticamente</small>
            </div>
            
            <div class="form-group">
                <label for="obs_recepcion">Observaciones</label>
                <textarea id="obs_recepcion" name="observaciones" rows="3" style="width: 100%; padding: 8px;" placeholder="Notas sobre la recepción..."></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 15px;">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function mostrarFormFaltante() {
    document.getElementById('form_faltante').style.display = 'block';
}

function ocultarFormFaltante() {
    document.getElementById('form_faltante').style.display = 'none';
}

function mostrarFormRecepcion(detalleId, nombreVariedad, cantidadSolicitada) {
    document.getElementById('detalle_id').value = detalleId;
    document.getElementById('modal_titulo').textContent = 'Recibir: ' + nombreVariedad;
    document.getElementById('cantidad_recibida').value = cantidadSolicitada;
    document.getElementById('cantidad_recibida').max = cantidadSolicitada * 2; // Permitir recibir hasta el doble
    document.getElementById('modal_recepcion').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('modal_recepcion').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
document.getElementById('modal_recepcion')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>
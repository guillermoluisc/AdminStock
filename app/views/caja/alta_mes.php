<!-- app/views/caja/alta_mes.php -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>💵 Dar de Alta Caja — <?= date('F Y') ?></h2>
    <a href="index.php?c=caja&a=index" class="btn btn-secondary">← Volver</a>
</div>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <h3 style="margin-bottom: 20px; color: #27ae60;">Registrar ingreso de caja</h3>

    <form method="POST" action="index.php?c=caja&a=altaMes">
        <div class="form-group">
            <label for="monto">Monto a ingresar ($)</label>
            <input 
                type="number" 
                id="monto" 
                name="monto" 
                min="0.01" 
                step="0.01" 
                placeholder="Ej: 50000" 
                style="width: 100%; padding: 12px; font-size: 18px;"
                required
                autofocus>
            <small style="color: #666;">Este monto se sumará al total del período actual.</small>
        </div>

        <div class="form-group">
            <label for="descripcion_extra">Descripción (opcional)</label>
            <input 
                type="text" 
                id="descripcion_extra" 
                name="descripcion_extra" 
                placeholder="Ej: Puesta inicial, cobro pendiente, etc."
                style="width: 100%; padding: 10px;">
        </div>

        <div class="form-actions" style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-success" style="flex: 1; padding: 12px; font-size: 16px;">
                ✅ Guardar Caja del Mes
            </button>
            <a href="index.php?c=caja&a=index" class="btn btn-secondary" style="padding: 12px 20px;">
                Cancelar
            </a>
        </div>
    </form>
</div>
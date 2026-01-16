// app/views/assets/precio-format.js

/**
 * Formatea un input como precio mientras el usuario escribe
 */
function formatearPrecioInput(input) {
    let valor = input.value.replace(/[^\d]/g, '');
    if (valor === '') {
        input.value = '';
        return;
    }
    
    let numero = parseInt(valor);
    input.value = (numero / 100).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Obtiene el valor numérico de un input formateado
 */
function obtenerValorNumerico(input) {
    let valor = input.value.replace(/\./g, '').replace(',', '.');
    return parseFloat(valor) || 0;
}

/**
 * Inicializa todos los inputs de precio en la página
 */
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[type="number"][step="0.01"]');
    
    inputs.forEach(input => {
        // Al cargar, formatear el valor existente
        if (input.value) {
            let valor = parseFloat(input.value);
            input.value = valor.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
        
        // Al escribir
        input.addEventListener('blur', function() {
            if (this.value) {
                let valor = obtenerValorNumerico(this);
                this.value = valor.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        });
        
        // Al enfocar, mostrar sin formato para edición fácil
        input.addEventListener('focus', function() {
            if (this.value) {
                let valor = obtenerValorNumerico(this);
                this.value = valor;
            }
        });
    });
});
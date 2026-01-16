<?php
// app/helpers/FormatHelper.php
class FormatHelper {
    /**
     * Formatea un número como precio con separador de miles y 2 decimales
     * Ejemplo: 1234.56 -> $1.234,56
     */
    public static function precio($valor) {
        return '$' . number_format($valor, 2, ',', '.');
    }
    
    /**
     * Formatea un número sin el símbolo de peso
     * Ejemplo: 1234.56 -> 1.234,56
     */
    public static function numero($valor) {
        return number_format($valor, 2, ',', '.');
    }
}
?>
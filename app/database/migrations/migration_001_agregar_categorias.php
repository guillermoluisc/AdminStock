<?php
// app/database/migrations/migration_001_agregar_categorias.php
// Ejemplo de cómo crear una migración

return [
    'version' => '001_agregar_categorias',
    'descripcion' => 'Agregar tabla de categorías y relación con artículos',
    'sql' => "
        -- Crear tabla categorias
        CREATE TABLE IF NOT EXISTS categorias (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre VARCHAR(100) NOT NULL,
            descripcion TEXT,
            activo INTEGER DEFAULT 1,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        
        -- Agregar columna categoria_id a articulos
        ALTER TABLE articulos ADD COLUMN categoria_id INTEGER;
        
        -- Insertar categorías por defecto
        INSERT INTO categorias (nombre, descripcion) VALUES 
            ('General', 'Categoría general'),
            ('Electrónica', 'Productos electrónicos'),
            ('Hogar', 'Artículos para el hogar');
    "
];
?>
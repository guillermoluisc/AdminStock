<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donna</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding-top: 80px; }
        .header { background: #da48dc; color: white; padding: 15px 0; position: fixed; top: 0; left: 0; right: 0; z-index: 1000; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .header-content { display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .header-logo { display: flex; align-items: center; }
        .header-logo img { height: 65px; width: auto; object-fit: contain; }
        
        /* Menú de navegación */
        .nav { display: flex; gap: 15px; flex-wrap: wrap; flex: 1; }
        .nav a { color: white; text-decoration: none; padding: 10px 18px; background: rgba(255,255,255,0.2); border-radius: 4px; font-size: 14px; transition: background 0.3s; }
        .nav a:hover { background: rgba(255,255,255,0.3); }
        .nav a.active { background: rgba(255,255,255,0.4); }
        
        /* Botón hamburguesa */
        .menu-toggle { display: none; background: rgba(255,255,255,0.2); border: none; color: white; font-size: 24px; padding: 8px 12px; cursor: pointer; border-radius: 4px; }
        .menu-toggle:hover { background: rgba(255,255,255,0.3); }
        
        /* Responsive */
        @media (max-width: 768px) {
            body { padding-top: 70px; }
            .header-logo img { height: 50px; }
            
            .menu-toggle { display: block; }
            
            .nav { 
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                background: #da48dc;
                flex-direction: column;
                padding: 15px 20px;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
            
            .nav.active { max-height: 500px; }
            
            .nav a { 
                display: block;
                width: 100%;
                text-align: left;
                padding: 12px 15px;
                margin-bottom: 5px;
            }
        }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .content { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn { display: inline-block; padding: 10px 20px; text-decoration: none; border-radius: 4px; cursor: pointer; border: none; font-size: 14px; }
        .btn-primary { background: #3498db; color: white; }
        .btn-primary:hover { background: #2980b9; }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #229954; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn-secondary:hover { background: #7f8c8d; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-warning:hover { background: #e67e22; }
        .btn-info { background: #16a085; color: white; }
        .btn-info:hover { background: #138d75; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background: #55345e; color: white; }
        table tr:hover { background: #f5f5f5; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .form-group textarea { min-height: 100px; }
        .form-actions { margin-top: 20px; }
        .checkbox-group { display: flex; align-items: center; }
        .checkbox-group input { width: auto; margin-right: 8px; }
        .badge { padding: 5px 10px; border-radius: 3px; font-size: 12px; color: white; }
        .badge-activo { background: #27ae60; }
        .badge-inactivo { background: #95a5a6; }
        .badge-stock-bajo { background: #e74c3c; }
        .badge-stock-ok { background: #27ae60; }
        .badge-pendiente { background: #f39c12; }
        .badge-realizado { background: #27ae60; }
        .badge-faltante { background: #e74c3c; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .card { background: #ecf0f1; padding: 15px; border-radius: 5px; margin-bottom: 15px; }
        .filtros { background: #ecf0f1; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .filtros form { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
        .filtros .form-group { margin-bottom: 0; flex: 1; min-width: 150px; }
        .precio-input {text-align: right; font-family: 'Courier New', monospace; font-weight: bold; }
        
        @media (max-width: 768px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-content">
                <div class="header-logo">
                    <img src="../public/logo.svg" alt="Logo">
                </div>
                <button class="menu-toggle" onclick="toggleMenu()">☰</button>
                <div class="nav" id="mainNav">
                    <a href="index.php?c=productoPadre&a=index">📁 Productos</a>
                    <a href="index.php?c=variedad&a=index">🏷️ Variedades</a>
                    <a href="index.php?c=venta&a=index">💰 Ventas</a>
                    <a href="index.php?c=venta&a=nueva">➕ Nueva Venta</a>
                    <a href="index.php?c=pedido&a=index">📋 Pedidos</a>
                    <a href="index.php?c=pedido&a=nuevo">🛒 Nuevo Pedido</a>
                    <a href="index.php?c=egreso&a=index">💸 Egresos</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="content">
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?>">
                    <?= htmlspecialchars($_SESSION['mensaje']) ?>
                </div>
                <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
            <?php endif; ?>
            
            <?= $content ?>
        </div>
    </div>
    
    <script>
        function toggleMenu() {
            const nav = document.getElementById('mainNav');
            nav.classList.toggle('active');
        }
        
        // Cerrar menú al hacer clic en un enlace (en móvil)
        document.querySelectorAll('.nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    document.getElementById('mainNav').classList.remove('active');
                }
            });
        });
        
        // Cerrar menú al hacer clic fuera (en móvil)
        document.addEventListener('click', (e) => {
            const nav = document.getElementById('mainNav');
            const toggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth <= 768 && 
                !nav.contains(e.target) && 
                !toggle.contains(e.target) && 
                nav.classList.contains('active')) {
                nav.classList.remove('active');
            }
        });
    </script>
</body>
</html>
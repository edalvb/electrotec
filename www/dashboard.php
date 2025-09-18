<?php
// dashboard.php
// Maquetado del dashboard replicando la imagen proporcionada.
//
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Electrotec</title>
  <link rel="stylesheet" href="css/dashboard.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-logo">i</div>
        <div class="brand-text">
          <div class="brand-title">ELECTROTEC</div>
          <div class="brand-sub">Sistema de certificados</div>
        </div>
      </div>

      <nav class="nav">
        <a class="nav-item active" href="#">Dashboard</a>
        <a class="nav-item" href="#">Certificados</a>
        <a class="nav-item" href="#">Equipos</a>
        <a class="nav-item" href="#">Clientes</a>
        <a class="nav-item highlight" href="#">Gestión de Usuarios</a>
      </nav>
    </aside>

    <main class="main">
      <header class="main-header">
        <div>
          <h2>Dashboard</h2>
          <p class="subtitle">Panel de control y estadísticas</p>
        </div>
        <div class="user">Bienvenido<br><strong>Edward Vásquez <span class="role">ADMIN</span></strong></div>
      </header>

      <section class="metrics">
        <div class="card large">
          <div class="card-icon success">✔</div>
          <div class="card-body">
            <div class="card-title">Certificados emitidos</div>
            <div class="card-sub">Este mes</div>
          </div>
          <div class="card-badge">1</div>
        </div>

        <div class="card large warning">
          <div class="card-icon">⏰</div>
          <div class="card-body">
            <div class="card-title">Próximas calibraciones</div>
            <div class="card-sub">Siguientes 30 días</div>
          </div>
          <div class="card-badge">1</div>
        </div>
      </section>

      <section class="quick-actions">
        <h3>Acciones rápidas</h3>
        <div class="actions-grid">
          <div class="action glass">
            <div class="action-icon plus">+</div>
            <div class="action-body">
              <div class="action-title">Nuevo certificado <span class="muted">Generar</span></div>
              <div class="action-sub">certificado</div>
            </div>
          </div>

          <div class="action glass">
            <div class="action-icon user">👤</div>
            <div class="action-body">
              <div class="action-title">Crear cliente <span class="muted">Añadir nuevo cliente</span></div>
            </div>
          </div>

          <div class="action glass">
            <div class="action-icon gear">⚙</div>
            <div class="action-body">
              <div class="action-title">Crear equipo <span class="muted">Registrar equipo</span></div>
            </div>
          </div>

          <div class="action glass wide">
            <div class="action-icon users">👥</div>
            <div class="action-body">
              <div class="action-title">Gestión de usuarios <span class="muted">Invitar y administrar</span></div>
            </div>
          </div>
        </div>
      </section>

    </main>
  </div>
</body>
</html>

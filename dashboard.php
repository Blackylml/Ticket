<?php
// dashboard.php
require_once 'config/database.php';
verificarSesion();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Tickets IT - Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        body { background-color: #f8f9fa; }
        
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card-dashboard {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .card-dashboard:hover {
            transform: translateY(-5px);
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
        }
        
        .ticket-priority-urgente { border-left: 5px solid var(--danger-color); }
        .ticket-priority-alta { border-left: 5px solid var(--warning-color); }
        .ticket-priority-media { border-left: 5px solid var(--primary-color); }
        .ticket-priority-baja { border-left: 5px solid #6c757d; }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
        }
        
        .user-status {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .status-disponible { background-color: var(--success-color); }
        .status-ocupado { background-color: var(--warning-color); }
        .status-ausente { background-color: var(--danger-color); }
        
        .modal-header-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .btn-floating {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .section { display: none; }
        .section.active { display: block; }
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -35px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        
        @media (max-width: 768px) {
            .btn-floating {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
            }
        }
        /* Agregar después de los estilos existentes */
.ticket-estado-abierto { background-color: rgba(13, 110, 253, 0.1); }
.ticket-estado-en_proceso { background-color: rgba(255, 193, 7, 0.1); }
.ticket-estado-resuelto { background-color: rgba(25, 135, 84, 0.1); }
.ticket-estado-cerrado.satisfactoria { background-color: rgba(25, 135, 84, 0.2); }
.ticket-estado-cerrado.insatisfactoria { background-color: rgba(220, 53, 69, 0.1); }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#dashboard">
                <i class="fas fa-headset me-2"></i>
                <strong>Sistema IT</strong>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto" id="mainNav">
                    <?php if ($_SESSION['rol'] === 'it'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#dashboard" onclick="mostrarSeccion('dashboard')">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#todos-tickets" onclick="mostrarSeccion('todos-tickets')">
                                <i class="fas fa-ticket-alt me-1"></i>Todos los Tickets
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#reportes" onclick="mostrarSeccion('reportes')">
                                <i class="fas fa-chart-bar me-1"></i>Reportes
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#dashboard" onclick="mostrarSeccion('dashboard')">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#mis-tickets" onclick="mostrarSeccion('mis-tickets')">
                                <i class="fas fa-list me-1"></i>Mis Tickets
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <span><?= htmlspecialchars($_SESSION['nombre']) ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#perfil"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard Principal -->
    <div id="dashboard" class="section active">
        <div class="container-fluid py-4">
            <!-- Estadísticas -->
            <div class="row mb-4" id="statsCards">
                <!-- Se llenan dinámicamente -->
            </div>
            
            <!-- Estado del Personal IT (Solo visible para usuarios normales) -->
            <?php if ($_SESSION['rol'] === 'solicitante'): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card card-dashboard">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Estado del Personal IT</h5>
                        </div>
                        <div class="card-body">
                            <div class="row" id="personalIT">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Tickets Recientes -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-dashboard">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Tickets Recientes</h5>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary btn-sm active" onclick="filtrarTickets('todos')">Todos</button>
                                <button class="btn btn-outline-primary btn-sm" onclick="filtrarTickets('abierto')">Abiertos</button>
                                <button class="btn btn-outline-primary btn-sm" onclick="filtrarTickets('en_proceso')">En Proceso</button>
                                <button class="btn btn-outline-primary btn-sm" onclick="filtrarTickets('resuelto')">Resueltos</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Prioridad</th>
                                            <th>Estado</th>
                                            <th>Solicitante</th>
                                            <th>Asignado</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ticketsTable">
                                        <!-- Se llena dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Mis Tickets -->
    <div id="mis-tickets" class="section">
        <div class="container-fluid py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-list me-2"></i>Mis Tickets</h2>
                <button class="btn btn-primary" onclick="mostrarModalNuevoTicket()">
                    <i class="fas fa-plus me-2"></i>Nuevo Ticket
                </button>
            </div>
            
            <div class="card card-dashboard">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Asignado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="misTicketsTable">
                                <!-- Se llena dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección Todos los Tickets (Solo IT) -->
    <?php if ($_SESSION['rol'] === 'it'): ?>
    <div id="todos-tickets" class="section">
        <div class="container-fluid py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-ticket-alt me-2"></i>Todos los Tickets</h2>
                <div class="btn-group" role="group">
                    <button class="btn btn-outline-primary active" onclick="filtrarTodosTickets('todos')">Todos</button>
                    <button class="btn btn-outline-primary" onclick="filtrarTodosTickets('abierto')">Abiertos</button>
                    <button class="btn btn-outline-primary" onclick="filtrarTodosTickets('en_proceso')">En Proceso</button>
                    <button class="btn btn-outline-primary" onclick="filtrarTodosTickets('resuelto')">Resueltos</button>
                </div>
            </div>
            
            <div class="card card-dashboard">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Solicitante</th>
                                    <th>Asignado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="todosTicketsTable">
                                <!-- Se llena dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Sección Reportes (Solo IT) -->
    <?php if ($_SESSION['rol'] === 'it'): ?>
    <div id="reportes" class="section">
        <div class="container-fluid py-4">
            <h2 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Reportes</h2>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card card-dashboard">
                        <div class="card-body text-center">
                            <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                            <h5>Reporte General</h5>
                            <p class="text-muted">Exportar todos los tickets a Excel</p>
                            <button class="btn btn-success" onclick="generarReporteExcel('general')">
                                <i class="fas fa-download me-2"></i>Descargar
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card card-dashboard">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-pie fa-3x text-info mb-3"></i>
                            <h5>Por Estado</h5>
                            <p class="text-muted">Reporte filtrado por estado</p>
                            <select class="form-select mb-3" id="filtroEstado">
                                <option value="todos">Todos los estados</option>
                                <option value="abierto">Abiertos</option>
                                <option value="en_proceso">En Proceso</option>
                                <option value="resuelto">Resueltos</option>
                                <option value="cerrado">Cerrados</option>
                            </select>
                            <button class="btn btn-info" onclick="generarReporteExcel('estado')">
                                <i class="fas fa-download me-2"></i>Descargar
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card card-dashboard">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar fa-3x text-warning mb-3"></i>
                            <h5>Por Fecha</h5>
                            <p class="text-muted">Reporte por rango de fechas</p>
                            <input type="date" class="form-control mb-2" id="fechaInicio">
                            <input type="date" class="form-control mb-3" id="fechaFin">
                            <button class="btn btn-warning" onclick="generarReporteExcel('fecha')">
                                <i class="fas fa-download me-2"></i>Descargar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Modal Nuevo Ticket -->
    <div class="modal fade" id="modalNuevoTicket" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Nuevo Ticket</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevoTicket">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Título *</label>
                                <input type="text" class="form-control" id="titulo" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Prioridad</label>
                                <select class="form-select" id="prioridad">
                                    <option value="baja">Baja</option>
                                    <option value="media" selected>Media</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Categoría</label>
                                <select class="form-select" id="categoria">
                                    <option value="Hardware">Hardware</option>
                                    <option value="Software">Software</option>
                                    <option value="Red">Red/Conectividad</option>
                                    <option value="Email">Email</option>
                                    <option value="Impresoras">Impresoras</option>
                                    <option value="Accesos">Accesos/Permisos</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción *</label>
                            <textarea class="form-control" id="descripcion" rows="4" required placeholder="Describe detalladamente el problema..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="crearTicket()">
                        <i class="fas fa-save me-2"></i>Crear Ticket
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Ticket -->
    <div class="modal fade" id="modalVerTicket" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="modalVerTicketTitle"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalVerTicketBody">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer" id="modalVerTicketFooter">
                    <!-- Botones dinámicos -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Resolver Ticket -->
    <div class="modal fade" id="modalResolverTicket" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-check me-2"></i>Resolver Ticket</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Descripción de la resolución</label>
                        <textarea class="form-control" id="resolucionTexto" rows="4" placeholder="Describe cómo se resolvió el problema..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="confirmarResolucion()">
                        <i class="fas fa-check me-2"></i>Marcar como Resuelto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón flotante para nuevo ticket (solo usuarios) -->
    <?php if ($_SESSION['rol'] === 'solicitante'): ?>
    <button class="btn btn-primary btn-floating" onclick="mostrarModalNuevoTicket()">
        <i class="fas fa-plus"></i>
    </button>
    <?php endif; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let usuarioActual = {
            id: <?= $_SESSION['usuario_id'] ?>,
            nombre: '<?= addslashes($_SESSION['nombre']) ?>',
            email: '<?= addslashes($_SESSION['email']) ?>',
            rol: '<?= $_SESSION['rol'] ?>'
        };

        let ticketSeleccionado = null;

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            cargarDashboard();
            configurarFechas();
        });

        function cargarDashboard() {
            cargarEstadisticas();
            cargarTickets();
            if (usuarioActual.rol === 'solicitante') {
                cargarPersonalIT();
            }
        }

        async function cargarEstadisticas() {
            try {
                const response = await fetch('api/tickets.php?action=estadisticas');
                const data = await response.json();
                
                if (data.success) {
                    mostrarEstadisticas(data.data);
                }
            } catch (error) {
                console.error('Error al cargar estadísticas:', error);
            }
        }

        function mostrarEstadisticas(stats) {
            const container = document.getElementById('statsCards');
            let statCards = [];
            
        if (usuarioActual.rol === 'it') {
    statCards = [
        { titulo: 'Total Tickets', valor: stats.total, icono: 'fas fa-ticket-alt', color: 'primary' },
        { titulo: 'Abiertos', valor: stats.abiertos, icono: 'fas fa-folder-open', color: 'danger' },
        { titulo: 'En Proceso', valor: stats.en_proceso, icono: 'fas fa-cog', color: 'warning' },
        { titulo: 'Resueltos', valor: stats.resueltos, icono: 'fas fa-check-circle', color: 'success' },
        { titulo: 'No Resueltos', valor: stats.no_resueltos, icono: 'fas fa-times-circle', color: 'danger' },
        { titulo: 'Pendientes', valor: stats.pendientes, icono: 'fas fa-clock', color: 'info' }
    ];
} else {
    statCards = [
        { titulo: 'Mis Tickets', valor: stats.total, icono: 'fas fa-ticket-alt', color: 'primary' },
        { titulo: 'Abiertos', valor: stats.abiertos, icono: 'fas fa-folder-open', color: 'danger' },
        { titulo: 'En Proceso', valor: stats.en_proceso, icono: 'fas fa-cog', color: 'warning' },
        { titulo: 'Resueltos', valor: stats.resueltos, icono: 'fas fa-check-circle', color: 'success' },
        { titulo: 'No Resueltos', valor: stats.no_resueltos, icono: 'fas fa-times-circle', color: 'danger' },
        { titulo: 'Pendientes', valor: stats.pendientes, icono: 'fas fa-clock', color: 'info' }
    ];
}
            
            container.innerHTML = statCards.map(stat => `
                <div class="col-md-2 mb-4">
                    <div class="card stat-card">
                        <div class="card-body text-center">
                            <i class="${stat.icono} fa-2x mb-3"></i>
                            <h3 class="fw-bold">${stat.valor}</h3>
                            <p class="mb-0">${stat.titulo}</p>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        async function cargarPersonalIT() {
            try {
                const response = await fetch('api/tickets.php?action=personal-it');
                const data = await response.json();
                
                if (data.success) {
                    mostrarPersonalIT(data.data);
                }
            } catch (error) {
                console.error('Error al cargar personal IT:', error);
            }
        }

        function mostrarPersonalIT(personal) {
            const container = document.getElementById('personalIT');
            
            container.innerHTML = personal.map(persona => `
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-user-circle fa-2x mb-2"></i>
                            <h6 class="fw-bold">${persona.nombre}</h6>
                            <span class="badge bg-${persona.estado === 'disponible' ? 'success' : persona.estado === 'ocupado' ? 'warning' : 'danger'}">
                                <span class="user-status status-${persona.estado}"></span>
                                ${persona.estado.charAt(0).toUpperCase() + persona.estado.slice(1)}
                            </span>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        async function cargarTickets() {
            try {
                const response = await fetch('api/tickets.php');
                const data = await response.json();
                
                if (data.success) {
                    mostrarTickets(data.data);
                }
            } catch (error) {
                console.error('Error al cargar tickets:', error);
            }
        }

    function mostrarTickets(tickets) {
   const tableBody = document.getElementById('ticketsTable');
   
   tableBody.innerHTML = tickets.map(ticket => {
       const claseEstado = ticket.estado === 'cerrado' 
           ? `ticket-estado-${ticket.estado} ${ticket.satisfaccion || ''}` 
           : `ticket-estado-${ticket.estado}`;
       
       return `
           <tr class="ticket-priority-${ticket.prioridad} ${claseEstado}" data-estado="${ticket.estado}">
               <td><strong>#${ticket.id}</strong></td>
               <td>${ticket.titulo}</td>
               <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)} status-badge">${ticket.prioridad.toUpperCase()}</span></td>
               <td><span class="badge bg-${obtenerColorEstado(ticket.estado)} status-badge">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
               <td>${ticket.solicitante_nombre}</td>
               <td>${ticket.asignado_nombre || '<span class="text-muted">Sin asignar</span>'}</td>
               <td>${formatearFecha(ticket.fecha_creacion)}</td>
               <td>
                   <button class="btn btn-sm btn-outline-primary" onclick="verTicket(${ticket.id})">
                       <i class="fas fa-eye"></i>
                   </button>
                   ${usuarioActual.rol === 'it' && ticket.estado === 'abierto' ? 
                       `<button class="btn btn-sm btn-outline-success ms-1" onclick="tomarTicket(${ticket.id})">
                           <i class="fas fa-hand-paper"></i>
                       </button>` : ''}
               </td>
           </tr>
       `;
   }).join('');
}


        async function cargarTodosTickets() {
            try {
                const response = await fetch('api/tickets.php?action=todos');
                const data = await response.json();
                
                if (data.success) {
                    mostrarTodosTickets(data.data);
                }
            } catch (error) {
                console.error('Error al cargar todos los tickets:', error);
            }
        }

     function mostrarTodosTickets(tickets) {
   const tableBody = document.getElementById('todosTicketsTable');
   
   tableBody.innerHTML = tickets.map(ticket => {
       const claseEstado = ticket.estado === 'cerrado' 
           ? `ticket-estado-${ticket.estado} ${ticket.satisfaccion || ''}` 
           : `ticket-estado-${ticket.estado}`;
       
       return `
           <tr class="ticket-priority-${ticket.prioridad} ${claseEstado}" data-estado="${ticket.estado}">
               <td><strong>#${ticket.id}</strong></td>
               <td>${ticket.titulo}</td>
               <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)} status-badge">${ticket.prioridad.toUpperCase()}</span></td>
               <td><span class="badge bg-${obtenerColorEstado(ticket.estado)} status-badge">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
               <td>${ticket.solicitante_nombre}</td>
               <td>${ticket.asignado_nombre || '<span class="text-muted">Sin asignar</span>'}</td>
               <td>${formatearFecha(ticket.fecha_creacion)}</td>
               <td>
                   <button class="btn btn-sm btn-outline-primary" onclick="verTicket(${ticket.id})">
                       <i class="fas fa-eye"></i>
                   </button>
                   ${ticket.estado === 'abierto' ? 
                       `<button class="btn btn-sm btn-outline-success ms-1" onclick="tomarTicket(${ticket.id})">
                           <i class="fas fa-hand-paper"></i>
                       </button>` : ''}
               </td>
           </tr>
       `;
   }).join('');
}

        function mostrarModalNuevoTicket() {
            new bootstrap.Modal(document.getElementById('modalNuevoTicket')).show();
        }

        async function crearTicket() {
            const titulo = document.getElementById('titulo').value;
            const descripcion = document.getElementById('descripcion').value;
            const prioridad = document.getElementById('prioridad').value;
            const categoria = document.getElementById('categoria').value;
            
            if (!titulo || !descripcion) {
                mostrarMensaje('Por favor completa todos los campos obligatorios', 'danger');
                return;
            }
            
            try {
                const response = await fetch('api/tickets.php?action=crear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        titulo: titulo,
                        descripcion: descripcion,
                        prioridad: prioridad,
                        categoria: categoria
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Cerrar modal y limpiar formulario
                    bootstrap.Modal.getInstance(document.getElementById('modalNuevoTicket')).hide();
                    document.getElementById('formNuevoTicket').reset();
                    
                    // Recargar datos
                    cargarDashboard();
                    
                    mostrarMensaje('Ticket creado exitosamente', 'success');
                } else {
                    mostrarMensaje(data.message || 'Error al crear el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        async function verTicket(ticketId) {
            try {
                const response = await fetch(`api/tickets.php?action=detalle&id=${ticketId}`);
                const data = await response.json();
                
                if (data.success) {
                    mostrarDetalleTicket(data.data);
                } else {
                    mostrarMensaje(data.message || 'Error al cargar el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        function mostrarDetalleTicket(ticket) {
            const modal = document.getElementById('modalVerTicket');
            const title = document.getElementById('modalVerTicketTitle');
            const body = document.getElementById('modalVerTicketBody');
            const footer = document.getElementById('modalVerTicketFooter');
            
            title.innerHTML = `<i class="fas fa-ticket-alt me-2"></i>Ticket #${ticket.id} - ${ticket.titulo}`;
            
            body.innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <h6><i class="fas fa-info-circle me-2"></i>Detalles del Ticket</h6>
                        <table class="table table-borderless">
                            <tr><td><strong>Título:</strong></td><td>${ticket.titulo}</td></tr>
                            <tr><td><strong>Descripción:</strong></td><td>${ticket.descripcion}</td></tr>
                            <tr><td><strong>Categoría:</strong></td><td>${ticket.categoria}</td></tr>
                            <tr><td><strong>Prioridad:</strong></td><td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span></td></tr>
                            <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td></tr>
                            <tr><td><strong>Solicitante:</strong></td><td>${ticket.solicitante_nombre}</td></tr>
                            <tr><td><strong>Asignado a:</strong></td><td>${ticket.asignado_nombre || '<span class="text-muted">Sin asignar</span>'}</td></tr>
                            <tr><td><strong>Fecha creación:</strong></td><td>${formatearFecha(ticket.fecha_creacion)}</td></tr>
                            ${ticket.fecha_resolucion ? `<tr><td><strong>Fecha resolución:</strong></td><td>${formatearFecha(ticket.fecha_resolucion)}</td></tr>` : ''}
                            ${ticket.resolucion ? `<tr><td><strong>Resolución:</strong></td><td>${ticket.resolucion}</td></tr>` : ''}
                        </table>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-clock me-2"></i>Historial</h6>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Ticket Creado</h6>
                                    <p class="timeline-text">${formatearFecha(ticket.fecha_creacion)}</p>
                                </div>
                            </div>
                            ${ticket.asignado_nombre ? `
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Asignado</h6>
                                        <p class="timeline-text">Tomado por: ${ticket.asignado_nombre}</p>
                                    </div>
                                </div>
                            ` : ''}
                            ${ticket.estado === 'resuelto' || ticket.estado === 'cerrado' ? `
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Resuelto</h6>
                                        <p class="timeline-text">${formatearFecha(ticket.fecha_resolucion)}</p>
                                    </div>
                                </div>
                            ` : ''}
                            ${ticket.estado === 'cerrado' ? `
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-secondary"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Cerrado</h6>
                                        <p class="timeline-text">Satisfacción: ${ticket.satisfaccion ? ticket.satisfaccion.toUpperCase() : 'N/A'}</p>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
            
            // Configurar botones del footer según el estado y rol
            let footerButtons = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>';
            
            if (usuarioActual.rol === 'it') {
                if (ticket.estado === 'abierto') {
                    footerButtons = `
                        <button type="button" class="btn btn-success" onclick="tomarTicket(${ticket.id})">
                            <i class="fas fa-hand-paper me-2"></i>Tomar Ticket
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    `;
                } else if (ticket.estado === 'en_proceso' && ticket.asignado_a == usuarioActual.id) {
                    footerButtons = `
                        <button type="button" class="btn btn-success" onclick="mostrarModalResolver(${ticket.id})">
                            <i class="fas fa-check me-2"></i>Marcar como Resuelto
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    `;
                }
            } else if (usuarioActual.rol === 'solicitante' && ticket.solicitante_id == usuarioActual.id) {
                if (ticket.estado === 'resuelto') {
                    footerButtons = `
                        <button type="button" class="btn btn-success" onclick="cerrarTicket(${ticket.id}, 'satisfactoria')">
                            <i class="fas fa-thumbs-up me-2"></i>Satisfactoria
                        </button>
                        <button type="button" class="btn btn-warning" onclick="cerrarTicket(${ticket.id}, 'insatisfactoria')">
                            <i class="fas fa-thumbs-down me-2"></i>Insatisfactoria
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    `;
                }
            }
            
            footer.innerHTML = footerButtons;
            new bootstrap.Modal(modal).show();
        }

        async function tomarTicket(ticketId) {
            try {
                const response = await fetch(`api/tickets.php?action=tomar&id=${ticketId}`, {
                    method: 'POST'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    cargarDashboard();
                    bootstrap.Modal.getInstance(document.getElementById('modalVerTicket'))?.hide();
                    mostrarMensaje('Ticket tomado exitosamente', 'success');
                } else {
                    mostrarMensaje(data.message || 'Error al tomar el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        function mostrarModalResolver(ticketId) {
            ticketSeleccionado = ticketId;
            bootstrap.Modal.getInstance(document.getElementById('modalVerTicket')).hide();
            new bootstrap.Modal(document.getElementById('modalResolverTicket')).show();
        }

        async function confirmarResolucion() {
            if (!ticketSeleccionado) return;
            
            const resolucion = document.getElementById('resolucionTexto').value;
            
            try {
                const response = await fetch(`api/tickets.php?action=resolver&id=${ticketSeleccionado}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        resolucion: resolucion
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalResolverTicket')).hide();
                    document.getElementById('resolucionTexto').value = '';
                    cargarDashboard();
                    mostrarMensaje('Ticket marcado como resuelto', 'success');
                } else {
                    mostrarMensaje(data.message || 'Error al resolver el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        async function cerrarTicket(ticketId, satisfaccion) {
            try {
                const response = await fetch(`api/tickets.php?action=cerrar&id=${ticketId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        satisfaccion: satisfaccion,
                        comentarios: ''
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    cargarDashboard();
                    bootstrap.Modal.getInstance(document.getElementById('modalVerTicket')).hide();
                    mostrarMensaje('Ticket cerrado exitosamente', 'success');
                } else {
                    mostrarMensaje(data.message || 'Error al cerrar el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        function filtrarTickets(estado) {
            event.target.parentNode.querySelectorAll('.btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            const rows = document.querySelectorAll('#ticketsTable tr');
            rows.forEach(row => {
                if (estado === 'todos') {
                    row.style.display = '';
                } else {
                    const estadoTicket = row.getAttribute('data-estado');
                    row.style.display = estadoTicket === estado ? '' : 'none';
                }
            });
        }

        function filtrarTodosTickets(estado) {
            event.target.parentNode.querySelectorAll('.btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            const rows = document.querySelectorAll('#todosTicketsTable tr');
            rows.forEach(row => {
                if (estado === 'todos') {
                    row.style.display = '';
                } else {
                    const estadoTicket = row.getAttribute('data-estado');
                    row.style.display = estadoTicket === estado ? '' : 'none';
                }
            });
        }

        function generarReporteExcel(tipo) {
            let url = 'api/reportes.php?tipo=' + tipo;
            
            if (tipo === 'estado') {
                const estado = document.getElementById('filtroEstado').value;
                url += '&estado=' + estado;
            } else if (tipo === 'fecha') {
                const fechaInicio = document.getElementById('fechaInicio').value;
                const fechaFin = document.getElementById('fechaFin').value;
                
                if (!fechaInicio || !fechaFin) {
                    mostrarMensaje('Por favor selecciona ambas fechas', 'warning');
                    return;
                }
                
                url += '&fecha_inicio=' + fechaInicio + '&fecha_fin=' + fechaFin;
            }
            
            const link = document.createElement('a');
            link.href = url;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            mostrarMensaje('Generando reporte Excel...', 'info');
        }

        function mostrarMensaje(mensaje, tipo) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${tipo} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        function formatearFecha(fecha) {
            if (!fecha) return '-';
            return new Date(fecha).toLocaleString('es-ES');
        }

        function obtenerColorPrioridad(prioridad) {
            switch(prioridad) {
                case 'urgente': return 'danger';
                case 'alta': return 'warning';
                case 'media': return 'info';
                case 'baja': return 'secondary';
                default: return 'secondary';
            }
        }

        function obtenerColorEstado(estado) {
            switch(estado) {
                case 'abierto': return 'primary';
                case 'en_proceso': return 'warning';
                case 'resuelto': return 'success';
                case 'cerrado': return 'secondary';
                default: return 'secondary';
            }
        }

        function configurarFechas() {
            const hoy = new Date().toISOString().split('T')[0];
            const hace30dias = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            
            const fechaInicio = document.getElementById('fechaInicio');
            const fechaFin = document.getElementById('fechaFin');
            
            if (fechaInicio && fechaFin) {
                fechaInicio.value = hace30dias;
                fechaFin.value = hoy;
            }
        }

        // Actualizar datos automáticamente cada 30 segundos
        setInterval(() => {
            cargarDashboard();
        }, 30000);
        function mostrarSeccion(seccion) {
    // Ocultar todas las secciones
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    
    // Mostrar la sección seleccionada
    document.getElementById(seccion).classList.add('active');
    
    // Cargar contenido específico
    if (seccion === 'mis-tickets') {
        cargarMisTickets();
    } else if (seccion === 'todos-tickets') {
        cargarTodosTickets();
    }
}

async function cargarMisTickets() {
    try {
        const response = await fetch('api/tickets.php?action=mis-tickets');
        const data = await response.json();
        
        if (data.success) {
            mostrarMisTickets(data.data);
        }
    } catch (error) {
        console.error('Error al cargar mis tickets:', error);
    }
}

function mostrarMisTickets(tickets) {
   const tableBody = document.getElementById('misTicketsTable');
   
   tableBody.innerHTML = tickets.map(ticket => {
       const claseEstado = ticket.estado === 'cerrado' 
           ? `ticket-estado-${ticket.estado} ${ticket.satisfaccion || ''}` 
           : `ticket-estado-${ticket.estado}`;
       
       return `
           <tr class="ticket-priority-${ticket.prioridad} ${claseEstado}">
               <td><strong>#${ticket.id}</strong></td>
               <td>${ticket.titulo}</td>
               <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)} status-badge">${ticket.prioridad.toUpperCase()}</span></td>
               <td><span class="badge bg-${obtenerColorEstado(ticket.estado)} status-badge">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
               <td>${ticket.asignado_nombre || '<span class="text-muted">Sin asignar</span>'}</td>
               <td>${formatearFecha(ticket.fecha_creacion)}</td>
               <td>
                   <button class="btn btn-sm btn-outline-primary" onclick="verTicket(${ticket.id})">
                       <i class="fas fa-eye"></i> Ver
                   </button>
               </td>
           </tr>
       `;
   }).join('');
}
    </script>
</body>
</html>
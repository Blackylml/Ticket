<?php
// admin.php - Panel de administración para personal IT
require_once 'config/database.php';
iniciarSesion();

// Verificar login de administrador
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, nombre, email, rol, password FROM usuarios WHERE email = ? AND rol = 'it'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_nombre'] = $row['nombre'];
            $_SESSION['admin_email'] = $row['email'];
            
            // Actualizar último acceso
            $updateQuery = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(1, $row['id']);
            $updateStmt->execute();
            
            header("Location: admin.php");
            exit();
        } else {
            $error_message = 'Credenciales incorrectas';
        }
    } else {
        $error_message = 'Usuario no encontrado o no tiene permisos de administrador';
    }
}

// Verificar si ya está logueado como admin
$isLoggedIn = isset($_SESSION['admin_id']);

// Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_nombre']);
    unset($_SESSION['admin_email']);
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Sistema IT</title>
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
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .modal-header-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        /* Timeline styles */
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
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
        }

        .timeline-content {
            padding-left: 10px;
        }

        .timeline-title {
            font-size: 0.9rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .timeline-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 6px;
            bottom: 0;
            width: 2px;
            background-color: #dee2e6;
        }
    </style>
</head>
<body>
    <?php if (!$isLoggedIn): ?>
    <!-- Login Form -->
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                            <h2 class="fw-bold text-dark">Administración IT</h2>
                            <p class="text-muted">Panel de Control</p>
                        </div>

                        <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> <?= $error_message ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="admin_login" value="1">
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Acceder
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Sistema
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Admin Panel -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt me-2"></i>
                <strong>Panel IT</strong>
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <span><?= htmlspecialchars($_SESSION['admin_nombre']) ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?logout=1"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Estadísticas -->
        <div class="row mb-4" id="statsCards">
            <!-- Se llenan dinámicamente -->
        </div>
        
        <!-- Navegación por pestañas -->
        <ul class="nav nav-tabs mb-4" id="mainTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="todos-tickets-tab" data-bs-toggle="tab" data-bs-target="#todos-tickets" type="button" role="tab">
                    <i class="fas fa-ticket-alt me-2"></i>Todos los Tickets
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reportes-tab" data-bs-toggle="tab" data-bs-target="#reportes" type="button" role="tab">
                    <i class="fas fa-chart-bar me-2"></i>Reportes
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="mainTabsContent">
            <!-- Dashboard -->
            <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-dashboard">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>Tickets Recientes</h5>
                                <div class="d-flex gap-2">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-primary btn-sm active" onclick="filtrarTickets('todos')">Todos</button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="filtrarTickets('abierto')">Abiertos</button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="filtrarTickets('en_proceso')">En Proceso</button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="filtrarTickets('resuelto')">Resueltos</button>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-secondary btn-sm active" onclick="filtrarTicketsPorMes('todos')" id="btnFiltroMesTodos">
                                            <i class="fas fa-list me-1"></i>Todos
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="filtrarTicketsPorMes('mes_actual')" id="btnFiltroMesActual">
                                            <i class="fas fa-calendar-day me-1"></i>Este Mes
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Prioridad</th>
                                                <th>Estado</th>
                                                <th>Solicitante</th>
                                                <th>Ficha</th>
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

            <!-- Todos los Tickets -->
            <div class="tab-pane fade" id="todos-tickets" role="tabpanel">
                <div class="card card-dashboard">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Gestión de Tickets</h5>
                        <div class="d-flex gap-2">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary active" onclick="filtrarTodosTickets('todos')">Todos</button>
                                <button class="btn btn-outline-primary" onclick="filtrarTodosTickets('abierto')">Abiertos</button>
                                <button class="btn btn-outline-primary" onclick="filtrarTodosTickets('en_proceso')">En Proceso</button>
                                <button class="btn btn-outline-primary" onclick="filtrarTodosTickets('resuelto')">Resueltos</button>
                            </div>
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-secondary active" onclick="filtrarTodosTicketsPorMes('todos')" id="btnFiltroMesTodosTodos">
                                    <i class="fas fa-list me-1"></i>Todos
                                </button>
                                <button class="btn btn-outline-secondary" onclick="filtrarTodosTicketsPorMes('mes_actual')" id="btnFiltroMesTodosMesActual">
                                    <i class="fas fa-calendar-day me-1"></i>Este Mes
                                </button>
                            </div>
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
                                        <th>Ficha</th>
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

            <!-- Reportes -->
            <div class="tab-pane fade" id="reportes" role="tabpanel">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let usuarioActual = {
            id: <?= $_SESSION['admin_id'] ?>,
            nombre: '<?= addslashes($_SESSION['admin_nombre']) ?>',
            email: '<?= addslashes($_SESSION['admin_email']) ?>',
            rol: 'it'
        };

        let ticketSeleccionado = null;
        let ticketsDashboard = []; // Almacenar tickets del dashboard
        let ticketsCompletos = []; // Almacenar todos los tickets

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            cargarDashboard();
            configurarFechas();
        });

        function cargarDashboard() {
            cargarEstadisticas();
            cargarTickets();
        }

        async function cargarEstadisticas() {
            try {
                const response = await fetch('api/tickets_admin.php?action=estadisticas');
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
            const statCards = [
                { titulo: 'Total Tickets', valor: stats.total, icono: 'fas fa-ticket-alt', color: 'primary' },
                { titulo: 'Abiertos', valor: stats.abiertos, icono: 'fas fa-folder-open', color: 'danger' },
                { titulo: 'En Proceso', valor: stats.en_proceso, icono: 'fas fa-cog', color: 'warning' },
                { titulo: 'Resueltos', valor: stats.resueltos, icono: 'fas fa-check-circle', color: 'success' },
                { titulo: 'No Resueltos', valor: stats.no_resueltos, icono: 'fas fa-times-circle', color: 'danger' },
                { titulo: 'Pendientes', valor: stats.pendientes, icono: 'fas fa-clock', color: 'info' }
            ];
            
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

        async function cargarTickets() {
            try {
                const response = await fetch('api/tickets_admin.php');
                const data = await response.json();
                
                if (data.success) {
                    mostrarTickets(data.data);
                }
            } catch (error) {
                console.error('Error al cargar tickets:', error);
            }
        }

        function mostrarTickets(tickets) {
            ticketsDashboard = tickets; // Guardar tickets para filtrado posterior
            const tableBody = document.getElementById('ticketsTable');

            tableBody.innerHTML = tickets.map(ticket => `
                <tr class="ticket-priority-${ticket.prioridad}" data-estado="${ticket.estado}" data-fecha="${ticket.fecha_creacion}">
                    <td>${ticket.titulo}</td>
                    <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)} status-badge">${ticket.prioridad.toUpperCase()}</span></td>
                    <td><span class="badge bg-${obtenerColorEstado(ticket.estado)} status-badge">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
                    <td>${ticket.solicitante_nombre}</td>
                    <td>${ticket.numero_ficha}</td>
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
            `).join('');
        }

        // Event listener para cambio de pestaña
        document.getElementById('todos-tickets-tab').addEventListener('shown.bs.tab', function() {
            cargarTodosTickets();
        });

        async function cargarTodosTickets() {
            try {
                const response = await fetch('api/tickets_admin.php?action=todos');
                const data = await response.json();
                
                if (data.success) {
                    mostrarTodosTickets(data.data);
                }
            } catch (error) {
                console.error('Error al cargar todos los tickets:', error);
            }
        }

        function mostrarTodosTickets(tickets) {
            ticketsCompletos = tickets; // Guardar tickets para filtrado posterior
            const tableBody = document.getElementById('todosTicketsTable');

            tableBody.innerHTML = tickets.map(ticket => `
                <tr class="ticket-priority-${ticket.prioridad}" data-estado="${ticket.estado}" data-fecha="${ticket.fecha_creacion}">
                    <td><strong>#${ticket.id}</strong></td>
                    <td>${ticket.titulo}</td>
                    <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)} status-badge">${ticket.prioridad.toUpperCase()}</span></td>
                    <td><span class="badge bg-${obtenerColorEstado(ticket.estado)} status-badge">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
                    <td>${ticket.solicitante_nombre}</td>
                    <td>${ticket.numero_ficha}</td>
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
            `).join('');
        }

        async function verTicket(ticketId) {
            try {
                const response = await fetch(`api/tickets_admin.php?action=detalle&id=${ticketId}`);
                const data = await response.json();
                
                if (data.success) {
                    // Obtener archivos adjuntos
                    try {
                        const archivosResponse = await fetch(`api/tickets_publico.php?action=detalle&id=${ticketId}`);
                        const archivosData = await archivosResponse.json();
                        
                        if (archivosData.success && archivosData.data.archivos) {
                            data.data.archivos = archivosData.data.archivos;
                        }
                    } catch (error) {
                        console.log('No se pudieron cargar los archivos adjuntos:', error);
                        data.data.archivos = [];
                    }
                    
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
            
            // Preparar archivos adjuntos si existen
            let archivosHtml = '';
            if (ticket.archivos && ticket.archivos.length > 0) {
                archivosHtml = `
                    <div class="col-12 mt-3">
                        <h6><i class="fas fa-paperclip me-2"></i>Archivos Adjuntos</h6>
                        <div class="row">
                            ${ticket.archivos.map((archivo, index) => {
                                const esImagen = ['jpg', 'jpeg', 'png', 'gif'].includes(archivo.tipo_archivo.toLowerCase());
                                
                                if (esImagen) {
                                    return `
                                        <div class="col-md-3 mb-3">
                                            <div class="card">
                                                <img src="uploads/${archivo.ruta_archivo}" 
                                                     class="card-img-top" 
                                                     style="height: 150px; object-fit: cover; cursor: pointer;"
                                                     onclick="verImagenCompleta('uploads/${archivo.ruta_archivo}', '${archivo.nombre_archivo}')"
                                                     title="Clic para ver tamaño completo">
                                                <div class="card-body p-2">
                                                    <small class="text-muted">${archivo.nombre_archivo}</small>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                } else {
                                    return `
                                        <div class="col-md-3 mb-3">
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <i class="fas fa-file-alt fa-3x text-primary mb-2"></i>
                                                    <h6 class="card-title">${archivo.nombre_archivo}</h6>
                                                    <a href="uploads/${archivo.ruta_archivo}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download me-1"></i>Descargar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }
                            }).join('')}
                        </div>
                    </div>
                `;
            }
            
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
                            <tr><td><strong>Número de Ficha:</strong></td><td>${ticket.numero_ficha}</td></tr>
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
                        </div>
                    </div>
                    ${archivosHtml}
                </div>
            `;
            
            // Configurar botones del footer según el estado
            let footerButtons = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>';
            
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
            
            footer.innerHTML = footerButtons;
            new bootstrap.Modal(modal).show();
        }

        // Función para ver imagen en tamaño completo
        function verImagenCompleta(rutaImagen, nombreArchivo) {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-image me-2"></i>${nombreArchivo}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="${rutaImagen}" class="img-fluid" style="max-height: 70vh;">
                        </div>
                        <div class="modal-footer">
                            <a href="${rutaImagen}" download="${nombreArchivo}" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Descargar
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            
            // Remover modal del DOM cuando se cierre
            modal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modal);
            });
        }

        async function tomarTicket(ticketId) {
            try {
                const response = await fetch(`api/tickets_admin.php?action=tomar&id=${ticketId}`, {
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
                const response = await fetch(`api/tickets_admin.php?action=resolver&id=${ticketSeleccionado}`, {
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

        function filtrarTicketsPorMes(filtro) {
            // Actualizar botones activos
            document.getElementById('btnFiltroMesTodos').classList.remove('active');
            document.getElementById('btnFiltroMesActual').classList.remove('active');

            if (filtro === 'todos') {
                document.getElementById('btnFiltroMesTodos').classList.add('active');
            } else {
                document.getElementById('btnFiltroMesActual').classList.add('active');
            }

            const ahora = new Date();
            const mesActual = ahora.getMonth();
            const añoActual = ahora.getFullYear();

            let ticketsFiltrados = ticketsDashboard;

            if (filtro === 'mes_actual') {
                ticketsFiltrados = ticketsDashboard.filter(ticket => {
                    const fechaTicket = new Date(ticket.fecha_creacion);
                    return fechaTicket.getMonth() === mesActual && fechaTicket.getFullYear() === añoActual;
                });
            }

            // Mostrar tickets filtrados
            const tableBody = document.getElementById('ticketsTable');
            tableBody.innerHTML = ticketsFiltrados.map(ticket => `
                <tr class="ticket-priority-${ticket.prioridad}" data-estado="${ticket.estado}" data-fecha="${ticket.fecha_creacion}">
                    <td>${ticket.titulo}</td>
                    <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)} status-badge">${ticket.prioridad.toUpperCase()}</span></td>
                    <td><span class="badge bg-${obtenerColorEstado(ticket.estado)} status-badge">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
                    <td>${ticket.solicitante_nombre}</td>
                    <td>${ticket.numero_ficha}</td>
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
            `).join('');
        }

        function filtrarTodosTicketsPorMes(filtro) {
            // Actualizar botones activos
            document.getElementById('btnFiltroMesTodosTodos').classList.remove('active');
            document.getElementById('btnFiltroMesTodosMesActual').classList.remove('active');

            if (filtro === 'todos') {
                document.getElementById('btnFiltroMesTodosTodos').classList.add('active');
            } else {
                document.getElementById('btnFiltroMesTodosMesActual').classList.add('active');
            }

            const ahora = new Date();
            const mesActual = ahora.getMonth();
            const añoActual = ahora.getFullYear();

            let ticketsFiltrados = ticketsCompletos;

            if (filtro === 'mes_actual') {
                ticketsFiltrados = ticketsCompletos.filter(ticket => {
                    const fechaTicket = new Date(ticket.fecha_creacion);
                    return fechaTicket.getMonth() === mesActual && fechaTicket.getFullYear() === añoActual;
                });
            }

            // Mostrar tickets filtrados
            const tableBody = document.getElementById('todosTicketsTable');
            tableBody.innerHTML = ticketsFiltrados.map(ticket => `
                <tr class="ticket-priority-${ticket.prioridad}" data-estado="${ticket.estado}" data-fecha="${ticket.fecha_creacion}">
                    <td><strong>#${ticket.id}</strong></td>
                    <td>${ticket.titulo}</td>
                    <td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)} status-badge">${ticket.prioridad.toUpperCase()}</span></td>
                    <td><span class="badge bg-${obtenerColorEstado(ticket.estado)} status-badge">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td>
                    <td>${ticket.solicitante_nombre}</td>
                    <td>${ticket.numero_ficha}</td>
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
            `).join('');
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
            if (document.querySelector('#dashboard-tab').classList.contains('active')) {
                cargarDashboard();
            } else if (document.querySelector('#todos-tickets-tab').classList.contains('active')) {
                cargarTodosTickets();
            }
        }, 30000);
    </script>
    <?php endif; ?>
</body>
</html>
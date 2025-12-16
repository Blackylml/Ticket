<?php
// index.php - Página principal sin login
require_once 'config/database.php';
iniciarSesion();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Tickets IT</title>
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
        
        body { 
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
        }
        
        .main-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .tab-content {
            min-height: 400px;
        }
        
        .ticket-card {
            border-radius: 10px;
            transition: transform 0.2s;
        }
        
        .ticket-card:hover {
            transform: translateY(-2px);
        }
        
        .ticket-priority-urgente { border-left: 5px solid var(--danger-color); }
        .ticket-priority-alta { border-left: 5px solid var(--warning-color); }
        .ticket-priority-media { border-left: 5px solid var(--primary-color); }
        .ticket-priority-baja { border-left: 5px solid #6c757d; }
        
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
        }
        
        .btn {
            border-radius: 10px;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
   
<div class="container py-4">
    <!-- Header -->
    <div class="text-center mb-4">
        <h1 class="text-white mb-2">
            <i class="fas fa-headset me-2"></i>Sistema de Tickets Para Soporte De TI
        </h1>
        <p class="text-white-50">Gestión de solicitudes de soporte técnico</p>
        
        <!-- Aviso destacado -->
        <div class="alert alert-warning alert-dismissible fade show mx-auto mt-3" style="max-width: 600px; border-left: 5px solid #ffc107;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
                <div>
                    <h6 class="alert-heading mb-2">
                        <i class="fas fa-info-circle me-2"></i>Importante
                    </h6>
                    <p class="mb-0">
                        <strong>Para ver tus tickets:</strong> Usa exactamente la misma combinación de nombre y apellido con la que los creaste.
                    </p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
        <!-- Tarjeta principal -->
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card main-card">
                    <div class="card-body p-4">
                        <!-- Identificación del usuario -->
                        <div class="row mb-4" id="identificacionSection">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Número de Ficha *</label>
                                <input type="text" class="form-control" id="numeroFicha" placeholder="Ingresa tu número de ficha">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre Completo *</label>
                                <input type="text" class="form-control" id="nombreCompleto" placeholder="Ingresa tu nombre completo">
                            </div>
                        </div>

                        <!-- Botones de acceso -->
                        <div class="text-center mb-4">
                            <button class="btn btn-primary me-2" onclick="accederSistema()">
                                <i class="fas fa-sign-in-alt me-2"></i>Acceder al Sistema
                            </button>
                            <button class="btn btn-success" onclick="mostrarFormularioTicket()">
                                <i class="fas fa-plus me-2"></i>Crear Nuevo Ticket
                            </button>
                            <a href="admin.php" class="btn btn-secondary ms-2">
                                <i class="fas fa-cog me-2"></i>Administración
                            </a>
                            <button class="btn btn-primary me-2" onclick="cargartodo()">
                              <i class="fas fa-sync-alt me-2"></i>Actualizar
                            </button>
                        </div>

                        <!-- Contenido dinámico -->
                        <div id="contenidoDinamico" style="display: none;">
                            <!-- Estado del Personal IT -->
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Estado del Personal IT</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="personalIT">
                                        <!-- Se llena dinámicamente -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tabs -->
                            <ul class="nav nav-tabs mb-3" id="mainTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#tickets" type="button" role="tab">
                                        <i class="fas fa-list me-2"></i>Mis Tickets
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="nuevo-tab" data-bs-toggle="tab" data-bs-target="#nuevo" type="button" role="tab">
                                        <i class="fas fa-plus me-2"></i>Nuevo Ticket
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab content -->
                            <div class="tab-content" id="mainTabsContent">
                                <!-- Mis Tickets -->
                                <div class="tab-pane fade show active" id="tickets" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Mis Tickets</h5>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-outline-primary btn-sm active" onclick="filtrarTicketsPorFecha('todos')">
                                                    <i class="fas fa-list me-1"></i>Todos
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm" onclick="filtrarTicketsPorFecha('mes_actual')">
                                                    <i class="fas fa-calendar-day me-1"></i>Este Mes
                                                </button>
                                            </div>
                                            <span class="badge bg-primary" id="totalTickets">0</span>
                                        </div>
                                    </div>
                                    <div id="listaTickets">
                                        <!-- Se llena dinámicamente -->
                                    </div>
                                </div>

                                <!-- Nuevo Ticket -->
                                <div class="tab-pane fade" id="nuevo" role="tabpanel">
                                    <h5 class="mb-3">Crear Nuevo Ticket</h5>
                                    
                                    <!-- Mostrar datos del usuario -->
                                    <div class="alert alert-info mb-3" id="infoUsuario" style="display: none;">
                                        <strong>Usuario:</strong> <span id="mostrarNombre"></span> - 
                                        <strong>Ficha:</strong> <span id="mostrarFicha"></span>
                                    </div>
                                    
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
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Categoría</label>
                                            <select class="form-select" id="categoria">
                                                <option value="Hardware">Hardware</option>
                                                <option value="Software">Software</option>
                                                <option value="Red">Red/Conectividad</option>
                                                <option value="Email">Email</option>
                                                <option value="Impresoras">Impresoras</option>
                                                <option value="Accesos">Accesos/Permisos</option>
                                                                                                <option value="Otro">Desarrollo</option>

                                                <option value="Otro">Otro</option>

                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Descripción *</label>
                                            <textarea class="form-control" id="descripcion" rows="4" required placeholder="Describe detalladamente el problema..."></textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Archivos adjuntos</label>
                                            <input type="file" class="form-control" id="archivos" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                                            <small class="text-muted">Máximo 5MB por archivo. Formatos: JPG, PNG, PDF, DOC, DOCX, TXT</small>
                                        </div>
                                    
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Crear Ticket
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Ticket -->
    <div class="modal fade" id="modalVerTicket" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTicketTitle"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalTicketBody">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer" id="modalTicketFooter">
                    <!-- Botones dinámicos -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let usuarioActual = null;

        async function verificarNombresSimilares(numeroFicha, nombreCompleto) {
            try {
                const response = await fetch(`api/tickets_publico.php?action=verificar-nombre&numero_ficha=${numeroFicha}&nombre=${encodeURIComponent(nombreCompleto)}`);
                const data = await response.json();
                
                if (data.success && data.sugerencia) {
                    return data.sugerencia;
                }
                return null;
            } catch (error) {
                console.error('Error verificando nombres:', error);
                return null;
            }
        }

        async function accederSistema() {
            const numeroFicha = document.getElementById('numeroFicha').value.trim();
            const nombreCompleto = document.getElementById('nombreCompleto').value.trim();
            
            if (!numeroFicha || !nombreCompleto) {
                mostrarMensaje('Por favor completa todos los campos', 'warning');
                return;
            }
            
            // Verificar nombres similares
            const sugerencia = await verificarNombresSimilares(numeroFicha, nombreCompleto);
            if (sugerencia) {
                if (confirm(`¿Quizás quisiste decir "${sugerencia}"?`)) {
                    document.getElementById('nombreCompleto').value = sugerencia;
                    nombreCompleto = sugerencia;
                }
            }
            
            usuarioActual = {
                numeroFicha: numeroFicha,
                nombre: nombreCompleto
            };
            
            document.getElementById('contenidoDinamico').style.display = 'block';
            document.getElementById('identificacionSection').style.display = 'none';
            
            cargarTicketsUsuario();
            cargarPersonalIT();
        }

        function mostrarFormularioTicket() {
            const numeroFicha = document.getElementById('numeroFicha').value.trim();
            const nombreCompleto = document.getElementById('nombreCompleto').value.trim();
            
            if (!numeroFicha || !nombreCompleto) {
                mostrarMensaje('Por favor completa tu número de ficha y nombre antes de crear un ticket', 'warning');
                return;
            }
            
            usuarioActual = {
                numeroFicha: numeroFicha,
                nombre: nombreCompleto
            };
            
            document.getElementById('contenidoDinamico').style.display = 'block';
            document.getElementById('identificacionSection').style.display = 'none';
            
            // Activar tab de nuevo ticket
            const nuevoTab = new bootstrap.Tab(document.getElementById('nuevo-tab'));
            nuevoTab.show();
            
            cargarPersonalIT();
        }

        async function cargarPersonalIT() {
            try {
                const response = await fetch('api/tickets_publico.php?action=personal-it');
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
                <div class="col-md-4 col-lg-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-user-circle fa-2x mb-2 text-primary"></i>
                            <h6 class="fw-bold mb-2">${persona.nombre}</h6>
                            <span class="badge bg-${obtenerColorEstadoIT(persona.estado)} px-3 py-2">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                ${persona.estado.charAt(0).toUpperCase() + persona.estado.slice(1)}
                            </span>
                            ${persona.tickets_activos > 0 ? `<small class="d-block mt-2 text-muted">${persona.tickets_activos} ticket(s) activo(s)</small>` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function obtenerColorEstadoIT(estado) {
            switch(estado) {
                case 'disponible': return 'success';
                case 'ocupado': return 'warning';
                case 'ausente': return 'danger';
                default: return 'secondary';
            }
        }

        async function cargarTicketsUsuario() {
            if (!usuarioActual) return;
            
            try {
                const response = await fetch(`api/tickets_publico.php?action=mis-tickets&numero_ficha=${usuarioActual.numeroFicha}&nombre=${encodeURIComponent(usuarioActual.nombre)}`);
                const data = await response.json();
                
                if (data.success) {
                    mostrarTickets(data.data);
                } else {
                    mostrarMensaje(data.message || 'Error al cargar tickets', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
        }

        let todosLosTickets = []; // Variable global para almacenar todos los tickets

        function mostrarTickets(tickets) {
            todosLosTickets = tickets; // Guardar los tickets para filtrado posterior
            const container = document.getElementById('listaTickets');
            const total = document.getElementById('totalTickets');

            total.textContent = tickets.length;

            if (tickets.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No tienes tickets creados</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = tickets.map(ticket => `
                <div class="card ticket-card ticket-priority-${ticket.prioridad} mb-3" data-fecha="${ticket.fecha_creacion}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="card-title mb-1">#${ticket.id} - ${ticket.titulo}</h6>
                                <p class="card-text text-muted mb-2">${ticket.descripcion.substring(0, 100)}...</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span>
                                    <span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span>
                                    <span class="badge bg-secondary">${ticket.categoria}</span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <small class="text-muted d-block mb-2">${formatearFecha(ticket.fecha_creacion)}</small>
                                <button class="btn btn-outline-primary btn-sm" onclick="verTicket(${ticket.id})">
                                    <i class="fas fa-eye me-1"></i>Ver Detalles
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function filtrarTicketsPorFecha(filtro) {
            // Actualizar botones activos
            event.target.parentNode.querySelectorAll('.btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            const ahora = new Date();
            const mesActual = ahora.getMonth();
            const añoActual = ahora.getFullYear();

            let ticketsFiltrados = todosLosTickets;

            if (filtro === 'mes_actual') {
                ticketsFiltrados = todosLosTickets.filter(ticket => {
                    const fechaTicket = new Date(ticket.fecha_creacion);
                    return fechaTicket.getMonth() === mesActual && fechaTicket.getFullYear() === añoActual;
                });
            }

            // Actualizar contador y mostrar tickets filtrados
            const total = document.getElementById('totalTickets');
            total.textContent = ticketsFiltrados.length;

            const container = document.getElementById('listaTickets');

            if (ticketsFiltrados.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay tickets ${filtro === 'mes_actual' ? 'para este mes' : ''}</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = ticketsFiltrados.map(ticket => `
                <div class="card ticket-card ticket-priority-${ticket.prioridad} mb-3" data-fecha="${ticket.fecha_creacion}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="card-title mb-1">#${ticket.id} - ${ticket.titulo}</h6>
                                <p class="card-text text-muted mb-2">${ticket.descripcion.substring(0, 100)}...</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span>
                                    <span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span>
                                    <span class="badge bg-secondary">${ticket.categoria}</span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <small class="text-muted d-block mb-2">${formatearFecha(ticket.fecha_creacion)}</small>
                                <button class="btn btn-outline-primary btn-sm" onclick="verTicket(${ticket.id})">
                                    <i class="fas fa-eye me-1"></i>Ver Detalles
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

    // Reemplaza la función del formulario en index.php (línea ~348-380)
document.getElementById('formNuevoTicket').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!usuarioActual) {
        mostrarMensaje('Error: Datos de usuario no encontrados', 'danger');
        return;
    }
    
    // Crear FormData para incluir archivos
    const formData = new FormData();
    formData.append('numero_ficha', usuarioActual.numeroFicha);
    formData.append('nombre', usuarioActual.nombre);
    formData.append('titulo', document.getElementById('titulo').value);
    formData.append('descripcion', document.getElementById('descripcion').value);
    formData.append('prioridad', document.getElementById('prioridad').value);
    formData.append('categoria', document.getElementById('categoria').value);
    
    // Agregar archivos si existen
    const archivos = document.getElementById('archivos').files;
    for (let i = 0; i < archivos.length; i++) {
        formData.append('archivos[]', archivos[i]);
    }
    
    try {
        const response = await fetch('api/tickets_publico.php?action=crear', {
            method: 'POST',
            body: formData  // Usar FormData en lugar de JSON
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarMensaje('Ticket creado exitosamente', 'success');
            
            // 🔔 ENVIAR NOTIFICACIÓN USANDO EL MÉTODO QUE FUNCIONA (ANTES del reset)
            const titulo = document.getElementById('titulo').value;
            const categoria = document.getElementById('categoria').value;
            const prioridad = document.getElementById('prioridad').value;
            const nombreSolicitante = usuarioActual.nombre;
            
            const notificationTitle = `🎫 Nuevo Ticket - ${prioridad.toUpperCase()}`;
            const notificationBody = `📁 ${categoria} - ${nombreSolicitante} - ${titulo}`;
            
            console.log('📧 Enviando notificación automática para ticket:', data.ticket_id);
            sendTicketNotification(notificationTitle, notificationBody, {
                ticket_id: data.ticket_id,
                action: 'new_ticket'
            });
            
            // Resetear formulario DESPUÉS de obtener los datos
            document.getElementById('formNuevoTicket').reset();
            
            // Cambiar a la pestaña de tickets y recargar
            const ticketsTab = new bootstrap.Tab(document.getElementById('tickets-tab'));
            ticketsTab.show();
            cargarTicketsUsuario();
        } else {
            mostrarMensaje(data.message || 'Error al crear el ticket', 'danger');
            console.error('Error detallado:', data);
        }
    } catch (error) {
        mostrarMensaje('Error de conexión', 'danger');
        console.error('Error:', error);
    }
});

        async function verTicket(ticketId) {
            try {
                const response = await fetch(`api/tickets_publico.php?action=detalle&id=${ticketId}`);
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
            const title = document.getElementById('modalTicketTitle');
            const body = document.getElementById('modalTicketBody');
            const footer = document.getElementById('modalTicketFooter');
            
            title.innerHTML = `Ticket #${ticket.id} - ${ticket.titulo}`;
            
            let archivosHtml = '';
            if (ticket.archivos && ticket.archivos.length > 0) {
                archivosHtml = `
                    <tr><td colspan="2"><strong>Archivos adjuntos:</strong></td></tr>
                    <tr><td colspan="2">
                        ${ticket.archivos.map(archivo => `
                            <a href="uploads/${archivo.ruta_archivo}" target="_blank" class="btn btn-sm btn-outline-primary me-2 mb-1">
                                <i class="fas fa-paperclip me-1"></i>${archivo.nombre_archivo}
                            </a>
                        `).join('')}
                    </td></tr>
                `;
            }
            
            body.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>Título:</strong></td><td>${ticket.titulo}</td></tr>
                            <tr><td><strong>Descripción:</strong></td><td>${ticket.descripcion}</td></tr>
                            <tr><td><strong>Categoría:</strong></td><td>${ticket.categoria}</td></tr>
                            <tr><td><strong>Prioridad:</strong></td><td><span class="badge bg-${obtenerColorPrioridad(ticket.prioridad)}">${ticket.prioridad.toUpperCase()}</span></td></tr>
                            <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${obtenerColorEstado(ticket.estado)}">${ticket.estado.replace('_', ' ').toUpperCase()}</span></td></tr>
                            ${archivosHtml}
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>Solicitante:</strong></td><td>${ticket.solicitante_nombre}</td></tr>
                            <tr><td><strong>Ficha:</strong></td><td>${ticket.numero_ficha}</td></tr>
                            <tr><td><strong>Asignado a:</strong></td><td>${ticket.asignado_nombre || '<span class="text-muted">Sin asignar</span>'}</td></tr>
                            <tr><td><strong>Fecha creación:</strong></td><td>${formatearFecha(ticket.fecha_creacion)}</td></tr>
                            ${ticket.fecha_resolucion ? `<tr><td><strong>Fecha resolución:</strong></td><td>${formatearFecha(ticket.fecha_resolucion)}</td></tr>` : ''}
                            ${ticket.resolucion ? `<tr><td><strong>Resolución:</strong></td><td>${ticket.resolucion}</td></tr>` : ''}
                        </table>
                    </div>
                </div>
            `;
            
            let footerButtons = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>';
            
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
            
            footer.innerHTML = footerButtons;
            new bootstrap.Modal(modal).show();
        }

        async function cerrarTicket(ticketId, satisfaccion) {
            try {
                const response = await fetch(`api/tickets_publico.php?action=cerrar&id=${ticketId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        satisfaccion: satisfaccion
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarMensaje('Ticket cerrado exitosamente', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalVerTicket')).hide();
                    cargarTicketsUsuario();
                } else {
                    mostrarMensaje(data.message || 'Error al cerrar el ticket', 'danger');
                }
            } catch (error) {
                mostrarMensaje('Error de conexión', 'danger');
            }
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
        
        
        // Función para probar notificaciones
        async function testNotification() {
            try {
                mostrarMensaje('⏳ Enviando notificación de prueba...', 'info');
                
                const response = await fetch('http://192.168.1.134:5214/web/Ticket/api.php/api/test_notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        'title': '🧪 Test desde Web',
                        'body': 'Esta es una notificación de prueba desde la página web - ' + new Date().toLocaleTimeString()
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    mostrarMensaje('✅ Notificación de prueba enviada correctamente!', 'success');
                    console.log('Test notification response:', data);
                } else {
                    mostrarMensaje('❌ Error enviando notificación: ' + (data.error || 'Error desconocido'), 'danger');
                }
                
            } catch (error) {
                console.error('Error:', error);
                mostrarMensaje('❌ Error de conexión: ' + error.message, 'danger');
            }
        }
        
        // Función para enviar notificación de ticket usando el mismo método exitoso
        async function sendTicketNotification(title, body, data = {}) {
            try {
                console.log('📧 Enviando notificación de ticket...', { title, body, data });
                
                const response = await fetch('http://192.168.1.134:5214/web/Ticket/api.php/api/test_notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        'title': title,
                        'body': body
                    })
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    console.log('✅ Notificación de ticket enviada exitosamente!', result);
                } else {
                    console.error('❌ Error enviando notificación de ticket:', result);
                }
                
                return response.ok;
                
            } catch (error) {
                console.error('❌ Error enviando notificación de ticket:', error);
                return false;
            }
        }
       
        // Actualizar personal IT cada 30 segundos
        function cargartodo(){
            cargarPersonalIT();
            accederSistema();
        }
       
    </script>
</body>
</html>
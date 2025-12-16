<?php
// ===== ARCHIVO:  =====
require_once '../config/database.php';
iniciarSesion();
verificarSesion();

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch($method) {
    case 'GET':
        if ($action === 'mis-tickets') {
            obtenerMisTickets($db);
        } elseif ($action === 'todos') {
            obtenerTodosTickets($db);
        } elseif ($action === 'estadisticas') {
            obtenerEstadisticas($db);
        } elseif ($action === 'personal-it') {
            obtenerPersonalIT($db);
        } elseif ($action === 'detalle' && isset($_GET['id'])) {
            obtenerDetalleTicket($db, $_GET['id']);
        } else {
            obtenerTickets($db);
        }
        break;
        
    case 'POST':
        if ($action === 'crear') {
            crearTicket($db);
        } elseif ($action === 'tomar' && isset($_GET['id'])) {
            tomarTicket($db, $_GET['id']);
        } elseif ($action === 'resolver' && isset($_GET['id'])) {
            resolverTicket($db, $_GET['id']);
        } elseif ($action === 'cerrar' && isset($_GET['id'])) {
            cerrarTicket($db, $_GET['id']);
        }
        break;
}

function obtenerTickets($db) {
    $query = "SELECT t.*, 
                     u_sol.nombre as solicitante_nombre,
                     u_asig.nombre as asignado_nombre
              FROM tickets t
              LEFT JOIN usuarios u_sol ON t.solicitante_id = u_sol.id
              LEFT JOIN usuarios u_asig ON t.asignado_a = u_asig.id
              ORDER BY t.fecha_creacion DESC";
    
    if ($_SESSION['rol'] === 'solicitante') {
        $query = "SELECT t.*, 
                         u_sol.nombre as solicitante_nombre,
                         u_asig.nombre as asignado_nombre
                  FROM tickets t
                  LEFT JOIN usuarios u_sol ON t.solicitante_id = u_sol.id
                  LEFT JOIN usuarios u_asig ON t.asignado_a = u_asig.id
                  WHERE t.solicitante_id = ?
                  ORDER BY t.fecha_creacion DESC";
    }
    
    $stmt = $db->prepare($query);
    
    if ($_SESSION['rol'] === 'solicitante') {
        $stmt->bindParam(1, $_SESSION['usuario_id']);
    }
    
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $tickets]);
}

function obtenerMisTickets($db) {
    $query = "SELECT t.*, 
                     u_sol.nombre as solicitante_nombre,
                     u_asig.nombre as asignado_nombre
              FROM tickets t
              LEFT JOIN usuarios u_sol ON t.solicitante_id = u_sol.id
              LEFT JOIN usuarios u_asig ON t.asignado_a = u_asig.id
              WHERE t.solicitante_id = ?
              ORDER BY t.fecha_creacion DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $_SESSION['usuario_id']);
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $tickets]);
}

function obtenerTodosTickets($db) {
    $query = "SELECT t.*, 
                     u_sol.nombre as solicitante_nombre,
                     u_asig.nombre as asignado_nombre
              FROM tickets t
              LEFT JOIN usuarios u_sol ON t.solicitante_id = u_sol.id
              LEFT JOIN usuarios u_asig ON t.asignado_a = u_asig.id
              ORDER BY t.fecha_creacion DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $tickets]);
}

function obtenerEstadisticas($db) {
    $stats = [];
    
  if ($_SESSION['rol'] === 'it') {
    $queries = [
        'total' => "SELECT COUNT(*) as count FROM tickets",
        'abiertos' => "SELECT COUNT(*) as count FROM tickets WHERE estado = 'abierto'",
        'en_proceso' => "SELECT COUNT(*) as count FROM tickets WHERE estado = 'en_proceso'",
        'resueltos' => "SELECT COUNT(*) as count FROM tickets WHERE estado = 'cerrado' AND satisfaccion = 'satisfactoria'",
        'no_resueltos' => "SELECT COUNT(*) as count FROM tickets WHERE estado = 'cerrado' AND satisfaccion = 'insatisfactoria'",
        'pendientes' => "SELECT COUNT(*) as count FROM tickets WHERE estado = 'resuelto'"
    ];
} else {
    $userId = $_SESSION['usuario_id'];
    $queries = [
        'total' => "SELECT COUNT(*) as count FROM tickets WHERE solicitante_id = $userId",
        'abiertos' => "SELECT COUNT(*) as count FROM tickets WHERE solicitante_id = $userId AND estado = 'abierto'",
        'en_proceso' => "SELECT COUNT(*) as count FROM tickets WHERE solicitante_id = $userId AND estado = 'en_proceso'",
        'resueltos' => "SELECT COUNT(*) as count FROM tickets WHERE solicitante_id = $userId AND estado = 'cerrado' AND satisfaccion = 'satisfactoria'",
        'no_resueltos' => "SELECT COUNT(*) as count FROM tickets WHERE solicitante_id = $userId AND estado = 'cerrado' AND satisfaccion = 'insatisfactoria'",
        'pendientes' => "SELECT COUNT(*) as count FROM tickets WHERE solicitante_id = $userId AND estado = 'resuelto'"
    ];
}
    
    foreach ($queries as $key => $query) {
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[$key] = $result['count'];
    }
    
    echo json_encode(['success' => true, 'data' => $stats]);
}

function obtenerPersonalIT($db) {
    $query = "SELECT id, nombre, estado FROM usuarios WHERE rol = 'it' ORDER BY nombre";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $personal]);
}

function obtenerDetalleTicket($db, $ticketId) {
    $query = "SELECT t.*, 
                     u_sol.nombre as solicitante_nombre,
                     u_asig.nombre as asignado_nombre
              FROM tickets t
              LEFT JOIN usuarios u_sol ON t.solicitante_id = u_sol.id
              LEFT JOIN usuarios u_asig ON t.asignado_a = u_asig.id
              WHERE t.id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $ticketId);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($ticket) {
        // Verificar permisos
        if ($_SESSION['rol'] === 'solicitante' && $ticket['solicitante_id'] != $_SESSION['usuario_id']) {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos para ver este ticket']);
            return;
        }
        
        echo json_encode(['success' => true, 'data' => $ticket]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ticket no encontrado']);
    }
}

function crearTicket($db) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $titulo = $input['titulo'] ?? '';
    $descripcion = $input['descripcion'] ?? '';
    $prioridad = $input['prioridad'] ?? 'media';
    $categoria = $input['categoria'] ?? '';
    
    if (empty($titulo) || empty($descripcion)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios titulo o descripcion']);
        return;
    }
    
    $query = "INSERT INTO tickets (titulo, descripcion, prioridad, categoria, solicitante_id) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $titulo);
    $stmt->bindParam(2, $descripcion);
    $stmt->bindParam(3, $prioridad);
    $stmt->bindParam(4, $categoria);
    $stmt->bindParam(5, $_SESSION['usuario_id']);
    
    if ($stmt->execute()) {
        $ticketId = $db->lastInsertId();
        echo json_encode(['success' => true, 'message' => 'Ticket creado exitosamente', 'ticket_id' => $ticketId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear el ticket']);
    }
}

function tomarTicket($db, $ticketId) {
    if ($_SESSION['rol'] !== 'it') {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para esta acción']);
        return;
    }
    
    $db->beginTransaction();
    
    try {
        // Verificar que el ticket esté disponible
        $query = "SELECT estado FROM tickets WHERE id = ? AND estado = 'abierto'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $ticketId);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'El ticket no está disponible']);
            $db->rollback();
            return;
        }
        
        // Actualizar ticket
        $query = "UPDATE tickets SET estado = 'en_proceso', asignado_a = ?, fecha_asignacion = NOW() WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $_SESSION['usuario_id']);
        $stmt->bindParam(2, $ticketId);
        $stmt->execute();
        
        // Actualizar estado del usuario IT
        $query = "UPDATE usuarios SET estado = 'ocupado' WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $_SESSION['usuario_id']);
        $stmt->execute();
        
        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Ticket tomado exitosamente']);
        
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'Error al tomar el ticket']);
    }
}

function resolverTicket($db, $ticketId) {
    if ($_SESSION['rol'] !== 'it') {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para esta acción']);
        return;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $resolucion = $input['resolucion'] ?? '';
    
    $db->beginTransaction();
    
    try {
        // Verificar que el ticket le pertenece al usuario IT
        $query = "SELECT asignado_a FROM tickets WHERE id = ? AND estado = 'en_proceso'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $ticketId);
        $stmt->execute();
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$ticket || $ticket['asignado_a'] != $_SESSION['usuario_id']) {
            echo json_encode(['success' => false, 'message' => 'No puedes resolver este ticket']);
            $db->rollback();
            return;
        }
        
        // Actualizar ticket
        $query = "UPDATE tickets SET estado = 'resuelto', resolucion = ?, fecha_resolucion = NOW() WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $resolucion);
        $stmt->bindParam(2, $ticketId);
        $stmt->execute();
        
        // Actualizar estado del usuario IT
        $query = "UPDATE usuarios SET estado = 'disponible' WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $_SESSION['usuario_id']);
        $stmt->execute();
        
        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Ticket resuelto exitosamente']);
        
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'Error al resolver el ticket']);
    }
}

function cerrarTicket($db, $ticketId) {
    $input = json_decode(file_get_contents('php://input'), true);
    $satisfaccion = $input['satisfaccion'] ?? '';
    $comentarios = $input['comentarios'] ?? '';
    
    // Verificar que el ticket le pertenece al solicitante
    $query = "SELECT solicitante_id FROM tickets WHERE id = ? AND estado = 'resuelto'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $ticketId);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ticket || ($ticket['solicitante_id'] != $_SESSION['usuario_id'] && $_SESSION['rol'] !== 'it')) {
        echo json_encode(['success' => false, 'message' => 'No puedes cerrar este ticket']);
        return;
    }
    
    $query = "UPDATE tickets SET estado = 'cerrado', satisfaccion = ?, comentarios_cierre = ?, fecha_cierre = NOW() WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $satisfaccion);
    $stmt->bindParam(2, $comentarios);
    $stmt->bindParam(3, $ticketId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Ticket cerrado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al cerrar el ticket']);
    }
}
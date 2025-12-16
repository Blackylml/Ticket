<?php
// 
require_once '../config/database.php';
iniciarSesion();

// Verificar que esté logueado como admin
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch($method) {
    case 'GET':
        if ($action === 'todos') {
            obtenerTodosTickets($db);
        } elseif ($action === 'estadisticas') {
            obtenerEstadisticas($db);
        } elseif ($action === 'detalle' && isset($_GET['id'])) {
            obtenerDetalleTicket($db, $_GET['id']);
        } else {
            obtenerTicketsRecientes($db);
        }
        break;
        
    case 'POST':
        if ($action === 'tomar' && isset($_GET['id'])) {
            tomarTicket($db, $_GET['id']);
        } elseif ($action === 'resolver' && isset($_GET['id'])) {
            resolverTicket($db, $_GET['id']);
        }
        break;
}

function obtenerTicketsRecientes($db) {
    $query = "SELECT t.*, 
                     u_asig.nombre as asignado_nombre
              FROM tickets t
              LEFT JOIN usuarios u_asig ON t.asignado_a = u_asig.id
              ORDER BY t.fecha_creacion DESC
              LIMIT 50";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $tickets]);
}

function obtenerTodosTickets($db) {
    $query = "SELECT t.*, 
                     u_asig.nombre as asignado_nombre
              FROM tickets t
              LEFT JOIN usuarios u_asig ON t.asignado_a = u_asig.id
              ORDER BY t.fecha_creacion DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $tickets]);
}

function obtenerEstadisticas($db) {
    $stats = [];
    
    $queries = [
        'total' => "SELECT COUNT(*) as count FROM tickets",
        'abiertos' => "SELECT COUNT(*) as count FROM tickets WHERE estado = 'abierto'",
        'en_proceso' => "SELECT COUNT(*) as count FROM tickets WHERE estado = 'en_proceso'",
        'resueltos' => "SELECT COUNT(*) as count FROM tickets WHERE estado = 'cerrado' AND satisfaccion = 'satisfactoria'",
        'no_resueltos' => "SELECT COUNT(*) as count FROM tickets WHERE estado = 'cerrado' AND satisfaccion = 'insatisfactoria'",
        'pendientes' => "SELECT COUNT(*) as count FROM tickets WHERE estado = 'resuelto'"
    ];
    
    foreach ($queries as $key => $query) {
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats[$key] = $result['count'];
    }
    
    echo json_encode(['success' => true, 'data' => $stats]);
}

function obtenerDetalleTicket($db, $ticketId) {
    $query = "SELECT t.*, 
                     u_asig.nombre as asignado_nombre
              FROM tickets t
              LEFT JOIN usuarios u_asig ON t.asignado_a = u_asig.id
              WHERE t.id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $ticketId);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($ticket) {
        echo json_encode(['success' => true, 'data' => $ticket]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ticket no encontrado']);
    }
}

function tomarTicket($db, $ticketId) {
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
        $stmt->bindParam(1, $_SESSION['admin_id']);
        $stmt->bindParam(2, $ticketId);
        $stmt->execute();
        
        // Actualizar estado del usuario IT
        $query = "UPDATE usuarios SET estado = 'ocupado' WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $_SESSION['admin_id']);
        $stmt->execute();
        
        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Ticket tomado exitosamente']);
        
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'Error al tomar el ticket']);
    }
}

function resolverTicket($db, $ticketId) {
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
        
        if (!$ticket || $ticket['asignado_a'] != $_SESSION['admin_id']) {
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
        $stmt->bindParam(1, $_SESSION['admin_id']);
        $stmt->execute();
        
        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Ticket resuelto exitosamente']);
        
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'Error al resolver el ticket']);
    }
}
?>
<?php
// api/tickets_publico.php
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch($method) {
    case 'GET':
        if ($action === 'mis-tickets') {
            obtenerTicketsPorFicha($db);
        } elseif ($action === 'detalle' && isset($_GET['id'])) {
            obtenerDetalleTicket($db, $_GET['id']);
        } elseif ($action === 'verificar-nombre') {
            verificarNombresSimilares($db);
        } elseif ($action === 'personal-it') {
            obtenerPersonalIT($db);
        }
        break;
        
    case 'POST':
        if ($action === 'crear') {
            crearTicket($db);
        } elseif ($action === 'cerrar' && isset($_GET['id'])) {
            cerrarTicket($db, $_GET['id']);
        }
        break;
}

function obtenerTicketsPorFicha($db) {
    $numeroFicha = $_GET['numero_ficha'] ?? '';
    $nombre = $_GET['nombre'] ?? '';
    
    if (empty($numeroFicha) || empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos de identificación']);
        return;
    }
    
    $query = "SELECT t.*, 
                     u_asig.nombre as asignado_nombre
              FROM tickets t
              LEFT JOIN usuarios u_asig ON t.asignado_a = u_asig.id
              WHERE t.numero_ficha = ? AND t.solicitante_nombre = ?
              ORDER BY t.fecha_creacion DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $numeroFicha);
    $stmt->bindParam(2, $nombre);
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $tickets]);
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
        // Obtener archivos adjuntos
        $queryArchivos = "SELECT * FROM archivos_adjuntos WHERE ticket_id = ?";
        $stmtArchivos = $db->prepare($queryArchivos);
        $stmtArchivos->bindParam(1, $ticketId);
        $stmtArchivos->execute();
        $archivos = $stmtArchivos->fetchAll(PDO::FETCH_ASSOC);
        
        $ticket['archivos'] = $archivos;
        
        echo json_encode(['success' => true, 'data' => $ticket]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ticket no encontrado']);
    }
}

function crearTicket($db) {
    try {
        // Debug: Mostrar todos los datos recibidos
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));
        
        $numeroFicha = $_POST['numero_ficha'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $titulo = $_POST['titulo'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $prioridad = $_POST['prioridad'] ?? 'media';
        $categoria = $_POST['categoria'] ?? '';
        
        // Debug: Verificar datos procesados
        $debugData = [
            'numero_ficha' => $numeroFicha,
            'nombre' => $nombre,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'prioridad' => $prioridad,
            'categoria' => $categoria
        ];
        error_log("Datos procesados: " . print_r($debugData, true));
        
        if (empty($numeroFicha) || empty($nombre) || empty($titulo) || empty($descripcion)) {
            $missing = [];
            if (empty($numeroFicha)) $missing[] = 'número de ficha';
            if (empty($nombre)) $missing[] = 'nombre';
            if (empty($titulo)) $missing[] = 'título';
            if (empty($descripcion)) $missing[] = 'descripción';
            
            echo json_encode([
                'success' => false, 
                'message' => 'Faltan: ' . implode(', ', $missing),
                'debug' => $debugData
            ]);
            return;
        }
        
        $db->beginTransaction();
        
        // Crear ticket
        $query = "INSERT INTO tickets (numero_ficha, solicitante_nombre, titulo, descripcion, prioridad, categoria) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $numeroFicha);
        $stmt->bindParam(2, $nombre);
        $stmt->bindParam(3, $titulo);
        $stmt->bindParam(4, $descripcion);
        $stmt->bindParam(5, $prioridad);
        $stmt->bindParam(6, $categoria);
        
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            error_log("SQL Error: " . print_r($errorInfo, true));
            throw new Exception('Error al crear ticket: ' . $errorInfo[2]);
        }
        
        $ticketId = $db->lastInsertId();
        error_log("Ticket creado con ID: " . $ticketId);
        
   // Reemplaza la sección de archivos en la función crearTicket (líneas ~140-180)
// Procesar archivos adjuntos
if (!empty($_FILES['archivos']['name'])) {
    error_log("Procesando archivos adjuntos...");
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        error_log("Carpeta uploads creada");
    }
    
    $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'txt'];
    
    // Manejar múltiples archivos
    $files = $_FILES['archivos'];
    
    // Si es un solo archivo, convertir a array
    if (!is_array($files['name'])) {
        $files = [
            'name' => [$files['name']],
            'tmp_name' => [$files['tmp_name']],
            'size' => [$files['size']],
            'error' => [$files['error']]
        ];
    }
    
    for ($i = 0; $i < count($files['name']); $i++) {
        if (empty($files['name'][$i]) || $files['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }
        
        $fileName = $files['name'][$i];
        $fileTmp = $files['tmp_name'][$i];
        $fileSize = $files['size'][$i];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        error_log("Procesando archivo $i: $fileName, tamaño: $fileSize, ext: $fileExt");
        
        if (!in_array($fileExt, $allowedTypes)) {
            error_log("Archivo $fileName rechazado por extensión");
            continue;
        }
        
        if ($fileSize > 5 * 1024 * 1024) {
            error_log("Archivo $fileName rechazado por tamaño");
            continue;
        }
        
        $newFileName = $ticketId . '_' . time() . '_' . $i . '.' . $fileExt;
        $uploadPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($fileTmp, $uploadPath)) {
            error_log("Archivo movido a: $uploadPath");
            $insertArchivo = "INSERT INTO archivos_adjuntos (ticket_id, nombre_archivo, ruta_archivo, tipo_archivo, tamaño_archivo) VALUES (?, ?, ?, ?, ?)";
            $stmtArchivo = $db->prepare($insertArchivo);
            $stmtArchivo->bindParam(1, $ticketId);
            $stmtArchivo->bindParam(2, $fileName);
            $stmtArchivo->bindParam(3, $newFileName);
            $stmtArchivo->bindParam(4, $fileExt);
            $stmtArchivo->bindParam(5, $fileSize);
            
            if (!$stmtArchivo->execute()) {
                error_log("Error insertando archivo en BD: " . print_r($stmtArchivo->errorInfo(), true));
            } else {
                error_log("Archivo insertado en BD correctamente");
            }
        } else {
            error_log("Error moviendo archivo $fileName");
        }
    }
} else {
            error_log("No hay archivos para procesar");
        }
        
        $db->commit();
        error_log("Transacción completada exitosamente");
        echo json_encode(['success' => true, 'message' => 'Ticket creado exitosamente', 'ticket_id' => $ticketId]);
        
    } catch (Exception $e) {
        $db->rollback();
        error_log("Exception en crearTicket: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

function cerrarTicket($db, $ticketId) {
    $input = json_decode(file_get_contents('php://input'), true);
    $satisfaccion = $input['satisfaccion'] ?? '';
    
    if (empty($satisfaccion)) {
        echo json_encode(['success' => false, 'message' => 'Falta especificar la satisfacción']);
        return;
    }
    
    // Verificar que el ticket esté en estado resuelto
    $query = "SELECT estado FROM tickets WHERE id = ? AND estado = 'resuelto'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $ticketId);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'El ticket no está disponible para cerrar']);
        return;
    }
    
    $query = "UPDATE tickets SET estado = 'cerrado', satisfaccion = ?, fecha_cierre = NOW() WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $satisfaccion);
    $stmt->bindParam(2, $ticketId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Ticket cerrado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al cerrar el ticket']);
    }
}

function verificarNombresSimilares($db) {
    $numeroFicha = $_GET['numero_ficha'] ?? '';
    $nombreNuevo = $_GET['nombre'] ?? '';
    
    if (empty($numeroFicha) || empty($nombreNuevo)) {
        echo json_encode(['success' => false]);
        return;
    }
    
    // Obtener nombres únicos para esa ficha
    $query = "SELECT DISTINCT solicitante_nombre FROM tickets WHERE numero_ficha = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $numeroFicha);
    $stmt->execute();
    $nombresExistentes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $mejorCoincidencia = null;
    $mayorSimilitud = 0;
    
    foreach ($nombresExistentes as $nombreExistente) {
        // Calcular similitud usando levenshtein y similar_text
        $distancia = levenshtein(strtolower($nombreNuevo), strtolower($nombreExistente));
        similar_text(strtolower($nombreNuevo), strtolower($nombreExistente), $porcentaje);
        
        // Si es muy similar pero no exacto
        if ($nombreExistente !== $nombreNuevo && 
            ($distancia <= 3 || $porcentaje > 70)) {
            if ($porcentaje > $mayorSimilitud) {
                $mayorSimilitud = $porcentaje;
                $mejorCoincidencia = $nombreExistente;
            }
        }
    }
    
    if ($mejorCoincidencia && $mayorSimilitud > 70) {
        echo json_encode(['success' => true, 'sugerencia' => $mejorCoincidencia, 'similitud' => $mayorSimilitud]);
    } else {
        echo json_encode(['success' => true, 'sugerencia' => null]);
    }
}

function obtenerPersonalIT($db) {
    $query = "SELECT u.id, u.nombre, u.estado,
                     COUNT(t.id) as tickets_activos
              FROM usuarios u
              LEFT JOIN tickets t ON t.asignado_a = u.id AND t.estado = 'en_proceso'
              WHERE u.rol = 'it'
              GROUP BY u.id, u.nombre, u.estado
              ORDER BY u.nombre";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Actualizar estado automáticamente basado en tickets activos
    foreach ($personal as &$persona) {
        if ($persona['tickets_activos'] > 0) {
            $persona['estado'] = 'ocupado';
            // Actualizar en base de datos
            $updateQuery = "UPDATE usuarios SET estado = 'ocupado' WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(1, $persona['id']);
            $updateStmt->execute();
        } else {
            $persona['estado'] = 'disponible';
            // Actualizar en base de datos
            $updateQuery = "UPDATE usuarios SET estado = 'disponible' WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(1, $persona['id']);
            $updateStmt->execute();
        }
    }
    
    echo json_encode(['success' => true, 'data' => $personal]);
}
?>
<?php
require_once '../config/database.php';
require_once '../vendor/autoload.php'; // Para PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

iniciarSesion();

// Verificar que esté logueado como admin
if (!isset($_SESSION['admin_id'])) {
    die('No tienes permisos para acceder a los reportes');
}

$database = new Database();
$db = $database->getConnection();

$tipo = $_GET['tipo'] ?? 'general';
$filtro_estado = $_GET['estado'] ?? 'todos';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

// Construir consulta según filtros - ACTUALIZADA para nuevos campos
$query = "SELECT t.*, 
                 u_asig.nombre as asignado_nombre
          FROM tickets t
          LEFT JOIN usuarios u_asig ON t.asignado_a = u_asig.id";

$where_conditions = [];
$params = [];

if ($tipo === 'estado' && $filtro_estado !== 'todos') {
    $where_conditions[] = "t.estado = ?";
    $params[] = $filtro_estado;
}

if ($tipo === 'fecha' && $fecha_inicio && $fecha_fin) {
    $where_conditions[] = "DATE(t.fecha_creacion) BETWEEN ? AND ?";
    $params[] = $fecha_inicio;
    $params[] = $fecha_fin;
}

if (!empty($where_conditions)) {
    $query .= " WHERE " . implode(" AND ", $where_conditions);
}

$query .= " ORDER BY t.fecha_creacion DESC";

$stmt = $db->prepare($query);
foreach ($params as $index => $param) {
    $stmt->bindValue($index + 1, $param);
}
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear archivo Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Establecer encabezados - ACTUALIZADOS
$headers = [
    'A1' => 'ID',
    'B1' => 'Número Ficha',
    'C1' => 'Solicitante',
    'D1' => 'Título',
    'E1' => 'Descripción',
    'F1' => 'Prioridad',
    'G1' => 'Categoría',
    'H1' => 'Estado',
    'I1' => 'Asignado',
    'J1' => 'Fecha Creación',
    'K1' => 'Fecha Resolución',
    'L1' => 'Fecha Cierre',
    'M1' => 'Satisfacción',
    'N1' => 'Resolución'
];

foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Estilo para encabezados
$sheet->getStyle('A1:N1')->getFont()->setBold(true);
$sheet->getStyle('A1:N1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('A1:N1')->getFill()->getStartColor()->setARGB('FFCCCCCC');

// Llenar datos - ACTUALIZADO
$row = 2;
foreach ($tickets as $ticket) {
    $sheet->setCellValue('A' . $row, $ticket['id']);
    $sheet->setCellValue('B' . $row, $ticket['numero_ficha']);
    $sheet->setCellValue('C' . $row, $ticket['solicitante_nombre']);
    $sheet->setCellValue('D' . $row, $ticket['titulo']);
    $sheet->setCellValue('E' . $row, $ticket['descripcion']);
    $sheet->setCellValue('F' . $row, strtoupper($ticket['prioridad']));
    $sheet->setCellValue('G' . $row, $ticket['categoria']);
    $sheet->setCellValue('H' . $row, strtoupper(str_replace('_', ' ', $ticket['estado'])));
    $sheet->setCellValue('I' . $row, $ticket['asignado_nombre'] ?: 'Sin asignar');
    $sheet->setCellValue('J' . $row, $ticket['fecha_creacion']);
    $sheet->setCellValue('K' . $row, $ticket['fecha_resolucion'] ?: 'N/A');
    $sheet->setCellValue('L' . $row, $ticket['fecha_cierre'] ?: 'N/A');
    $sheet->setCellValue('M' . $row, $ticket['satisfaccion'] ? strtoupper($ticket['satisfaccion']) : 'Pendiente');
    $sheet->setCellValue('N' . $row, $ticket['resolucion'] ?: 'N/A');
    $row++;
}

// Ajustar ancho de columnas
foreach (range('A', 'N') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Generar nombre de archivo
$fecha_actual = date('Y-m-d_H-i-s');
$nombre_archivo = "reporte_tickets_{$tipo}_{$fecha_actual}.xlsx";

// Configurar headers para descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombre_archivo . '"');
header('Cache-Control: max-age=0');

// Guardar archivo
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
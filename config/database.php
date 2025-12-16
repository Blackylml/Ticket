<?php
// config/database.php
class Database {
    private $host = "127.0.0.1";
    private $db_name = "sistema_tickets_it";
    private $username = "root";
    private $password = "fami123.";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// Funciones globales
function iniciarSesion() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function verificarSesion() {
    iniciarSesion();
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit();
    }
}

function esIT() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'it';
}

function formatearFecha($fecha) {
    if (!$fecha) return '-';
    return date('d/m/Y H:i', strtotime($fecha));
}

function obtenerColorPrioridad($prioridad) {
    switch($prioridad) {
        case 'urgente': return 'danger';
        case 'alta': return 'warning';
        case 'media': return 'info';
        case 'baja': return 'secondary';
        default: return 'secondary';
    }
}

function obtenerColorEstado($estado) {
    switch($estado) {
        case 'abierto': return 'primary';
        case 'en_proceso': return 'warning';
        case 'resuelto': return 'success';
        case 'cerrado': return 'secondary';
        default: return 'secondary';
    }
}
?>
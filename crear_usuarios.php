<?php
// crear_usuarios.php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Usuarios a crear
$usuarios = [
    [
        'nombre' => 'Oscar Romo',
        'email' => 'oeromo@famisa.mx',
        'password' => 'FMI010119',
        'rol' => 'it'
    ],
    [
        'nombre' => 'Marcos Palomo',
        'email' => 'marcosp@famisa.mx',
        'password' => 'Nanoymarkitos26',
        'rol' => 'it'
    ],
    [
        'nombre' => 'Gilberto Treviño',
        'email' => 'gilbertot@famisa.mx',
        'password' => 'lossimpson',
        'rol' => 'it'
    ]
];

try {
    $query = "INSERT INTO usuarios (nombre, email, password, rol, estado) VALUES (?, ?, ?, ?, 'disponible')";
    $stmt = $db->prepare($query);
    
    foreach ($usuarios as $usuario) {
        $passwordHash = password_hash($usuario['password'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(1, $usuario['nombre']);
        $stmt->bindParam(2, $usuario['email']);
        $stmt->bindParam(3, $passwordHash);
        $stmt->bindParam(4, $usuario['rol']);
        
        if ($stmt->execute()) {
            echo "✓ Usuario creado: {$usuario['nombre']} ({$usuario['email']})\n";
        } else {
            echo "✗ Error creando usuario: {$usuario['nombre']}\n";
        }
    }
    
    echo "\n¡Usuarios creados exitosamente!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
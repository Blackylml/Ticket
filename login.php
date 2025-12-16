<?php
// login.php
require_once 'config/database.php';
iniciarSesion();

if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, nombre, email, rol, password FROM usuarios WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['rol'] = $row['rol'];
            
            // Actualizar último acceso
            $updateQuery = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(1, $row['id']);
            $updateStmt->execute();
            
            echo json_encode(['success' => true, 'rol' => $row['rol']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Tickets IT - Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
        }
        .btn-login {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                            <h2 class="fw-bold text-dark">Sistema IT</h2>
                            <p class="text-muted">Gestión de Tickets</p>
                        </div>

                        <div id="mensaje"></div>

                        <form id="loginForm">
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <strong>Usuarios de prueba:</strong><br>
                                Admin: admin@empresa.com<br>
                                Usuario: user@empresa.com<br>
                                Contraseña: password
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const mensajeDiv = document.getElementById('mensaje');
            
            mensajeDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Iniciando sesión...</div>';
            
            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mensajeDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check"></i> Login exitoso. Redirigiendo...</div>';
                    setTimeout(() => window.location.href = 'dashboard.php', 1000);
                } else {
                    mensajeDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ${data.message}</div>`;
                }
            })
            .catch(error => {
                mensajeDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error de conexión</div>';
            });
        });
    </script>
</body>
</html>
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Moderno - Registro de Turnos</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, sans-serif;
            background: #f0f0f0;
        }
        .login-row {
            min-height: 100vh;
        }
        .login-left {
            background: linear-gradient(135deg, rgb(110, 7, 7), rgb(130, 27, 27));
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            width: 50%;
            height: 100vh;
        }
        .login-right {
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 50%;
            height: 100vh;
            box-shadow: -10px 0 20px rgba(0,0,0,0.1);
        }
        .login-form {
            width: 100%;
            max-width: 650px;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 6px;
            height: 55px;
            padding-left: 45px;
            box-shadow: inset 0 2px 3px rgba(0,0,0,0.1);
        }
        .form-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: rgba(110, 7, 7, 0.7);
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: rgba(110, 7, 7, 0.7);
            cursor: pointer;
        }
        .btn-custom {
            background-color: rgb(110, 7, 7);
            color: white;
            padding: 15px 20px;
            width: 100%;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            transition: background-color 0.3s ease-in-out;
        }
        .btn-custom:hover {
            background-color: rgb(130, 27, 27);
        }
        .icon {
            font-size: 120px;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row no-gutters">
            <div class="col-md-6 login-left">
                <i class="fas fa-chalkboard-teacher icon"></i>
                <h1>Bienvenido</h1>
            </div>
            <div class="col-md-6 login-right">
                <form class="login-form" method="POST" action="/Proyectofinal/control-asistencia-docente/controladores/login_controller.php">
                    <h2 class="mb-4">Acceso Docentes</h2>
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo "<div class='alert alert-danger'>{$_SESSION['error']}</div>";
                        unset($_SESSION['error']);
                    }
                    ?>
                    <div class="form-group">
                        <i class="fas fa-user form-icon"></i>
                        <input name="cedula" type="text" class="form-control" placeholder="Usuario o Identificación" required>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-lock form-icon"></i>
                        <input name="password" type="password" class="form-control" id="password" placeholder="Contraseña" required>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                    <button type="submit" class="btn btn-custom" name="btniniciarSesion">Iniciar sesión</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function (e) {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>

<?php
session_start();
include("../admin/config/sbd.php");
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["inicar_sesion"])) {
    // Recibir los datos del usuario
    $email = $_POST["email"];
    $password = $_POST["clave"];

    // Verificar si el usuario existe
    $sql_usuario = $con->prepare("SELECT * FROM usuarios WHERE email = :email");
    $sql_usuario->bindParam(":email", $email);
    $sql_usuario->execute();
    $usuario = $sql_usuario->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if (password_verify($password, $usuario["clave"])) {
            // Iniciar sesión
            $_SESSION["usuario"] = $usuario["id_usuario"];
            $_SESSION["email"] = $usuario["email"];
            $_SESSION["permiso"] = $usuario["id_permiso"];

            // Definir redirección y tipo de éxito
            if ($usuario["id_permiso"] == 1) {
                $redirectUrl = "../admin/admin.php";
            } else {
                $redirectUrl = "../panelUsuario.php";
            }

            // Redirigir con una señal de éxito
            echo "<script>
                    window.location.href = '$redirectUrl?status=success';
                  </script>";
            exit;
        } else {
            // Enviar señal de error
            echo "<script>
                    window.location.href = '../iniciarSesion.php?status=error';
                  </script>";
            exit;
        }
    } else {
        // Enviar señal de error
        echo "<script>
                window.location.href = '../iniciarSesion.php?status=error';
              </script>";
        exit;
    }
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["registrar_usuario"])) {
    // recibir los datos del usuario
    $email = $_POST["email"];
    $password = $_POST["clave"];

    // verificar si el usuario ya existe
    $sql_verificar = $con->prepare("SELECT * FROM usuarios WHERE email = :email");
    $sql_verificar->bindParam(":email", $email);
    $sql_verificar->execute();
    $usuario_existente = $sql_verificar->fetch(PDO::FETCH_ASSOC);

    if ($usuario_existente) {
        echo 
        "<script>
                window.location.href = '../registro.php?status=error';
        </script>";
        exit;
    } else {
        // hashear la contraseña
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // insertar el nuevo usuario en la base de datos
        $sql_insertar = $con->prepare("INSERT INTO usuarios (email, clave) VALUES (:email, :clave)");
        $sql_insertar->bindParam(":email", $email);
        $sql_insertar->bindParam(":clave", $password_hash);
        $sql_insertar->execute();

        echo
        "<script>
            window.location.href = '../registro.php?status=success'; 
        </script>";
    }
}

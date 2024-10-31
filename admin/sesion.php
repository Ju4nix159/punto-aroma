<?php
session_start();
include("../admin/config/sbd.php");
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["inicar_sesion"])) {
    //recibir los datos del usuario
    $email = $_POST["email"];
    $password = $_POST["clave"];
    //verificar si el usuario existe
    $sql_usuario = $con->prepare("SELECT * FROM usuarios WHERE email = :email");
    $sql_usuario->bindParam(":email", $email);
    $sql_usuario->execute();
    $usuario = $sql_usuario->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        /* if (password_verify($password, $usuario["clave"])) { */
        if ($password == $usuario["clave"]) {
            //iniciar sesión
            $_SESSION["usuario"] = $usuario["id_usuario"];
            $_SESSION["email"] = $usuario["email"];
            $_SESSION["permiso"] = $usuario["id_permiso"];
            if ($usuario["id_permiso"] == 1) {
                header("Location: /pa/admin/admin.php");
            } elseif ($usuario["id_permiso"] == 2) {
                header("Location: /pa/panelUsuario.php");
            }
        } else {
            echo
            "<script>
                alert('Usuario o contraseña incorrecta');
                window.location.href = '/pa/iniciarSesion.php'; // Redirigir al login o a la página actual
            </script>";
        }
        exit;
    } else {
        echo
        "<script>
            alert('Usuario o contraseña incorrecta');
            window.location.href = '/pa/iniciarSesion.php'; // Redirigir al login o a la página actual
        </script>";
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
            alert('El usuario ya existe');
            window.location.href = '/pa/registro.php'; // Redirigir al registro o a la página actual
        </script>";
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
            alert('Usuario registrado exitosamente');
            window.location.href = '/pa/iniciarSesion.php'; // Redirigir al login
        </script>";
    }
}

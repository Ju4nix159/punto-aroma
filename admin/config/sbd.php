<?php
    class Database
    {
        private $hostname = "localhost";
        private $database = "prueba2";
        private $username = "root";
        private $password = "";

        /* private $hostname = "127.0.0.1:3306";
        private $database = "u836293947_ayb_sbd";
        private $username = "u836293947_admin";
        private $password = "Aybpruebas1"; */

        
        private $charset = "utf8";


        
        
        function conectar()
        {
            try{
                $conexion = "mysql:host=" . $this->hostname . "; dbname=" . $this->database . ";charset=" . $this->charset;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => FALSE
                ];
                $pdo = new PDO($conexion, $this->username, $this->password,$options);
                return $pdo;
            }catch(PDOException $e){
                echo 'Error conexion: ' . $e->getMessage();
                exit;
            }
        }
    }
    $db = new Database();
    $con = $db->conectar();    
 ?>

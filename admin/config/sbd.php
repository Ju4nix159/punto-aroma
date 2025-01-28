<?php
    class Database
    {
        private $hostname = "localhost";
        /* private $database = "punto_aroma_sbd"; */
        private $database = "prueba";
        private $username = "root";
        private $password = "";
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

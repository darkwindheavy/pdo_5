<?php
class BaseDeDatos {
    private $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=localhost;dbname=daniel_peris_mvc", "root", "");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }

    // Método para obtener la conexión
    public function getConexion() {
        if ($this->conn instanceof PDO) {
            return $this->conn;
        } else {
            throw new Exception("No se pudo obtener la conexión a la base de datos.");
        }
    }

    public function ejecutarConsulta($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("Error en la consulta: " . $e->getMessage());
        }
    }
}



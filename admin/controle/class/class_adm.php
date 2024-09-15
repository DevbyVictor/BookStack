<?php
require_once '../includes/db_connect.php';

class Adm
{
    private $conn;
    private $table_name = "adminstradores";

    public function __construct()
    {
        $this->conn = Conectar::getInstance();
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function getPicture($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Define o administrador como online

}
?>
<?php
ob_start(); // Inicia o buffer de saída
class Conectar extends PDO {
    private static $instancia;
    private $host = "localhost";
    private $usuario = "root";
    private $senha = ""; // senha do banco de dados, se aplicável
    private $db = "bookstack";

    // Construtor
    public function __construct() {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=utf8"; // Adicionado charset=utf8
        parent::__construct($dsn, $this->usuario, $this->senha);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Adicionado para lançar exceções em caso de erro
    }

    // Método estático para obter a instância única da conexão PDO
    public static function getInstance() {
        if (!isset(self::$instancia)) {
            try {
                self::$instancia = new Conectar();
            } catch (PDOException $e) {
                echo 'Erro ao conectar: ' . $e->getMessage();
                exit();
            }
        }
        return self::$instancia;
    }

    // Método para executar consultas SQL
    public function sql($query, $params = []) {
        $stmt = $this->prepare($query);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->execute();
        return $stmt;
    }
}
ob_end_flush(); // Libera o buffer de saída
?>

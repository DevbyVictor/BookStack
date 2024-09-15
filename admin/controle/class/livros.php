<?php
require_once '../includes/db_connect.php';

class Livro {
    private $conn;
    private $table_name = "livros";

    public function __construct() {
        $this->conn = Conectar::getInstance();
    }

    public function getConnection() {
        return $this->conn;
    }

    public function getLivro($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateLivro($id, $titulo, $subtitulo, $sinopse, $autor, $instituicao, $ano, $isbn, $capa_path = null, $categoria, $exemplares, $condicao) {
        $query = "UPDATE " . $this->table_name . " SET titulo = :titulo, subtitulo = :subtitulo, sinopse = :sinopse, autor = :autor, instituicao = :instituicao, ano = :ano, isbn = :isbn, categoria = :categoria, exemplares = :exemplares, condicao = :condicao" . ($capa_path ? ", capa = :capa" : "") . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':subtitulo', $subtitulo);
        $stmt->bindParam(':sinopse', $sinopse);
        $stmt->bindParam(':autor', $autor);
        $stmt->bindParam(':instituicao', $instituicao);
        $stmt->bindParam(':ano', $ano);
        $stmt->bindParam(':isbn', $isbn);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':exemplares', $exemplares);
        $stmt->bindParam(':condicao', $condicao);
        if ($capa_path) {
            $stmt->bindParam(':capa', $capa_path);
        }
        return $stmt->execute();
        
    }
            

    public function deleteLivro($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}


<?php

include_once(__DIR__ . '/../../includes/db_connect.php');

class Usuario {

    private $nome;
    private $email;
    private $senha;
    private $nivel_acesso;
    private $con;

    public function getEmail() {
        return $this->email;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function getNivelAcesso() {
        return $this->nivel_acesso;
    }

    public function setEmail($email): void {
        $this->email = $email;
    }

    public function setSenha($senha): void {
        $this->senha = $senha;
    }
    public function setNivelAcesso($nivel_acesso) {
         $this->nivel_acesso = $nivel_acesso;
    }
    
    public function consultar() {
        try {
            $this->con = Conectar::getInstance();
            $sql = "SELECT * FROM adminstradores WHERE email = ? AND senha = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->bindValue(1, $this->email);
            $stmt->bindValue(2, sha1($this->senha));

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $this->nivel_acesso = $result['nivel_acesso'];
                return $result;
            } else {
                return false;
            }
        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
    }
}
?>

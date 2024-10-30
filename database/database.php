<?php


class Database
{
    private $host = "localhost";
    private $dbname = "blog";
    private $username = "root";
    private $password = "";
    private $pdo;    

    public function __construct()
    {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->dbname,
                $this->username,
                $this->password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Não foi possível conectar ao banco de dados: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}

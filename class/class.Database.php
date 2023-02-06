<?php

/*
This is Class Of Product.
Copyright (c) 2023 AbdulbariSH
*/

class Database
{
    /**
     * IP Or localhost For MySQL Connection 
     * @var String
     */
    private $host = "localhost";

    /**
     * Username For MySQL Connection
     * @var String
     */
    private $user = "root";

    /**
     * Password For MySQL Connection
     * @var String
     */
    private $password = 'A$HpA$$F0rE7er';
    /**
     * Database name 
     * 
     * Default value: store
     * 
     * @var Sting
     */
    private $db_name = "store";
    /**
     * Var That handle MySQL Action
     * @var SQL
     */
    private $conn;

    /**
     * MySQL Connection (PDO)
     * @return PDO|SQL
     */
    public function __construct()
    {
        $this->conn = null;

        try {

            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->user, $this->password);

        } catch (PDOException $exception) {

            die("Connection error: Please Contact With Devoloper Team ");

        }
    }
    /**
     * MySQL Connection (PDO)
     * @return PDO|SQL
     */
    public function getConnection()
    {
        return $this->conn;
    }
}

?>
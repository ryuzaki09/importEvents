<?php

class DB
{
    private static $instance;
    private $pdo;

    private function __construct()
    {
        return $this->connect();
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }


    private function connect()
    {
        $username = Ini::getConfig("database.mysql_username");
        $pwd = Ini::getConfig("database.mysql_password");
        $host = Ini::getConfig("database.mysql_host");
        $dbname = Ini::getConfig("database.mysql_database");

        try {
            $this->pdo = new \PDO("mysql:host=$host; dbname=$dbname", $username, $pwd);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException $e) {
            echo "cannot connect: ".$e->getMessage();
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }

}

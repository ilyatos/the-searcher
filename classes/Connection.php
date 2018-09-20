<?php


class Connection {
    /**
     * @var PDO
     */
    private static $instance;

    private function __construct() {
        $dbConfig = include '../config/db.php';

        self::$_instance = new PDO(
            "mysql:dbname={$dbConfig['name']}; host={$dbConfig['host']}",
            "{$dbConfig['username']}",
            "{$dbConfig['password']}");

        self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    /**
     * Get the instance of Connection class.
     *
     * @return Connection|PDO
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new Connection();
        }

        return self::$instance;
    }
}
<?php


namespace Engine\Traits;

use PDO;

trait ConnectionTrait {

    /**
     * Get the PDO database connection.
     *
     * @return PDO
     */
    protected static function getConnection() {
        static $connection;

        if ($connection === null) {
            $dbConfig = require '../config/db.php';

            try {
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ];

                $connection = new PDO(
                    "mysql:dbname={$dbConfig['name']}; host={$dbConfig['host']}",
                    "{$dbConfig['username']}",
                    "{$dbConfig['password']}",
                    $options);
            } catch (\PDOException $e) {
                $e->getMessage();
            }
        }

        return $connection;
    }
}
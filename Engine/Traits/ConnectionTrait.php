<?php


namespace Engine\Traits;

use Engine\Config\Config;
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
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            try {
                $connection = new PDO(
                    sprintf("mysql:dbname=%s; host=%s; charset=utf8", Config::DB_NAME, Config::DB_HOST),
                    Config::DB_USER,
                    Config::DB_PASSWORD,
                    $options);
            } catch (\PDOException $e) {
                echo $e->getMessage();
                die;
            }
        }

        return $connection;
    }
}
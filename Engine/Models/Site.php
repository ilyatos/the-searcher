<?php


namespace Engine\Models;

use Engine\BaseModel;

class Site extends BaseModel {

    /**
     * @param string $url
     *
     * @return bool
     */
    public static function exists(string $url): bool {
        $connection = self::getConnection();

        $query = $connection->prepare('SELECT * FROM sites WHERE url = :u');

        $query->bindValue(':u', $url);
        $query->execute();

        return $query->rowCount() !== 0;
    }

    /**
     * Insert a given url.
     *
     * @param string $url
     * @param string $title A title of an url
     * @param string $description A description of an url
     * @param string $keywords Keywords of an url
     *
     * @return bool
     */
    public static function insert(string $url, string $title, string $description, string $keywords): bool {
        $connection = self::getConnection();

        $query = $connection->prepare('INSERT INTO sites(url, title, description, keywords) 
                                             VALUES (:u,:t,:d,:k)');

        $query->bindParam(':u', $url);
        $query->bindParam(':t', $title);
        $query->bindParam(':d', $description);
        $query->bindParam(':k', $keywords);

        return $query->execute();
    }
}
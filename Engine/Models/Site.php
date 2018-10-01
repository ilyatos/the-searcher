<?php


namespace Engine\Models;

use Engine\Model;
use PDO;

class Site extends Model {

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
    public static function insertUrl(string $url): bool {
        $connection = self::getConnection();

        $query = $connection->prepare('INSERT INTO sites(url) VALUES (:u)');

        $query->bindParam(':u', $url);

        return $query->execute();
    }


    public static function updateSeoForUrl(string $url, string $title, string $description, string $keywords): bool {
        $connection = self::getConnection();

        $query = $connection->prepare('UPDATE sites SET title=:t, description=:d, keywords=:k WHERE url=:u');

        $query->bindParam(':u', $url);
        $query->bindParam(':t', $title);
        $query->bindParam(':d', $description);
        $query->bindParam(':k', $keywords);

        return $query->execute();
    }

    public static function firstWhithoutSeo() {
        $connection = self::getConnection();

        $query = $connection->prepare('SELECT url FROM sites WHERE title IS NULL AND broken=0 LIMIT 1');

        $query->execute();

        return $query->fetch(PDO::FETCH_LAZY);
    }
}
<?php


namespace Engine\Models;

use Engine\Model;

class Image extends Model {

    /**
     * @param string $url
     *
     * @return bool
     */
    public static function exists(string $url) {
        $connection = self::getConnection();

        $query = $connection->prepare('SELECT * FROM images WHERE imageUrl = :u');

        $query->bindParam(':u', $url);
        $query->execute();

        return $query->rowCount() !== 0;
    }


    /**
     * @param string $url
     * @param string $src
     * @param string $alt
     * @param string $title
     *
     * @return bool
     */
    public static function insert(string $url, string $src, string $alt, string $title): bool {
        $connection = self::getConnection();

        $query = $connection->prepare("INSERT INTO images(siteUrl, imageUrl, alt, title) 
                                             VALUES (:su,:si,:a,:t)");

        $query->bindParam(':su', $url);
        $query->bindParam(':si', $src);
        $query->bindParam(':a', $alt);
        $query->bindParam(':t', $title);

        return $query->execute();
    }
}
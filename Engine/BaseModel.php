<?php


namespace Engine;

use Engine\Traits\ConnectionTrait;
use ICanBoogie\Inflector;
use ReflectionClass;

abstract class BaseModel {
    use ConnectionTrait;

    protected static $table;

    protected static function tableName() {
        if (static::$table === null) {
            $reflection = new ReflectionClass(static::class);

            $inflector = Inflector::get();

            static::$table = $inflector->pluralize(lcfirst($reflection->getShortName()));
        }

        return static::$table;
    }

    public static function all() {
        $connection = static::getConnection();

        $query = $connection->prepare('SELECT * FROM ' . static::tableName());

        $query->execute();

        return $query->fetchAll();
    }

    public static function findOne(int $id) {
        $connection = static::getConnection();

        $query = $connection->prepare('SELECT * FROM ' . static::tableName() . ' WHERE id = :id');

        $query->bindParam(':id', $id);

        $query->execute();

        return $query->fetch();
    }
}
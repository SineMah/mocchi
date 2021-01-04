<?php

namespace Mocchi\Model;

class Db {

    protected static array $instances = [];

    public static function create(string $name)
    {
        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        self::$instances[$name] = new \PDO(
            self::getDsn($name),
            self::env($name, 'user'),
            self::env($name, 'password'),
            $options
        );
    }

    public static function get(string $name): \PDO
    {

        if(!isset(self::$instances[$name])) {

            self::create($name);
        }

        return self::$instances[$name];
    }

    protected static function getDsn($name): string
    {
        $prefix = self::prefix($name);
        $type = env($prefix . '_TYPE');

        if($type !== 'mysql') {

            throw new \Exception(sprintf('type %s not supported', $type));
        }

        return sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            self::env($name, 'host'),
            self::env($name, 'name'),
            self::env($name, 'charset')
        );
    }

    protected static function prefix(string $name): string
    {
        return $prefix = 'DB_' . strtoupper($name);
    }

    protected static function env(string $name, string $field): string
    {
        return env(self::prefix($name) . '_' . strtoupper($field));
    }
}
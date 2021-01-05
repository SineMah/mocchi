<?php

namespace Mocchi\Model;

use App\Container;

class Instance {

    const DB_ALL = 1;
    const DB_ONE = 0;

    protected string $db = '';
    protected string $instance = '';
    protected \PDOStatement $statement;

    public function boot(Container $container) {

        if(isset($container->config('database')[$this->db])) {

            $this->instance = $container->config('database')[$this->db];
        }else {

            throw new \Exception('no config available');
        }
    }

    protected function getDb(): \PDO
    {
        return Db::get($this->instance);
    }

    protected function insert(string $table, array $data): bool
    {
        $fields = array_keys($data);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s);',
            $table,
            implode(',', $fields),
            implode(',', array_map(function($field) {
                return ':' . $field;
            }, $fields)));

        return (bool) $this->execute($sql, $data);
    }

    protected function execute(string $sql, array $params=[])
    {
        $this->statement = $this->getDb()->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        $this->statement->execute($params);

        return $this;
    }

    protected function fetch($mode = Instance::DB_ALL) {
        $res = null;

        switch ($mode) {

            case Instance::DB_ONE:
                $res = $this->statement->fetch(\PDO::FETCH_ASSOC);
                break;
            default:
                $res = $this->statement->fetchAll(\PDO::FETCH_ASSOC);
                break;
        }

        return $res;
    }
}
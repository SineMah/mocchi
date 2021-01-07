<?php

namespace Mocchi\Model;

use App\Container;

class Instance {

    const DB_ALL = 1;
    const DB_ONE = 0;

    protected string $db = '';
    protected string $table = '';
    protected string $instance = '';
    protected array $fields = ['*'];

    protected \PDOStatement $statement;

    public function boot(Container $container) {

        if(isset($container->config('database')[$this->db])) {

            $this->instance = $container->config('database')[$this->db];
        }else {

            throw new \Exception('no config available');
        }
    }

    public function fields(array $fields): Instance
    {

        $this->fields = $fields;

        return $this;
    }

    public function findAll($where=null, array $data=[]): array
    {
        $result = [];

        if(is_string($where)) {
            $sql = sprintf('SELECT %s FROM %s WHERE %s;', implode(',', $this->fields), $this->table, $where);

            if(count($data) === 0) {

                throw new \Exception(sprintf('Data for where %s missing', $where));
            }
        }elseif (is_array($where)) {
            $sql = sprintf('SELECT %s FROM %s WHERE %s;', implode(',', $this->fields), $this->table, $this->buildWhere($where));

            $data = $where;
        }else {
            $sql = sprintf('SELECT %s FROM %s WHERE 1;', implode(',', $this->fields), $this->table);
        }

        $rows = $this->execute($sql, $data)->fetch(Instance::DB_ALL);

        if($rows) {

            $result = $rows;
        }

        return $result;
    }

    public function exists(array $data): bool
    {
        return $this->isFound($this->table, $data);
    }

    public function update($where, array $data): bool
    {
        $fields = array_keys($data);
        $data = array_merge($where, $data);
        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $this->table,
            implode(',', array_map(function($field) {
                return sprintf('%s=:%s', $field, $field);
            }, $fields)),
            $this->buildWhere($where)
        );

        return (bool) $this->execute($sql, $data);
    }

    protected function getDb(): \PDO
    {
        return Db::get($this->instance);
    }

    protected function isFound(string $table, array $where): bool
    {
        $cnt = 0;
        $fields = array_keys($where);
        $sql = sprintf(
            'SELECT COUNT(*) AS cnt FROM %s WHERE %s;',
            $table,
            implode(' AND ', array_map(function($field) {
                return sprintf('%s=:%s', $field, $field);
            }, $fields))
        );

        $row = $this->execute($sql, $where)->fetch(Instance::DB_ONE);

        if($row) {

            $cnt = (int) $row['cnt'];
        }

        return $cnt > 0;
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
            }, $fields))
        );

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

    protected function buildWhere($where): string
    {
        $fields = array_keys($where);

        return implode(' AND ', array_map(function($field) {
            return sprintf('%s=:%s', $field, $field);
        }, $fields));
    }
}
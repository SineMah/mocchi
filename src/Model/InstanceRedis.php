<?php

namespace Mocchi\Model;

use App\Container;

class InstanceRedis {

    public string $uuid;

    protected \Redis $redis;
    protected string $host;
    protected int $port;
    protected int $db;
    protected int $timeout;
    protected int $reconnect;
    protected array $auth = [];

    public function boot(Container $container) {

        $this->host = (string) env('DB_REDIS_HOST', '127.0.0.1');
        $this->port = (int) env('DB_REDIS_PORT', 6379);
        $this->db = (int) env('DB_REDIS_NAME', 0);
        $this->timeout = (int) env('DB_REDIS_TIMEOUT', 1);
        $this->reconnect = (int) env('DB_REDIS_RECONNECT', 250);

        if(env('DB_REDIS_USER') && env('DB_REDIS_PASSWORD')) {

            $this->auth = ['auth' => [env('DB_REDIS_USER'), env('DB_REDIS_PASSWORD')]];
        }

        $this->redis = new \Redis();

        if(count($this->auth) > 0) {

            $this->redis->connect($this->host, $this->port, $this->timeout, null, $this->reconnect, $this->auth);
        }else {

            $this->redis->connect($this->host, $this->port, $this->timeout, null, $this->reconnect);
        }

        $this->redis->select($this->db);
    }

    public function write($data): bool
    {

        if(is_array($data)) {

            $data = \json_encode($data);
        }

        return $this->redis->set($this->uuid, $data);
    }

    public function get()
    {
        $data = $this->redis->get($this->uuid);

        if($json = \json_decode($data, true)) {

            $data = $json;
        }

        return $data;
    }

    public function exists(): bool
    {
        return $this->redis->exists($this->uuid);
    }

    public function delete(): bool
    {
        return $this->redis->del($this->uuid);
    }

    public function ttl(int $ttl)
    {
        return $this->redis->expire($this->uuid, $ttl);
    }
}

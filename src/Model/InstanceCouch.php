<?php

namespace Mocchi\Model;

use GuzzleHttp\Client;
use App\Container;

class InstanceCouch {

    public string $name = 'entry';
    public string $id;

    protected Client $http;
    protected string $host;
    protected string $port;
    protected string $db;
    protected array $auth = [];
    protected array $headers = ['content-type' => 'application/json'];

    public function __construct(string $name = null)
    {

    }

    public function boot(Container $container) {

        $this->http = new Client();

        $this->host = (string) env('DB_COUCH_HOST', '127.0.0.1');
        $this->port = (string) env('DB_COUCH_PORT', 5984);
        $this->db   = (string) env('DB_COUCH_NAME', 'doc_db');
        $this->auth = [
            env('DB_COUCH_USER', ''),
            env('DB_COUCH_PASSWORD', ''),
        ];
    }

    public function write(string $uuid, $data): bool
    {
        $this->id = $uuid;
        return $this->put((array)$data);
    }

    public function getById(string $uuid): array
    {
        $this->id = $uuid;
        return $this->get();
    }

    public function get(array $fields = []): array
    {
        return $this->from($this->name, $this->id, $fields);
    }

    public function from(string $foreignName, string $uuid, $fields = []): array
    {
        $options = [
            'selector' => [
                'id'       => $uuid,
                'doc_type' => $foreignName
            ],
            'limit'    => 1,
            'fields'   => $fields
        ];

        $entries = $this->find($options);

        $entry = reset($entries['docs']);

        // use union types in PHP8 to avoid this
        if (!$entry) {

            $entry = [];
        }

        return $entry;
    }

    protected function find($options)
    {
        $res = $this->request('post', '_find', $options);
        $body = $res->getBody()->getContents();


        return json_decode($body, true);
    }

    protected function findList($options)
    {
        $res = $this->request('post', '_find', $options);
//        Log::stack(['single'])->info('Something happened!'.print_r($res,1));
        $entries = json_decode($res->getBody()->getContents(), true);
//        Log::stack(['single'])->info('Something happened!'.print_r($entries,1));

        return isset($entries['docs']) ? $entries['docs'] : [];
    }

    protected function request(string $method, string $endpoint, array $body)
    {
        return $this->http->$method(
            $this->getEntryPoint($endpoint),
            [
                'body'            => json_encode($body),
                'allow_redirects' => false,
                'timeout'         => 5,
                'auth'            => $this->auth,
                'headers'         => $this->headers
            ]
        );
    }

    public function getList(int $start = 0, int $limit = 10, array $fields = []): array
    {
        $options = [
            'selector' => [
                'doc_type' => [
                    '$eq' => $this->name
                ]
            ],
            'fields'   => $fields
        ];

        if ($start != $limit) {
            $options['skip']  = $start;
            $options['limit'] = $limit;
        }

        return $this->findList($options);
    }

    public function put(array $value): bool
    {
        $value['doc_type'] = $this->name;

        $this->id = $value['id'];

        if ($doc = $this->get(['_rev'])) {
//        if($doc = $this->get()) {
//            $value = array_replace_recursive($doc, $value);
            $value['_rev'] = $doc['_rev'];
        }

        $res = $this->request('put', $value['id'], $value);
        return $res->getStatusCode() === 200;
    }

    public function exists(): bool
    {
        return count($this->get(['id'])) > 0;
    }

    public function update(array $value): bool
    {
        return $this->put($value);
    }

    public function purge(): bool
    {
        $error = false;
        $body  = [];
        $list  = $this->getList(0, $this->count(), ['id', '_rev']);

        foreach ($list as $entry) {
            $this->id        = $entry['id'];
            $body[$this->id] = $this->getRevisions();
        }

        if (count($body) > 0) {
            $res   = $this->request('post', '_purge', $body);
            $error = $res->getStatusCode() !== 200;
        }

        return $error;
    }

    public function getRevisions(): array
    {
        $res = $this->http->get(
//            $this->getEntryPoint($this->id) . '?revs=true',
            $this->getEntryPoint($this->id) . '?revs_info=true',
            [
//                'body'              => json_encode(),
                'timeout' => 5,
                'auth'    => $this->auth,
                'headers' => $this->headers
            ]
        );

        $doc = json_decode($res->getBody()->getContents(), true);

        return array_map(
            function ($info) {

                return $info['rev'];
            },
            $doc['_revs_info']
        );
    }

    public function count(): int
    {
        $options = [
            'selector' => [
                'doc_type' => [
                    '$eq' => $this->name
                ]
            ],
            'skip'     => 0,
            'limit'    => PHP_INT_MAX,
            'fields'   => ['_id']
        ];

        $entries = $this->find($options);
        return !isset($entries['docs']) ? 0 : count($entries['docs']);
    }

    protected function getEntryPoint(string $path = ''): string
    {
        return sprintf('%s:%s/%s/%s', $this->host, $this->port, $this->db, $path);
    }
}

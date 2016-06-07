<?php
namespace CsDavidson\LaravelElastica;

use Elastica\Client;
use Elastica\Type\Mapping;

/**
 * Class ElasticaWrapper
 * @package CsDavidson\LaravelElastica
 */
class ElasticaWrapper
{
    /**
     * @var Client
     */
    protected $client;

    public function initClient(array $config)
    {
        $this->client = new Client($config);
    }

    /**
     * @return Mapping
     */
    public static function mapping()
    {
        return new Mapping();
    }
}

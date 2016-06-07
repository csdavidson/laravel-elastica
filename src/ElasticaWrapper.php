<?php
namespace CsDavidson\LaravelElastica;

use Elastica\Client;
use Elastica\Index;
use Elastica\Response;
use Elastica\Type;
use Elastica\Type\Mapping;
use Psr\Log\LoggerInterface;

/**
 * Class ElasticaWrapper
 * @package CsDavidson\LaravelElastica
 */
class ElasticaWrapper
{
    /**
     * @var Client
     */
    protected static $_client;

    /**
     * @param array $config
     * @param null $callback
     * @param LoggerInterface|null $logger
     */
    public function initClient(array $config, $callback=null, LoggerInterface $logger=null)
    {
        static::$_client = new Client($config, $callback, $logger);
    }

    /**
     * @param $name
     * @param array $index_args
     * @param array $options
     * @return bool|Index
     */
    public static function createIndex($name, array $index_args, $options=['recreate'=>true])
    {
        $index = static::$_client->getIndex($name);

        /**
         * Create the Index on ElasticSearch
         * @var Response $response
         */
        $response = $index->create($index_args, $options);
        
        if ($response->isOk() && !$response->hasError()) {
            return $index;
        }
        
        return false;
    }
    
    /**
     * @param $name
     * @return Index
     */
    public static function index($name)
    {
        return static::$_client->getIndex($name);
    }

    /**
     * @param Type|null $type
     * @param array $properties
     * @return Mapping
     */
    public static function mapping(Type $type=null, array $properties=[])
    {
        return new Mapping($type, $properties);
    }

    /**
     * @param Type $type
     * @param array $mapping_properties
     * @param array $mapping_params
     * @return bool|Mapping
     */
    public static function sendMapping(Type $type, array $mapping_properties=[], array $mapping_params=[])
    {
        $mapping = new Mapping($type, $mapping_properties);

        // Apply params to the mapping
        foreach ($mapping_params as $param_key => $param_value) {
            $mapping->setParam($param_key, $param_value);
        }

        // Send the mapping to ElasticSearch
        $response = $mapping->send();

        if ($response->isOk() && !$response->hasError()) {
            return $mapping;
        }

        return false;
    }

    /**
     * @param Index $index
     * @param $name
     * @return Type
     */
    public static function type(Index $index, $name)
    {
        return new Type($index, $name);
    }
}

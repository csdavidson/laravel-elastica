<?php
namespace CsDavidson\LaravelElastica;

use Elastica\Client;
use Elastica\Document;
use Elastica\Index;
use Elastica\Query;
use Elastica\Response;
use Elastica\ResultSet\BuilderInterface;
use Elastica\Search;
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
     * @param $document_id
     * @param array $document_data
     * @param Type $type
     * @param bool $refresh_index
     * @return bool|\Elastica\Document
     */
    public static function addDocument($document_id, array $document_data=[], Type $type, $refresh_index=true)
    {
        $document = new Document($document_id, $document_data);

        /**
         * @var Response $response
         */
        $response = $type->addDocument($document);

        if ($response->isOk() && !$response->hasError()) {
            // Refresh the index to make the new document searchable
            if ($refresh_index) {
                $type->getIndex()->refresh();
            }

            return $document;
        }

        return false;
    }

    /**
     * @param $name
     * @param array $index_args
     * @param array $options
     * @return bool|\Elastica\Index
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
     * @param $id
     * @param array $data
     * @param Type|null $type
     * @param Index|null $index
     * @return \Elastica\Document
     */
    public static function getDocument($id, $data=[], Type $type=null, Index $index=null)
    {
        if (null === $index && $type instanceof Type) {
            $index = $type->getIndex();
        }

        return new Document($id, $data, $type, $index);
    }

    /**
     * @param $name
     * @return \Elastica\Index
     */
    public static function getIndex($name)
    {
        return static::$_client->getIndex($name);
    }

    /**
     * @param $query
     * @return \Elastica\Query
     */
    public static function getQuery($query=null)
    {
        return new Query($query);
    }

    /**
     * @param Type|null $type
     * @param array $properties
     * @return \Elastica\Type\Mapping
     */
    public static function getMapping(Type $type=null, array $properties=[])
    {
        return new Mapping($type, $properties);
    }

    /**
     * @param Type $type
     * @param Query|null $query
     * @param array $options
     * @param BuilderInterface|null $builder
     * @return \Elastica\Search
     */
    public static function getSearch(Type $type, Query $query=null, array $options=[], BuilderInterface $builder=null)
    {
        $search = new Search(static::$_client, $builder);
        $search->addType($type);
        $search->addIndex($type->getIndex());
        $search->setOptions($options);
        $search->setQuery($query);

        return $search;
    }

    /**
     * @param Index $index
     * @param $name
     * @return \Elastica\Type
     */
    public static function getType(Index $index, $name)
    {
        return new Type($index, $name);
    }

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
     * @param Query $query
     * @param Type $type
     * @param array $options
     * @param BuilderInterface|null $builder
     * @return \Elastica\ResultSet
     */
    public static function search(Query $query, Type $type, array $options=[], BuilderInterface $builder=null)
    {
        $search = new Search(static::$_client, $builder);
        $search->addType($type);
        $search->addIndex($type->getIndex());
        $search->setOptions($options);
        $search->setQuery($query);

        return $search->search();
    }

    /**
     * @param Type $type
     * @param array $mapping_properties
     * @param array $mapping_params
     * @return bool|\Elastica\Type\Mapping
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
}

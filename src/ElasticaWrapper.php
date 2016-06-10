<?php
namespace CsDavidson\LaravelElastica;

use Elastica\Client;
use Elastica\Document;
use Elastica\Index;
use Elastica\Query;
use Elastica\ResultSet;
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
     * @var \Elastica\Client
     */
    protected $_client;

    /**
     * @param $document_id
     * @param array $document_data
     * @param \Elastica\Type $type
     * @param bool $refresh_index
     * @return bool|\Elastica\Document
     */
    public function addDocument($document_id, array $document_data=[], Type $type, $refresh_index=true)
    {
        $document = new Document($document_id, $document_data);

        /**
         * @var \Elastica\Response $response
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
    public function createIndex($name, array $index_args, $options=['recreate'=>true])
    {
        $index = $this->_client->getIndex($name);

        /**
         * Create the Index on ElasticSearch
         * @var \Elastica\Response $response
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
     * @param \Elastica\Type|null $type
     * @param \Elastica\Index|null $index
     * @return \Elastica\Document
     */
    public function getDocument($id, $data=[], Type $type=null, Index $index=null)
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
    public function getIndex($name)
    {
        return $this->_client->getIndex($name);
    }

    /**
     * @param $query
     * @return \Elastica\Query
     */
    public function getQuery($query=null)
    {
        return new Query($query);
    }

    /**
     * @param \Elastica\Type|null $type
     * @param array $properties
     * @return \Elastica\Type\Mapping
     */
    public function getMapping(Type $type=null, array $properties=[])
    {
        return new Mapping($type, $properties);
    }

    /**
     * @param \Elastica\Type $type
     * @param \Elastica\Query|null $query
     * @param array $options
     * @param \Elastica\ResultSet\BuilderInterface|null $builder
     * @return \Elastica\Search
     */
    public function getSearch(Type $type, Query $query=null, array $options=[], BuilderInterface $builder=null)
    {
        $search = new Search($this->_client, $builder);
        $search->addType($type);
        $search->addIndex($type->getIndex());
        $search->setOptions($options);
        $search->setQuery($query);

        return $search;
    }

    /**
     * @param \Elastica\Index $index
     * @param $name
     * @return \Elastica\Type
     */
    public function getType(Index $index, $name)
    {
        return new Type($index, $name);
    }

    /**
     * @param array $config
     * @param null $callback
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function initClient(array $config, $callback=null, LoggerInterface $logger=null)
    {
        $this->_client = new Client($config, $callback, $logger);
    }

    /**
     * @param \Elastica\ResultSet $result_set
     * @return array
     */
    public function parseResultSetHits(ResultSet $result_set)
    {
        $parsed_results = [];

        foreach ($result_set->getResults() as $result) {
            $hit        = $result->getHit();
            $hit_fields = [];

            foreach ($hit['fields'] as $field_name => $value) {
                $hit_fields[$field_name] = $value[0];
            }

            $parsed_results[] = $hit_fields;
        }

        return $parsed_results;
    }

    /**
     * @param \Elastica\Query $query
     * @param \Elastica\Type $type
     * @param bool $parse_hit_fields
     * @param array $options
     * @param \Elastica\ResultSet\BuilderInterface|null $builder
     * @return \Elastica\ResultSet
     */
    public function search(Query $query, Type $type, $parse_hit_fields=true, array $options=[], BuilderInterface $builder=null)
    {
        $search = new Search($this->_client, $builder);
        $search->addType($type);
        $search->addIndex($type->getIndex());
        $search->setOptions($options);
        $search->setQuery($query);

        $search_result_set = $search->search();

        if ($parse_hit_fields) {
            return $this->parseResultSetHits($search_result_set);
        }

        return $search->search();
    }

    /**
     * @param \Elastica\Type $type
     * @param array $mapping_properties
     * @param array $mapping_params
     * @return bool|\Elastica\Type\Mapping
     */
    public function sendMapping(Type $type, array $mapping_properties=[], array $mapping_params=[])
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

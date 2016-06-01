<?php
namespace CsDavidson\LaravelElastica\Facades;

use CsDavidson\LaravelElastica\ElasticSearchConstants;
use Illuminate\Support\Facades\Facade;

/**
 * Class ElasticSearch
 * @package CsDavidson\LaravelElastica\Facades
 */
class ElasticSearch extends Facade
{
    /**
     * @inheritdoc
     */
    public static function getFacadeAccessor()
    {
        return ElasticSearchConstants::FACADE_ACCESSOR;
    }
}

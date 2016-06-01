<?php
namespace CsDavidson\LaravelElastica;

use Elastica\Type\Mapping;

/**
 * Class ElasticaWrapper
 * @package CsDavidson\LaravelElastica
 */
class ElasticaWrapper
{
    /**
     * @return Mapping
     */
    public static function mapping()
    {
        return new Mapping();
    }
}

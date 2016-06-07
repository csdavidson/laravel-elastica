<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ElasticSearch Node Connections
    |--------------------------------------------------------------------------
    */

    'cluster' => [

        'host'              => null,
        'port'              => null,
        'path'              => null,
        'url'               => null,
        'proxy'             => null,
        'transport'         => null,
        'persistent'        => true,
        'timeout'           => null,
        'roundRobin'        => false,
        'log'               => false,
        'retryOnConflict'   => 0,
        'bigintConversion'  => false,
        'username'          => null,
        'password'          => null,

        'connections'       => [ // host, port, path, timeout, transport, compression, persistent, timeout, config -> (curl, headers, url)
            
            'node_1' => [
                'host'          => env('ELASTICSEARCH_NODE_1_HOST', 'localhost'),
                'port'          => env('ELASTICSEARCH_NODE_1_PORT', 9200),
                'path'          => null,
                'timeout'       => null,
                'transport'     => null,
                'compression'   => null,
                'persistent'    => true,
            ],
        ],
        
    ],

];

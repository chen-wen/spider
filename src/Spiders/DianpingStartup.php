<?php 
namespace Wayne\Spider\Spiders;

use Wayne\Spider\Spider;
use Redis;

class DianpingStartup extends Spider{
    
    protected $name = 'dianping-startup';

    protected $startup = [
        'http://www.dianping.com/search/category/2/35/g33831',
    ];

    public function setup()
    {

    }

    public function getPageGenerator()
    {
        return false;
    }

    public function getSchema()
    {
        return [
            'name'=> 'regions',
            'data'=>'array',       // collection|json|array|string|integer
            'type'=>'css',  // css|xpath|regex
            'expression'=>'#region-nav a::attr(href)',
        ];
    }

    public function handle($data, $response)
    {
        return $data;
    }

    public function terminal($data, $response)
    {
        // logger($data);
        Redis::sadd('dianping-list', ...$data['regions']);
    }
}

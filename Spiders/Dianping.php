<?php 
namespace Wayne\Spider\Spiders;

use Wayne\Spider\Spider;

class Dianping extends Spider{
    
    protected $name = 'dianping-startup';

    public function getDescription()
    {
        return [
            'data'=>'collection|object|array',
            'type'=>'css|xpath|regex',
            'expression'=>'collection',
            'items' => [
                'name' => [
                    'data'=>'string',
                    'type'=>'css|xpath|regex',
                    'expression'=>'collection',
                ],
                'address' => [
                    'data'=>'integer',
                    'type'=>'css|xpath|regex',
                    'expression'=>'collection',
                ],
            ],
        ];
    }

    public function setup()
    {
        
    }

    public function getPageGenerator()
    {
        return null;
    }

    public function handle($data, $response)
    {

    }

    public function terminal($data, $response)
    {

    }
}

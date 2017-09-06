<?php 
namespace Wayne\Spider\Spiders;

use Wayne\Spider\Spider;

class Dianping extends Spider{
    
    protected $name = 'dianping-startup';

    public function getDescription()
    {
        return [
            'name'=> 'list',
            'data'=>'collection|json|array|string|integer',
            'type'=>'css|xpath|regex',
            'expression'=>'collection',
            'items' => [
                [
                    'name'=>'name',
                    'data'=>'string',
                    'type'=>'css|xpath|regex',
                    'expression'=>'collection',
                ],
                [
                    'name' => 'address',
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
        logger($data->getBody());
    }

    public function terminal($data, $response)
    {

    }
}

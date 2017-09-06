<?php 
namespace Wayne\Spider\Spiders;

use Wayne\Spider\Spider;

class DianpingList extends Spider{
    
    protected $name = 'dianping-startup';
    protected $startup = [
        'http://www.dianping.com/search/category/2/35/g2926',
    ];

    public function getSchema()
    {
        return [
            'name'=> 'list',
            'data'=>'collection',       // collection|json|array|string|integer
            'type'=>'css',  // css|xpath|regex
            'expression'=>'#shop-all-list li::html()',
            'items' => [
                [
                    'name'=>'name',
                    'data'=>'string',
                    'type'=>'css',
                    'expression'=>'.tit h4',
                ],
                [
                    'name' => 'comment',
                    'data'=>'integer',
                    'type'=>'css',
                    'expression'=>'.review-num b',
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
        logger($data);
        return $data;
    }

    public function terminal($data, $response)
    {

    }
}

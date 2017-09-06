<?php 
namespace Wayne\Spider\Spiders;

use Wayne\Spider\Spider;

class DianpingDetail extends Spider{
    
    protected $name = 'dianping-detail';
    protected $startup = [
        'https://zhuanlan.zhihu.com/p/27784974'
    ];

    public function getSchema()
    {
        return [
            'name'=> 'detail',
            'data'=>'json',       // collection|json|array|string|integer
            'type'=>'css',  // css|xpath|regex
            'expression'=>'html::html()',
            'items' => [
                [
                    'name'=>'name',
                    'data'=>'string',
                    'type'=>'css',
                    'expression'=>'title',
                ],
                [
                    'name' => 'config',
                    'data'=>'json',
                    'type'=>'regex',
                    'expression'=>'/<textarea id="clientConfig" hidden>([\s\S]*?)<\/textarea>/',
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
